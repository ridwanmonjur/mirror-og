# Load Testing Guide: Simulating 50,000 Users

**Platform:** Driftwood Esports Tournament Platform
**Goal:** Test scalability and identify bottlenecks before reaching production scale

---

## Table of Contents
1. [PC Requirements](#pc-requirements)
2. [Tool Selection](#tool-selection)
3. [Recommended Tool: K6](#recommended-tool-k6)
4. [Test Scenarios for Esports Platform](#test-scenarios)
5. [Step-by-Step Guide](#step-by-step-guide)
6. [Interpreting Results](#interpreting-results)
7. [Distributed Testing](#distributed-testing)
8. [Alternative Tools](#alternative-tools)

---

## PC Requirements

### Minimum Requirements for 50,000 Virtual Users
- **CPU:** 8+ cores (16 threads recommended)
- **RAM:** 16GB minimum (32GB recommended)
- **Network:** Gigabit ethernet or strong WiFi
- **OS:** Windows 10/11, Linux, or macOS

### Reality Check
**Can a single PC simulate 50,000 users?**

**Yes, but with limitations:**
- **K6:** Can simulate 30,000-50,000+ VUs on a high-end PC (more with low think time)
- **JMeter:** Struggles beyond 5,000-10,000 VUs (Java heap memory limits)
- **Locust:** Can handle 10,000-20,000 VUs (Python GIL limitations)
- **Gatling:** Can handle 20,000-40,000 VUs (Scala/JVM based)

**Important:** Virtual users ≠ real users. You're testing concurrent connections, not simulating full browser behavior.

---

## Tool Selection

### Comparison Matrix

| Tool | Max VUs (Single PC) | Language | Ease of Use | Cost | Best For |
|------|---------------------|----------|-------------|------|----------|
| **K6** | 30,000-50,000+ | JavaScript | ⭐⭐⭐⭐⭐ | Free/Paid cloud | API testing, High VU count |
| **JMeter** | 5,000-10,000 | Java/GUI | ⭐⭐⭐ | Free | Comprehensive testing, GUI |
| **Locust** | 10,000-20,000 | Python | ⭐⭐⭐⭐ | Free | Python developers, Web UI |
| **Gatling** | 20,000-40,000 | Scala | ⭐⭐⭐ | Free/Paid | Enterprise, High performance |
| **Artillery** | 10,000-20,000 | Node.js | ⭐⭐⭐⭐ | Free/Paid | Quick tests, YAML config |

### Recommendation: K6

**Why K6?**
✅ Best performance for high VU counts
✅ JavaScript-based (familiar for web devs)
✅ Excellent documentation
✅ Built-in metrics and thresholds
✅ Can export to InfluxDB + Grafana
✅ Free and open-source
✅ Cloud option for distributed testing

---

## Recommended Tool: K6

### Installation

#### Windows
```bash
# Using Chocolatey
choco install k6

# Or download from GitHub
# https://github.com/grafana/k6/releases
```

#### macOS
```bash
brew install k6
```

#### Linux
```bash
# Debian/Ubuntu
sudo gpg -k
sudo gpg --no-default-keyring --keyring /usr/share/keyrings/k6-archive-keyring.gpg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys C5AD17C747E3415A3642D57D77C6C491D6AC1D69
echo "deb [signed-by=/usr/share/keyrings/k6-archive-keyring.gpg] https://dl.k6.io/deb stable main" | sudo tee /etc/apt/sources.list.d/k6.list
sudo apt-get update
sudo apt-get install k6
```

#### Docker (Any OS)
```bash
docker pull grafana/k6
```

---

## Test Scenarios

### Realistic Traffic Distribution for Esports Platform

Based on typical tournament platform usage:

| Scenario | % of Traffic | Description |
|----------|-------------|-------------|
| Browse Tournaments | 40% | Homepage, search, filter tournaments |
| View Tournament Details | 30% | Bracket, standings, schedule |
| User Authentication | 10% | Login, register, OAuth |
| View User Profiles | 10% | Player stats, match history |
| Submit Actions | 5% | Match results, team registration |
| Real-time Updates | 5% | WebSocket/Firebase connections |

### User Behavior Patterns

**Concurrent Users vs Total Users:**
- **50,000 concurrent users** = ~500,000-1,000,000 daily active users
- **Peak times:** Tournament finals, major events
- **Think time:** 5-30 seconds between actions (realistic)

---

## Step-by-Step Guide

### Test 1: Simple Smoke Test (10 Users)

Create `smoke-test.js`:

```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  vus: 10,
  duration: '30s',
  thresholds: {
    http_req_duration: ['p(95)<500'], // 95% of requests must complete within 500ms
    http_req_failed: ['rate<0.01'],   // Error rate must be less than 1%
  },
};

export default function () {
  // Browse homepage
  const homeRes = http.get('http://localhost:8000');
  check(homeRes, {
    'homepage status is 200': (r) => r.status === 200,
    'homepage loads in <500ms': (r) => r.timings.duration < 500,
  });

  sleep(2); // Think time between requests

  // Browse tournaments
  const tournamentsRes = http.get('http://localhost:8000/tournaments');
  check(tournamentsRes, {
    'tournaments status is 200': (r) => r.status === 200,
  });

  sleep(3);
}
```

**Run:**
```bash
k6 run smoke-test.js
```

---

### Test 2: Load Test (1,000 Users)

Create `load-test.js`:

```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '2m', target: 100 },    // Ramp up to 100 users
    { duration: '5m', target: 1000 },   // Ramp up to 1,000 users
    { duration: '10m', target: 1000 },  // Stay at 1,000 users
    { duration: '2m', target: 0 },      // Ramp down to 0 users
  ],
  thresholds: {
    http_req_duration: ['p(95)<1000', 'p(99)<2000'],
    http_req_failed: ['rate<0.01'],
  },
};

export default function () {
  // Scenario: Browse tournaments (40% of traffic)
  if (Math.random() < 0.4) {
    browseTournaments();
  }

  // Scenario: View tournament details (30% of traffic)
  if (Math.random() < 0.3) {
    viewTournamentDetails();
  }

  // Scenario: User authentication (10% of traffic)
  if (Math.random() < 0.1) {
    userLogin();
  }

  sleep(Math.random() * 10 + 5); // Random think time 5-15 seconds
}

function browseTournaments() {
  const res = http.get('http://localhost:8000/tournaments');
  check(res, {
    'tournaments loaded': (r) => r.status === 200,
  });
  sleep(2);

  // Search tournaments
  http.get('http://localhost:8000/tournaments?search=fortnite');
  sleep(1);
}

function viewTournamentDetails() {
  // Replace with actual tournament ID from your database
  const tournamentId = Math.floor(Math.random() * 100) + 1;

  const res = http.get(`http://localhost:8000/tournaments/${tournamentId}`);
  check(res, {
    'tournament details loaded': (r) => r.status === 200 || r.status === 404,
  });
  sleep(3);

  // View bracket
  http.get(`http://localhost:8000/tournaments/${tournamentId}/bracket`);
  sleep(2);
}

function userLogin() {
  const loginUrl = 'http://localhost:8000/login';

  // GET login page first
  http.get(loginUrl);
  sleep(1);

  // POST login credentials
  const payload = JSON.stringify({
    email: `testuser${Math.floor(Math.random() * 10000)}@test.com`,
    password: 'testpassword123',
  });

  const params = {
    headers: {
      'Content-Type': 'application/json',
    },
  };

  const res = http.post(loginUrl, payload, params);
  check(res, {
    'login attempted': (r) => r.status === 200 || r.status === 401,
  });
  sleep(2);
}
```

**Run:**
```bash
k6 run load-test.js
```

---

### Test 3: Stress Test (10,000 Users)

Create `stress-test.js`:

```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '5m', target: 1000 },    // Warm up
    { duration: '5m', target: 5000 },    // Ramp up
    { duration: '10m', target: 10000 },  // Peak load
    { duration: '10m', target: 10000 },  // Sustain peak
    { duration: '5m', target: 0 },       // Ramp down
  ],
  thresholds: {
    http_req_duration: ['p(95)<2000', 'p(99)<5000'],
    http_req_failed: ['rate<0.05'], // Allow 5% error rate
  },
};

export default function () {
  const rand = Math.random();

  if (rand < 0.4) {
    // 40% - Browse tournaments
    http.get('http://localhost:8000/tournaments');
  } else if (rand < 0.7) {
    // 30% - View tournament details
    const tournamentId = Math.floor(Math.random() * 100) + 1;
    http.get(`http://localhost:8000/tournaments/${tournamentId}`);
  } else if (rand < 0.8) {
    // 10% - User profiles
    const userId = Math.floor(Math.random() * 1000) + 1;
    http.get(`http://localhost:8000/users/${userId}`);
  } else {
    // 20% - Other pages
    http.get('http://localhost:8000');
  }

  sleep(Math.random() * 5 + 1); // 1-6 seconds think time
}
```

**Run:**
```bash
k6 run stress-test.js
```

---

### Test 4: Spike Test (50,000 Users)

Create `spike-test.js`:

```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '1m', target: 1000 },    // Normal load
    { duration: '30s', target: 50000 },  // Spike to 50k users
    { duration: '5m', target: 50000 },   // Sustain spike
    { duration: '1m', target: 1000 },    // Return to normal
    { duration: '30s', target: 0 },      // Ramp down
  ],
  thresholds: {
    http_req_duration: ['p(95)<5000'],   // More lenient during spike
    http_req_failed: ['rate<0.1'],       // Allow 10% errors during spike
  },
};

export default function () {
  // Simplified test for maximum VU count
  // Reduced think time to maximize throughput

  const responses = http.batch([
    ['GET', 'http://localhost:8000', null, { tags: { name: 'homepage' } }],
    ['GET', 'http://localhost:8000/tournaments', null, { tags: { name: 'tournaments' } }],
  ]);

  check(responses[0], {
    'homepage status 200': (r) => r.status === 200,
  });

  sleep(0.5); // Minimal think time for max load
}
```

**Run:**
```bash
k6 run spike-test.js --out json=results.json
```

**Warning:** This will stress your PC and application significantly!

---

### Test 5: Realistic Esports Tournament Scenario

Create `tournament-scenario.js`:

```javascript
import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Counter, Trend } from 'k6/metrics';

