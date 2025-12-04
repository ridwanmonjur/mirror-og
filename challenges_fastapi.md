# Technical Challenges & Solutions - Driftwood Platform (FastAPI Version)

*This document shows how the same challenges would be solved using FastAPI framework*

## Challenge 1: Concurrent Payment Processing with Race Conditions

### The Challenge
Users could double-pay for tournament entries due to race conditions.

### FastAPI Solution

#### 1. Async Database Transactions with SQLAlchemy
```python
# app/services/payment_service.py
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select
from sqlalchemy.orm import selectinload
import hashlib

class PaymentService:
    def __init__(self, db: AsyncSession, stripe_client):
        self.db = db
        self.stripe = stripe_client

    async def create_payment_intent(
        self,
        user_id: int,
        amount: float,
        purpose: str
    ):
        # Idempotency key
        idempotency_key = hashlib.sha256(
            f"{user_id}_{amount}_{purpose}".encode()
        ).hexdigest()

        # Check Redis cache first
        from app.cache import redis_client
        cached = await redis_client.get(f"payment_intent:{idempotency_key}")
        if cached:
            return json.loads(cached)

        async with self.db.begin():  # Transaction context
            # Lock wallet row
            stmt = (
                select(Wallet)
                .where(Wallet.user_id == user_id)
                .with_for_update()  # SELECT FOR UPDATE
            )
            result = await self.db.execute(stmt)
            wallet = result.scalar_one()

            # Create Stripe payment intent
            payment_intent = await asyncio.to_thread(
                self.stripe.PaymentIntent.create,
                amount=int(amount * 100),
                currency='myr',
                idempotency_key=idempotency_key
            )

            # Save to database
            db_payment = PaymentIntent(
                user_id=user_id,
                payment_intent_id=payment_intent.id,
                amount=amount,
                status=payment_intent.status
            )
            self.db.add(db_payment)

            await self.db.commit()

            # Cache for 1 hour
            await redis_client.setex(
                f"payment_intent:{idempotency_key}",
                3600,
                json.dumps(payment_intent)
            )

            return payment_intent
```

#### 2. Dependency Injection with FastAPI
```python
# app/api/deps.py
from fastapi import Depends
from sqlalchemy.ext.asyncio import AsyncSession
from app.db.session import get_db
from app.services.payment_service import PaymentService
import stripe

async def get_payment_service(
    db: AsyncSession = Depends(get_db)
) -> PaymentService:
    return PaymentService(db, stripe)

# app/api/endpoints/checkout.py
from fastapi import APIRouter, Depends, HTTPException
from app.api import deps
from app.schemas import PaymentRequest

router = APIRouter()

@router.post("/checkout")
async def checkout(
    request: PaymentRequest,
    payment_service: PaymentService = Depends(deps.get_payment_service),
    current_user: User = Depends(deps.get_current_user)
):
    try:
        result = await payment_service.create_payment_intent(
            user_id=current_user.id,
            amount=request.amount,
            purpose=request.purpose
        )
        return {"success": True, "payment_intent": result}
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))
```

#### 3. Optimistic Locking with Version Field
```python
# app/models/wallet.py
from sqlalchemy import Column, Integer, Float
from sqlalchemy.orm import validates

class Wallet(Base):
    __tablename__ = "wallets"

    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, unique=True)
    usable_balance = Column(Float, default=0.0)
    version = Column(Integer, default=0)  # Optimistic lock

    @validates('usable_balance')
    def validate_balance(self, key, value):
        if value < 0:
            raise ValueError("Balance cannot be negative")
        return value

# app/services/payment_service.py
async def deduct_wallet(self, user_id: int, amount: float):
    async with self.db.begin():
        # Get current version
        stmt = select(Wallet).where(Wallet.user_id == user_id)
        result = await self.db.execute(stmt)
        wallet = result.scalar_one()

        current_version = wallet.version

        # Update with version check
        update_stmt = (
            update(Wallet)
            .where(
                Wallet.user_id == user_id,
                Wallet.version == current_version  # Check version
            )
            .values(
                usable_balance=Wallet.usable_balance - amount,
                version=Wallet.version + 1
            )
        )

        result = await self.db.execute(update_stmt)

        if result.rowcount == 0:
            raise HTTPException(
                status_code=409,
                detail="Payment already processed by another request"
            )

        await self.db.commit()
```

---

## Challenge 2: Event-Driven Architecture with Background Tasks

### The Challenge
Tournament actions trigger slow operations.

### FastAPI Solution

#### 1. Background Tasks (Lightweight)
```python
from fastapi import BackgroundTasks

@router.post("/events/{event_id}/join")
async def join_event(
    event_id: int,
    background_tasks: BackgroundTasks,
    current_user: User = Depends(deps.get_current_user),
    db: AsyncSession = Depends(get_db)
):
    # Main business logic (fast)
    join_event = JoinEvent(
        user_id=current_user.id,
        event_id=event_id,
        status='pending'
    )
    db.add(join_event)
    await db.commit()

    # Queue background tasks (non-blocking)
    background_tasks.add_task(send_confirmation_email, current_user.email, event_id)
    background_tasks.add_task(notify_organizer, event_id)
    background_tasks.add_task(log_activity, current_user.id, 'joined_event')

    # Return immediately
    return {"success": True, "join_event_id": join_event.id}

# Background task functions
async def send_confirmation_email(email: str, event_id: int):
    """Runs after response sent"""
    await send_email(
        to=email,
        subject="Event Registration Confirmed",
        body=f"You've joined event {event_id}"
    )

async def notify_organizer(event_id: int):
    """Notify organizer of new participant"""
    # Notification logic
    pass
```

#### 2. Celery Integration for Heavy Tasks
```python
# app/worker/celery_app.py
from celery import Celery

celery_app = Celery(
    'driftwood',
    broker='redis://localhost:6379/0',
    backend='redis://localhost:6379/0'
)

celery_app.conf.update(
    task_serializer='json',
    accept_content=['json'],
    result_serializer='json',
    timezone='UTC',
    enable_utc=True,
    task_routes={
        'app.worker.tasks.send_email': {'queue': 'high'},
        'app.worker.tasks.process_results': {'queue': 'default'},
        'app.worker.tasks.analytics': {'queue': 'low'},
    }
)

# app/worker/tasks.py
from app.worker.celery_app import celery_app

@celery_app.task(
    bind=True,
    autoretry_for=(Exception,),
    retry_kwargs={'max_retries': 3},
    retry_backoff=True
)
def send_confirmation_email(self, user_email: str, event_id: int):
    """Heavy email task"""
    # Email sending logic
    send_email(user_email, f"Confirmed for event {event_id}")

@celery_app.task
def process_tournament_results(event_id: int):
    """Process results for large tournament"""
    # Heavy computation
    pass

# Usage in FastAPI endpoint
@router.post("/events/{event_id}/confirm")
async def confirm_registration(event_id: int):
    # Lightweight: queue for Celery
    send_confirmation_email.delay(current_user.email, event_id)

    return {"success": True}
```

