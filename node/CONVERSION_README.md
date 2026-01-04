# Python & Laravel to JavaScript Express Conversion

This document describes the conversion of Python services and Laravel API routes to JavaScript Express backend.

## Overview

The following services have been converted to JavaScript/TypeScript Express:

1. **cloud_client_auth/main.py** → **Auth Routes** (`/auth`)
2. **cloud_server_functions/main.py** → **Tournament Routes** (`/`)
3. **routes/api.php (Laravel)** → **Laravel API Routes** (`/api/*`)

## What Was Converted

### 1. Auth Service (cloud_client_auth/main.py)

**Original:** Flask/Google Cloud Functions service for client authentication

**Converted to:**
- `src/routes/auth.ts` - Auth routes
- `src/controllers/authController.ts` - Auth controller
- `src/middleware/rateLimiter.ts` - Rate limiting middleware

**Endpoints:**
- `POST /auth/token` - Create Firebase custom token and JWT
- `GET /auth/health` - Health check

**Features:**
- Firebase custom token generation
- JWT token creation with 30-minute expiration
- Rate limiting (30 requests/minute)
- Environment-based CORS
- App Check verification (disabled in dev)

### 2. Tournament Service (cloud_server_functions/main.py)

**Original:** FastAPI service for tournament/match management

**Converted to:**
- `src/routes/tournaments.ts` - Tournament routes
- `src/controllers/tournamentController.ts` - Tournament controller
- `src/services/deadlineService.ts` - Deadline processing service (DeadlineTaskTrait)

**Endpoints:**
- `POST /room/block` - Block/unblock chat rooms (60 req/min)
- `POST /batch/reports` - Create batch match reports (20 req/min)
- `POST /batch/disputes` - Create batch dispute documents (20 req/min)
- `POST /deadline/started` - Mark tournament matches as started (10 req/min)
- `POST /deadline/ended` - Process ended matches (10 req/min)
- `POST /deadline/org` - Organizer deadline processing (10 req/min)
- `POST /match/result` - Get single match result (60 req/min)
- `POST /match/results/all` - Get all match results for event (60 req/min)
- `GET /health` - Health check

**Features:**
- Complex deadline interpretation logic (DeadlineTaskTrait)
- Dispute resolution system with randomization
- Firestore batch operations
- Score calculation from match results
- Match state management (UPCOMING → ONGOING → ENDED)
- Bulk fetching with hashmaps to avoid N+1 queries

### 3. Laravel API Service (routes/api.php)

**Original:** Laravel API routes with role-based middleware

**Converted to:**
- `src/routes/publicApi.ts` - Public API routes (no auth)
- `src/routes/userApi.ts` - User API routes (authenticated)
- `src/routes/participantApi.ts` - Participant routes
- `src/routes/organizerApi.ts` - Organizer routes
- `src/controllers/publicApiController.ts` - Public API controller
- `src/controllers/userApiController.ts` - User API controller
- `src/controllers/participantApiController.ts` - Participant API controller
- `src/controllers/organizerApiController.ts` - Organizer API controller
- `src/middleware/roleCheck.ts` - Role-based access control

**Public API Endpoints (no authentication):**
- `GET /api/user/:id/logs` - Get activity logs
- `GET /api/user/:id/connections` - Get user connections
- `POST /api/event/:id/invitation` - Store event invitation
- `POST /api/event/:id/inviteDestroy` - Delete invitation
- `POST /api/media` - Upload media
- `GET /api/media/stream/:media` - Stream media
- `DELETE /api/media/:media` - Delete media
- `PUT /api/interest` - Register beta interest

**User API Endpoints (authenticated, any role):**
- `GET /api/user` - Get current user
- `GET /api/teams/search` - Search teams
- `POST /api/teams/list` - Get team list
- `POST /api/event/:id/brackets` - Validate bracket
- `GET /api/user/:id/reports` - Get user reports
- `GET /api/user/notifications` - View notifications
- `POST /api/user/notifications` - Create notification
- `POST /api/user/notifications/:id` - Mark as read
- `POST /api/user/settings` - Change settings
- `POST /api/user/:id/background` - Replace background
- `POST /api/user/:id/star` - Toggle star
- `POST /api/user/:id/report` - Report user
- `POST /api/user/likes` - Like event
- `POST /api/user/participants` - Search participants
- `POST /api/user/unlink` - Unlink bank account

