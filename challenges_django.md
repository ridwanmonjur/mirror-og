# Technical Challenges & Solutions - Driftwood Platform (Django Version)

*This document shows how the same challenges would be solved using Django framework*

## Challenge 1: Concurrent Payment Processing with Race Conditions

### The Challenge
Users could double-pay for tournament entries due to race conditions when multiple payment requests hit the server simultaneously.

### Django Solution

#### 1. Idempotency with Database Transactions
```python
# payments/services.py
from django.db import transaction
from django.core.cache import cache
import hashlib

class PaymentService:
    def create_payment_intent(self, user, amount, purpose):
        # Generate idempotency key
        idempotency_key = hashlib.sha256(
            f"{user.id}_{amount}_{purpose}".encode()
        ).hexdigest()

        # Check cache for existing payment
        cached_intent = cache.get(f"payment_intent:{idempotency_key}")
        if cached_intent:
            return cached_intent

        with transaction.atomic():
            # Use select_for_update to lock wallet row
            wallet = Wallet.objects.select_for_update().get(user=user)

            # Create payment intent
            payment_intent = stripe.PaymentIntent.create(
                amount=int(amount * 100),
                currency='myr',
                customer=user.stripe_customer_id,
                idempotency_key=idempotency_key
            )

            # Save to database
            PaymentIntent.objects.create(
                user=user,
                payment_intent_id=payment_intent.id,
                amount=amount,
                status=payment_intent.status
            )

            # Cache for 1 hour
            cache.set(
                f"payment_intent:{idempotency_key}",
                payment_intent,
                3600
            )

            return payment_intent
```

#### 2. Database-Level Locking
```python
# events/views.py
from django.db import transaction
from django.db.models import F

class ParticipantCheckoutView(APIView):
    @transaction.atomic
    def post(self, request, event_id):
        # Lock wallet for update
        wallet = Wallet.objects.select_for_update().get(user=request.user)

        # Use F() expressions for atomic updates
        if wallet.usable_balance < request.data['wallet_to_decrement']:
            return Response(
                {'error': 'Insufficient balance'},
                status=400
            )

        # Atomic update prevents race conditions
        Wallet.objects.filter(id=wallet.id).update(
            usable_balance=F('usable_balance') - request.data['wallet_to_decrement'],
            current_balance=F('current_balance') - request.data['wallet_to_decrement']
        )

        # Create payment record
        payment = ParticipantPayment.objects.create(
            user=request.user,
            amount=request.data['payment_amount'],
            payment_type='wallet'
        )

        return Response({'success': True})
```

#### 3. Using Django's get_or_create
```python
# Atomic get or create pattern
payment_intent, created = PaymentIntent.objects.get_or_create(
    payment_intent_id=stripe_payment_intent_id,
    defaults={
        'user': user,
        'amount': amount,
        'status': 'pending'
    }
)

if not created:
    # Already exists - don't process again
    return Response({'message': 'Payment already processed'}, status=400)
```

---

## Challenge 2: Event-Driven Architecture with Celery

### The Challenge
Tournament actions trigger multiple slow operations blocking user requests.

### Django Solution

#### 1. Django Signals for Event Broadcasting
```python
# events/signals.py
from django.dispatch import Signal, receiver
from django.db.models.signals import post_save

# Define custom signals
join_event_confirmed = Signal()
match_result_reported = Signal()
payment_completed = Signal()

# events/models.py
class JoinEvent(models.Model):
    # ... fields ...

    def save(self, *args, **kwargs):
        is_new = self.pk is None
        was_confirmed = False

        if not is_new:
            old_instance = JoinEvent.objects.get(pk=self.pk)
            was_confirmed = (old_instance.join_status != 'confirmed'
                           and self.join_status == 'confirmed')

        super().save(*args, **kwargs)

        # Emit signal on confirmation
        if was_confirmed:
            from .signals import join_event_confirmed
            join_event_confirmed.send(
                sender=self.__class__,
                join_event=self,
                user=self.user
            )
```

