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

## Testing
- PHPUnit for unit/feature tests
- Laravel Dusk for browser testing
- Separate MySQL test database
- Firebase and external service mocking

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
composer tf:dev:plan        # Plan changes (preview only)
composer tf:dev:plan-save   # Save plan to dev.tfplan file
composer tf:dev:apply-plan  # Apply saved plan file
composer tf:dev:apply       # Direct apply (skips plan file)
composer tf:dev:output      # Show outputs

# Staging environment  
composer tf:staging:plan
composer tf:staging:plan-save
composer tf:staging:apply-plan
composer tf:staging:apply
composer tf:staging:output

# Production environment
composer tf:prod:plan
composer tf:prod:plan-save
composer tf:prod:apply-plan
composer tf:prod:apply
composer tf:prod:output

# Utilities
composer tf:init            # Initialize Terraform
composer tf:validate        # Validate configuration
composer tf:fmt             # Format configuration
composer tf:state:list      # List Terraform state
```

**Plan Output Files:**
- `terraform/dev.tfplan` - Development plan file
- `terraform/staging.tfplan` - Staging plan file  
- `terraform/prod.tfplan` - Production plan file

**Recommended workflow:**
1. `composer tf:dev:plan-save` - Save plan to `terraform/dev.tfplan`
2. Review the plan file at `terraform/dev.tfplan`
3. `composer tf:dev:apply-plan` - Apply exact saved plan

All `.env` files are automatically updated with Firebase configuration when Terraform runs.

### OAuth Client Setup
Terraform generates a setup script for creating Google OAuth clients manually:

**After running Terraform:**
1. Run `./create-oauth-client.sh` to see OAuth client setup instructions
2. Create OAuth client in Google Cloud Console with the provided settings
3. Update `.env` files with the generated Client ID and Client Secret

**OAuth Configuration:**
- **JavaScript Origins**: `http://localhost:8000`, `https://oceansgaming.gg`, `https://driftwood.gg`
- **Redirect URIs**: `/auth/google/callback` for each origin
- **Type**: Web application

### Terraform State Management
Remote state storage is configured for team collaboration:

**Bootstrap Commands (Run First):**
```bash
# Development environment
composer tf:bootstrap:dev:init      # Initialize bootstrap
composer tf:bootstrap:dev:plan      # Plan bootstrap
composer tf:bootstrap:dev:apply     # Create state storage bucket

# Staging/Production
composer tf:bootstrap:staging:apply
composer tf:bootstrap:prod:apply

# Get bucket information
composer tf:bootstrap:output        # Shows bucket name for backend config
```

**Backend Configuration:**
After bootstrap, update the appropriate backend file (`backend-dev.tf`, `backend-staging.tf`, `backend-prod.tf`) with the bucket name from bootstrap output, then run `composer tf:init` to migrate to remote state.

**State Features:**
- Versioning enabled for rollback capability
- Automatic lifecycle management (90-day retention)
- Environment isolation (separate state per environment)  
- Team collaboration with state locking

See `terraform/README-STATE-MANAGEMENT.md` for detailed setup and troubleshooting.