#### 3. Event Broadcasting with Redis Pub/Sub
```python
# app/events/broadcaster.py
import aioredis
import json

class EventBroadcaster:
    def __init__(self):
        self.redis = None

    async def connect(self):
        self.redis = await aioredis.from_url("redis://localhost")

    async def publish(self, channel: str, event_type: str, data: dict):
        """Publish event to Redis"""
        message = json.dumps({
            'event_type': event_type,
            'data': data,
            'timestamp': datetime.utcnow().isoformat()
        })

        await self.redis.publish(channel, message)

    async def subscribe(self, channel: str):
        """Subscribe to events"""
        pubsub = self.redis.pubsub()
        await pubsub.subscribe(channel)

        async for message in pubsub.listen():
            if message['type'] == 'message':
                data = json.loads(message['data'])
                yield data

# Usage
broadcaster = EventBroadcaster()

@router.post("/events/{event_id}/join")
async def join_event(event_id: int):
    # ... business logic ...

    # Broadcast event
    await broadcaster.publish(
        'events',
        'join_event_confirmed',
        {
            'user_id': current_user.id,
            'event_id': event_id
        }
    )

    return {"success": True}

# Listener (separate process)
async def event_listener():
    async for event in broadcaster.subscribe('events'):
        if event['event_type'] == 'join_event_confirmed':
            await handle_join_confirmation(event['data'])
```

---

## Challenge 3: Dependency Injection & Service Layer

### The Challenge
Views became bloated with business logic.

### FastAPI Solution

#### 1. Service Layer with FastAPI DI
```python
# app/services/payment_service.py
from typing import Optional
from sqlalchemy.ext.asyncio import AsyncSession

class PaymentService:
    def __init__(self, db: AsyncSession):
        self.db = db

    async def refund_payments_for_event(
        self,
        join_event_id: int,
        penalty_percent: float = 0
    ) -> dict:
        """Refund all payments for a canceled event"""
        async with self.db.begin():
            stmt = (
                select(ParticipantPayment)
                .where(ParticipantPayment.join_events_id == join_event_id)
                .options(selectinload(ParticipantPayment.user))
            )
            result = await self.db.execute(stmt)
            payments = result.scalars().all()

            refunds = {}

            for payment in payments:
                refund_amount = payment.payment_amount * (1 - penalty_percent / 100)

                if payment.payment_type == 'wallet':
                    refunds[payment.user_id] = {
                        'wallet': await self._refund_to_wallet(
                            payment.user_id,
                            refund_amount
                        )
                    }
                elif payment.payment_type == 'stripe':
                    refunds[payment.user_id] = {
                        'stripe': await self._refund_to_stripe(
                            payment.payment_id,
                            refund_amount
                        )
                    }

            return refunds

    async def _refund_to_wallet(self, user_id: int, amount: float) -> float:
        """Private helper for wallet refund"""
        # Implementation
        pass

# app/api/deps.py
from fastapi import Depends
from sqlalchemy.ext.asyncio import AsyncSession
from app.services import PaymentService, EventMatchService

async def get_payment_service(
    db: AsyncSession = Depends(get_db)
) -> PaymentService:
    return PaymentService(db)

async def get_event_match_service(
    db: AsyncSession = Depends(get_db),
    payment_service: PaymentService = Depends(get_payment_service)
) -> EventMatchService:
    return EventMatchService(db, payment_service)

# app/api/endpoints/events.py
@router.post("/events/{event_id}/confirm")
async def confirm_event(
    event_id: int,
    event_service: EventMatchService = Depends(deps.get_event_match_service),
    current_user: User = Depends(deps.get_current_user)
):
    result = await event_service.confirm_registration(
        join_event_id=event_id,
        user=current_user
    )

    return result
```

#### 2. Protocol-Based DI (Type Hints)
```python
# app/interfaces/payment.py
from typing import Protocol

class PaymentInterface(Protocol):
    async def refund_payments_for_event(
        self,
        join_event_id: int,
        penalty_percent: float = 0
    ) -> dict:
        ...

# app/services/payment_service.py
class PaymentService:
    """Implements PaymentInterface"""
    async def refund_payments_for_event(
        self,
        join_event_id: int,
        penalty_percent: float = 0
    ) -> dict:
        # Implementation
        pass

# For testing: create mock
class MockPaymentService:
    async def refund_payments_for_event(
        self,
        join_event_id: int,
        penalty_percent: float = 0
    ) -> dict:
        return {'user_1': {'wallet': 100.0}}

# In tests, override dependency
def override_payment_service():
    return MockPaymentService()

app.dependency_overrides[get_payment_service] = override_payment_service
```

---

## Challenge 4: Query Optimization with SQLAlchemy

### The Challenge
N+1 query problems.

### FastAPI Solution

#### 1. Eager Loading with Async SQLAlchemy
```python
# app/crud/events.py
from sqlalchemy import select
from sqlalchemy.orm import selectinload, joinedload

class EventCRUD:
    def __init__(self, db: AsyncSession):
        self.db = db

    async def get_events_optimized(
        self,
        skip: int = 0,
        limit: int = 20
    ):
        """Optimized query with eager loading"""
        stmt = (
            select(EventDetail)
            .options(
                # joinedload for one-to-one/many-to-one (INNER JOIN)
                joinedload(EventDetail.tier),
                joinedload(EventDetail.game),
                joinedload(EventDetail.user),
                joinedload(EventDetail.type),

                # selectinload for one-to-many (separate query)
                selectinload(EventDetail.join_events).joinedload(JoinEvent.team)
            )
            .where(EventDetail.status != 'DRAFT')
            .offset(skip)
            .limit(limit)
        )

        result = await self.db.execute(stmt)
        events = result.unique().scalars().all()

        # Result: 2-3 queries instead of 700+
        return events

    async def get_events_with_counts(self):
        """Add aggregate counts"""
        from sqlalchemy import func

        stmt = (
            select(
                EventDetail,
                func.count(JoinEvent.id)
                    .filter(JoinEvent.join_status == 'confirmed')
                    .label('confirmed_count'),
                func.count(JoinEvent.id)
                    .filter(JoinEvent.join_status == 'pending')
                    .label('pending_count')
            )
            .outerjoin(EventDetail.join_events)
            .group_by(EventDetail.id)
        )

        result = await self.db.execute(stmt)
        return result.all()
```