#### 2. Celery Tasks as Listeners
```python
# events/tasks.py
from celery import shared_task
from django.core.mail import send_mail
from .signals import join_event_confirmed

@shared_task(
    bind=True,
    autoretry_for=(Exception,),
    retry_kwargs={'max_retries': 3, 'countdown': 60},
    retry_backoff=True
)
def send_confirmation_email(self, join_event_id):
    """Send confirmation email to participant"""
    join_event = JoinEvent.objects.get(id=join_event_id)

    send_mail(
        subject=f'Registration Confirmed: {join_event.event.event_name}',
        message=f'Your registration has been confirmed!',
        from_email='noreply@driftwood.com',
        recipient_list=[join_event.user.email],
        fail_silently=False
    )

@shared_task
def notify_organizer_new_participant(join_event_id):
    """Notify organizer of new participant"""
    join_event = JoinEvent.objects.select_related('event__user').get(id=join_event_id)

    Notification.objects.create(
        user=join_event.event.user,
        type='event',
        message=f'New participant joined your event!',
        link=f'/organizer/events/{join_event.event.id}'
    )

@shared_task
def log_participant_activity(join_event_id, user_id):
    """Log activity"""
    ActivityLog.objects.create(
        user_id=user_id,
        action='joined_event',
        subject_type='EventDetail',
        subject_id=join_event_id
    )

# Connect signals to tasks
@receiver(join_event_confirmed)
def handle_join_event_confirmed(sender, join_event, user, **kwargs):
    # Queue all tasks asynchronously
    send_confirmation_email.delay(join_event.id)
    notify_organizer_new_participant.delay(join_event.id)
    log_participant_activity.delay(join_event.id, user.id)
```

#### 3. Celery Configuration
```python
# config/celery.py
from celery import Celery
import os

os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'config.settings')

app = Celery('driftwood')
app.config_from_object('django.conf:settings', namespace='CELERY')

# Priority queues
app.conf.task_routes = {
    'events.tasks.send_confirmation_email': {'queue': 'high'},
    'events.tasks.notify_organizer': {'queue': 'high'},
    'events.tasks.log_participant_activity': {'queue': 'low'},
    'analytics.tasks.*': {'queue': 'low'},
}

app.conf.task_default_priority = 5
app.conf.task_default_queue = 'default'

# Auto-discover tasks
app.autodiscover_tasks()

# settings.py
CELERY_BROKER_URL = 'redis://localhost:6379/0'
CELERY_RESULT_BACKEND = 'redis://localhost:6379/0'
CELERY_TASK_SERIALIZER = 'json'
CELERY_ACCEPT_CONTENT = ['json']
CELERY_TASK_TRACK_STARTED = True
CELERY_TASK_TIME_LIMIT = 30 * 60  # 30 minutes
```

#### 4. Task Chaining & Grouping
```python
from celery import chain, group, chord

# Sequential execution (chain)
def process_event_cancellation(join_event_id):
    result = chain(
        cancel_registration.si(join_event_id),
        process_refunds.si(join_event_id),
        send_cancellation_emails.si(join_event_id),
        log_cancellation.si(join_event_id)
    ).apply_async()

    return result

# Parallel execution (group)
def process_tournament_results(event_id):
    event = EventDetail.objects.get(id=event_id)
    teams = event.join_events.all()

    # Process teams in parallel (chunks of 50)
    job = group(
        calculate_team_standings.s(chunk)
        for chunk in chunks(teams, 50)
    )

    result = job.apply_async()
    return result.id

# Chord (parallel then callback)
def process_with_callback(event_id):
    callback = finalize_tournament_results.s(event_id)

    header = group(
        calculate_team_standings.s(chunk)
        for chunk in chunks(teams, 50)
    )

    result = chord(header)(callback)
    return result
```

---

## Challenge 3: Service Layer with Dependency Injection

### The Challenge
Views became bloated with business logic.

### Django Solution

#### 1. Service Layer Classes
```python
# payments/services.py
class PaymentService:
    def __init__(self, stripe_client=None):
        self.stripe_client = stripe_client or stripe

    def refund_payments_for_event(self, join_event_id, penalty_percent=0):
        """Refund payments with optional penalty"""
        payments = ParticipantPayment.objects.filter(
            join_events_id=join_event_id
        ).select_related('user')

        refunds = {}

        with transaction.atomic():
            for payment in payments:
                refund_amount = payment.payment_amount * (1 - penalty_percent / 100)

                if payment.payment_type == 'wallet':
                    refunds[payment.user_id] = {
                        'wallet': self._refund_to_wallet(payment.user_id, refund_amount)
                    }
                elif payment.payment_type == 'stripe':
                    refunds[payment.user_id] = {
                        'stripe': self._refund_to_stripe(payment.payment_id, refund_amount)
                    }

        return refunds

    def _refund_to_wallet(self, user_id, amount):
        """Private method for wallet refund"""
        wallet = Wallet.objects.select_for_update().get(user_id=user_id)

        Wallet.objects.filter(id=wallet.id).update(
            usable_balance=F('usable_balance') + amount,
            current_balance=F('current_balance') + amount
        )

        TransactionHistory.objects.create(
            name=f"Event Cancellation Refund: RM {amount}",
            transaction_type='refund',
            amount=amount,
            user_id=user_id
        )

        return amount

    def _refund_to_stripe(self, payment_id, amount):
        """Private method for Stripe refund"""
        stripe_payment = RecordStripe.objects.get(id=payment_id)

        self.stripe_client.Refund.create(
            payment_intent=stripe_payment.payment_intent_id,
            amount=int(amount * 100)
        )

        return amount
```

