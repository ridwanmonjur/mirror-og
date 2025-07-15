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
- When creating/updating database tables in laravel migration:
  * Check table existence/non-existence in create operations
  * In update operations, verify both table and column do not exist before proceeding
- Email Sending Workflow:
  * Always check one email event live to understand email sending process

## Analytics Implementation
The platform includes comprehensive analytics tracking using Google Tag Manager and a custom analytics service.

### Configuration
- **Google Tag Manager ID**: `AW-345473430` (via environment variable)
- **Package**: Spatie Laravel Google Tag Manager (`spatie/laravel-googletagmanager`)
- **Environment Variables**: 
  - `GOOGLE_TAG_MANAGER_ID`
  - `GOOGLE_TAG_MANAGER_ENABLED`
  - `VITE_GOOGLE_TAG_MANAGER_ID`
  - `VITE_GOOGLE_TAG_MANAGER_ENABLED`

### Analytics Service (`resources/js/custom/analytics.js`)
A comprehensive JavaScript service that provides:
- **Dual Support**: Google Tag Manager (primary) + Direct Google Analytics (fallback)
- **Event Tracking**: Custom events with detailed parameters
- **Social Interaction Tracking**: Follow/unfollow actions with context
- **Form Submission Tracking**: Form interactions and submissions
- **Page View Tracking**: Enhanced page views with event data
- **User Engagement**: Engagement metrics and conversion tracking
- **Event Registration**: Tournament registration tracking with entry fees

### Key Features
- **Event Card Clicks**: Tracks user interactions with event cards including tier, type, esport, and location data
- **Social Features**: Follow interactions, social sharing with event context
- **Tournament Data**: Registration tracking, entry fees, event details
- **User Attribution**: Custom dimensions for user_id, event_id, tier_id, type_id, game_id
- **Real-time Tracking**: Immediate event pushing to dataLayer

### Implementation Details
- **Template Integration**: GTM includes in blade templates (`@include('googletagmanager::head')` and `@include('googletagmanager::body')`)
- **Data Attributes**: Event cards and forms include analytics data attributes
- **Analytics Data Element**: Uses `id="analytics-data"` for event-specific tracking
- **Event Context**: Comprehensive event data extraction and tracking
- **Fallback Support**: Graceful degradation when GTM is unavailable

### Usage Examples
```javascript
// Track event card click
window.trackEventCardClick(element);

// Track social interaction
window.trackSocialInteraction('follow', userId, 'user');

// Track form submission
window.trackFormSubmission('eventRegistration', {event_id: 123});

// Track page view with event data
window.analytics.trackPageViewWithEventData(title, path, eventData);
```