#### 2. Custom Query Helpers
```python
# app/crud/base.py
from typing import Generic, TypeVar, Type
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select

ModelType = TypeVar("ModelType")

class CRUDBase(Generic[ModelType]):
    def __init__(self, model: Type[ModelType], db: AsyncSession):
        self.model = model
        self.db = db

    async def get(self, id: int) -> Optional[ModelType]:
        stmt = select(self.model).where(self.model.id == id)
        result = await self.db.execute(stmt)
        return result.scalar_one_or_none()

    async def get_multi(
        self,
        skip: int = 0,
        limit: int = 100,
        filters: dict = None
    ) -> list[ModelType]:
        stmt = select(self.model)

        if filters:
            for key, value in filters.items():
                stmt = stmt.where(getattr(self.model, key) == value)

        stmt = stmt.offset(skip).limit(limit)

        result = await self.db.execute(stmt)
        return result.scalars().all()

# Usage
event_crud = CRUDBase(EventDetail, db)
events = await event_crud.get_multi(skip=0, limit=20, filters={'status': 'LIVE'})
```

#### 3. Redis Caching
```python
# app/cache/redis.py
from aioredis import Redis
import json
from functools import wraps

class RedisCache:
    def __init__(self, redis: Redis):
        self.redis = redis

    def cached(self, expire: int = 3600):
        """Decorator for caching"""
        def decorator(func):
            @wraps(func)
            async def wrapper(*args, **kwargs):
                # Generate cache key
                cache_key = f"{func.__name__}:{args}:{kwargs}"

                # Check cache
                cached_value = await self.redis.get(cache_key)
                if cached_value:
                    return json.loads(cached_value)

                # Call function
                result = await func(*args, **kwargs)

                # Store in cache
                await self.redis.setex(
                    cache_key,
                    expire,
                    json.dumps(result)
                )

                return result

            return wrapper
        return decorator

# Usage
cache = RedisCache(redis_client)

@cache.cached(expire=3600)
async def get_event_details(event_id: int):
    # Expensive database query
    return await db.query(EventDetail).filter_by(id=event_id).first()

# Manual caching
async def get_wallet_cached(user_id: int) -> Wallet:
    cache_key = f"wallet:user:{user_id}"

    cached = await redis_client.get(cache_key)
    if cached:
        return Wallet(**json.loads(cached))

    wallet = await db.get(Wallet, user_id)

    await redis_client.setex(
        cache_key,
        3600,
        json.dumps(wallet.dict())
    )

    return wallet
```

---

## Challenge 5: Rate Limiting & Throttling

### The Challenge
API abuse from excessive requests.

### FastAPI Solution

#### 1. SlowAPI for Rate Limiting
```python
# app/main.py
from fastapi import FastAPI, Request
from slowapi import Limiter, _rate_limit_exceeded_handler
from slowapi.util import get_remote_address
from slowapi.errors import RateLimitExceeded

limiter = Limiter(key_func=get_remote_address)
app = FastAPI()
app.state.limiter = limiter
app.add_exception_handler(RateLimitExceeded, _rate_limit_exceeded_handler)

# Apply rate limiting
@app.post("/login")
@limiter.limit("5/minute")
async def login(request: Request):
    # Login logic
    pass

@app.post("/events")
@limiter.limit("20/hour")
async def create_event(request: Request):
    # Event creation
    pass

# Dynamic rate limiting
def get_user_limit(request: Request) -> str:
    """Dynamic limit based on user type"""
    if hasattr(request.state, 'user'):
        user = request.state.user

        if user.is_staff:
            return "1000/minute"  # Admin
        elif user.subscription_tier == 'premium':
            return "200/minute"
        else:
            return "100/minute"

    return "30/minute"  # Anonymous

@app.get("/api/search")
@limiter.limit(get_user_limit)
async def search(request: Request):
    # Search logic
    pass
```

#### 2. Redis-Based Custom Rate Limiter
```python
# app/middleware/rate_limit.py
from fastapi import Request, HTTPException
from app.cache import redis_client
import time

class RateLimiter:
    def __init__(self, calls: int, period: int):
        self.calls = calls
        self.period = period

    async def __call__(self, request: Request):
        # Get identifier (user ID or IP)
        if hasattr(request.state, 'user'):
            key = f"rate_limit:{request.state.user.id}"
        else:
            key = f"rate_limit:{request.client.host}"

        # Get current count
        current = await redis_client.get(key)

        if current is None:
            # First request in period
            await redis_client.setex(key, self.period, 1)
        elif int(current) >= self.calls:
            # Rate limit exceeded
            raise HTTPException(
                status_code=429,
                detail="Rate limit exceeded"
            )
        else:
            # Increment counter
            await redis_client.incr(key)

# Usage with dependency injection
@router.post("/payment")
async def payment(
    request: Request,
    rate_limit: None = Depends(RateLimiter(calls=30, period=60))
):
    # Payment logic
    pass
```

---

## Challenge 6: Middleware Pipeline

### The Challenge
Cross-cutting concerns like JWT validation, CORS, logging.

### FastAPI Solution

#### 1. JWT Authentication Middleware
```python
# app/middleware/auth.py
from fastapi import Request, HTTPException
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
import jwt

security = HTTPBearer()

async def jwt_middleware(
    request: Request,
    credentials: HTTPAuthorizationCredentials = Depends(security)
):
    """JWT authentication dependency"""
    token = credentials.credentials

    try:
        payload = jwt.decode(
            token,
            settings.SECRET_KEY,
            algorithms=["HS256"]
        )

        user_id = payload.get("user_id")
        if not user_id:
            raise HTTPException(status_code=401, detail="Invalid token")

        # Get user from database
        user = await get_user(user_id)
        if not user:
            raise HTTPException(status_code=404, detail="User not found")

        # Attach to request state
        request.state.user = user
        return user

    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail="Token expired")
    except jwt.InvalidTokenError:
        raise HTTPException(status_code=401, detail="Invalid token")

# Usage in routes
@router.get("/profile")
async def get_profile(current_user: User = Depends(jwt_middleware)):
    return current_user
```

