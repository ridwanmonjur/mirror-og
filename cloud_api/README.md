# Driftwood FastAPI

FastAPI version of the Driftwood esports platform API, converted from Node.js/Express.

## Architecture

- **Framework**: FastAPI 0.104+
- **Database**: Databases library (SQLAlchemy Core query builder) with MySQL
- **Firebase**: Firebase Admin SDK for Firestore operations
- **Authentication**: JWT (compatible with Laravel tokens)
- **Validation**: Pydantic v2

## Features

- ✅ 60+ API endpoints across 5 controllers
- ✅ JWT authentication (Laravel-compatible)
- ✅ Role-based access control (Participant, Organizer, Admin)
- ✅ Tournament deadline processing and dispute resolution
- ✅ Firebase/Firestore integration for real-time features
- ✅ Rate limiting
- ✅ Query builder (no ORM)

## Project Structure

```
cloud_api/
├── app/
│   ├── main.py                           # FastAPI app entry point
│   ├── core/
│   │   ├── config.py                     # Pydantic Settings
│   │   ├── database.py                   # Databases library (query builder)
│   │   ├── firebase.py                   # Firebase Admin SDK
│   │   ├── dependencies.py               # JWT auth + role checking
│   │   ├── rate_limit.py                 # slowapi rate limiter
│   │   └── logging.py                    # Loguru logging
│   ├── models/
│   │   ├── tables.py                     # SQLAlchemy Core tables
│   │   └── schemas.py                    # Pydantic request/response models
│   ├── services/
│   │   ├── deadline_service.py           # Tournament deadline processing
│   │   ├── validation_service.py         # Bracket validation
│   │   └── firestore_service.py          # Firestore operations
│   └── api/v1/routers/
│       ├── public.py                     # Public API (8 endpoints)
│       ├── user.py                       # User API (17 endpoints)
│       ├── participant.py                # Participant API (9 endpoints)
│       ├── organizer.py                  # Organizer API (11 endpoints)
│       └── tournament.py                 # Tournament/Firebase (9 endpoints)
├── tests/
├── requirements.txt
└── .env
```

## Setup

### 1. Prerequisites

- Python 3.11+
- MySQL 8.0+ (running on port 3306 or custom port)
- Firebase project with Firestore enabled
- Service account key JSON file for Firebase

### 2. Installation

```bash
# Navigate to cloud_api directory
cd cloud_api

# Create virtual environment
python -m venv venv

# Activate virtual environment
# On Windows:
venv\Scripts\activate
# On macOS/Linux:
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt
```

### 3. Configuration

```bash
# Copy example environment file
cp .env.example .env

# Edit .env with your configuration
# IMPORTANT: JWT_SECRET must match Laravel's APP_KEY
```

### 4. Database

The API uses the same MySQL database as Laravel. No migrations needed - it connects to existing tables.

```bash
# Verify database connection
python -c "from app.core.database import test_database_connection; import asyncio; asyncio.run(test_database_connection())"
```

### 5. Firebase Setup

Place your Firebase service account key JSON file in the `cloud_api` directory:

```bash
# Download from Firebase Console > Project Settings > Service Accounts
# Save as serviceAccountKey.json
```

Or set the path in `.env`:
```
FIREBASE_CREDENTIALS_PATH=./path/to/serviceAccountKey.json
```

## Running

### Development Server

```bash
# With auto-reload
uvicorn app.main:app --reload --port 3000

# Or using Python
python app/main.py
```

### Production Server

```bash
# Using uvicorn with workers
uvicorn app.main:app --host 0.0.0.0 --port 3000 --workers 4
```

### Docker

```bash
# Build image
docker build -t driftwood-fastapi .

# Run container
docker run -p 3000:3000 --env-file .env driftwood-fastapi
```

## API Documentation

Once the server is running, access:

- **Interactive API docs**: http://localhost:3000/docs
- **Alternative docs**: http://localhost:3000/redoc
- **Health check**: http://localhost:3000/health

## API Endpoints

### Public API (No Auth)
- `GET /api/user/{user_id}/logs` - User activity logs
- `GET /api/user/{user_id}/connections` - User connections
- `POST /api/media` - Upload media
- `GET /api/media/stream/{media}` - Stream media
- `DELETE /api/media/{media}` - Delete media
- `PUT /api/interest` - Register beta interest

### User API (Authenticated)
- `GET /api/user` - Get current user
- `GET /api/user/notifications` - Get notifications
- `POST /api/user/notifications/{id}` - Mark notification as read
- `POST /api/user/settings` - Update settings
- `POST /api/user/likes` - Like/unlike event
- `GET /api/teams/search` - Search teams