#### 2. Dependency Injection with Django Extensions
```python
# Using django-injector for DI
from injector import Module, provider, singleton
from django_injector import inject

class ServiceModule(Module):
    @singleton
    @provider
    def provide_payment_service(self) -> PaymentService:
        return PaymentService()

    @singleton
    @provider
    def provide_event_match_service(
        self,
        payment_service: PaymentService
    ) -> EventMatchService:
        return EventMatchService(payment_service)

# events/views.py
from django_injector import inject

class ParticipantEventView(APIView):
    @inject
    def __init__(
        self,
        payment_service: PaymentService,
        event_match_service: EventMatchService,
        **kwargs
    ):
        super().__init__(**kwargs)
        self.payment_service = payment_service
        self.event_match_service = event_match_service

    def post(self, request, event_id):
        # Use injected services
        result = self.event_match_service.confirm_registration(
            join_event=join_event,
            user=request.user
        )

        return Response(result)
```

#### 3. Alternative: Manual DI with Class Methods
```python
# Simple DI without third-party libraries
class ParticipantEventView(APIView):
    payment_service_class = PaymentService
    event_match_service_class = EventMatchService

    def get_payment_service(self):
        return self.payment_service_class()

    def get_event_match_service(self):
        return self.event_match_service_class(
            payment_service=self.get_payment_service()
        )

    def post(self, request, event_id):
        service = self.get_event_match_service()
        result = service.confirm_registration(
            join_event=join_event,
            user=request.user
        )
        return Response(result)

# In tests, override service classes
class TestParticipantEventView(TestCase):
    def setUp(self):
        self.view = ParticipantEventView()
        self.view.payment_service_class = MockPaymentService
```

---

## Challenge 4: Query Optimization with Django ORM

### The Challenge
N+1 query problems causing slow page loads.

### Django Solution

#### 1. Select Related & Prefetch Related
```python
# events/views.py
from django.db.models import Prefetch

class EventListView(APIView):
    def get(self, request):
        # Efficient query with joins
        events = EventDetail.objects.select_related(
            'tier',          # ForeignKey - use select_related
            'game',
            'user',
            'type',
            'signup'
        ).prefetch_related(   # ManyToMany/Reverse FK - use prefetch_related
            Prefetch(
                'join_events',
                queryset=JoinEvent.objects.filter(
                    join_status='confirmed'
                ).select_related('team')
            )
        ).only(  # Select only needed columns
            'id', 'event_name', 'event_banner', 'start_date',
            'start_time', 'event_tier_id', 'user_id'
        ).filter(
            status__ne='DRAFT'
        )[:20]

        # Result: 3-5 queries instead of 700+
        serializer = EventSerializer(events, many=True)
        return Response(serializer.data)
```

#### 2. Custom Query Methods (Managers)
```python
# events/models.py
from django.db.models import Q, Count, F

class EventQuerySet(models.QuerySet):
    def landing_page_query(self, current_datetime):
        """Optimized query for landing page"""
        return self.select_related(
            'tier', 'game', 'user'
        ).only(
            'id', 'event_name', 'event_banner',
            'start_date', 'start_time'
        ).filter(
            ~Q(status='PENDING'),
            Q(start_date__gt=current_datetime.date()) |
            Q(
                start_date=current_datetime.date(),
                start_time__gt=current_datetime.time()
            )
        )

    def with_join_counts(self):
        """Add aggregate counts"""
        return self.annotate(
            confirmed_count=Count(
                'join_events',
                filter=Q(join_events__join_status='confirmed')
            ),
            pending_count=Count(
                'join_events',
                filter=Q(join_events__join_status='pending')
            )
        )

    def filter_by_params(self, params):
        """Dynamic filtering"""
        qs = self

        if 'category' in params:
            qs = qs.filter(event_category_id=params['category'])
        if 'tier' in params:
            qs = qs.filter(event_tier_id=params['tier'])
        if 'search' in params:
            qs = qs.filter(event_name__icontains=params['search'])

        return qs

class EventManager(models.Manager):
    def get_queryset(self):
        return EventQuerySet(self.model, using=self._db)

    def landing_page_query(self, current_datetime):
        return self.get_queryset().landing_page_query(current_datetime)

    def with_join_counts(self):
        return self.get_queryset().with_join_counts()

class EventDetail(models.Model):
    # ... fields ...

    objects = EventManager()

    # Usage
    events = EventDetail.objects.landing_page_query(now).filter_by_params(request.GET)
```

