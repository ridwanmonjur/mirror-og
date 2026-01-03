# Python to JavaScript Express Conversion

This document describes the conversion of Python services to JavaScript Express backend.

## Overview

Two Python services have been converted to JavaScript/TypeScript Express:

1. **cloud_client_auth/main.py** → **Auth Routes** (`/auth`)
2. **cloud_server_functions/main.py** → **Tournament Routes** (`/`)

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

### Original Python Files

The original Python files are **NOT MODIFIED**:
- `cloud_client_auth/main.py` - Still exists
- `cloud_server_functions/main.py` - Still exists

This conversion creates **new** Express endpoints alongside the Python services.

### Differences from Python

1. **Async/Await:** JavaScript uses `async/await` instead of Python's implicit async
2. **Type Safety:** TypeScript provides compile-time type checking
3. **Error Handling:** Uses try/catch with Express error handlers
4. **Logging:** Uses Winston logger (existing in project)
5. **JSON Handling:** Native JavaScript object handling vs Pydantic models

### Next Steps

If you want to fully replace the Python services:

1. Update Laravel to call the new Express endpoints
2. Update any cron jobs or scheduled tasks
3. Update Firebase Cloud Functions triggers
4. Test thoroughly in staging
5. Deploy to production
6. Deprecate Python services

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
