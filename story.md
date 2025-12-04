# Interview Stories - Driftwood Esports Platform

## Story 1: Backend - Payment System Race Condition Crisis

### The Situation
During development of the tournament registration system, we discovered a critical bug where users could double-pay for event entries. When a user clicked "Register" and immediately refreshed the page, the system would create duplicate payment intents, charging them twice. This was discovered during beta testing when a user complained about being charged $40 instead of $20.

### The Problem
The issue had multiple layers:
1. **No idempotency protection** - Each request created a new Stripe payment intent
2. **Race condition** - Payment confirmation and database updates weren't atomic
3. **Wallet + Stripe hybrid** - Our system allowed splitting payments between wallet balance and credit card, making the logic even more complex
4. **Refund complexity** - If registration failed halfway, we needed to refund both wallet and Stripe portions correctly

### My Approach

**Step 1: Investigated the Root Cause**
```php
// Original problematic code
$paymentIntent = $this->stripeClient->createPaymentIntent([
    'amount' => $request->paymentAmount * 100,
    'customer' => $customer->id,
]);
// Problem: No idempotency key!
```

I traced through the checkout flow and found we weren't using idempotency keys, so Stripe treated each request as unique.

**Step 2: Research & Design**
- Read Stripe's best practices documentation on idempotent requests
- Analyzed our wallet deduction logic happening in separate transaction
- Drew out state diagram of all possible payment states
- Considered edge cases: partial payments, timeout scenarios, browser crashes

**Step 3: Implementation**

Created **idempotency key** based on user + amount + purpose:
```php
$idempotencyKey = hash('sha256', $user->id . '_' . $request->paymentAmount . '_'. $request->purpose);

$paymentIntentStripeBody = [
    'amount' => +$request->paymentAmount * 100,
    'metadata' => $request->metadata,
];

$paymentIntent = $this->stripeClient->createPaymentIntent(
    $paymentIntentStripeBody,
    $idempotencyKey  // Stripe now recognizes duplicates!
);
```

Added **database transaction** wrapping wallet + payment:
```php
DB::beginTransaction();
try {
    // Deduct from wallet first
    $userWallet->update([
        'usable_balance' => $walletAmount - $request->wallet_to_decrement,
        'current_balance' => $currentAmount - $request->wallet_to_decrement,
    ]);

    // Create payment record
    ParticipantPayment::create([...]);

    // Only commit if both succeed
    DB::commit();
} catch (Exception $e) {
    DB::rollBack();
    // Rollback Stripe if needed
}
```

Implemented **payment intent tracking**:
```php
PaymentIntent::insert([
    'user_id' => $user->id,
    'payment_intent_id' => $paymentIntent->id,
    'amount' => $request->paymentAmount,
    'status' => $paymentIntent->status,
]);
```

**Step 4: Testing Edge Cases**
- Tested double-click scenarios
- Simulated network timeouts
- Verified wallet refunds on failure
- Checked Stripe dashboard for duplicate prevention

### The Result
- **Zero duplicate charges** after deployment
- Reduced payment-related support tickets by 100%
- Built confidence in our payment system's reliability
- Created reusable pattern for all payment flows in the application

### What I Learned
1. **Financial systems require paranoid programming** - Always assume the worst
2. **Idempotency is critical** for any payment operation
3. **Database transactions** are essential for maintaining data consistency
4. **Testing edge cases** matters more than happy path testing
5. **Reading documentation** thoroughly prevented a production disaster

---

## Story 2: Frontend - Real-Time Tournament Bracket Synchronization

### The Situation
Our platform needed real-time bracket updates where multiple users (2 teams, 1 organizer, spectators) could view match results updating live. The challenge: When Team A reports "We won Game 1," Team B should see this update instantly in their browser, and the organizer should be able to override if there's a dispute.

### The Problem
The complexity came from multiple sources:
1. **Conflicting state** - Team A says they won, Team B hasn't reported yet
2. **Organizer overrides** - Must take precedence over team reports
3. **Dispute system** - Teams can dispute results with evidence (images/videos)
4. **Multiple games per match** - A match has 3-5 games, each with separate winners
5. **Browser state sync** - JavaScript state must match Firebase real-time updates
6. **Visual updates** - UI must reflect changes without page refresh