#### 3. Database Indexing
```python
# events/models.py
class EventDetail(models.Model):
    event_name = models.CharField(max_length=255, db_index=True)
    status = models.CharField(max_length=50, db_index=True)
    start_date = models.DateField(db_index=True)
    event_tier = models.ForeignKey(
        'EventTier',
        on_delete=models.CASCADE,
        db_index=True  # Automatic for ForeignKey
    )

    class Meta:
        # Compound indexes
        indexes = [
            models.Index(fields=['status', 'start_date', 'start_time']),
            models.Index(fields=['user', 'status']),
        ]

        # Unique together (also creates index)
        unique_together = [('event', 'user')]
```

#### 4. Caching with Django Cache Framework
```python
from django.core.cache import cache
from django.views.decorators.cache import cache_page

# Function-based view caching
@cache_page(60 * 15)  # Cache for 15 minutes
def event_list(request):
    events = EventDetail.objects.all()
    return render(request, 'events/list.html', {'events': events})

# Manual caching
class WalletManager(models.Manager):
    def retrieve_or_create_cached(self, user_id):
        cache_key = f'wallet:user:{user_id}'

        wallet = cache.get(cache_key)
        if wallet is None:
            wallet, created = self.get_or_create(
                user_id=user_id,
                defaults={
                    'usable_balance': 0.00,
                    'current_balance': 0.00,
                }
            )
            cache.set(cache_key, wallet, 3600)  # 1 hour

        return wallet

# Clear cache on save
class Wallet(models.Model):
    # ... fields ...

    def save(self, *args, **kwargs):
        super().save(*args, **kwargs)
        cache_key = f'wallet:user:{self.user_id}'
        cache.delete(cache_key)
```

---

## Challenge 5: API Throttling with Django REST Framework

### The Challenge
API endpoints being abused with excessive requests.

### Django Solution

#### 1. DRF Throttling Classes
```python
# config/throttling.py
from rest_framework.throttling import UserRateThrottle, AnonRateThrottle

class LoginRateThrottle(AnonRateThrottle):
    rate = '5/minute'

class PaymentRateThrottle(UserRateThrottle):
    rate = '30/minute'

class SearchRateThrottle(UserRateThrottle):
    rate = '30/minute'

# Per-user dynamic throttling
class DynamicUserRateThrottle(UserRateThrottle):
    def get_rate(self):
        user = self.request.user

        if user and user.is_staff:
            return None  # Unlimited for staff

        if user and hasattr(user, 'subscription_tier'):
            if user.subscription_tier == 'premium':
                return '200/minute'

        if user and user.is_authenticated:
            return '100/minute'

        return '30/minute'  # Anonymous users

    def get_cache_key(self, request, view):
        if request.user and request.user.is_authenticated:
            ident = request.user.pk
        else:
            ident = self.get_ident(request)

        return self.cache_format % {
            'scope': self.scope,
            'ident': ident
        }

# settings.py
REST_FRAMEWORK = {
    'DEFAULT_THROTTLE_CLASSES': [
        'config.throttling.DynamicUserRateThrottle',
    ],
    'DEFAULT_THROTTLE_RATES': {
        'anon': '30/minute',
        'user': '100/minute',
    }
}
```

#### 2. Apply to Views
```python
from rest_framework.views import APIView
from rest_framework.decorators import api_view, throttle_classes

class LoginView(APIView):
    throttle_classes = [LoginRateThrottle]

    def post(self, request):
        # Login logic
        pass

class PaymentCheckoutView(APIView):
    throttle_classes = [PaymentRateThrottle]
    permission_classes = [IsAuthenticated]

    def post(self, request):
        # Payment logic
        pass

# Function-based views
@api_view(['POST'])
@throttle_classes([LoginRateThrottle])
def login(request):
    # Login logic
    pass
```

---

## Challenge 6: Custom Middleware Pipeline

### The Challenge
Cross-cutting concerns like JWT validation, logging, CORS scattered across views.

### Django Solution

#### 1. JWT Middleware
```python
# middleware/jwt_middleware.py
import jwt
from django.http import JsonResponse
from django.contrib.auth.models import User

class JWTAuthenticationMiddleware:
    def __init__(self, get_response):
        self.get_response = get_response

    def __call__(self, request):
        # Process request
        auth_header = request.META.get('HTTP_AUTHORIZATION', '')

        if auth_header.startswith('Bearer '):
            token = auth_header.split(' ')[1]

            try:
                payload = jwt.decode(
                    token,
                    settings.SECRET_KEY,
                    algorithms=['HS256']
                )

                user = User.objects.get(id=payload['user_id'])
                request.user = user

            except jwt.ExpiredSignatureError:
                return JsonResponse(
                    {'error': 'Token expired'},
                    status=401
                )
            except jwt.InvalidTokenError:
                return JsonResponse(
                    {'error': 'Invalid token'},
                    status=401
                )
            except User.DoesNotExist:
                return JsonResponse(
                    {'error': 'User not found'},
                    status=404
                )

        response = self.get_response(request)
        return response
```

