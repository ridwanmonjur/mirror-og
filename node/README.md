# Driftwood Bracket API Server

Secure Express TypeScript server for handling bracket and dispute Firestore updates with SQL validation. This server replaces insecure client-side Firestore writes by validating all operations against the MySQL database before updating Firestore.

## Architecture

```
Frontend → Node Express API → MySQL Validation → Firestore Update → Response
```

### Key Features
- **Server-side Firestore writes only** - No direct client writes
- **SQL validation** - Validates deadlines, permissions, and match existence
- **JWT authentication** - Validates Laravel JWT tokens
- **Comprehensive testing** - Integration and unit tests with >90% coverage

## Tech Stack

- **Runtime**: Node.js 18+
- **Framework**: Express
- **Language**: TypeScript
- **Database**: MySQL (shared with Laravel)
- **Firestore**: Firebase Admin SDK
- **Auth**: JWT (Laravel tokens)
- **Validation**: Zod
- **Testing**: Jest + Supertest
- **Logging**: Winston

## Project Structure

```
/node
├── src/
│   ├── index.ts                    # Express app entry point
│   ├── config/
│   │   ├── database.ts             # MySQL connection pool
│   │   ├── firebase.ts             # Firebase Admin SDK
│   │   └── jwt.ts                  # JWT configuration
│   ├── middleware/
│   │   ├── auth.ts                 # JWT authentication
│   │   ├── errorHandler.ts         # Error handling
│   │   └── validateRequest.ts      # Request validation
│   ├── routes/
│   │   └── brackets.ts             # Bracket API routes
│   ├── controllers/
│   │   ├── bracketController.ts    # Report endpoints
│   │   └── disputeController.ts    # Dispute endpoints
│   ├── services/
│   │   ├── validationService.ts    # SQL validation logic
│   │   └── firestoreService.ts     # Firestore operations
│   ├── models/
│   │   └── types.ts                # TypeScript interfaces
│   ├── schemas/
│   │   └── validators.ts           # Zod validation schemas
│   └── utils/
│       └── logger.ts               # Winston logger
└── tests/
    ├── integration/
    │   ├── bracket.test.ts
    │   └── dispute.test.ts
    ├── unit/
    │   └── validation.test.ts
    └── helpers/
        ├── testDb.ts               # Database seeding
        └── testAuth.ts             # Mock JWT tokens
```

## Setup

### Prerequisites
- Node.js 18+ and npm
- MySQL database (shared with Laravel app)
- Firebase project with service account key
- Laravel APP_KEY for JWT validation

### Installation

```bash
# Navigate to node directory
cd node

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Configure environment variables (see below)
nano .env
```

### Environment Variables

Create a `.env` file:

```env
# Server
PORT=3000
NODE_ENV=development

# MySQL Database (from Laravel)
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=driftwood
DB_USERNAME=root
DB_PASSWORD=

# JWT (copy Laravel's APP_KEY)
JWT_SECRET=base64:your_laravel_app_key_here
JWT_ALGORITHM=HS256

# Firebase
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_DATABASE_ID=(default)
FIREBASE_CREDENTIALS_PATH=./serviceAccountKey.json

# Logging
LOG_LEVEL=debug
```

### Firebase Service Account

1. Download service account key from Firebase Console
2. Save as `serviceAccountKey.json` in the `/node` directory
3. **Do not commit this file** (already in `.gitignore`)

## API Endpoints

All endpoints require JWT authentication via `Authorization: Bearer <token>` header.

### POST /api/brackets/:eventId/report

Report match results.

**Request Body:**
```json
{
  "team1_id": "1",
  "team1_position": "W1",
  "team2_id": "2",
  "team2_position": "W2",
  "my_team_id": "1",
  "willCheckDeadline": true,
  "reportData": {
    "score": [2, 1],
    "stageName": "U",
    "realWinners": ["0", "1", "0"],
    "organizerWinners": ["0", "1", "0"],
    "team1Id": "1",
    "team2Id": "2",
    "position": "W1.W2",
    "completeMatchStatus": "ENDED",
    "randomWinners": [null, null, null],
    "defaultWinners": [null, null, null],
    "disqualified": false,
    "disputeResolved": [null, null, null],
    "team1Winners": ["0", "1", "0"],
    "team2Winners": ["1", "0", "1"],
    "matchStatus": ["ENDED", "ENDED", "ENDED"]
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Report updated successfully"
}
```

