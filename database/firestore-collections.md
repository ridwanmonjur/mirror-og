# Firestore Collections for Analytics

## Collection Structure

### 1. `analytics_global_counts`
Stores global analytics counters across all users.

**Document ID:** `global`

**Schema:**
```json
{
  "eventClickCount": 0,
  "tierCounts": {
    "Bronze": 0,
    "Silver": 0,
    "Gold": 0,
    "Diamond": 0
  },
  "typeCounts": {
    "Tournament": 0,
    "Scrim": 0,
    "Practice": 0
  },
  "esportCounts": {
    "Valorant": 0,
    "Counter-Strike": 0,
    "League of Legends": 0
  },
  "locationCounts": {
    "Online": 0,
    "New York": 0,
    "Los Angeles": 0
  },
  "lastUpdated": "2024-01-01T00:00:00Z"
}
```

### 2. `analytics_user_activity`
Tracks individual user activity per day.

**Document ID:** `{userId}_{date}` (e.g., `123_2024-01-15`)

**Schema:**
```json
{
  "userId": 123,
  "date": "2024-01-15",
  "clickCount": 5,
  "lastActivity": "2024-01-15T14:30:00Z",
  "events": {
    "456": 3,
    "789": 2
  }
}
```

### 3. `analytics_daily_stats`
Aggregated daily statistics.

**Document ID:** `{date}` (e.g., `2024-01-15`)

**Schema:**
```json
{
  "date": "2024-01-15",
  "totalClicks": 1250,
  "tierClicks": {
    "Bronze": 300,
    "Silver": 400,
    "Gold": 350,
    "Diamond": 200
  },
  "typeClicks": {
    "Tournament": 800,
    "Scrim": 300,
    "Practice": 150
  },
  "esportClicks": {
    "Valorant": 500,
    "Counter-Strike": 400,
    "League of Legends": 350
  },
  "locationClicks": {
    "Online": 900,
    "New York": 200,
    "Los Angeles": 150
  },
  "lastUpdated": "2024-01-15T23:59:59Z"
}
```

## Security Rules

Add these rules to your Firestore security rules:

```javascript
// Allow read access to analytics data for authenticated users
match /analytics_global_counts/{document} {
  allow read: if request.auth != null;
  allow write: if request.auth != null;
}

match /analytics_user_activity/{document} {
  allow read, write: if request.auth != null;
}

match /analytics_daily_stats/{document} {
  allow read: if request.auth != null;
  allow write: if request.auth != null;
}
```

## Initialization

To initialize the global counts document in Firestore:

```javascript
// Run this once in your browser console or admin script
const db = getFirestore();
await setDoc(doc(db, 'analytics_global_counts', 'global'), {
  eventClickCount: 0,
  tierCounts: {},
  typeCounts: {},
  esportCounts: {},
  locationCounts: {},
  lastUpdated: serverTimestamp()
});
```

## Usage Examples

```javascript
// Get current global counts
const counts = await getEventCounts();

// Get analytics summary (global + daily)
const summary = await getAnalyticsSummary();

// Reset all counts (admin only)
await resetEventCounts();

// Get specific tier count
const goldCount = getTierCount('Gold');
```

## Performance Considerations

- Global counters use Firestore's `increment()` for atomic updates
- Daily stats are partitioned by date to avoid hot spotting
- User activity is tracked separately to enable user-specific analytics
- Local caching reduces Firestore read operations
- Fallback to localStorage if Firestore is unavailable

## Data Retention

- Global counts: Permanent
- User activity: Suggest 90-day retention
- Daily stats: Suggest 1-year retention

## Migration from localStorage

The system automatically falls back to localStorage if Firestore is unavailable, ensuring continuity during migration or outages.


without any database letter i want to query gtag('event', 'view_item', { currency: 'USD', value: 15.25, items: [{ item_id: 'SKU_12345', item_name: 'Product Name', category: 'Category', quantity: 1, price: 15.25 }] }); // Add to cart gtag('event', 'add_to_cart', { currency: 'USD', value: 15.25, items: [/* item details */] }); how many category 'Catergories' and 'view_items' occurred

what apis will i use?