// Custom metrics
const tournamentViews = new Counter('tournament_views');
const bracketLoads = new Trend('bracket_load_time');

export const options = {
  scenarios: {
    // Spectators browsing (majority of users)
    spectators: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '5m', target: 30000 },
        { duration: '20m', target: 30000 },
        { duration: '5m', target: 0 },
      ],
      gracefulRampDown: '30s',
      exec: 'spectatorBehavior',
    },

    // Active participants (checking matches)
    participants: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '5m', target: 15000 },
        { duration: '20m', target: 15000 },
        { duration: '5m', target: 0 },
      ],
      gracefulRampDown: '30s',
      exec: 'participantBehavior',
    },

    // Organizers (admin actions)
    organizers: {
      executor: 'constant-vus',
      vus: 100,
      duration: '30m',
      exec: 'organizerBehavior',
    },
  },
  thresholds: {
    'http_req_duration{scenario:spectators}': ['p(95)<1000'],
    'http_req_duration{scenario:participants}': ['p(95)<2000'],
    'http_req_failed': ['rate<0.01'],
    'tournament_views': ['count>100000'],
  },
};

const BASE_URL = 'http://localhost:8000';

export function spectatorBehavior() {
  group('Spectator Flow', function () {
    // Browse homepage
    http.get(`${BASE_URL}`);
    sleep(3);

    // View tournaments list
    http.get(`${BASE_URL}/tournaments`);
    sleep(2);

    // View specific tournament
    const tournamentId = Math.floor(Math.random() * 50) + 1;
    const res = http.get(`${BASE_URL}/tournaments/${tournamentId}`);

    if (res.status === 200) {
      tournamentViews.add(1);
      sleep(5);

      // View bracket
      const bracketRes = http.get(`${BASE_URL}/tournaments/${tournamentId}/bracket`);
      bracketLoads.add(bracketRes.timings.duration);
      sleep(8);
    }

    // Random navigation
    if (Math.random() < 0.3) {
      http.get(`${BASE_URL}/tournaments?search=valorant`);
      sleep(2);
    }
  });

  sleep(Math.random() * 15 + 5); // Think time 5-20 seconds
}