#### 2. Request Logging Middleware
```python
import time
import logging

logger = logging.getLogger(__name__)

class RequestLoggingMiddleware:
    def __init__(self, get_response):
        self.get_response = get_response

    def __call__(self, request):
        start_time = time.time()

        # Log incoming request
        logger.info(f"Incoming: {request.method} {request.path}", extra={
            'ip': self.get_client_ip(request),
            'user_id': getattr(request.user, 'id', None)
        })

        response = self.get_response(request)

        # Log outgoing response
        duration = time.time() - start_time
        logger.info(f"Outgoing: {request.method} {request.path} - {response.status_code}", extra={
            'duration_ms': round(duration * 1000, 2)
        })

        # Alert on slow requests
        if duration > 2:
            logger.warning(f"Slow request: {request.path} took {duration}s")

        return response

    def get_client_ip(self, request):
        x_forwarded_for = request.META.get('HTTP_X_FORWARDED_FOR')
        if x_forwarded_for:
            return x_forwarded_for.split(',')[0]
        return request.META.get('REMOTE_ADDR')
```

#### 3. CORS Middleware
```python
class CorsMiddleware:
    def __init__(self, get_response):
        self.get_response = get_response
        self.allowed_origins = settings.CORS_ALLOWED_ORIGINS

    def __call__(self, request):
        origin = request.META.get('HTTP_ORIGIN')

        # Handle preflight
        if request.method == 'OPTIONS':
            response = HttpResponse()
            response['Access-Control-Allow-Origin'] = origin
            response['Access-Control-Allow-Methods'] = 'GET, POST, PUT, DELETE, OPTIONS'
            response['Access-Control-Allow-Headers'] = 'Content-Type, Authorization'
            response['Access-Control-Max-Age'] = '86400'
            return response

        response = self.get_response(request)

        if origin in self.allowed_origins or '*' in self.allowed_origins:
            response['Access-Control-Allow-Origin'] = origin
            response['Access-Control-Allow-Credentials'] = 'true'

        return response
```

#### 4. Middleware Ordering
```python
# settings.py
MIDDLEWARE = [
    'django.middleware.security.SecurityMiddleware',
    'middleware.cors.CorsMiddleware',  # CORS first
    'middleware.logging.RequestLoggingMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'middleware.jwt.JWTAuthenticationMiddleware',  # Auth before views
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
]
```

---

## Challenge 7: Social Relationship Management System

### The Challenge
Building a multi-faceted social graph system supporting bidirectional friendships, unidirectional follows, private starring, and user reporting with moderation workflows.

### Django Solution

#### Architecture Overview
**Models**: `Friend`, `ParticipantFollow`, `OrganizerFollow`, `TeamFollow`, `UserStar`, `Report`
**Services**: `SocialService` class with methods `handle_friend_operation`, `handle_participant_follow`, `toggle_star`
**Views**: `SocialAPIView` with endpoints using DRF viewsets
**Serializers**: `FriendSerializer`, `FollowSerializer`, `ReportSerializer` with nested user representations

#### Key Methods and Functions

**Friend Model Methods**:
- `get_friend_count(user_id)`: Static method calculating bidirectional friendship count by querying where user_id matches either `user_id` or `friend_id` columns, then dividing result by 2 to avoid double-counting
- `get_friends_paginate(user_id, logged_user_id, per_page, page, search)`: Returns paginated friend list with relationship status for logged-in user
- `get_relationship_status(user_id, target_id)`: Returns enum values: 'none', 'friends', 'request_sent', 'request_received' based on bidirectional query

**SocialService Methods**:
- `send_friend_request(user_id, target_id)`: Creates `Friend` instance with status='pending', checks for existing relationships using Q objects with OR conditions, prevents duplicate requests
- `accept_friend_request(user_id, requester_id)`: Updates Friend.status from 'pending' to 'accepted', triggers Django signal `friend_request_accepted`
- `reject_friend_request(user_id, requester_id)`: Either deletes Friend record or updates status to 'rejected' depending on business logic
- `remove_friend(user_id, friend_id)`: Deletes Friend record matching either direction of relationship

**ParticipantFollow Model (Unidirectional)**:
- `get_follower_count(user_id)`: Counts records where `followed_user_id` equals user_id
- `get_following_count(user_id)`: Counts records where `user_id` equals user_id
- `get_followers_paginate`: Uses Django ORM `select_related` on user FK, applies search filter with Q objects on name/username using `__icontains` lookup
- Returns list including `is_following` boolean calculated by checking if logged user follows each person