#### 2. Request Logging Middleware
```python
# app/middleware/logging.py
import time
import logging
from fastapi import Request

logger = logging.getLogger(__name__)

@app.middleware("http")
async def log_requests(request: Request, call_next):
    start_time = time.time()

    # Log request
    logger.info(f"Request: {request.method} {request.url.path}")

    response = await call_next(request)

    # Log response
    duration = time.time() - start_time
    logger.info(
        f"Response: {request.method} {request.url.path} "
        f"Status: {response.status_code} "
        f"Duration: {duration*1000:.2f}ms"
    )

    # Alert on slow requests
    if duration > 2:
        logger.warning(f"Slow request: {request.url.path} took {duration}s")

    # Add headers
    response.headers["X-Process-Time"] = str(duration)

    return response
```

#### 3. CORS Middleware
```python
from fastapi.middleware.cors import CORSMiddleware

app.add_middleware(
    CORSMiddleware,
    allow_origins=["https://frontend.com", "http://localhost:3000"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)
```

#### 4. Custom Error Handling
```python
from fastapi import FastAPI, Request, status
from fastapi.responses import JSONResponse
from fastapi.exceptions import RequestValidationError

@app.exception_handler(RequestValidationError)
async def validation_exception_handler(
    request: Request,
    exc: RequestValidationError
):
    return JSONResponse(
        status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
        content={
            "detail": exc.errors(),
            "message": "Validation error occurred"
        }
    )

@app.exception_handler(HTTPException)
async def http_exception_handler(request: Request, exc: HTTPException):
    return JSONResponse(
        status_code=exc.status_code,
        content={
            "message": exc.detail,
            "path": request.url.path
        }
    )
```

---

## Challenge 7: Request Validation with Pydantic

### The Challenge
Validation logic scattered and inconsistent.

### FastAPI Solution

#### 1. Pydantic Models for Validation
```python
# app/schemas/payment.py
from pydantic import BaseModel, Field, validator
from datetime import datetime
from typing import Optional

class PaymentMethodCreate(BaseModel):
    bank_name: str = Field(..., min_length=1, max_length=255)
    account_number: str = Field(..., regex=r'^\d{10,18}$')
    account_holder_name: str = Field(..., regex=r'^[a-zA-Z\s]+$')

    @validator('account_holder_name')
    def validate_name(cls, v):
        if not v.strip():
            raise ValueError('Account holder name cannot be empty')
        return v.strip()

class WithdrawalRequest(BaseModel):
    withdrawal: float = Field(..., gt=0, description="Amount to withdraw")

    @validator('withdrawal')
    def validate_withdrawal(cls, v, values, **kwargs):
        if v < 10:
            raise ValueError('Minimum withdrawal is RM 10')
        if v > 5000:
            raise ValueError('Maximum withdrawal is RM 5000')
        return v

    class Config:
        schema_extra = {
            "example": {
                "withdrawal": 100.50
            }
        }

# Usage in endpoint
@router.post("/withdrawal")
async def request_withdrawal(
    request: WithdrawalRequest,
    current_user: User = Depends(get_current_user),
    wallet_service: WalletService = Depends(get_wallet_service)
):
    # Request already validated by Pydantic
    await wallet_service.process_withdrawal(
        current_user.id,
        request.withdrawal
    )

    return {"success": True}
```

#### 2. Complex Validation with Dependencies
```python
# app/schemas/events.py
class JoinEventRequest(BaseModel):
    team_id: Optional[int] = None

    @validator('team_id', always=True)
    def validate_team_id(cls, v, values):
        # Validation happens automatically
        return v

# But for database validation, use dependency
async def validate_join_event_request(
    event_id: int,
    request: JoinEventRequest,
    current_user: User = Depends(get_current_user),
    db: AsyncSession = Depends(get_db)
):
    """Complex validation requiring database"""
    event = await db.get(EventDetail, event_id)
    if not event:
        raise HTTPException(404, "Event not found")

    # Check if user already joined
    stmt = select(JoinEvent).where(
        JoinEvent.event_id == event_id,
        JoinEvent.user_id == current_user.id
    )
    existing = await db.execute(stmt)
    if existing.scalar_one_or_none():
        raise HTTPException(400, "Already joined this event")

    # Validate team if required
    if not event.is_solo and not request.team_id:
        raise HTTPException(400, "Team ID required for team events")

    if request.team_id:
        team = await db.get(Team, request.team_id)
        if not team:
            raise HTTPException(404, "Team not found")

        if current_user.id not in [m.user_id for m in team.members]:
            raise HTTPException(403, "You are not a member of this team")

    return request

# Usage
@router.post("/events/{event_id}/join")
async def join_event(
    event_id: int,
    validated_request: JoinEventRequest = Depends(validate_join_event_request)
):
    # All validation already done
    pass
```

---

## Challenge 8: Social Graph System with Async Operations

### The Challenge
Building high-performance social networking features using FastAPI's async capabilities for bidirectional friendships, unidirectional follows, and user reporting.

### FastAPI Solution

#### Architecture: Async Service Layer Pattern

**Models (SQLAlchemy 2.0 async)**:
- `Friend`: Table with columns `id`, `user_id`, `friend_id`, `status` (pending/accepted/rejected), `created_at`
- `ParticipantFollow`: Table with columns `id`, `user_id`, `followed_user_id`, `created_at`
- `UserStar`: Many-to-many association table with `user_id`, `starred_user_id`
- `Report`: Table with `reporter_id`, `reported_user_id`, `reason`, `description`, `status`, `created_at`

**Dependency Injection Chain**:
```
get_db() → get_friend_service() → get_social_service() → endpoint handler
```

#### Key Async Methods

**FriendService Class**:

**`async get_friend_count(db: AsyncSession, user_id: int) -> int`**:
- Creates SQLAlchemy select statement: `select(func.count(Friend.id))`
- Adds WHERE clause with OR condition: `.where(or_(Friend.user_id == user_id, Friend.friend_id == user_id))`
- Filters by accepted status: `.where(Friend.status == 'accepted')`
- Executes with `await db.execute(stmt)` returning scalar result
- Divides count by 2 to handle bidirectional relationship

**`async get_friends_paginate(db, user_id, logged_user_id, per_page, page, search)`**:
- Builds query using `selectinload` for eager loading: `select(Friend).options(selectinload(Friend.user), selectinload(Friend.friend))`
- Applies bidirectional filter using SQLAlchemy `or_()` expression
- For search: Adds `join(User).where(User.name.ilike(f'%{search}%'))`
- Applies offset/limit: `.offset((page - 1) * per_page).limit(per_page)`
- Returns list comprehension mapping each Friend to dict with relationship status
- Calls `get_relationship_status` async method for each result

