# Driftwood - Community Esports Platform

## Project Overview
Driftwood is a comprehensive community esports platform that facilitates competitive gaming tournaments and events. Players can compete, meet like-minded gamers, and build esports communities through organized tournaments with paid entries and automated bracket management.

## Core Features
- **Tournament Management**: Single/double elimination brackets with automated progression
- **Payment Processing**: Stripe integration for entry fees and winner payouts
- **Team System**: Team creation, roster management, and invitation system
- **User Management**: Multi-role system (Participants, Organizers, Admins)
- **Social Features**: Following, friends, achievements, and community interaction
- **Admin Panel**: Comprehensive Filament-based administrative interface
- **Wallet System**: User wallets with bank account withdrawal integration

## Architecture
- **Backend**: Laravel 10 with JWT authentication
- **Frontend**: Vite + Petite Vue + Alpine.js + Bootstrap 5
- **Admin**: Filament 3.2 with Livewire 3.5
- **Database**: MySQL with Redis caching
- **Payments**: Stripe for transactions
- **Real-time**: Firebase integration
- **Auth**: Social login (Google, Steam)
- **Analytics**: Google Tag Manager + Custom Analytics Service

## Key Directories
- `app/Models/` - 40+ models including User, EventDetail, Team, Bracket
- `app/Http/Controllers/` - Organized by user roles (Organizer, Participant, Shared)
- `app/Services/` - Business logic (PaymentService, BracketDataService)
- `app/Filament/Resources/` - Admin interface resources
- `database/migrations/` - 80+ migrations showing platform evolution

## Testing Strategy

### Test Syllabus (Priority Order)
1. **Mock Infrastructure** (Week 1)
   - MocksStripe, MocksFirebase, MocksEmail, MocksGoogleAuth, MocksSteamAuth
   - CreatesTestUsers, CreatesTestEvents, CreatesTestTeams, CreatesTestPayments

2. **Critical Services** (Week 1-2) - 95%+ coverage
   - PaymentService (refunds, captures, wallet operations)
   - BracketDataService (8/16/32 team brackets)
   - AuthService (OAuth, JWT, role determination)
   - EventMatchService (event lifecycle)

3. **Core Models** (Week 2-3) - 85%+ coverage
   - Priority: User, EventDetail, Team, Wallet, RecordStripe
   - All 66 models: relationships, scopes, casts, methods

4. **Controllers** (Week 3-4) - 90%+ coverage
   - All 24 controllers via Feature tests
   - Auth, payment, event, team workflows

5. **Form Requests** (Week 4) - 90%+ coverage
   - All 29 validation classes

6. **Jobs & Mail** (Week 5) - 90%+ coverage
   - 5 Jobs, 19 Mail classes, 6 Events/Listeners

7. **Middleware** (Week 5) - 95%+ coverage
   - All 15 middleware (security critical)

8. **Integration & Performance** (Week 6)
   - End-to-end payment/event/team flows
   - Bracket generation performance (<100ms for 32 teams)

### Database Strategy: Use Transactions (FAST)

**All tests use `DatabaseTransactions`** - wraps each test in transaction, rolls back after.

```php
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentServiceTest extends TestCase
{
    use DatabaseTransactions; // ← Fast: Only transaction rollback, no migrations

    /** @test */
    public function it_processes_payment()
    {
        // Transaction START
        $payment = RecordStripe::factory()->create();
        $service->refund($payment);
        $this->assertDatabaseHas('record_stripes', ['payment_status' => 'refunded']);
        // Transaction ROLLBACK (automatic)
    }
}
```

### Mock All External Services (NEVER hit real APIs)
- **Stripe**: MocksStripe trait
- **Firebase/Firestore**: MocksFirebase trait
- **Email**: Mail::fake() or MocksEmail trait
- **Google OAuth**: MocksGoogleAuth trait
- **Steam API**: MocksSteamAuth trait

### Test Pattern Template
```php
use DatabaseTransactions, MocksStripe, CreatesTestUsers;

/** @test */
public function it_does_something()
{
    // Arrange
    $this->mockStripeClient();
    $user = $this->createParticipant();

    // Act
    $result = $service->doSomething($user);

    // Assert
    $this->assertTrue($result);
    $this->assertDatabaseHas('table', ['key' => 'value']);
}
```

### Coverage Targets
- Critical Services: 95%+
- Models: 85%+
- Controllers: 90%+
- Jobs/Mail/Middleware: 90%+
- **Overall: 90%+**

### Test Execution
```bash
vendor/bin/phpunit                    # All tests
vendor/bin/phpunit --testsuite Unit   # Unit tests only
vendor/bin/phpunit --coverage-html coverage  # With coverage report
```

### Configuration
- Database: `driftwood_test` (MySQL on port 3307)
- Cache/Session: `array` (in-memory)
- Mail: Mailhog (SMTP on port 1025, UI on port 8025)
- Queue: `sync` (no background)
- Firebase: Emulator (Firestore:8080, Auth:9099, UI:4000)
- OAuth: Dex Mock Server (port 5556)

### Test Environment (docker-compose.local.yml)
```bash
# Start all test services
docker-compose -f docker-compose.local.yml up -d

# Services available:
# - test_db: MySQL test database (port 3307)
# - mailhog: Email testing (SMTP 1025, UI 8025)
# - firebase_emulator: Firebase services (UI 4000)
# - dex: OAuth mock server (port 5556)
```

## Requirements
- PHP 8.2+ with GRPC extension
- MySQL with UTC timezone
- Redis for sessions/cache
- Node.js for frontend builds
- Stripe account for payments
- Firebase project for real-time features
- Google OAuth and Steam API credentials

## Development Notes
- Must log errors using Logger::log (backend) or console.error (frontend) in all try catches 
- use BEM for CSS
- don't use icons from bootstrap. use <svg> from bootstrap. if not found, go elsewhere
- When creating/updating database tables in laravel migration:
  * Check table existence/non-existence in create operations
  * In update operations, verify both table and column do not exist before proceeding
- Email Sending Workflow:
  * Always check one email event live to understand email sending process

## Analytics Implementation
The platform includes comprehensive analytics tracking using Google Analytics.

## Terraform Firebase Infrastructure
Terraform automatically creates Firebase web apps and configures all environment variables:

### What Terraform Creates:
- Firebase project setup
- Firestore database with security rules
- Firebase web app configuration
- Google OAuth integration
- Initial Firestore collections (room, event, analytics-*)

### Environment Variables Auto-Configured:
- `FIREBASE_API_KEY` / `VITE_FIREBASE_API_KEY`
- `VITE_AUTH_DOMAIN`
- `VITE_STORAGE_BUCKET`
- `VITE_APP_ID`
- `VITE_PROJECT_ID`
- OAuth credentials

### Composer Commands for Terraform:
```bash
# Development environment
composer tf:dev:plan        # Plan changes 
composer tf:dev:apply       # Apply plan 
composer tf:dev2:plan        # Plan changes without composer node.js update
composer tf:dev2:apply       # Apply plan without composer node.js update

# Staging environment  
composer tf:staging:plan
composer tf:staging:apply

# Production environment
composer tf:prod:plan
composer tf:prod:apply

# Utilities
composer tf:init            # Initialize Terraform