export function participantBehavior() {
  group('Participant Flow', function () {
    // Login (simulate with cookie)
    const loginRes = http.get(`${BASE_URL}/login`);
    sleep(1);

    // View own tournaments
    http.get(`${BASE_URL}/my-tournaments`);
    sleep(3);

    // Check match schedule
    const tournamentId = Math.floor(Math.random() * 50) + 1;
    http.get(`${BASE_URL}/tournaments/${tournamentId}/matches`);
    sleep(4);

    // View bracket frequently
    http.get(`${BASE_URL}/tournaments/${tournamentId}/bracket`);
    sleep(5);

    // Check team page
    if (Math.random() < 0.5) {
      const teamId = Math.floor(Math.random() * 200) + 1;
      http.get(`${BASE_URL}/teams/${teamId}`);
      sleep(3);
    }
  });

  sleep(Math.random() * 10 + 3); // More frequent checks
}

export function organizerBehavior() {
  group('Organizer Flow', function () {
    // Admin dashboard
    http.get(`${BASE_URL}/admin`);
    sleep(5);

    // View tournament admin
    const tournamentId = Math.floor(Math.random() * 20) + 1;
    http.get(`${BASE_URL}/admin/tournaments/${tournamentId}`);
    sleep(4);

    // Check registrations
    http.get(`${BASE_URL}/admin/tournaments/${tournamentId}/registrations`);
    sleep(3);

    // Generate bracket (read-only test)
    http.get(`${BASE_URL}/admin/tournaments/${tournamentId}/bracket`);
    sleep(6);
  });

  sleep(Math.random() * 20 + 10); // Organizers check less frequently
}
```

**Run:**
```bash
k6 run tournament-scenario.js --out influxdb=http://localhost:8086/k6
```

---

## Interpreting Results

### Key Metrics to Monitor

#### K6 Output Metrics

```
✓ checks.........................: 98.50% ✓ 9850      ✗ 150
  data_received..................: 1.2 GB  40 MB/s
  data_sent......................: 89 MB   3.0 MB/s
  http_req_blocked...............: avg=1.2ms    min=1µs    med=3µs    max=500ms  p(95)=5ms
  http_req_connecting............: avg=500µs    min=0s     med=0s     max=200ms  p(95)=2ms