### My Approach

**Step 1: Designed State Architecture**

Created a **centralized state store** for bracket data:
```javascript
let reportStore = {
  list: {
    organizerWinners: Array(totalMatches).fill(null),  // Organizer's call
    randomWinners: Array(totalMatches).fill(null),     // Fallback
    defaultWinners: Array(totalMatches).fill(null),    // Auto-assign
    disputeResolved: Array(totalMatches).fill(null),   // After dispute
    realWinners: Array(totalMatches).fill(null),       // Final truth
    matchStatus: Array(totalMatches).fill('UPCOMING'),
    teams: [
      { winners: Array(totalMatches).fill(null) },  // Team 1 reports
      { winners: Array(totalMatches).fill(null) }   // Team 2 reports
    ],
  },
};
```

This design handles **winner hierarchy**: Organizer > Dispute Resolution > Team Agreement > Default

**Step 2: Firebase Real-Time Listeners**

Implemented **bidirectional sync** between Firebase and local state:
```javascript
function updateReportFromFirestore(baseReport, sourceData, matchNumber) {
  const score = sourceData.score || [0, 0];

  return {
    ...baseReport,
    organizerWinners: sourceData.organizerWinners[matchNumber],
    matchStatus: sourceData.matchStatus[matchNumber],
    realWinners: sourceData.realWinners[matchNumber],
    teams: [
      {
        ...baseReport.teams[0],
        score: score[0],
        winners: sourceData.team1Winners[matchNumber]
      },
      {
        ...baseReport.teams[1],
        score: score[1],
        winners: sourceData.team2Winners[matchNumber]
      }
    ]
  };
}
```

Set up **Firebase snapshot listener**:
```javascript
subscribeToCurrentReportDisputesSnapshot = onSnapshot(
  doc(db, "reports", reportId),
  (snapshot) => {
    if (snapshot.exists()) {
      const data = snapshot.data();
      reportStore.updateListFromFirestore(data);
      updateUIFromState();  // Re-render UI
    }
  }
);
```

**Step 3: Visual Score Indicators**

Created **dot-based score system** showing game-by-game results:
```javascript
function updateCurrentReportDots(reportStore, totalMatches) {
  let dottedScoreContainer = document.querySelectorAll('#reportModal .dotted-score-container');
  dottedScoreContainer.forEach((element, index) => {
    element.querySelectorAll('.dotted-score')?.forEach((dottedElement, dottedElementIndex) => {
      if (dottedElementIndex <= totalMatches - 1) {
        if (reportStore.realWinners[dottedElementIndex]) {
          if (reportStore.realWinners[dottedElementIndex] == index) {
            dottedElement.classList.remove('bg-secondary', 'bg-red', 'd-none');
            dottedElement.classList.add("bg-success");  // Green for win
          } else {
            dottedElement.classList.remove('bg-secondary', 'bg-success', 'd-none');
            dottedElement.classList.add("bg-red");  // Red for loss
          }
        }
      }
    });
  });
}
```

**Step 4: Dispute System with Evidence**

Built **dispute workflow** allowing file uploads:
```javascript
function createDisputeDto(newFormObject, files) {
  const disputeDto = {
    report_id: newFormObject.report_id,
    match_number: newFormObject.match_number,
    dispute_userId: newFormObject.dispute_userId,
    dispute_teamId: newFormObject.dispute_teamId,
    dispute_reason: newFormObject.dispute_reason,
    dispute_description: newFormObject.dispute_description || null,
    dispute_image_videos: files,  // Evidence files

    // Resolution fields (filled by organizer)
    response_explanation: null,
    resolution_winner: null,
    resolution_resolved_by: null,

    created_at: serverTimestamp(),
  };

  return disputeDto;
}
```

**Step 5: Countdown Timers**

Added **deadline enforcement** with live countdowns:
```javascript
function diffDateWithNow(targetDate) {
  targetDate = new Date(targetDate);
  const now = new Date();

  if (targetDate > now) {
    const diffMs = targetDate - now;
    const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

    if (days > 0) return `${days}d ${hours}h`;
    if (hours > 0) return `${hours}h ${minutes}m`;
    return `${minutes}m`;
  }
  return 'Time over';
}
```