**`async get_relationship_status(db, user_id, target_id) -> str`**:
- Queries Friend table with OR condition matching both directions
- Returns enum string: 'none', 'friends', 'request_sent', 'request_received'
- Uses pattern matching on query result to determine status
- Caches result in Redis with key `relationship:{user_id}:{target_id}` for 300 seconds

**SocialService Class Methods**:

**`async send_friend_request(db, user_id, target_id)`**:
- Begins async transaction: `async with db.begin():`
- Checks for existing request: `stmt = select(Friend).where(...)`
- Executes query: `result = await db.execute(stmt); existing = result.scalar_one_or_none()`
- If exists with status pending: Returns early with message "Request already sent"
- Creates new Friend instance: `friend = Friend(user_id=user_id, friend_id=target_id, status='pending')`
- Adds to session: `db.add(friend); await db.flush()` to get ID
- Triggers background task: `background_tasks.add_task(send_notification, target_id, 'Friend request received')`
- Commits transaction automatically at context exit

**`async accept_friend_request(db, user_id, requester_id)`**:
- Locks row for update: `select(Friend).where(...).with_for_update()`
- Updates status: `friendship.status = 'accepted'`
- Flushes changes: `await db.flush()`
- Invalidates relationship cache for both users using Redis: `await redis.delete(f'relationship:{user_id}:{requester_id}')`
- Queues notification background task
- Returns success dict with updated friendship data

**ParticipantFollowService (Unidirectional)**:

**`async toggle_follow(db, user_id, target_id)`**:
- Queries existing follow: `stmt = select(ParticipantFollow).where(ParticipantFollow.user_id == user_id, ParticipantFollow.followed_user_id == target_id)`
- If exists: Deletes using `await db.delete(existing_follow)`
- If not exists: Creates new using `db.add(ParticipantFollow(...))`
- Returns boolean indicating follow state: True if now following, False if unfollowed
- Updates follower count cache atomically: `await redis.incr(f'follower_count:{target_id}')` or `redis.decr()`

**`async get_follower_count(db, user_id) -> int`**:
- Checks Redis cache first: `count = await redis.get(f'follower_count:{user_id}')`
- If cache miss: Queries database with `select(func.count()).where(ParticipantFollow.followed_user_id == user_id)`
- Stores in cache: `await redis.setex(f'follower_count:{user_id}', 3600, count)`
- Returns integer count

**StarService (Private Favorites)**:

**`async toggle_star(db, user_id, target_id)`**:
- Queries star junction table: `select(UserStar).where(UserStar.user_id == user_id, UserStar.starred_user_id == target_id)`
- Uses `scalar_one_or_none()` to check existence
- If exists: Executes delete: `await db.execute(delete(UserStar).where(...))`
- If not: Inserts new star: `db.add(UserStar(user_id=user_id, starred_user_id=target_id))`
- Returns toggle state boolean
- Stars never returned in public API responses, only in user's own profile endpoint

**ReportService**:

**`async can_report(db, redis, reporter_id, reported_user_id) -> bool`**:
- Checks Redis rate limit key: `rate_limit_key = f'report_rate:{reporter_id}:{reported_user_id}'`
- Gets current count: `count = await redis.get(rate_limit_key)`
- If count exists and >= 1: Returns False (already reported today)
- If None: Returns True, allowing report
- Rate limit enforced via Redis TTL of 86400 seconds (24 hours)

**`async create_report(db, redis, background_tasks, reporter_id, reported_user_id, reason, description)`**:
- Validates reason against enum: `if reason not in ReportReason.__members__.values(): raise HTTPException(400)`
- Creates Report instance: `report = Report(reporter_id=reporter_id, reported_user_id=reported_user_id, reason=reason, description=description, status='pending')`
- Adds to session and flushes: `db.add(report); await db.flush()`
- Sets rate limit in Redis: `await redis.setex(rate_limit_key, 86400, '1')`
- Queues admin notification: `background_tasks.add_task(notify_moderators, report.id)`
- Returns created report dict

#### API Endpoints with Dependency Injection

**`@router.post("/friends/request")`**:
- Dependencies: `db: AsyncSession = Depends(get_db)`, `current_user = Depends(get_current_user)`, `friend_service = Depends(get_friend_service)`
- Request body: Pydantic model `FriendRequestCreate` with field `target_user_id: int`
- Calls: `result = await friend_service.send_friend_request(db, current_user.id, request.target_user_id)`
- Returns: `FriendResponse` Pydantic model with fields `success: bool`, `message: str`, `friendship: Optional[FriendSchema]`

**`@router.post("/friends/{friendship_id}/accept")`**:
- Path parameter: `friendship_id: int`
- Validates friendship exists and user is recipient using dependency function
- Calls service method: `await friend_service.accept_friend_request(db, current_user.id, friendship_id)`
- Returns HTTP 200 with updated friendship data

**`@router.post("/follow/{target_user_id}")`**:
- Idempotent toggle endpoint - POST request toggles follow state
- Returns: `{"following": bool, "follower_count": int}` showing new state
- Uses background task to update analytics: `background_tasks.add_task(track_follow_event, ...)`

**`@router.post("/users/{user_id}/star")`**:
- Private endpoint - only updates current user's stars
- Returns: `{"starred": bool}` indicating new state
- No public visibility of who starred whom

**`@router.post("/users/{user_id}/report")`**:
- Request body: `ReportCreate` Pydantic model with `reason: ReportReason` (enum), `description: Optional[str]`
- Validates using dependency: `can_report = Depends(validate_can_report)`
- Creates report via service method
- Returns 201 status with report ID and message "Report submitted for review"

#### Database Query Optimizations

**Eager Loading Strategy**:
- Uses `selectinload()` for one-to-many relationships
- Uses `joinedload()` for many-to-one relationships
- Example: `select(Friend).options(selectinload(Friend.user).joinedload(User.profile))`

**Index Configuration**:
- Composite index: `Index('idx_friend_user_status', Friend.user_id, Friend.friend_id, Friend.status)`
- Partial index for pending requests: `Index('idx_pending_requests', Friend.friend_id, postgresql_where=(Friend.status == 'pending'))`
- Covering index for follower counts: `Index('idx_follow_count', ParticipantFollow.followed_user_id)`