✓ http_req_duration..............: avg=350ms    min=50ms   med=250ms  max=5s     p(95)=800ms
  http_req_failed................: 1.50%  ✓ 150       ✗ 9850
  http_req_receiving.............: avg=5ms      min=100µs  med=2ms    max=500ms  p(95)=15ms
  http_req_sending...............: avg=2ms      min=50µs   med=500µs  max=200ms  p(95)=5ms
  http_req_tls_handshaking.......: avg=0s       min=0s     med=0s     max=0s     p(95)=0s
  http_req_waiting...............: avg=343ms    min=45ms   med=245ms  max=4.9s   p(95)=790ms
  http_reqs......................: 10000   333.33/s
  iteration_duration.............: avg=15s      min=10s    med=14s    max=30s    p(95)=20s
  iterations.....................: 500     16.67/s
  vus............................: 5000    min=0       max=5000
  vus_max........................: 5000    min=5000    max=5000
```

### What Each Metric Means

| Metric | Good | Warning | Critical | Action |
|--------|------|---------|----------|--------|
| **http_req_duration (p95)** | <500ms | 500-1000ms | >1000ms | Optimize queries, add caching |
| **http_req_duration (p99)** | <1000ms | 1000-2000ms | >2000ms | Check slow queries, add indexes |
| **http_req_failed** | <1% | 1-5% | >5% | Fix errors, check logs |
| **http_reqs (RPS)** | Target met | 80-99% of target | <80% | Scale infrastructure |
| **checks** | >99% | 95-99% | <95% | Investigate failures |
| **data_received** | Stable | Growing | Spiking | Check response sizes |

### Success Criteria for Driftwood

#### Early Stage (1,000 concurrent users)
- ✅ **p95 response time**: <500ms
- ✅ **p99 response time**: <1000ms
- ✅ **Error rate**: <0.5%
- ✅ **RPS**: 50-100 RPS sustained

#### Growth Stage (5,000 concurrent users)
- ✅ **p95 response time**: <1000ms
- ✅ **p99 response time**: <2000ms
- ✅ **Error rate**: <1%
- ✅ **RPS**: 200-500 RPS sustained

#### Start.gg Scale (50,000 concurrent users)
- ✅ **p95 response time**: <2000ms
- ✅ **p99 response time**: <5000ms
- ✅ **Error rate**: <2%
- ✅ **RPS**: 1000-3000 RPS sustained

---

## Monitoring During Tests

### Application Monitoring

#### 1. Laravel Telescope (Development)
```bash
# Enable Telescope
php artisan telescope:install
php artisan migrate
```

Visit `http://localhost:8000/telescope` during tests to see:
- Slow queries
- Request durations
- Exception tracking
- Queue monitoring