### POST /api/brackets/:eventId/disputes

Submit a dispute.

**Request Body:**
```json
{
  "report_id": "W1.W2",
  "match_number": 0,
  "dispute_userId": "1",
  "dispute_teamId": "1",
  "dispute_teamNumber": 0,
  "dispute_reason": "Opponent cheating",
  "dispute_description": "Details...",
  "dispute_image_videos": ["file1.jpg", "file2.mp4"],
  "team1_position": "W1",
  "team2_position": "W2"
}
```

### PATCH /api/brackets/:eventId/disputes/:disputeId/respond

Respond to a dispute.

### PATCH /api/brackets/:eventId/disputes/:disputeId/resolve

Resolve a dispute (organizers only).

## Development

### Run Development Server

```bash
npm run dev
```

Server runs on `http://localhost:3000` with hot reload.

### Build for Production

```bash
npm run build
```

Compiled JavaScript output to `/dist` directory.

### Run Production Server

```bash
npm start
```

## Testing

### Run All Tests

```bash
npm test
```

### Run Integration Tests

```bash
npm run test:integration
```

### Run Unit Tests

```bash
npm run test:unit
```

### Watch Mode

```bash
npm run test:watch
```

### Test Environment

Tests use:
- **Database**: `driftwood_test` on port 3307 (from docker-compose.local.yml)
- **Firebase**: Emulator on localhost:8080
- **Transactions**: All tests rollback automatically

### Coverage Report

```bash
npm test -- --coverage
```

## Validation Logic

The server replicates PHP validation from `ValidateBracketUpdateRequest.php`:

1. **Match Existence**: Verifies match exists in `brackets` table
2. **Organizer Permission**: Checks event ownership for organizers
3. **Deadline Validation**: Parses `bracket_deadlines` JSON and checks current date
4. **Team Membership**: Verifies user is accepted team member

## Firestore Operations

Replicates operations from `BracketData.js`:

### Document IDs
- **Bracket reports**: `{team1Position}.{team2Position}` (e.g., "W1.W2")
- **Disputes**: `{team1Position}.{team2Position}.{matchNumber}` (e.g., "W1.W2.0")

### Collections
- `event/{eventId}/brackets` - Match reports
- `event/{eventId}/disputes` - Dispute documents

## Error Handling

All errors are logged and return consistent JSON:

```json
{
  "success": false,
  "message": "Error description",
  "errors": [...]  // For validation errors
}
```

## Security

- **No client-side Firestore writes**: All writes go through validated API
- **JWT verification**: Laravel APP_KEY based authentication
- **SQL injection protection**: Parameterized queries
- **Input validation**: Zod schemas validate all requests
- **Error logging**: Winston logs all errors to files

## Docker Support

```bash
# Build image
docker build -t bracket-api .

# Run container
docker run -p 3000:3000 --env-file .env bracket-api
```

## Integration with Laravel

This server is designed to work alongside the existing Laravel app:

1. **Shared MySQL database**: Uses same tables for validation
2. **JWT tokens**: Validates Laravel-generated tokens
3. **Firestore**: Writes to same collections that frontend reads from

## Next Steps (Not in Current Scope)

1. **Frontend Migration**: Refactor `BracketData.js` to call this API instead of direct Firestore
2. **Firestore Rules**: Set write permissions to deny all client writes
3. **Rate Limiting**: Add request rate limiting
4. **Monitoring**: Set up error alerting and metrics

## Troubleshooting

### Database Connection Errors
- Verify MySQL is running on specified port
- Check credentials in `.env` match Laravel's
- Ensure `driftwood` database exists

### Firebase Errors
- Verify service account key path is correct
- Check Firebase project ID matches
- For tests, ensure emulator is running

### JWT Errors
- Verify `JWT_SECRET` matches Laravel's `APP_KEY` exactly
- Check token format is `Bearer <token>`
- Ensure tokens haven't expired

## License

MIT

## Support

For issues or questions, please contact the development team or create an issue in the project repository.