### Participant API (Participant/Admin)
- `POST /api/participant/events` - Search events
- `POST /api/participant/organizer/follow` - Follow organizer
- `POST /api/participant/team` - Create/update team
- `POST /api/participant/team/{team_id}/user/{user_id}/invite` - Invite member
- `POST /api/participant/team/member/{id}/deleteInvite` - Withdraw invite
- `POST /api/participant/team/member/{id}/rejectInvite` - Reject invite

### Organizer API (Organizer/Admin)
- `POST /api/organizer/events/search` - Search organizer events
- `POST /api/organizer/event/{id}/destroy` - Delete event
- `POST /api/organizer/event/{id}/results` - Store results
- `POST /api/organizer/event/{id}/notifications` - Send notifications
- `POST /api/organizer/event/{id}/matches` - Upsert matches
- `POST /api/organizer/event/{id}/awards` - Create award
- `DELETE /api/organizer/event/{id}/awards/{award_id}` - Delete award

### Tournament API (Firebase Operations)
- `POST /room/block` - Block/unblock chat room
- `POST /batch/reports` - Batch create reports
- `POST /batch/disputes` - Batch create disputes
- `POST /deadline/started` - Handle start deadline
- `POST /deadline/ended` - Handle end deadline
- `POST /deadline/org` - Handle organizer deadline
- `POST /match/result` - Get match result
- `POST /match/results/all` - Get all results
- `GET /health` - Firebase health check

## Authentication

All authenticated endpoints require a Bearer token:

```bash
curl -H "Authorization: Bearer YOUR_JWT_TOKEN" http://localhost:3000/api/user
```

The JWT token is generated by Laravel and verified by FastAPI using the same `APP_KEY`.

## Rate Limiting

- **Auth endpoints**: 30 requests/minute
- **Tournament endpoints**: 60 requests/minute
- **Batch operations**: 20 requests/minute
- **Deadline processing**: 10 requests/minute

## Testing

```bash
# Run all tests
pytest

# Run with coverage
pytest --cov=app --cov-report=html

# Run specific test file
pytest tests/test_services/test_deadline_service.py
```

## Development

### Code Style

```bash
# Format with Black
black app/

# Lint with Ruff
ruff check app/
```

### Database Queries

Using Databases library (SQLAlchemy Core):

```python
from app.core.database import database
from app.models.tables import users
from sqlalchemy import select

# SELECT
query = select(users).where(users.c.id == user_id)
result = await database.fetch_one(query)

# INSERT
query = users.insert().values(name="John", email="john@example.com")
user_id = await database.execute(query)

# UPDATE
query = users.update().where(users.c.id == user_id).values(name="Jane")
await database.execute(query)

# DELETE
query = users.delete().where(users.c.id == user_id)
await database.execute(query)
```

## Critical Services

### DeadlineService
Handles tournament deadline processing and dispute resolution. This is **critical business logic** ported line-by-line from Node.js to maintain competitive integrity.

**Key features**:
- Bulk fetches brackets/disputes from Firestore
- Resolves disputes based on deadlines and submission conflicts
- Handles tie-breaking with random selection when needed
- Manages disqualifications for non-submissions

### ValidationService
Validates bracket updates and enforces business rules:
- Match existence verification
- Organizer permission checks
- Deadline validation for participant submissions
- Team membership verification

### FirestoreService
Manages all Firebase/Firestore operations:
- Bracket report CRUD
- Dispute submission and resolution
- Chat room blocking
- Real-time tournament data

## Environment Variables

See `.env.example` for all available configuration options.

**Critical variables**:
- `JWT_SECRET` - Must match Laravel's `APP_KEY` (base64: format)
- `DB_DATABASE` - MySQL database name
- `FIREBASE_PROJECT_ID` - Firebase project ID
- `FIREBASE_CREDENTIALS_PATH` - Path to service account JSON

## Troubleshooting

### Database connection fails
- Verify MySQL is running
- Check `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` in `.env`
- Ensure database `DB_DATABASE` exists

### JWT authentication fails
- Ensure `JWT_SECRET` in `.env` matches Laravel's `APP_KEY`
- Verify token format: `base64:encodedstring`
- Check token is passed as `Authorization: Bearer TOKEN`

### Firebase connection fails
- Verify `FIREBASE_CREDENTIALS_PATH` points to valid JSON file
- Check Firebase project ID matches
- Ensure Firestore is enabled in Firebase Console
- For emulator: set `FIREBASE_EMULATOR_HOST=localhost:8080`

## License

Proprietary - Driftwood Platform

## Support

For issues or questions, contact the development team.