**Caching Strategy**:
- Redis cache for relationship statuses with 5-minute TTL
- Follower counts cached with 1-hour TTL, invalidated on follow/unfollow
- Friend counts cached per user with 30-minute TTL
- Cache keys pattern: `{entity}:{user_id}:{optional_target_id}`

#### Background Tasks Pattern

FastAPI `BackgroundTasks` used for non-blocking operations:
- `send_notification(user_id, message)`: Queues in-app notification creation
- `notify_moderators(report_id)`: Sends email/Slack to moderator team
- `track_follow_event(user_id, target_id)`: Logs to analytics platform
- `update_social_graph_cache(user_id)`: Rebuilds user's social graph cache

---

## Challenge 9: Real-Time Chat with Async Dual Storage

### The Challenge
Implementing WebSocket-based real-time chat with FastAPI while maintaining message persistence in PostgreSQL and real-time sync via Firebase.

### FastAPI Solution

#### Architecture: WebSocket + REST Hybrid

**WebSocket Endpoint**: `/ws/chat/{room_id}` for real-time messaging
**REST Endpoints**:
- `GET /chat/{room_id}/history` - Paginated message history from PostgreSQL
- `POST /chat/{room_id}/read` - Mark messages as read
- `GET /chat/rooms` - List user's conversation rooms

**Connection Manager Pattern**:

**`class ConnectionManager`**:
- Instance variable: `active_connections: Dict[str, List[WebSocket]]` mapping room_id to list of WebSocket connections
- Method `async connect(websocket: WebSocket, room_id: str, user_id: int)`:
  - Accepts WebSocket: `await websocket.accept()`
  - Validates user authorization to join room
  - Appends to connections dict: `self.active_connections[room_id].append(websocket)`
  - Broadcasts join message to room participants
- Method `async disconnect(websocket: WebSocket, room_id: str)`:
  - Removes from active connections list
  - Broadcasts leave message if last connection for user
- Method `async broadcast(room_id: str, message: dict)`:
  - Iterates through `self.active_connections[room_id]`
  - Sends to each: `await connection.send_json(message)`
  - Handles disconnected sockets gracefully with try/except

**ChatService Class Methods**:

**`async create_or_get_room(db, user1_id, user2_id) -> ChatRoom`**:
- Generates deterministic room_id: `room_id = f"{min(user1_id, user2_id)}_{max(user1_id, user2_id)}"`
- Queries existing: `stmt = select(ChatRoom).where(ChatRoom.room_id == room_id)`
- Uses `first_or_create` pattern with database locking
- Returns ChatRoom ORM instance with relationships loaded

**`async send_message(db, firebase_client, room_id, sender_id, message_text) -> ChatMessage`**:
- Async context manager for transaction: `async with db.begin():`
- Creates PostgreSQL record: `msg = ChatMessage(room_id=room_id, sender_id=sender_id, message=message_text)`
- Updates room timestamp: `await db.execute(update(ChatRoom).where(...).values(last_message_at=func.now()))`
- Asynchronously writes to Firebase: `await asyncio.to_thread(firebase_client.collection('room').document(room_id).collection('messages').add, message_data)`
- If Firebase fails: Logs error but doesn't rollback PostgreSQL (eventual consistency acceptable)
- Checks recipient online status: `await check_presence(firebase_client, recipient_id)`
- If offline: Queues notification via background task
- Returns created ChatMessage with sender relationship eager-loaded

**`async mark_as_read(db, firebase_client, room_id, user_id)`**:
- PostgreSQL bulk update: `stmt = update(ChatMessage).where(ChatMessage.room_id == room_id, ChatMessage.sender_id != user_id, ChatMessage.is_read == False).values(is_read=True)`
- Executes async: `await db.execute(stmt)`
- Parallel Firebase update: Queries unread messages and batch updates `is_read` field
- Uses asyncio.gather for concurrent execution: `await asyncio.gather(db.commit(), update_firebase_batch(...))`

**`async get_chat_history(db, room_id, limit, before_timestamp) -> List[ChatMessage]`**:
- Builds query: `select(ChatMessage).where(ChatMessage.room_id == room_id).order_by(ChatMessage.created_at.desc())`
- Applies cursor pagination: `.where(ChatMessage.created_at < before_timestamp)` if before_timestamp provided
- Limits results: `.limit(limit)`
- Eager loads sender: `.options(selectinload(ChatMessage.sender))`
- Executes and returns: `result = await db.execute(stmt); return result.scalars().all()`

#### WebSocket Endpoint Handler

**`@app.websocket("/ws/chat/{room_id}")`**:
- Parameters: `websocket: WebSocket`, `room_id: str`, `token: str = Query(...)`
- Validates JWT token from query parameter
- Calls `await manager.connect(websocket, room_id, current_user.id)`
- Enters infinite loop: `while True:`
  - Receives message: `data = await websocket.receive_json()`
  - Validates message schema using Pydantic: `msg = ChatMessageCreate(**data)`
  - Saves to databases: `saved_msg = await chat_service.send_message(...)`
  - Broadcasts to room: `await manager.broadcast(room_id, {...})`
- Exception handling: `except WebSocketDisconnect:` triggers `await manager.disconnect(...)`

#### Presence System

**Firebase Presence Structure**:
- Collection: `presence` with documents keyed by `user_id`
- Fields: `online: bool`, `last_seen: Timestamp`, `connection_count: int`

**`async set_presence_online(firebase_client, user_id)`**:
- Updates Firestore document: `doc_ref.set({'online': True, 'last_seen': firestore.SERVER_TIMESTAMP, 'connection_count': firestore.Increment(1)})`
- Uses Firestore `Increment` field transform for atomic counter

**`async set_presence_offline(firebase_client, user_id)`**:
- Decrements connection count: `connection_count: firestore.Increment(-1)`
- If count reaches 0: Sets `online: False`
- Updates last_seen timestamp

**`async check_presence(firebase_client, user_id) -> bool`**:
- Reads Firestore document: `doc = doc_ref.get()`
- Returns: `doc.exists and doc.get('online') == True`
- Used before sending offline notifications

#### Typing Indicators

**Ephemeral State in Redis**:
- Key pattern: `typing:{room_id}:{user_id}` with TTL of 3 seconds
- Set on typing start: `await redis.setex(f'typing:{room_id}:{user_id}', 3, '1')`
- Broadcast typing state change via WebSocket
- Auto-expires after 3 seconds of inactivity