#### 2. Database Monitoring

```bash
# MySQL slow query log
# Add to my.cnf
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 0.5

# Monitor in real-time
tail -f /var/log/mysql/slow-query.log
```

#### 3. Redis Monitoring

```bash
# Connect to Redis CLI
redis-cli

# Monitor commands in real-time
MONITOR

# Check memory usage
INFO memory

# Check connected clients
CLIENT LIST
```

#### 4. System Resources

```bash
# CPU and Memory (Linux/macOS)
htop

# Network usage
iftop

# Disk I/O
iotop

# Windows: Use Task Manager or Resource Monitor
```

### K6 + InfluxDB + Grafana (Recommended for Serious Testing)

#### Setup (Docker)

Create `docker-compose.monitoring.yml`:

```yaml
version: '3.8'

services:
  influxdb:
    image: influxdb:1.8
    ports:
      - "8086:8086"
    environment:
      - INFLUXDB_DB=k6
      - INFLUXDB_HTTP_AUTH_ENABLED=false
    volumes:
      - influxdb-data:/var/lib/influxdb

  grafana:
    image: grafana/grafana:latest
    ports:
      - "3000:3000"
    environment:
      - GF_AUTH_ANONYMOUS_ENABLED=true
      - GF_AUTH_ANONYMOUS_ORG_ROLE=Admin
    volumes:
      - grafana-data:/var/lib/grafana
    depends_on:
      - influxdb

volumes:
  influxdb-data:
  grafana-data:
```

**Start monitoring stack:**
```bash
docker-compose -f docker-compose.monitoring.yml up -d
```

**Run K6 with output to InfluxDB:**
```bash
k6 run --out influxdb=http://localhost:8086/k6 tournament-scenario.js
```

**View in Grafana:**
1. Open `http://localhost:3000`
2. Add InfluxDB data source (URL: `http://influxdb:8086`, Database: `k6`)
3. Import K6 dashboard (ID: 2587)
4. View real-time metrics

---

## Distributed Testing

### When Single PC Isn't Enough

If your PC can't handle 50,000 VUs, use distributed testing:

### Option 1: K6 Cloud (Easiest)

```bash
# Sign up at https://k6.io/cloud
k6 login cloud

# Run test in cloud
k6 cloud tournament-scenario.js
```

**Pricing:** $49-299/month (includes cloud execution + monitoring)

### Option 2: Multiple Local Machines

Run same test on multiple PCs:

**Machine 1:**
```bash
k6 run --vus 15000 --duration 30m tournament-scenario.js
```

**Machine 2:**
```bash
k6 run --vus 15000 --duration 30m tournament-scenario.js
```

**Machine 3:**
```bash
k6 run --vus 20000 --duration 30m tournament-scenario.js
```

**Total:** 50,000 VUs distributed

### Option 3: Kubernetes (Advanced)

Use `k6-operator` to run distributed K6 tests on Kubernetes:

```bash
# Install k6-operator
kubectl apply -f https://github.com/grafana/k6-operator/releases/latest/download/bundle.yaml

# Create test ConfigMap
kubectl create configmap test-script --from-file tournament-scenario.js

# Run distributed test
kubectl apply -f - <<EOF
apiVersion: k6.io/v1alpha1
kind: K6
metadata:
  name: k6-sample
spec:
  parallelism: 10
  script:
    configMap:
      name: test-script
      file: tournament-scenario.js
EOF
```

### Option 4: AWS/GCP (Cloud VMs)

Spin up 10x `t3.large` instances (2 vCPU, 8GB RAM each):

```bash
# Each instance runs 5,000 VUs
k6 run --vus 5000 --duration 30m tournament-scenario.js
```

**Cost:** ~$0.08/hour × 10 instances × 0.5 hours = **$0.40 per test run**

---

## Alternative Tools

### Apache JMeter

**Pros:** GUI, extensive plugins, comprehensive testing
**Cons:** Java heap limits, lower max VUs

#### Installation
```bash
# Download from https://jmeter.apache.org/download_jmeter.cgi
# Extract and run
bin/jmeter.bat  # Windows
bin/jmeter.sh   # Linux/macOS
```

#### Running Headless (Higher Performance)
```bash
jmeter -n -t test-plan.jmx -l results.jtl -e -o report/
```

**Max VUs:** ~5,000-10,000 per machine

---

### Locust

**Pros:** Python-based, Web UI, easy to script
**Cons:** Python GIL limits performance

#### Installation
```bash
pip install locust
```

#### Create `locustfile.py`:
```python
from locust import HttpUser, task, between

class DriftwoodUser(HttpUser):
    wait_time = between(1, 5)

    @task(4)  # 40% of requests
    def browse_tournaments(self):
        self.client.get("/tournaments")

    @task(3)  # 30% of requests
    def view_tournament(self):
        tournament_id = random.randint(1, 100)
        self.client.get(f"/tournaments/{tournament_id}")

    @task(1)  # 10% of requests
    def login(self):
        self.client.get("/login")
```

#### Run:
```bash
# Web UI
locust -f locustfile.py --host http://localhost:8000

# Headless
locust -f locustfile.py --host http://localhost:8000 --users 10000 --spawn-rate 100 --run-time 30m --headless
```

**Max VUs:** ~10,000-20,000 per machine

---

### Gatling

**Pros:** High performance, beautiful reports
**Cons:** Scala-based (learning curve)

#### Installation
```bash
# Download from https://gatling.io/open-source/
# Or use Maven/SBT for project setup
```

#### Create scenario in `scala`:
```scala
import io.gatling.core.Predef._
import io.gatling.http.Predef._
import scala.concurrent.duration._

class DriftwoodSimulation extends Simulation {
  val httpProtocol = http.baseUrl("http://localhost:8000")

  val scn = scenario("Tournament Scenario")
    .exec(http("Browse Tournaments").get("/tournaments"))
    .pause(2)
    .exec(http("View Tournament").get("/tournaments/1"))
    .pause(3)

  setUp(
    scn.inject(
      rampUsers(50000).during(10.minutes)
    ).protocols(httpProtocol)
  )
}
```