### The Result
- **Real-time updates** across all connected users with <500ms latency
- **Zero data loss** during concurrent edits from multiple teams
- **Intuitive UI** with visual feedback (color-coded dots, countdowns)
- **Dispute resolution** system handling 100+ disputes during beta
- **Scalable architecture** supporting 50+ concurrent matches

### What I Learned
1. **State management** is critical for complex real-time applications
2. **Firebase listeners** provide elegant solution for real-time sync
3. **Visual feedback** matters - users need immediate confirmation
4. **Separation of concerns** - Keep data layer separate from UI layer
5. **Edge case handling** - Disconnections, conflicts, and race conditions must be planned for

---

## Story 3: Hosting - Firebase Infrastructure Automation with Terraform

### The Situation
Our application heavily relied on Firebase for real-time features (chat, match reporting, analytics). Initially, we manually configured Firebase through the web console for each environment (dev, staging, production). This was error-prone - I once accidentally deleted the staging Firestore database while trying to update security rules, losing 2 weeks of test data.

### The Problem
Manual Firebase setup had serious issues:
1. **Configuration drift** - Dev, staging, and prod had different settings
2. **No version control** - Security rules weren't tracked in Git
3. **Onboarding friction** - New developers took 2 hours to setup Firebase
4. **Human errors** - Easy to misconfigure or delete critical resources
5. **Environment variables** - 12+ Firebase config values manually copied between .env files
6. **Inconsistent deploys** - Forgot to update security rules after code changes

### My Approach

**Step 1: Research & Planning**

Researched infrastructure-as-code options:
- Terraform (chosen for broad GCP support)
- Firebase CLI (limited to specific operations)
- Manual scripts (too brittle)

Identified what needed automation:
- Firebase project creation/configuration
- Firestore database + security rules
- Firebase web app credentials
- OAuth provider setup
- Initial Firestore collections
- Environment variable generation

**Step 2: Terraform Configuration**

Created **Firebase project resource**:
```hcl
resource "google_firebase_project" "default" {
  provider = google-beta
  project  = var.project_id
}

resource "google_firestore_database" "database" {
  project     = var.project_id
  name        = "(default)"
  location_id = var.region
  type        = "FIRESTORE_NATIVE"

  depends_on = [google_firebase_project.default]
}
```

Automated **security rules deployment**:
```hcl
resource "google_firebaserules_ruleset" "firestore" {
  project = var.project_id
  source {
    files {
      name    = "firestore.rules"
      content = file("${path.module}/firestore.rules")
    }
  }

  depends_on = [google_firestore_database.database]
}
```

Generated **web app configuration**:
```hcl
resource "google_firebase_web_app" "app" {
  provider     = google-beta
  project      = var.project_id
  display_name = var.app_name

  depends_on = [google_firebase_project.default]
}

# Auto-generate .env variables
resource "local_file" "env_firebase" {
  filename = "${path.module}/../../.env.firebase"
  content  = <<-EOT
    FIREBASE_API_KEY=${google_firebase_web_app.app.api_key}
    VITE_FIREBASE_API_KEY=${google_firebase_web_app.app.api_key}
    VITE_AUTH_DOMAIN=${var.project_id}.firebaseapp.com
    VITE_PROJECT_ID=${var.project_id}
    VITE_STORAGE_BUCKET=${var.project_id}.appspot.com
    VITE_APP_ID=${google_firebase_web_app.app.app_id}
  EOT
}
```

**Step 3: Composer Integration**

Made Terraform accessible to PHP developers via Composer:
```json
{
  "scripts": {
    "tf:dev:plan": "cd terraform/dev && terraform plan",
    "tf:dev:apply": "cd terraform/dev && terraform apply -auto-approve",
    "tf:staging:plan": "cd terraform/staging && terraform plan",
    "tf:staging:apply": "cd terraform/staging && terraform apply",
    "tf:prod:plan": "cd terraform/production && terraform plan",
    "tf:prod:apply": "cd terraform/production && terraform apply",
    "tf:init": "cd terraform && terraform init"
  }
}
```

