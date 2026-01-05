# Prisma Migration Guide

## Migration Progress

### ✅ Completed Migrations

1. **Database Config** (`src/config/database.ts`)
   - Added Prisma client export
   - Maintains backward compatibility with mysql2

2. **Prisma Client** (`src/config/prisma.ts`)
   - Singleton pattern
   - Connection management
   - Graceful shutdown support

3. **Validation Service** (`src/services/validationService.ts`)
   - Migrated all raw SQL queries to Prisma
   - Maintains same API interface
   - Old file backed up as `validationService.old.ts`

4. **Test Helpers** (`tests/helpers/testDb.ts`)
   - All seed functions use Prisma
   - `clearTestData()` uses Prisma deleteMany
   - Old file backed up as `testDb.old.ts`

5. **Organizer API Controller** (`src/controllers/organizerApiController.ts`)
   - All 9 endpoints migrated to Prisma
   - Uses Prisma's upsert, findFirst, findMany, create, update, delete
   - Old file backed up as `organizerApiController.old.ts`

6. **Participant API Controller** (`src/controllers/participantApiController.ts`)
   - All 13 endpoints migrated to Prisma
   - Includes like/unlike, follow/unfollow, team management
   - Old file backed up as `participantApiController.old.ts`

### 🔄 Remaining Migrations

#### User API Controller (`src/controllers/userApiController.ts`)

**Endpoints to migrate:**
```typescript
// Example Prisma conversions:

// OLD:
const [user] = await query('SELECT * FROM users WHERE id = ?', [userId]);

// NEW:
const user = await prisma.users.findUnique({
  where: { id: parseInt(userId) }
});

// OLD:
const notifications = await query(
  'SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50',
  [userId]
);

// NEW:
const notifications = await prisma.notification.findMany({
  where: { user_id: parseInt(userId) },
  orderBy: { created_at: 'desc' },
  take: 50
});

// OLD:
await query(
  'UPDATE users SET settings = ?, updated_at = NOW() WHERE id = ?',
  [JSON.stringify(settings), userId]
);

// NEW:
await prisma.users.update({
  where: { id: parseInt(userId) },
  data: { settings: settings }
});
```

#### Public API Controller (`src/controllers/publicApiController.ts`)

**Endpoints to migrate:**
```typescript
// Activity logs
const logs = await prisma.activity_logs.findMany({
  where: { user_id: parseInt(userId) },
  orderBy: { created_at: 'desc' },
  take: 50
});

// Connections (with relations)
const followers = await prisma.users.findMany({
  where: {
    following: {
      some: { organizer_id: parseInt(userId) }
    }
  }
});

// Media upload
const media = await prisma.imageVideo.create({
  data: {
    file_path,
    file_type,
    user_id: user_id ? parseInt(user_id) : null
  }
});

// Beta interest (upsert)
await prisma.betaInterest.upsert({
  where: { email },
  update: {},
  create: { email, name, interest_type }
});
```

## Prisma Query Patterns

### 1. Find Operations

```typescript
// Find unique
const user = await prisma.users.findUnique({
  where: { id: 1 }
});

// Find first matching
const event = await prisma.event_details.findFirst({
  where: {
    user_id: 1,
    status: 'active'
  }
});

// Find many with filters
const events = await prisma.event_details.findMany({
  where: {
    user_id: 1,
    OR: [
      { eventName: { contains: 'tournament' } },
      { description: { contains: 'tournament' } }
    ]
  },
  orderBy: { start_date: 'desc' },
  skip: 0,
  take: 20
});
```

### 2. Create Operations

```typescript
// Simple create
const team = await prisma.teams.create({
  data: {
    teamName: 'Warriors',
    creator_id: 1
  }
});

// Create many
await prisma.notification.createMany({
  data: [
    { user_id: 1, title: 'Hi', message: 'Hello', type: 'info' },
    { user_id: 2, title: 'Hi', message: 'Hello', type: 'info' }
  ]
});
```

### 3. Update Operations

```typescript
// Update single
await prisma.users.update({
  where: { id: 1 },
  data: { name: 'New Name' }
});

// Update many
await prisma.team_members.updateMany({
  where: {
    team_id: 1,
    user_id: 2
  },
  data: { is_captain: true }
});
```

### 4. Upsert Operations