**WebSocket Typing Events**:
- Client sends: `{"type": "typing", "room_id": "...", "is_typing": true}`
- Server broadcasts to other room participants: `{"type": "user_typing", "user_id": ..., "is_typing": true}`
- No database persistence - purely real-time state

#### REST History Endpoint

**`@router.get("/chat/{room_id}/history")`**:
- Query params: `limit: int = 50`, `before: Optional[datetime] = None`
- Returns: `HistoryResponse` Pydantic model with `messages: List[MessageSchema]`, `has_more: bool`
- Uses cursor-based pagination for efficient large dataset handling
- Each message includes sender info via nested Pydantic model

#### Caching Strategy

**Room Metadata Cache**:
- Key: `chat_room:{room_id}` cached in Redis for 1 hour
- Stores: participant IDs, room creation time, last message preview
- Invalidated on: New message, participant leave/join

**Unread Count Cache**:
- Key: `unread_count:{user_id}` with hash of room_ids to counts
- Updated on: New message, mark as read
- Used for notification badges in UI

#### Background Job: Message Archive

**Scheduled Task** (runs daily at 2 AM):
- Queries messages older than 90 days
- Moves to `chat_messages_archive` table (cheaper storage)
- Removes from Firestore to reduce costs
- Updates indexes for faster main table queries

---

## Challenge 10: Async Wallet System with Decimal Precision

### The Challenge
Building financially accurate wallet system in FastAPI with async database operations, maintaining atomic balance updates and comprehensive audit trails.

### FastAPI Solution

#### Data Model: SQLAlchemy with Decimal Type

**Wallet Model Fields**:
- `id`: Integer primary key
- `user_id`: Foreign key with unique constraint
- `current_balance`: `NUMERIC(10, 2)` SQLAlchemy type mapping to Python Decimal
- `usable_balance`: `NUMERIC(10, 2)` - funds available for use
- `pending_balance`: `NUMERIC(10, 2)` - funds locked in pending operations
- `has_bank_account`: Boolean flag
- `bank_name`: String(255), nullable
- `bank_last4`: String(4), stores last 4 digits only
- `account_number`: Text, encrypted using SQLAlchemy's `EncryptedType`
- `account_holder_name`: String(255)
- `version`: Integer for optimistic locking

**Model Constraint Validation**:
- Check constraint: `current_balance = usable_balance + pending_balance`
- Check constraint: `usable_balance >= 0` prevents negative balance
- Check constraint: `pending_balance >= 0`
- Validates in database AND Python model `__table_args__`

#### WalletService Async Methods

**`async retrieve_or_create_cached(redis, db, user_id) -> Wallet`**:
- Checks Redis first: `cached = await redis.get(f'wallet:{user_id}')`
- If hit: Deserializes and returns `WalletSchema.parse_raw(cached)`
- If miss: Queries database using `select(Wallet).where(Wallet.user_id == user_id)`
- If not exists: Creates new wallet with `Wallet(user_id=user_id, usable_balance=Decimal('0.00'), ...)`
- Caches serialized wallet: `await redis.setex(f'wallet:{user_id}', 3600, wallet.json())`
- Returns Wallet instance

**`async credit(db, redis, wallet_id, amount: Decimal, description: str, tx_type: str) -> Wallet`**:
- Validates amount > 0: `if amount <= Decimal('0'): raise ValueError`
- Begins transaction: `async with db.begin():`
- Locks wallet row: `stmt = select(Wallet).where(Wallet.id == wallet_id).with_for_update()`
- Executes: `result = await db.execute(stmt); wallet = result.scalar_one()`
- Updates using Decimal arithmetic: `wallet.usable_balance += amount; wallet.current_balance += amount`
- Creates audit record: `tx = TransactionHistory(user_id=wallet.user_id, amount=amount, balance_after=wallet.usable_balance, ...)`
- Adds transaction: `db.add(tx)`
- Flushes changes: `await db.flush()`
- Invalidates cache: `await redis.delete(f'wallet:{wallet.user_id}')`
- Transaction commits automatically at context exit
- Returns updated Wallet

**`async debit(db, redis, wallet_id, amount: Decimal, description: str, tx_type: str) -> Wallet`**:
- Identical pattern to credit but with subtraction
- Additional check: `if wallet.usable_balance < amount: raise InsufficientBalanceException`
- Records negative amount: `TransactionHistory(amount=-amount, ...)`
- Uses same locking mechanism to prevent race conditions

**`async lock_funds(db, redis, wallet_id, amount: Decimal, description: str) -> Wallet`**:
- Locks wallet row
- Validates sufficient usable balance
- Atomic update: `wallet.usable_balance -= amount; wallet.pending_balance += amount`
- Total balance unchanged: `current_balance` stays same
- Creates transaction with type='pending'
- User cannot withdraw pending funds