**Star System (Private Favorites)**:
- ManyToMany relationship using through table `user_stars` with columns `user_id` and `starred_user_id`
- `has_starred(user, target_user)`: Checks existence in pivot table using `exists()` query
- `toggle_star(user, target_user)`: Uses `attach` or `detach` methods on ManyToMany manager
- Stars are never exposed in public APIs, only accessible to owner

**Report System**:
- Model fields: `reporter_id`, `reported_user_id`, `reason` (enum), `description` (text), `status` (enum: pending/reviewed/dismissed)
- `can_report(reporter_id, reported_user_id)`: Static method checking self-reporting prevention and rate limit using `created_at__gte=timezone.now() - timedelta(days=1)`
- `get_reasons_enum()`: Returns dictionary of valid report reasons used in form validation
- Admin moderation uses Django Admin actions for bulk status updates

#### Database Queries and Optimizations

**Complex Bidirectional Friend Query**:
Uses Django Q objects: `Q(user_id=user_id) | Q(friend_id=user_id)` combined with `.filter(status='accepted')`

**N+1 Prevention**:
- `select_related('user', 'friend')` for ForeignKey relationships
- `prefetch_related('stars')` for ManyToMany relationships
- Custom manager methods using `annotate(follower_count=Count('followers'))`

**Pagination Strategy**:
Django's `Paginator` class with custom page_size, returns `Page` object with `has_next()`, `has_previous()` methods

**Index Strategy**:
- Composite indexes on `(user_id, friend_id, status)` for Friend lookups
- Single index on `followed_user_id` for follower counts
- Unique constraint on `(user_id, starred_user_id)` for stars

---

## Challenge 8: Real-Time Chat with Dual Storage Architecture

### The Challenge
Implementing hybrid storage pattern where Firebase provides real-time synchronization while PostgreSQL maintains permanent chat history for search and analytics.

### Django Solution

#### Architecture Pattern: CQRS (Command Query Responsibility Segregation)

**Write Path**: Django → PostgreSQL + Firebase (async)
**Read Path (Real-time)**: Frontend → Firebase Firestore listeners
**Read Path (History)**: Frontend → Django REST API → PostgreSQL

#### Key Components

**ChatService Class Methods**:
- `create_or_get_room(user1_id, user2_id)`: Generates deterministic room_id using `min(user1_id, user2_id)_max(user1_id, user2_id)` pattern, ensures consistent room identity regardless of who initiates
- `ensure_firebase_room(room_id, user1_id, user2_id)`: Checks Firestore document existence using `document_ref.get().exists`, creates if missing
- `send_message(room_id, sender_id, message)`: Atomic operation using `@transaction.atomic` decorator, writes to both PostgreSQL and Firestore

**Dual Write Pattern**:
1. Begin database transaction using Django's `transaction.atomic()` context manager
2. Create `ChatMessage` record in PostgreSQL using ORM create() method
3. Update `ChatRoom.last_message_at` timestamp using F expression for atomicity
4. Add message to Firestore collection using `collection.add()` method
5. Trigger `send_offline_notification` method checking presence in Firestore
6. Commit transaction - if Firestore fails, rollback PostgreSQL changes

**Presence Detection System**:
- Firestore collection `presence/{user_id}` with fields: `online` (boolean), `last_seen` (server timestamp)
- Frontend sets presence on mount using `set_doc(presence_ref, {online: true})`
- Uses Firebase `on_disconnect()` handler to set `online: false` when user disconnects
- Backend checks presence before sending offline notifications

**Typing Indicators**:
- Ephemeral Firestore document `typing/{room_id}` with map of `{user_id: boolean}`
- Frontend updates on keypress with 3-second auto-clear timeout
- Other participants listen via `onSnapshot` Firestore listener
- No PostgreSQL storage - purely real-time data

**Read Receipts Synchronization**:
- Method `mark_as_read(room_id, user_id)` updates both databases
- PostgreSQL: Bulk update using `ChatMessage.objects.filter(...).update(is_read=True)`
- Firestore: Batch update using Firestore batch writes for efficiency
- Uses F expressions to prevent race conditions: `filter(sender_id__ne=user_id)`

#### Frontend Integration (Petite Vue)

**Firebase Initialization Module**:
- `initFirebase()`: Initializes Firebase app with environment variables from Django settings
- Returns Firestore database instance stored in component data

**Message Listener Setup**:
- `listenToMessages()`: Creates Firestore query using `query(collection_ref, orderBy('timestamp', 'asc'))`
- Calls `onSnapshot` method which returns unsubscribe function
- Processes `docChanges()` array filtering for `change.type === 'added'`
- Appends new messages to reactive `messages` array
- Calls `$nextTick()` then `scrollToBottom()` for smooth UX