```typescript
// Upsert (update if exists, create if not)
await prisma.eventResult.upsert({
  where: {
    event_id_team_id: {
      event_id: 1,
      team_id: 1
    }
  },
  update: {
    placement: 1,
    prize_amount: 1000
  },
  create: {
    event_id: 1,
    team_id: 1,
    placement: 1,
    prize_amount: 1000
  }
});
```

### 5. Delete Operations

```typescript
// Delete single
await prisma.teams.delete({
  where: { id: 1 }
});

// Delete many
await prisma.bracketDeadline.deleteMany({
  where: { event_details_id: 1 }
});
```

### 6. Relations

```typescript
// Include relations
const team = await prisma.teams.findUnique({
  where: { id: 1 },
  include: {
    team_members: true,
    creator: true
  }
});

// Select specific fields
const user = await prisma.users.findUnique({
  where: { id: 1 },
  select: {
    id: true,
    name: true,
    email: true
  }
});

// Nested where clauses
const teams = await prisma.teams.findMany({
  where: {
    team_members: {
      some: {
        user_id: 1,
        status: 'accepted'
      }
    }
  }
});
```

### 7. Like/Unlike Toggle Pattern

```typescript
// Check if exists
const existing = await prisma.eventLike.findUnique({
  where: {
    user_id_event_id: {
      user_id: userId,
      event_id: eventId
    }
  }
});

if (existing) {
  // Unlike
  await prisma.eventLike.delete({
    where: {
      user_id_event_id: {
        user_id: userId,
        event_id: eventId
      }
    }
  });
} else {
  // Like
  await prisma.eventLike.create({
    data: { user_id: userId, event_id: eventId }
  });
}
```

## Type Safety

Prisma provides full type safety:

```typescript
// TypeScript knows the exact shape of the result
const user = await prisma.users.findUnique({
  where: { id: 1 }
});
// user is typed as: User | null

const users = await prisma.users.findMany();
// users is typed as: User[]
```

## Common Conversions

### SQL LIKE → Prisma contains
```typescript
// OLD: name LIKE '%search%'
// NEW:
where: {
  name: { contains: 'search' }
}
```

### SQL IN → Prisma in
```typescript
// OLD: id IN (1, 2, 3)
// NEW:
where: {
  id: { in: [1, 2, 3] }
}
```

### SQL JSON → Prisma Json type
```typescript
// JSON fields are typed as Prisma.JsonValue
data: {
  settings: settingsObject, // No need for JSON.stringify
  deadlines: deadlinesObject
}
```

### SQL NOW() → Prisma automatic
```typescript
// Prisma automatically handles created_at and updated_at
// if you use @default(now()) and @updatedAt in schema
```

## Running the Migration

1. **Install Dependencies**:
```bash
cd node
npm install
```

2. **Generate Prisma Client**:
```bash
npm run prisma:generate
```

3. **Set DATABASE_URL** in `.env`:
```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/driftwood"
```

4. **Test the Migration**:
```bash
# Run integration tests
npm run test:integration

# Run unit tests
npm run test:unit
```

5. **Compare Old vs New** (if issues arise):
   - Old files are backed up with `.old.ts` extension
   - Compare behavior if tests fail

## Benefits of Prisma

1. **Type Safety**: Full TypeScript support with auto-completion
2. **No SQL Injection**: Parameterized queries automatically
3. **Better DX**: Intuitive API, easier to read and maintain
4. **Auto-completion**: IDE suggestions for all models and fields
5. **Migrations**: Can track schema changes (though Laravel handles this)
6. **Relations**: Easy to work with related data
7. **Performance**: Connection pooling, prepared statements

## Next Steps

1. Migrate `userApiController.ts`
2. Migrate `publicApiController.ts`
3. Migrate remaining services (if any)
4. Remove `.old.ts` backup files once confirmed working
5. Update any remaining raw SQL queries in other files

## Troubleshooting

### If tests fail:

1. Check DATABASE_URL is correct
2. Ensure Prisma schema matches Laravel migrations
3. Run `npm run prisma:generate` again
4. Check type conversions (string IDs → parseInt)
5. Compare with `.old.ts` files to find differences

### If Prisma schema doesn't match database:

```bash
# Pull current schema from database
npm run prisma:pull

# This will update schema.prisma to match the actual database
```