**`async unlock_funds(db, redis, wallet_id, amount: Decimal) -> Wallet`**:
- Reverses lock_funds operation
- `wallet.pending_balance -= amount; wallet.usable_balance += amount`
- No transaction history (logged by calling code's credit/debit)

#### WithdrawalService Methods

**Service Constants**:
```python
class WithdrawalService:
    MIN_AMOUNT = Decimal('10.00')
    MAX_AMOUNT = Decimal('5000.00')
    FEE = Decimal('1.00')
```

**`async request_withdrawal(db, redis, background_tasks, user_id, amount: Decimal) -> dict`**:
- Retrieves wallet: `wallet = await wallet_service.retrieve_or_create_cached(redis, db, user_id)`
- Validates bank linked: Checks `wallet.has_bank_account` flag
- Validates bounds: Compares Decimal values against constants
- Calculates total: `total = amount + WithdrawalService.FEE` using Decimal addition
- Checks sufficient balance: `wallet.usable_balance >= total`
- Begins transaction block
- Debits wallet: `await wallet_service.debit(db, redis, wallet.id, total, f'Withdrawal: {amount}', 'withdrawal')`
- Creates withdrawal record: `withdrawal = Withdrawal(user_id=user_id, amount=amount, fee=FEE, status='pending', ...)`
- Encrypts account number before storage
- Adds to session: `db.add(withdrawal); await db.flush()`
- Queues admin notification: `background_tasks.add_task(notify_admin_withdrawal, withdrawal.id)`
- Commits transaction
- Returns dict: `{'success': True, 'withdrawal_id': withdrawal.id, 'message': '...'}`

**`async process_withdrawal(db, redis, withdrawal_id: int, admin_id: int) -> Withdrawal`**:
- Retrieves withdrawal: `withdrawal = await db.get(Withdrawal, withdrawal_id)`
- Validates status: `if withdrawal.status != 'pending': raise HTTPException(400)`
- Begins transaction
- Updates status: `withdrawal.status = 'completed'`
- Records processor: `withdrawal.processed_by = admin_id; withdrawal.processed_at = datetime.utcnow()`
- In production: Calls bank API using httpx async client
  ```python
  async with httpx.AsyncClient() as client:
      response = await client.post(bank_api_url, json={...})
  ```
- On API failure: Calls `refund_failed_withdrawal` method
- Commits transaction
- Queues email: `background_tasks.add_task(send_withdrawal_email, withdrawal.user_id, withdrawal.id)`
- Returns updated Withdrawal instance

**`async refund_failed_withdrawal(db, redis, withdrawal: Withdrawal)`**:
- Credits wallet: `await wallet_service.credit(db, redis, withdrawal.wallet_id, withdrawal.total_amount, f'Refund: Withdrawal #{withdrawal.id}', 'refund')`
- Updates withdrawal: `withdrawal.status = 'failed'; withdrawal.failure_reason = 'Bank API error'`
- Commits changes
- No exception raised - refund always succeeds

**`async link_bank_account(db, redis, user_id: int, bank_data: BankAccountCreate) -> dict`**:
- Validates using Pydantic: `bank_data` is BankAccountCreate model with validators
- Account number regex: `@validator('account_number')` checks `^[0-9]{10,18}$`
- Account holder name regex: `^[a-zA-Z\s]+$` enforced by Pydantic
- In production: Verifies account via bank API
- Retrieves wallet: `wallet = await wallet_service.retrieve_or_create_cached(...)`
- Updates fields:
  ```python
  wallet.has_bank_account = True
  wallet.bank_name = bank_data.bank_name
  wallet.bank_last4 = bank_data.account_number[-4:]
  wallet.account_number = encrypt(bank_data.account_number)  # Using cryptography.fernet
  wallet.account_holder_name = bank_data.account_holder_name
  ```
- Commits update
- Invalidates cache
- Returns success dict

#### TransactionHistory Query Service

**`async get_transaction_history(db, user_id, filters: TransactionFilters) -> Page[TransactionSchema]`**:
- Builds base query: `stmt = select(TransactionHistory).where(TransactionHistory.user_id == user_id).order_by(TransactionHistory.date.desc())`
- Applies type filter: `.where(TransactionHistory.transaction_type == filters.type)` if provided
- Applies date range: `.where(TransactionHistory.date.between(filters.start_date, filters.end_date))`
- Implements cursor pagination:
  - Offset: `.offset((filters.page - 1) * filters.limit)`
  - Limit: `.limit(filters.limit + 1)` to check `has_more`
- Executes: `result = await db.execute(stmt); transactions = result.scalars().all()`
- Checks for more: `has_more = len(transactions) > filters.limit`
- Returns Pydantic response: `TransactionHistoryResponse(transactions=transactions[:filters.limit], has_more=has_more, page=filters.page)`

#### API Endpoints

**`@router.post("/wallet/withdrawal")`**:
- Dependencies: `db = Depends(get_db)`, `redis = Depends(get_redis)`, `background_tasks: BackgroundTasks`, `current_user = Depends(get_current_user)`, `withdrawal_service = Depends(get_withdrawal_service)`
- Request body: `request: WithdrawalRequest` Pydantic model with `amount: Decimal` field
- Validates in Pydantic model using `@validator`:
  ```python
  @validator('amount')
  def validate_amount(cls, v):
      if v < Decimal('10'): raise ValueError('Minimum withdrawal RM 10')
      if v > Decimal('5000'): raise ValueError('Maximum withdrawal RM 5000')
      return v
  ```
- Calls service: `result = await withdrawal_service.request_withdrawal(db, redis, background_tasks, current_user.id, request.amount)`
- Returns: HTTP 201 with `WithdrawalResponse` model

**`@router.post("/wallet/bank-account")`**:
- Request body: `BankAccountCreate` Pydantic model
- Calls: `await withdrawal_service.link_bank_account(db, redis, current_user.id, request)`
- Returns: HTTP 200 with success message

**`@router.get("/wallet/transactions")`**:
- Query params: `filters: TransactionFilters = Depends()` using FastAPI's dependency as query param parser
- Calls: `history = await transaction_service.get_transaction_history(db, current_user.id, filters)`
- Returns: `TransactionHistoryResponse` Pydantic model with pagination

#### Admin Endpoints

**`@router.post("/admin/withdrawals/{withdrawal_id}/approve")`**:
- Requires: `current_admin = Depends(require_admin_role)`
- Path param: `withdrawal_id: int`
- Calls: `withdrawal = await withdrawal_service.process_withdrawal(db, redis, withdrawal_id, current_admin.id)`
- Returns: Updated withdrawal with 200 status

**`@router.get("/admin/withdrawals")`**:
- Query params: `status: Optional[str] = None`, `page: int = 1`, `limit: int = 50`
- Filters by status if provided
- Returns paginated list of pending withdrawals
- Only accessible to admins via `Depends(require_admin_role)` dependency

#### Security Implementation

**Encryption Service**:
- Uses `cryptography.fernet` for symmetric encryption
- Key stored in environment: `ENCRYPTION_KEY = os.getenv('ENCRYPTION_KEY')`
- `encrypt(value: str) -> str`: Returns base64-encoded encrypted string
- `decrypt(encrypted: str) -> str`: Returns decrypted original value
- Applied in SQLAlchemy type: `EncryptedType` custom type using TypeDecorator

**Audit Logging**:
- Every wallet operation creates immutable TransactionHistory record
- Fields: `id`, `user_id`, `name`, `transaction_type`, `amount`, `balance_after`, `metadata` (JSON), `created_at`
- No update/delete - append-only log
- Indexed on `(user_id, created_at DESC)` for fast queries

**Fraud Detection**:
- Async background task checks for suspicious patterns:
  ```python
  @app.on_event("startup")
  async def start_fraud_detector():
      asyncio.create_task(fraud_detection_loop())
  ```
- Checks: Daily withdrawal limits, velocity (5+ in 1 hour), unusual amounts
- Flags suspicious withdrawals for manual review
- Logs IP addresses: Middleware captures `request.client.host` and stores in withdrawal record

---

*FastAPI's async capabilities combined with SQLAlchemy 2.0's async support enable high-performance financial operations while maintaining ACID guarantees and decimal precision.*