**Max VUs:** ~20,000-40,000 per machine

---

## Common Bottlenecks & Solutions

### Problem: Database Connection Limit Reached

**Symptoms:**
```
SQLSTATE[HY000] [1040] Too many connections
```

**Solutions:**
1. Increase MySQL `max_connections`:
```sql
SET GLOBAL max_connections = 500;
```

2. Use connection pooling (Laravel default is good, but check):
```php
// config/database.php
'mysql' => [
    'pool' => [
        'min_connections' => 10,
        'max_connections' => 100,
    ],
],
```

3. Use read replicas to distribute load

---

### Problem: High Response Times

**Symptoms:** p95 > 2000ms, p99 > 5000ms

**Solutions:**
1. Enable Laravel Octane (3-5x performance boost)
2. Add database indexes on frequently queried columns
3. Implement Redis caching for tournament data
4. Use eager loading to prevent N+1 queries
5. Optimize database queries (use `EXPLAIN`)

---

### Problem: Memory Exhaustion

**Symptoms:**
```
Allowed memory size of 134217728 bytes exhausted
```

**Solutions:**
1. Increase PHP memory limit:
```ini
memory_limit = 512M
```

2. Use chunking for large datasets:
```php
Tournament::chunk(100, function ($tournaments) {
    // Process tournaments
});
```

3. Clear Laravel cache between iterations

---

### Problem: Redis Connection Issues

**Symptoms:**
```
Connection refused [tcp://127.0.0.1:6379]
```

**Solutions:**
1. Increase Redis max clients:
```
# redis.conf
maxclients 10000
```

2. Use connection pooling
3. Monitor Redis memory usage and eviction policy

---

### Problem: Rate Limiting Triggered

**Symptoms:** HTTP 429 Too Many Requests

**Solutions:**
1. Adjust Laravel rate limiting:
```php
// app/Http/Kernel.php
'api' => [
    'throttle:1000,1', // 1000 requests per minute
],
```

2. Disable rate limiting for load tests (dev only)
3. Use distributed rate limiting with Redis

---

## Test Execution Checklist

### Before Running Tests

- [ ] Backup production data (if testing on prod-like environment)
- [ ] Notify team about load test schedule
- [ ] Disable email sending (prevent spam)
- [ ] Disable external API calls (Stripe test mode, etc.)
- [ ] Enable slow query logging
- [ ] Set up monitoring dashboards
- [ ] Clear Laravel cache
- [ ] Restart services (PHP-FPM, MySQL, Redis)
- [ ] Check disk space (logs can grow)
- [ ] Disable rate limiting (for realistic test)

### During Tests

- [ ] Monitor system resources (CPU, RAM, disk I/O)
- [ ] Monitor database connections
- [ ] Monitor Redis memory usage
- [ ] Watch error logs in real-time
- [ ] Check response times in K6 output
- [ ] Verify application functionality (spot check)

### After Tests

- [ ] Analyze K6 report
- [ ] Review slow query log
- [ ] Check Laravel Telescope for issues
- [ ] Review error logs
- [ ] Document bottlenecks found
- [ ] Create action items for optimization
- [ ] Clear test data if needed
- [ ] Re-enable rate limiting
- [ ] Archive test results

---

## Sample Test Schedule

### Week 1: Baseline Testing
- **Day 1:** Smoke test (10 users) - Verify setup
- **Day 2:** Load test (100 users) - Identify obvious issues
- **Day 3:** Load test (500 users) - Stress current infrastructure
- **Day 4:** Fix identified issues
- **Day 5:** Retest at 500 users - Verify fixes

### Week 2: Growth Stage Testing
- **Day 1:** Load test (1,000 users) - Growth target
- **Day 2:** Load test (2,500 users) - 2.5x growth
- **Day 3:** Load test (5,000 users) - 5x growth
- **Day 4:** Fix bottlenecks
- **Day 5:** Retest at 5,000 users