**Optimistic UI Updates**:
- Immediately appends message to local `messages` array before Firebase write
- Sends message to Firebase using `addDoc(collection_ref, data)`
- Sends duplicate to Django API endpoint for PostgreSQL persistence
- On failure, removes optimistic message and shows error toast

**Room ID Generation**:
Deterministic algorithm ensuring same room_id regardless of initiator: `const roomId = userId1 < userId2 ? ${userId1}_${userId2} : ${userId2}_${userId1}`

#### Security Rules (Firestore)

**Authentication Check Function**:
`isParticipant(roomId)` helper splits room_id string on underscore, verifies `request.auth.uid` matches either participant

**Message Subcollection Rules**:
- Read: Allowed if `isParticipant(roomId)` returns true
- Create: Allowed if participant AND `request.resource.data.sender_id == request.auth.uid`
- Update: Only allowed for `is_read` field using `affectedKeys().hasOnly(['is_read'])`

**Presence Collection Rules**:
- Read: Public (anyone can see online status)
- Write: Only own document using `request.auth.uid == userId`

---

## Challenge 9: Financial Wallet System with Banking Integration

### The Challenge
Building mission-critical wallet system handling tournament winnings, entry fee payments, bank withdrawals with zero-tolerance for financial discrepancies.

### Django Solution

#### Architecture: Triple-Entry Bookkeeping Pattern

Every financial operation creates three records:
1. Wallet balance update (aggregate)
2. TransactionHistory entry (audit log)
3. Related business entity update (JoinEvent, Withdrawal, etc.)

#### Wallet Model Design

**Balance Fields**:
- `current_balance`: Decimal field (precision=10, scale=2) representing total wallet funds
- `usable_balance`: Decimal field for immediately available funds
- `pending_balance`: Decimal field for locked funds (tournament entries pending confirmation)
- Invariant: `current_balance = usable_balance + pending_balance` enforced in model clean() method

**Critical Methods**:

**`retrieve_or_create_cached(user_id)`**:
- Class method using Django cache framework with key `wallet:user:{user_id}`
- Cache timeout 3600 seconds (1 hour)
- Uses `cache.get_or_set()` with default factory calling `get_or_create()` on Wallet manager
- Returns Wallet instance with guaranteed values (0.00 for new wallets)

**`credit(amount, description, transaction_type)`**:
- Wrapped in `@transaction.atomic` decorator ensuring ACID properties
- Uses `select_for_update()` to acquire row-level lock preventing concurrent modifications
- Employs Python Decimal class for precise arithmetic: `self.usable_balance + Decimal(str(amount))`
- Never uses float math which causes rounding errors
- Updates balance using F expression: `F('usable_balance') + amount`
- Creates TransactionHistory record with `balance_after` snapshot
- Invalidates cache using `cache.delete(cache_key)`
- Returns updated Wallet instance

**`debit(amount, description, transaction_type)`**:
- Identical pattern to credit but subtracts amount
- Adds validation: `if self.usable_balance < amount: raise InsufficientBalanceException`
- Records negative amount in TransactionHistory: `-amount` for clear audit trail
- Uses same locking strategy to prevent overdraft race conditions

**`lock_funds(amount, description)`**:
- Moves money from `usable_balance` to `pending_balance`
- Used when user registers for tournament but payment not yet confirmed
- Prevents withdrawal of pending funds
- Creates transaction with type='pending'
- Reversible using `unlock_funds()` method if registration cancelled

**`unlock_funds(amount)`**:
- Moves money from `pending_balance` back to `usable_balance`
- Used when tournament registration cancelled/refunded
- No transaction history entry (reversed via credit/debit in caller)

#### Withdrawal Service Architecture

**WithdrawalService Class Constants**:
- `MIN_AMOUNT = Decimal('10.00')`: Minimum withdrawal enforced
- `MAX_AMOUNT = Decimal('5000.00')`: Maximum per transaction (anti-fraud)
- `FEE = Decimal('1.00')`: Fixed withdrawal processing fee

**`request_withdrawal(user, amount)` Method Flow**:
1. Retrieve wallet using `retrieve_or_create_cached(user.id)`
2. Validate bank account linked: `if not wallet.has_bank_account: raise ValidationException`
3. Validate amount bounds using Decimal comparisons
4. Calculate total deduction: `total = amount + FEE` using Decimal arithmetic
5. Begin atomic transaction using `with transaction.atomic():`
6. Debit wallet calling `wallet.debit(total, description, 'withdrawal')`
7. Create `Withdrawal` model instance with status='pending'
8. Store encrypted bank details: `account_number` field uses Django's `EncryptedCharField`
9. Trigger Django signal `withdrawal_requested` for admin notification
10. Commit transaction - if any step fails, automatic rollback

