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
- **Frontend**: Vite + Alpine.js + Bootstrap 5
- **Admin**: Filament 3.2 with Livewire 3.5
- **Database**: MySQL with Redis caching
- **Payments**: Stripe for transactions
- **Real-time**: Firebase integration
- **Auth**: Social login (Google, Steam)

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