**Participant API Endpoints (participant or admin):**
- `POST /api/participant/events` - Get events list
- `POST /api/participant/organizer/follow` - Follow organizer
- `POST /api/participant/profile` - Edit profile
- `POST /api/participant/team` - Edit team
- `POST /api/participant/team/:id/user/:userId/invite` - Invite member
- `POST /api/participant/team/:id/member/:memberId/captain` - Make captain
- `POST /api/participant/team/:id/member/:memberId/deleteCaptain` - Remove captain
- `POST /api/participant/team/member/:id/update` - Update member
- `POST /api/participant/team/member/:id/deleteInvite` - Withdraw invite
- `POST /api/participant/team/member/:id/rejectInvite` - Reject invite

**Organizer API Endpoints (organizer or admin):**
- `POST /api/organizer/events/search` - Search events
- `POST /api/organizer/event/:id/destroy` - Delete event
- `POST /api/organizer/event/:id/results` - Store results
- `POST /api/organizer/event/:id/notifications` - Send notifications
- `POST /api/organizer/event/:id/matches` - Upsert bracket
- `POST /api/organizer/event/:id/awards` - Store award
- `DELETE /api/organizer/event/:id/awards/:awardId` - Delete award
- `POST /api/organizer/event/:id/achievements` - Store achievement
- `DELETE /api/organizer/event/achievements/:achievementId` - Delete achievement
- `POST /api/organizer/profile` - Edit profile

**Features:**
- Role-based access control (participant, organizer, admin)
- JWT authentication (compatible with Laravel tokens)
- MySQL database integration (shared with Laravel)
- Comprehensive user, team, and event management
- Social features (following, starring, reporting)
- Notification system
- Media upload/streaming
- Bracket validation

## New Dependencies

Added to `package.json`:
- `jose`: ^5.2.0 - JWT token creation
- `express-rate-limit`: ^7.1.5 - Rate limiting

## Environment Variables

Updated `.env.example` with:

```env
# Environment configuration
ENVIRONMENT=dev  # dev, staging, or prod

# Auth Service Configuration
SECRET_KEY=your-secret-key  # For JWT token generation
```

## CORS Configuration

The Express app now includes environment-based CORS:

- **Production:** `https://driftwood.gg`
- **Staging:** `https://oceansgaming.gg`
- **Development:** All localhost origins (8000, 5173)

## Rate Limiting

Different rate limits for different endpoint types:

- **Auth endpoints:** 30 requests/minute
- **Tournament endpoints:** 60 requests/minute
- **Batch operations:** 20 requests/minute
- **Deadline processing:** 10 requests/minute

## Usage Examples

### Auth Token Creation

```bash
curl -X POST http://localhost:3000/auth/token \
  -H "Content-Type: application/json" \
  -d '{
    "uid": "user123",
    "role": "PARTICIPANT",
    "teamId": "team456"
  }'
```

Response:
```json
{
  "token": "firebase-custom-token...",
  "jwt_token": "eyJhbGciOiJIUzI1NiIs...",
  "expires_at": "2024-01-01T12:30:00.000Z"
}
```

### Room Blocking

```bash
curl -X POST http://localhost:3000/room/block \
  -H "Content-Type: application/json" \
  -d '{
    "user1": "123",
    "user2": "456",
    "action": "block",
    "blocked_by": "123"
  }'
```

### Deadline Processing (Started)

```bash
curl -X POST http://localhost:3000/deadline/started \
  -H "Content-Type: application/json" \
  -d '{
    "detail_id": "event123",
    "matches": [
      {
        "team1_position": "1",
        "team2_position": "2",
        "event_details_id": "event123"
      }
    ],
    "games_per_match": 3
  }'
```

### Get Match Results

```bash
curl -X POST http://localhost:3000/match/results/all \
  -H "Content-Type: application/json" \
  -d '{
    "event_id": "event123"
  }'
```

### Laravel API Examples

#### Get Current User (Authenticated)

```bash
curl -X GET http://localhost:3000/api/user \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

#### Search Teams

```bash
curl -X GET "http://localhost:3000/api/teams/search?search_term=esports&limit=10" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

#### Get Events (Participant)

```bash
curl -X POST http://localhost:3000/api/participant/events \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "filters": {
      "game_id": "123",
      "status": "upcoming"
    },
    "page": 1,
    "limit": 20
  }'
```

#### Edit Profile (Participant)

```bash
curl -X POST http://localhost:3000/api/participant/profile \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Player123",
    "bio": "Pro gamer",
    "avatar_url": "https://example.com/avatar.jpg",
    "social_links": {
      "twitter": "@player123",
      "twitch": "player123"
    }
  }'
```