**`process_withdrawal(withdrawal_id, admin_id)` Method**:
- Admin-only action processing pending withdrawals
- Updates Withdrawal.status from 'pending' to 'completed'
- Records `processed_by` admin user ID and `processed_at` timestamp
- In production: Calls bank API integration (e.g., Stripe Connect, bank ACH)
- On failure: Calls `refund_failed_withdrawal()` which credits wallet and marks withdrawal.status='failed'
- Sends confirmation email using Django's `send_mail()` function

**`link_bank_account(user, bank_data)` Method**:
- Validates bank_data using Django Forms or DRF Serializers
- Validates account_number regex pattern: `^[0-9]{10,18}$`
- Validates account_holder_name regex: `^[a-zA-Z\s]+$` (letters and spaces only)
- In production: Calls bank verification API (micro-deposits or instant verification)
- Encrypts account_number using `cryptography.fernet` or Django's EncryptedField
- Stores last 4 digits unencrypted for display: `bank_last4 = account_number[-4:]`
- Sets `has_bank_account=True` flag enabling withdrawal functionality
- Timestamps verification: `bank_details_updated_at = timezone.now()`

#### Transaction History Model

**Fields**:
- `user`: ForeignKey to User model with index for fast user queries
- `name`: CharField(255) human-readable description
- `transaction_type`: CharField with choices from `TRANSACTION_TYPES` enum
- `amount`: DecimalField(10, 2) positive for credits, negative for debits
- `balance_after`: DecimalField(10, 2) snapshot of wallet balance after transaction
- `metadata`: JSONField storing additional context (event_id, team_id, etc.)
- `date`: DateTimeField with auto_now_add=True and database index

**TRANSACTION_TYPES Enum**:
- 'credit': Generic money addition
- 'debit': Generic money deduction
- 'refund': Event cancellation refund
- 'withdrawal': Bank withdrawal
- 'winning': Tournament prize
- 'entry_fee': Tournament entry payment
- 'pending': Funds locked for pending operation

**Query Methods**:

**`get_transaction_history(user, filters)` Class Method**:
- Accepts filters dict with keys: type, start_date, end_date, limit, page
- Builds Django ORM query: `TransactionHistory.objects.filter(user=user).order_by('-date')`
- Applies optional filters using `filter(transaction_type=filters['type'])` if type provided
- Applies date range: `filter(date__gte=start_date, date__lte=end_date)`
- Uses Django Paginator: `Paginator(queryset, per_page=100)`
- Returns `Page` object with pagination metadata

**Index Strategy**:
- Composite index on `(user_id, date DESC)` for fast user history queries
- Separate index on `(user_id, transaction_type)` for filtered queries
- Partial index on pending transactions for cleanup jobs

#### Admin Interface (Django Admin / Filament equivalent)

**WithdrawalAdmin Configuration**:
- List display columns: id, user__username, amount, bank_name, status, requested_at
- List filters: status, requested_at date range
- Search fields: user__username, user__email, account_holder_name
- Custom actions: `approve_withdrawal`, `reject_withdrawal`
- Permissions: Only staff with 'can_process_withdrawals' permission can approve
- Audit trail: Automatically logs admin user ID in `processed_by` field

**Approval Workflow**:
1. Admin selects pending withdrawals from list view
2. Clicks "Approve Withdrawal" bulk action
3. System calls `WithdrawalService.process_withdrawal()` for each
4. Success notification shown with count of processed withdrawals
5. Failed withdrawals show error message with rollback confirmation

#### Security and Compliance

**Encryption**:
- Bank account numbers encrypted at rest using AES-256
- Encryption key stored in environment variable, never in code
- Uses Django's `Field.encrypt()` / `Field.decrypt()` methods
- Only last 4 digits stored unencrypted for UI display

**Audit Trail**:
- Every wallet operation logs TransactionHistory entry
- TransactionHistory records are immutable (no updates, only inserts)
- Soft delete pattern: Deletion sets `deleted_at` timestamp rather than removing record
- Admin actions logged in Django's LogEntry table

**Fraud Prevention**:
- Daily withdrawal limit enforced: Sum of user's withdrawals in last 24 hours
- Maximum pending withdrawals: User can't have more than 3 pending at once
- Velocity checks: Alert if user requests 5+ withdrawals in 1 hour
- IP logging: Track IP address of withdrawal requests
- Two-factor authentication required for withdrawals over RM 1000

---

*Django's ORM provides excellent transaction management, the Decimal type prevents float errors, and select_for_update ensures concurrent safety for financial operations.*