Now developers could run: `composer tf:dev:apply` instead of learning Terraform commands!

**Step 4: Firestore Initialization**

Automated **initial collection creation**:
```hcl
resource "google_firestore_document" "room_init" {
  project     = var.project_id
  database    = "(default)"
  collection  = "room"
  document_id = "_init"

  fields = jsonencode({
    initialized = { booleanValue = true }
    timestamp   = { timestampValue = timestamp() }
  })

  depends_on = [google_firestore_database.database]
}

# Create analytics collections
resource "google_firestore_document" "analytics_events" {
  project     = var.project_id
  database    = "(default)"
  collection  = "analytics-events"
  document_id = "_init"

  fields = jsonencode({
    initialized = { booleanValue = true }
  })
}
```

**Step 5: Multi-Environment Strategy**

Created **separate Terraform workspaces**:
```
terraform/
├── modules/
│   └── firebase/
│       ├── main.tf
│       ├── variables.tf
│       └── outputs.tf
├── dev/
│   ├── main.tf
│   └── terraform.tfvars
├── staging/
│   ├── main.tf
│   └── terraform.tfvars
└── production/
    ├── main.tf
    └── terraform.tfvars
```

Each environment has different settings:
```hcl
# dev/terraform.tfvars
project_id = "driftwood-dev"
region     = "us-central1"
app_name   = "Driftwood Dev"

# production/terraform.tfvars
project_id = "driftwood-prod"
region     = "asia-southeast1"  # Closer to users
app_name   = "Driftwood"
```

**Step 6: Documentation & Rollout**

Created comprehensive CLAUDE.md documentation:
```markdown
## Terraform Firebase Infrastructure

### What Terraform Creates:
- Firebase project setup
- Firestore database with security rules
- Firebase web app configuration
- Google OAuth integration
- Initial Firestore collections

### Composer Commands:
composer tf:dev:plan    # Preview changes
composer tf:dev:apply   # Apply changes
```

Migrated existing environments one by one:
1. **Exported** existing Firebase config manually
2. **Imported** into Terraform state: `terraform import google_firebase_project.default driftwood-dev`
3. **Verified** no changes: `terraform plan` showed 0 changes
4. **Documented** rollback procedure

### The Result
- **10 minutes** to setup complete Firebase environment (from 2 hours)
- **Zero configuration errors** after Terraform adoption
- **Version controlled** infrastructure - all changes in Git
- **Consistent environments** - dev/staging/prod match perfectly
- **Automated onboarding** - New devs run `composer tf:dev:apply` and they're ready
- **Safe deploys** - `terraform plan` shows changes before applying
- **Recovery capability** - Can rebuild environment from code in 10 minutes

### What I Learned
1. **Infrastructure as Code** is a game-changer for team productivity
2. **Terraform state management** requires careful planning
3. **Abstraction matters** - Hiding Terraform behind Composer made adoption easy
4. **Documentation is critical** - CLAUDE.md made the system accessible
5. **Gradual migration** worked better than big-bang rewrite
6. **Multi-environment strategy** prevents "works on my machine" issues

---

## Common Themes Across All Stories

### Problem-Solving Approach
1. **Understand the root cause** - Don't just fix symptoms
2. **Research best practices** - Stand on shoulders of giants
3. **Design before coding** - Draw diagrams, write pseudocode
4. **Test edge cases** - Murphy's law applies to code
5. **Document decisions** - Future you will thank present you

### Technical Growth
- **Backend**: Financial systems, transactions, race conditions
- **Frontend**: State management, real-time sync, user experience
- **DevOps**: Infrastructure automation, multi-environment management

### Soft Skills
- **Communication** - Explained technical decisions to non-technical stakeholders
- **Collaboration** - Worked with designers, product managers, and other developers
- **Ownership** - Took responsibility for entire feature lifecycle
- **Learning** - Quickly mastered new technologies (Stripe, Firebase, Terraform)

---

*These stories demonstrate junior-to-mid-level engineering skills: taking ownership of complex features, making architectural decisions, and delivering production-ready solutions.*