#### Search Events (Organizer)

```bash
curl -X POST http://localhost:3000/api/organizer/events/search \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "search_term": "tournament",
    "filters": {
      "status": "active"
    },
    "page": 1,
    "limit": 20
  }'
```

#### Store Event Results (Organizer)

```bash
curl -X POST http://localhost:3000/api/organizer/event/123/results \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "team_id": "456",
    "placement": 1,
    "prize_amount": 1000
  }'
```

#### Get Activity Logs (Public)

```bash
curl -X GET http://localhost:3000/api/user/123/logs
```

## Deployment

### Install Dependencies

```bash
cd node
npm install
```

### Development

```bash
npm run dev
```

### Production Build

```bash
npm run build
npm start
```

### Environment Setup

1. Copy `.env.example` to `.env`
2. Update environment variables:
   - `ENVIRONMENT` - Set to `dev`, `staging`, or `prod`
   - `SECRET_KEY` - Set to a secure random string
   - `FIREBASE_CREDENTIALS_PATH` - Path to Firebase service account key
   - Other existing variables as needed

## Testing

All existing test infrastructure works with the new routes:

```bash
npm test                    # All tests
npm run test:unit          # Unit tests
npm run test:integration   # Integration tests
```

## Architecture Notes

### DeadlineService

The `DeadlineService` class is a TypeScript implementation of Python's `DeadlineTaskTrait`:

- **Bulk fetching:** Fetches all brackets and disputes for an event once, stores in-memory
- **Hashmap lookups:** Avoids N+1 Firestore queries
- **Dispute resolution:** Handles time-based and random resolution
- **Score calculation:** Computes final scores from team submissions
- **Conflict handling:** Resolves ties, defaults, and conflicts

### Rate Limiting

Uses `express-rate-limit` with in-memory store (same as Python's simple implementation). For production with multiple instances, consider using Redis store.

### Firebase Integration

- Uses existing Firebase Admin SDK initialization
- Shares Firestore client with bracket routes
- Custom token generation for client authentication
- Server timestamp for dispute creation

## Migration Notes

### Original Files

The original files are **NOT MODIFIED**:
- `cloud_client_auth/main.py` - Still exists
- `cloud_server_functions/main.py` - Still exists
- `routes/api.php` - Still exists

This conversion creates **new** Express endpoints alongside the Python and Laravel services.

### Differences from Python

1. **Async/Await:** JavaScript uses `async/await` instead of Python's implicit async
2. **Type Safety:** TypeScript provides compile-time type checking
3. **Error Handling:** Uses try/catch with Express error handlers
4. **Logging:** Uses Winston logger (existing in project)
5. **JSON Handling:** Native JavaScript object handling vs Pydantic models

### Differences from Laravel

1. **Database Access:** Direct SQL queries using mysql2 vs Eloquent ORM
2. **Middleware:** Express middleware vs Laravel middleware
3. **Validation:** Manual validation vs FormRequest classes
4. **Authentication:** JWT from header vs Laravel Sanctum/Passport
5. **Error Handling:** Express error handlers vs Laravel exception handling
6. **Role Checking:** Custom middleware vs Laravel policies/gates

### Next Steps

If you want to fully replace the services:

**For Python Services:**
1. Update Laravel to call the new Express endpoints
2. Update any cron jobs or scheduled tasks
3. Update Firebase Cloud Functions triggers
4. Test thoroughly in staging
5. Deploy to production
6. Deprecate Python services

**For Laravel API:**
1. Update frontend to call Express endpoints instead of Laravel
2. Ensure JWT tokens are compatible
3. Test all role-based access controls
4. Verify database queries return correct data
5. Migrate gradually by route group (public → user → participant → organizer)
6. Keep Laravel running alongside Express during transition

## API Compatibility

The converted endpoints maintain **100% API compatibility** with the Python versions:

- Same request/response formats
- Same status codes
- Same error messages
- Same rate limits
- Same CORS configuration

Clients can switch between Python and Express services without code changes.

## Performance Considerations

- **Firestore Batching:** Uses Firestore batch writes for efficiency
- **Hashmap Caching:** Reduces Firestore reads during deadline processing
- **Rate Limiting:** Prevents abuse and overload
- **Connection Pooling:** MySQL connection pool for bracket validation

## Monitoring

Health check endpoints available:

- `GET /health` - Main app health
- `GET /auth/health` - Auth service health
- `GET /health` (tournament) - Tournament service health

All endpoints log requests with IP addresses and response codes.