### Week 3: Scale Testing
- **Day 1:** Stress test (10,000 users) - 10x growth
- **Day 2:** Stress test (25,000 users) - 25x growth
- **Day 3:** Spike test (50,000 users) - Start.gg scale
- **Day 4:** Analyze results, plan infrastructure
- **Day 5:** Document findings and recommendations

---

## Expected Results

### Baseline (No Optimization)

With default Laravel configuration:
- **Max sustainable VUs:** 500-1,000
- **Max RPS:** 50-100
- **p95 response time:** 500-1000ms
- **Bottleneck:** Database connections, no caching

### After Basic Optimization

With Laravel Octane + Redis caching + indexes:
- **Max sustainable VUs:** 5,000-10,000
- **Max RPS:** 500-1,000
- **p95 response time:** 200-500ms
- **Bottleneck:** Single database server

### After Advanced Optimization

With read replicas + aggressive caching + CDN:
- **Max sustainable VUs:** 25,000-50,000
- **Max RPS:** 2,000-5,000
- **p95 response time:** 200-800ms
- **Bottleneck:** Database writes, complex queries

---

## Real-World Testing Tips

### 1. **Start Small, Scale Gradually**
Don't jump straight to 50,000 users. Find your breaking point incrementally.

### 2. **Test During Off-Peak Hours**
If testing production-like environment, test when real users are minimal.

### 3. **Use Realistic Data**
Seed database with realistic tournament, user, and match data (thousands of records).

### 4. **Simulate Realistic Behavior**
Include think time (5-30 seconds) between requests - real users don't spam requests.

### 5. **Test Different Scenarios**
- Normal load (consistent traffic)
- Spike load (sudden tournament registration)
- Stress load (major tournament finals)
- Soak load (24-hour sustained traffic)

### 6. **Monitor Everything**
Application, database, cache, network, disk I/O - bottlenecks appear everywhere.

### 7. **Document Everything**
Record results, observations, and optimizations - you'll forget details quickly.

### 8. **Test After Every Major Change**
Regression testing ensures optimizations don't break functionality.

---

## Next Steps

1. **Run Smoke Test (10 users)** - Verify test setup works
2. **Run Load Test (1,000 users)** - Establish baseline performance
3. **Optimize Based on Results** - Fix bottlenecks discovered
4. **Gradually Increase Load** - Test at 5K, 10K, 25K, 50K users
5. **Compare with Requirements** - Does it meet start.gg scale needs?
6. **Plan Infrastructure Scaling** - Based on test results, choose cloud vs cloudless
7. **Implement Monitoring** - Set up Grafana/InfluxDB for production monitoring
8. **Schedule Regular Tests** - Quarterly load tests to catch regressions

---

## Conclusion

**Can you simulate 50,000 users on your PC?**
- ✅ **Yes with K6** - Modern PC can handle 30,000-50,000+ VUs
- ⚠️ **With limitations** - Reduced think time, simplified scenarios
- ✅ **Better option** - Distributed testing with multiple machines or cloud

**Most important takeaway:**
> Don't test 50,000 users until you've optimized for 1,000.

Find bottlenecks early, fix them, then scale testing gradually. This approach is faster, cheaper, and more informative than jumping straight to massive load tests.

---

## Quick Start Commands

```bash
# 1. Install K6
choco install k6  # Windows
brew install k6   # macOS

# 2. Create simple test
cat > simple-test.js << 'EOF'
import http from 'k6/http';
import { sleep } from 'k6';

export const options = {
  vus: 1000,
  duration: '5m',
};

export default function () {
  http.get('http://localhost:8000');
  sleep(1);
}
EOF

# 3. Run test
k6 run simple-test.js

# 4. Analyze results
# Check p95, p99 response times and error rate
```

**Good luck with your load testing!** 🚀

---

**Last Updated:** December 11, 2025
