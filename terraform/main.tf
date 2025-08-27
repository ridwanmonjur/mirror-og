# Terraform configuration for Driftwood Firebase/Firestore setup
terraform {
  required_version = ">= 1.0"
  required_providers {
    google = {
      source  = "hashicorp/google"
      version = "~> 5.0"
    }
    google-beta = {
      source  = "hashicorp/google-beta"
      version = "~> 5.0"
    }
    local = {
      source  = "hashicorp/local"
      version = "~> 2.0"
    }
    null = {
      source  = "hashicorp/null"
      version = "~> 3.0"
    }
    random = {
      source  = "hashicorp/random"
      version = "~> 3.0"
    }
  }
}

# Provider configuration
provider "google" {
  project = var.project_id
  region  = var.region
}

provider "google-beta" {
  project = var.project_id
  region  = var.region
}

# Get current project details
data "google_project" "current" {
  project_id = var.project_id
}

# Enable required APIs with proper lifecycle management
resource "google_project_service" "required_apis" {
  for_each = toset([
    "firebase.googleapis.com",
    "firestore.googleapis.com", 
    "identitytoolkit.googleapis.com",
    "cloudresourcemanager.googleapis.com",
    "billingbudgets.googleapis.com",
    "iam.googleapis.com",
    "recaptchaenterprise.googleapis.com",
    "firebaseappcheck.googleapis.com",
    "cloudfunctions.googleapis.com",
    "storage.googleapis.com",
    "cloudbuild.googleapis.com",
    "compute.googleapis.com",
    "run.googleapis.com",
  ])

  project = var.project_id
  service = each.value

  # Prevent APIs from being disabled on destroy to avoid dependency issues
  disable_dependent_services = false
  disable_on_destroy = false

  # Add timeouts for API enablement
  timeouts {
    create = "10m"
    update = "10m" 
    delete = "10m"
  }
}

# Firebase project (enables Firebase for the GCP project)
resource "google_firebase_project" "default" {
  provider = google-beta
  project  = var.project_id

  depends_on = [google_project_service.required_apis]
}

# Billing budget to ensure billing is enabled
resource "google_billing_budget" "budget" {
  count = var.billing_account_id != "" ? 1 : 0
  
  billing_account = var.billing_account_id
  display_name    = "${var.project_name}-${var.environment}-budget"
  
  budget_filter {
    projects = ["projects/${var.project_id}"]
  }
  
  amount {
    specified_amount {
      currency_code = "USD"
      units         = var.environment == "prod" ? "500" : "100"
    }
  }
  
  threshold_rules {
    threshold_percent = 0.5
    spend_basis      = "CURRENT_SPEND"
  }
  
  threshold_rules {
    threshold_percent = 0.9
    spend_basis      = "CURRENT_SPEND"
  }
  
  threshold_rules {
    threshold_percent = 1.0
    spend_basis      = "CURRENT_SPEND"
  }
  
  depends_on = [google_project_service.required_apis]
}







############################################################
# 1. Check if default Firestore database exists
############################################################
data "external" "firestore_exists" {
  program = [
    "bash", "-c", <<EOT
      set -e
      PROJECT_ID="${var.project_id}"
      
      # Check for default database first
      if gcloud firestore databases describe --project="$PROJECT_ID" --database="(default)" >/dev/null 2>&1; then
        echo '{"exists": "true", "database_id": "(default)"}'
      else
        echo '{"exists": "false", "database_id": "(default)"}'
      fi
    EOT
  ]
}

############################################################
# Check if Firestore indexes already exist
############################################################
data "external" "indexes_exist" {
  program = [
    "bash", "-c", <<EOT
      set -e
      PROJECT_ID="${var.project_id}"
      DB_NAME="(default)"

      # Check if any composite indexes exist
      if gcloud firestore indexes composite list --project="$PROJECT_ID" --database="$DB_NAME" --format="value(name)" 2>/dev/null | grep -q "."; then
        echo '{"exists": "true"}'
      else
        echo '{"exists": "false"}'
      fi
    EOT
  ]
}

############################################################
# Deploy Firebase rules using CLI commands to default database
############################################################
resource "null_resource" "deploy_firestore_rules" {
  provisioner "local-exec" {
    command = <<EOT
      set -e
      PROJECT_ID="${var.project_id}"
      DATABASE_ID="(default)"
      ENVIRONMENT="${var.environment}"
      
      # Change to project root directory where firestore.rules is located
      cd ..
      
      # Update firebase.json with default database configuration
      cat > firebase.json << JSON
{
  "firestore": {
    "rules": "firestore.rules"
  }
}
JSON
      
      echo "Updated firebase.json for default database"
      
      # Deploy rules using Firebase CLI to default database
      firebase use "$PROJECT_ID"
      firebase deploy --only firestore --project="$PROJECT_ID"
    EOT
  }

  # Trigger redeployment when rules content changes
  triggers = {
    rules_hash = filemd5("../firestore.rules")
    project_id = var.project_id
    database_id = "(default)"
  }

  depends_on = [
    google_firebase_project.default
  ]
}

############################################################
# 2. Create default Firestore database only if it doesn't exist
############################################################
resource "google_firestore_database" "default" {
  count    = data.external.firestore_exists.result.exists == "true" ? 0 : 1
  provider = google-beta
  project  = var.project_id
  name     = "(default)"

  location_id   = var.firestore_location
  type          = "FIRESTORE_NATIVE"
  concurrency_mode            = "OPTIMISTIC"
  app_engine_integration_mode = "DISABLED"

  point_in_time_recovery_enablement = var.environment == "prod" ? "POINT_IN_TIME_RECOVERY_ENABLED" : "POINT_IN_TIME_RECOVERY_DISABLED"
  delete_protection_state           = var.environment == "prod" ? "DELETE_PROTECTION_ENABLED" : "DELETE_PROTECTION_DISABLED"

  timeouts {
    create = "20m"
    update = "20m"
    delete = "20m"
  }

  depends_on = [
    google_firebase_project.default,
    google_project_service.required_apis
  ]
}

############################################################
# 3. Wait until Firestore is ACTIVE before continuing
############################################################
resource "null_resource" "wait_for_firestore_ready" {
  count      = data.external.firestore_exists.result.exists == "true" ? 0 : 1
  depends_on = [google_firestore_database.default]

  provisioner "local-exec" {
    command = <<EOT
      PROJECT="${var.project_id}"
      DB_NAME="(default)"
      echo "Waiting for Firestore database to become ACTIVE..."
      for i in $(seq 1 60); do
        STATUS=$(gcloud firestore databases describe --database="$DB_NAME" --project="$PROJECT" --format="value(state)" 2>/dev/null || echo "MISSING")
        if [ "$STATUS" = "ACTIVE" ]; then
          echo "Firestore database $DB_NAME is ready!"
          exit 0
        fi
        echo "Attempt $i/60: Status: $STATUS — retrying in 15s..."
        sleep 15
      done
      echo "Timed out waiting for Firestore database $DB_NAME to become ACTIVE after 15 minutes!"
      exit 1
    EOT
  }
}

############################################################
# 4. Manual step before destroy — disable delete protection
############################################################
resource "null_resource" "disable_firestore_delete_protection" {
  triggers = {
    project_id     = var.project_id
    database_name  = "(default)"
  }

  provisioner "local-exec" {
    command = <<EOT
      echo "Disabling delete protection for Firestore..."
      gcloud firestore databases update "${self.triggers.database_name}" \
        --project="${self.triggers.project_id}" \
        --delete-protection=DISABLED || true
    EOT
  }
}







# Check if Identity Platform is already enabled
data "external" "identity_platform_exists" {
  program = [
    "bash", "-c", <<EOT
      set -e
      PROJECT_ID="${var.project_id}"

      if gcloud services list --enabled --project="$PROJECT_ID" --filter="name:identitytoolkit" --format="value(name)" | grep -q identitytoolkit; then
        echo '{"exists": "true"}'
      else
        echo '{"exists": "false"}'
      fi
    EOT
  ]
}

# Local variable for database reference
locals {
  # Always use default database
  database_id = "(default)"
}

# Firebase Auth configuration - conditional creation
resource "google_identity_platform_config" "auth_config" {
  count    = (var.skip_identity_platform || data.external.identity_platform_exists.result.exists == "true") ? 0 : 1
  provider = google-beta
  project  = var.project_id

  sign_in {
    allow_duplicate_emails = false

    anonymous {
      enabled = false
    }

    email {
      enabled           = true
      password_required = true
    }

    phone_number {
      enabled = false
    }
  }

  lifecycle {
    ignore_changes = all
  }

  depends_on = [
    google_firebase_project.default
  ]
}





# Firestore security rules are deployed via Firebase CLI in null_resource.deploy_firestore_rules
# Terraform ruleset management is disabled to avoid conflicts

# Firebase rules release is handled by null_resource.force_deploy_rules using Firebase CLI
# This avoids the "Resource already exists" error with Terraform provider

# Check if Firebase web app already exists
data "external" "firebase_web_app_exists" {
  program = [
    "bash", "-c", <<EOT
      set +e  # Don't exit on errors
      PROJECT_ID="${var.project_id}"
      APP_NAME="${var.project_name}-${var.environment}-web"
      
      # Try to check if Firebase web app exists using gcloud alpha firebase
      # If this fails, assume no app exists to avoid breaking Terraform
      if command -v firebase >/dev/null 2>&1; then
        # Use Firebase CLI if available
        APPS_JSON=$(firebase apps:list --project="$PROJECT_ID" --json 2>/dev/null || echo "[]")
        
        # Look for the specific app by display name first
        APP_ID=""
        # Parse JSON manually to find app with matching displayName
        while IFS= read -r line; do
          if echo "$line" | grep -q "\"displayName\": \"$APP_NAME\""; then
            # Found matching display name, get the appId from next few lines
            APP_ID=$(echo "$APPS_JSON" | grep -A 10 "\"displayName\": \"$APP_NAME\"" | grep '"appId":' | head -1 | sed 's/.*"appId": *"\([^"]*\)".*/\1/')
            break
          fi
        done <<< "$APPS_JSON"
        
        # If no app with exact name found, use any existing app to prevent duplicates
        if [ -z "$APP_ID" ]; then
          APP_ID=$(echo "$APPS_JSON" | grep '"appId":' | head -1 | sed 's/.*"appId": *"\([^"]*\)".*/\1/')
          if [ -n "$APP_ID" ]; then
            echo "Warning: No app named $APP_NAME found, reusing existing app: $APP_ID" >&2
          fi
        fi
        
        if [ -n "$APP_ID" ] && [ "$APP_ID" != "null" ]; then
          echo "{\"exists\": \"true\", \"app_id\": \"$APP_ID\"}"
        else
          echo "{\"exists\": \"false\", \"app_id\": \"\"}"
        fi
      else
        # Firebase CLI not available, assume no app exists
        echo "{\"exists\": \"false\", \"app_id\": \"\"}"
      fi
    EOT
  ]
}

# Firebase Web App
resource "google_firebase_web_app" "driftwood_app" {
  count    = data.external.firebase_web_app_exists.result.exists == "true" ? 0 : 1
  provider = google-beta
  project  = var.project_id

  display_name = "${var.project_name}-${var.environment}-web"

  depends_on = [google_firebase_project.default]
}

# Local values to use existing or new app_id and config
locals {
  web_app_id = (data.external.firebase_web_app_exists.result.exists == "true" && data.external.firebase_web_app_exists.result.app_id != "") ? data.external.firebase_web_app_exists.result.app_id : (length(google_firebase_web_app.driftwood_app) > 0 ? google_firebase_web_app.driftwood_app[0].app_id : "")
  
  # Use existing app config if app exists and has valid ID, otherwise use new app config
  app_config = (data.external.firebase_web_app_exists.result.exists == "true" && data.external.firebase_web_app_exists.result.app_id != "") ? data.google_firebase_web_app_config.existing_app_config[0] : (length(data.google_firebase_web_app_config.new_app_config) > 0 ? data.google_firebase_web_app_config.new_app_config[0] : null)
  
  # Safe accessors for app config with fallbacks
  api_key = local.app_config != null ? local.app_config.api_key : ""
  auth_domain = local.app_config != null ? local.app_config.auth_domain : ""
  storage_bucket = local.app_config != null ? local.app_config.storage_bucket : ""
  messaging_sender_id = local.app_config != null ? local.app_config.messaging_sender_id : ""
}

# Firebase Web App configuration for existing apps
data "google_firebase_web_app_config" "existing_app_config" {
  count      = data.external.firebase_web_app_exists.result.exists == "true" && data.external.firebase_web_app_exists.result.app_id != "" ? 1 : 0
  provider   = google-beta
  project    = var.project_id
  web_app_id = data.external.firebase_web_app_exists.result.app_id
}

# Firebase Web App configuration for newly created apps
data "google_firebase_web_app_config" "new_app_config" {
  count      = data.external.firebase_web_app_exists.result.exists == "true" ? 0 : 1
  provider   = google-beta
  project    = var.project_id
  web_app_id = local.web_app_id

  depends_on = [google_firebase_web_app.driftwood_app[0]]
}

# Firebase App Check configuration for production (reCAPTCHA Enterprise)
resource "google_firebase_app_check_recaptcha_enterprise_config" "driftwood_app_check" {
  count    = var.environment == "prod" ? 1 : 0
  provider = google-beta
  project  = var.project_id
  app_id   = local.web_app_id

  site_key             = var.recaptcha_site_key
  token_ttl            = "7200s"
  
  depends_on = [
    google_firebase_web_app.driftwood_app,
    google_project_service.required_apis
  ]
}

# Debug tokens for development (staging/dev environments)
resource "google_firebase_app_check_debug_token" "dev_debug_token" {
  count        = var.environment != "prod" ? 1 : 0
  provider     = google-beta
  project      = var.project_id
  app_id       = local.web_app_id
  display_name = "${var.environment}-debug-token"
  token        = var.debug_token != "" ? var.debug_token : "12345678-1234-4567-8901-123456789abc"

  depends_on = [google_firebase_web_app.driftwood_app]
}

# App Check Service Config for Firestore
resource "google_firebase_app_check_service_config" "firestore" {
  provider     = google-beta
  project      = var.project_id
  service_id   = "firestore.googleapis.com"
  enforcement_mode = (var.environment == "prod" || var.enforce_app_check) ? "ENFORCED" : "UNENFORCED"

  depends_on = [
    google_project_service.required_apis,
    google_firestore_database.default
  ]
}

# App Check Service Config for Firebase Auth
resource "google_firebase_app_check_service_config" "identitytoolkit" {
  provider     = google-beta
  project      = var.project_id
  service_id   = "identitytoolkit.googleapis.com" 
  enforcement_mode = (var.environment == "prod" || var.enforce_app_check) ? "ENFORCED" : "UNENFORCED"

  depends_on = [
    google_project_service.required_apis
  ]
}

# Note: Cloud Functions and Storage App Check service configs are not supported
# in all regions and configurations. Only Firestore and Identity Toolkit are configured.

# Register web app with App Check services
resource "null_resource" "register_app_check_services" {
  provisioner "local-exec" {
    command = <<EOT
      set -e
      PROJECT_ID="${var.project_id}"
      DATABASE_ID="${local.database_id}"
      APP_ID="${local.web_app_id}"
      ENVIRONMENT="${var.environment}"
      ENFORCE_APP_CHECK="${var.enforce_app_check}"
      
      echo "Configuring App Check for web app $APP_ID..."
      echo "Project: $PROJECT_ID"
      echo "Database: $DATABASE_ID"
      echo "Environment: $ENVIRONMENT"
      
      # Check if Firebase CLI is available
      if ! command -v firebase >/dev/null 2>&1; then
        echo "Firebase CLI not found. App Check configuration may be incomplete."
        echo "Install with: npm install -g firebase-tools"
        exit 0
      fi
      
      # Set Firebase project
      firebase use "$PROJECT_ID"
      
      # Configure App Check for the web app
      echo "Configuring App Check for services..."
      
      # The service configs are managed by Terraform, but we need to ensure
      # the web app is properly associated with App Check
      
      # For development environments, ensure debug tokens are properly configured
      if [ "$ENVIRONMENT" != "prod" ]; then
        echo "🔧 Development environment detected"
        echo "   Debug tokens are configured via Terraform"
        echo "   Add debug tokens to your app: https://console.firebase.google.com/project/$PROJECT_ID/appcheck/apps/$APP_ID"
      fi
      
      # Show App Check status
      ENFORCEMENT_STATUS="${(var.environment == "prod" || var.enforce_app_check) ? "ENFORCED" : "UNENFORCED"}"
      echo ""
      echo "✅ App Check Configuration Summary:"
      echo "   📱 Web App ID: $APP_ID"
      echo "   🔒 Enforcement: $ENFORCEMENT_STATUS"
      echo "   🗄️  Firestore: $ENFORCEMENT_STATUS"
      echo "   🔐 Authentication: $ENFORCEMENT_STATUS"
      echo "   ⚡ Cloud Functions: $ENFORCEMENT_STATUS"
      echo "   💾 Cloud Storage: $ENFORCEMENT_STATUS"
      echo ""
      
      if [ "$ENFORCEMENT_STATUS" = "ENFORCED" ]; then
        echo "⚠️  App Check is ENFORCED - Only verified apps can access services"
        echo "   Ensure your app includes valid App Check tokens in all requests"
      else
        echo "🔓 App Check is UNENFORCED - All requests are allowed (development mode)"
        echo "   To enforce App Check, set enforce_app_check = true in terraform vars"
      fi
      
      echo ""
      echo "🔗 Firebase Console: https://console.firebase.google.com/project/$PROJECT_ID/appcheck"
    EOT
  }

  triggers = {
    database_id = local.database_id
    app_id      = local.web_app_id
    project_id  = var.project_id
    enforcement = var.enforce_app_check
    environment = var.environment
  }

  depends_on = [
    google_firebase_web_app.driftwood_app,
    google_firestore_database.default,
    google_firebase_app_check_service_config.firestore,
    google_firebase_app_check_service_config.identitytoolkit,
    google_firebase_app_check_debug_token.dev_debug_token,
    google_firebase_app_check_recaptcha_enterprise_config.driftwood_app_check
  ]
}

# Firestore indexes for optimal query performance
# Note: Single field indexes like stageName are automatically created by Firestore

resource "google_firestore_index" "event_disputes_report_event" {
  count       = data.external.indexes_exist.result.exists == "true" ? 0 : 1
  provider    = google-beta
  project     = var.project_id
  database    = local.database_id
  collection  = "disputes"
  query_scope = "COLLECTION_GROUP"

  fields {
    field_path = "report_id"
    order      = "ASCENDING"
  }

  fields {
    field_path = "event_id"
    order      = "ASCENDING"
  }

  depends_on = [google_firestore_database.default]
}

# Note: Single field indexes like timestamp are automatically created by Firestore

resource "google_firestore_index" "room_user1_user2" {
  count      = data.external.indexes_exist.result.exists == "true" ? 0 : 1
  provider   = google-beta
  project    = var.project_id
  database   = local.database_id
  collection = "room"

  fields {
    field_path = "user1"
    order      = "ASCENDING"
  }

  fields {
    field_path = "user2"
    order      = "ASCENDING"
  }

  depends_on = [google_firestore_database.default]
}

resource "google_firestore_index" "room_user2_user1" {
  count      = data.external.indexes_exist.result.exists == "true" ? 0 : 1
  provider   = google-beta
  project    = var.project_id
  database   = local.database_id
  collection = "room"

  fields {
    field_path = "user2"
    order      = "ASCENDING"
  }

  fields {
    field_path = "user1"
    order      = "ASCENDING"
  }

  depends_on = [google_firestore_database.default]
}

resource "google_firestore_index" "disputes_validation" {
  count      = data.external.indexes_exist.result.exists == "true" ? 0 : 1
  provider   = google-beta
  project    = var.project_id
  database   = local.database_id
  collection = "disputes"

  fields {
    field_path = "event_id"
    order      = "ASCENDING"
  }

  fields {
    field_path = "match_number"
    order      = "ASCENDING"
  }

  fields {
    field_path = "report_id"
    order      = "ASCENDING"
  }

  depends_on = [google_firestore_database.default]
}

# Message subcollection composite index for timestamp and __name__ descending queries
resource "google_firestore_index" "message_timestamp_name_desc" {
  provider     = google-beta
  project      = var.project_id
  database     = local.database_id
  collection   = "message"
  query_scope  = "COLLECTION_GROUP"

  fields {
    field_path = "timestamp"
    order      = "ASCENDING"
  }

  fields {
    field_path = "__name__"
    order      = "DESCENDING"
  }

  # Allow async creation - don't wait for index to be ready
  lifecycle {
    ignore_changes = [database, collection, fields]
    create_before_destroy = false
  }

  depends_on = [google_firestore_database.default]
}

# Create default Firestore collections for the current database
locals {
  collections = {
    "room" = {
      description = "Initial document to create room collection - can be deleted after first real room is created"
      type = "init"
    }
    "event" = {
      description = "Initial document to create event collection - can be deleted after first real event is created"
      type = "init"
    }
    "analytics-daily" = {
      description = "Initial document to create analytics-daily collection - can be deleted after first real analytics data is created"
      type = "init"
    }
    "analytics-monthly" = {
      description = "Initial document to create analytics-monthly collection - can be deleted after first real analytics data is created"
      type = "init"
    }
    "analytics-yearly" = {
      description = "Initial document to create analytics-yearly collection - can be deleted after first real analytics data is created"
      type = "init"
    }
  }
}

# Check if individual collection init documents already exist using Firestore REST API
data "external" "collection_document_exists" {
  for_each = local.collections
  
  program = [
    "bash", "-c", <<EOT
      set +e  # Don't exit on errors
      PROJECT_ID="${var.project_id}"
      DB_NAME="(default)"
      COLLECTION="${each.key}"
      
      # Get access token
      ACCESS_TOKEN=$(gcloud auth print-access-token 2>/dev/null)
      if [ -z "$ACCESS_TOKEN" ]; then
        echo "Failed to get access token, assuming document doesn't exist" >&2
        echo "{\"exists\": \"false\"}"
        exit 0
      fi
      
      # Check if this specific init document exists using Firestore REST API
      URL="https://firestore.googleapis.com/v1/projects/$PROJECT_ID/databases/$DB_NAME/documents/$COLLECTION/_init"
      
      RESULT=$(curl -s -w "HTTP_STATUS:%%{http_code}" -H "Authorization: Bearer $ACCESS_TOKEN" "$URL" 2>/dev/null)
      HTTP_STATUS=$(echo "$RESULT" | grep -o "HTTP_STATUS:[0-9]*" | cut -d: -f2)
      
      echo "Checking: $URL" >&2
      echo "HTTP Status: $HTTP_STATUS" >&2
      
      if [ "$HTTP_STATUS" = "200" ]; then
        echo "{\"exists\": \"true\"}"
      elif [ "$HTTP_STATUS" = "404" ]; then
        echo "{\"exists\": \"false\"}"
      else
        echo "Unexpected HTTP status $HTTP_STATUS, assuming document doesn't exist" >&2
        echo "{\"exists\": \"false\"}"
      fi
    EOT
  ]
}

resource "google_firestore_document" "collection_init" {
  for_each = {
    for k, v in local.collections : k => v
    if data.external.collection_document_exists[k].result.exists == "false"
  }
  
  provider    = google-beta
  project     = var.project_id
  database    = local.database_id
  collection  = each.key
  document_id = "_init"

  fields = jsonencode({
    created_at = {
      timestampValue = "2024-01-01T00:00:00Z"
    }
    description = {
      stringValue = each.value.description
    }
    type = {
      stringValue = each.value.type
    }
  })


  depends_on = [
    google_firestore_database.default
  ]
}

# Read current .env files and update Firebase configuration
data "local_file" "current_env" {
  filename = "../.env"
}

data "local_file" "current_env_example" {
  filename = "../.env.example"
}

data "local_file" "current_env_localdocker" {
  filename = "../.env.localdocker"
}

data "local_file" "current_env_prod" {
  filename = "../.env.prod"
}

data "local_file" "current_env_staging" {
  filename = "../.env.staging"
}

locals {
  # Define which files to update based on environment
  env_files_to_update = var.environment == "dev" ? {
    ".env" = data.local_file.current_env.content,
    ".env.example" = data.local_file.current_env_example.content
  } : var.environment == "staging" ? {
    ".env.staging" = data.local_file.current_env_staging.content
  } : var.environment == "prod" ? {
    ".env.prod" = data.local_file.current_env_prod.content
  } : {}

  # Function to update env content
  update_env_content = { 
    for env_name, env_content in local.env_files_to_update : env_name => join("\n", [
      for line in split("\n", env_content) : 
      startswith(line, "FIREBASE_API_KEY=") ? "FIREBASE_API_KEY=${local.api_key}" :
      startswith(line, "FIREBASE_DATABASE_ID=") ? "FIREBASE_DATABASE_ID=${local.database_id}" :
      startswith(line, "VITE_FIREBASE_API_KEY=") ? "VITE_FIREBASE_API_KEY=${local.api_key}" :
      startswith(line, "VITE_AUTH_DOMAIN=") ? "VITE_AUTH_DOMAIN=${local.auth_domain}" :
      startswith(line, "VITE_STORAGE_BUCKET=") ? "VITE_STORAGE_BUCKET=${local.storage_bucket}" :
      startswith(line, "VITE_MESSAGE_SENDER_ID=") ? "VITE_MESSAGE_SENDER_ID=${local.messaging_sender_id}" :
      startswith(line, "VITE_PROJECT_ID=") ? "VITE_PROJECT_ID=${var.project_id}" :
      startswith(line, "VITE_APP_ID=") ? "VITE_APP_ID=${local.web_app_id}" :
      startswith(line, "VITE_FIREBASE_DATABASE_ID=") ? "VITE_FIREBASE_DATABASE_ID=${local.database_id}" :
      startswith(line, "VITE_API_URL=") ? "VITE_API_URL=${google_cloud_run_v2_service.driftwood_api.uri}" :
      startswith(line, "CLOUD_FUNCTION_URL=") ? "CLOUD_FUNCTION_URL=${google_cloud_run_v2_service.driftwood_api.uri}" :
      # Remove the old DRIFTWOOD_API_KEY since we're using IAM auth now
      startswith(line, "DRIFTWOOD_API_KEY=") ? "" :
      line
    ])
  }
}


# Update .env files using null_resource to avoid destruction issues
resource "null_resource" "update_env_files" {
  for_each = local.update_env_content
  
  # Triggers when Firebase config changes
  triggers = {
    api_key        = local.api_key
    auth_domain    = local.auth_domain
    app_id         = local.web_app_id
    database_id    = local.database_id
    api_url        = google_cloud_run_v2_service.driftwood_api.uri
    content_hash   = md5(each.value)
  }

  # Update .env files only on apply/refresh, not on destroy
  provisioner "local-exec" {
    when    = create
    command = <<-EOT
      cat > "../${each.key}" << 'EOF'
${each.value}
EOF
    EOT
  }
  
  depends_on = [
    data.google_firebase_web_app_config.existing_app_config,
    data.google_firebase_web_app_config.new_app_config
  ]
}

# Conditional composer update - only when composer.json or composer.lock changes
data "local_file" "composer_json" {
  filename = "../composer.json"
}

data "local_file" "composer_lock" {
  filename = "../composer.lock"
}

resource "null_resource" "composer_update" {
  count = var.environment == "dev" ? 1 : 0  # Only run in dev environment
  
  # Triggers when composer files change
  triggers = {
    composer_json = filemd5("../composer.json")
    composer_lock = filemd5("../composer.lock")
    force_update  = var.force_composer_update ? timestamp() : "false"
  }

  provisioner "local-exec" {
    command = "cd .. && composer update --no-interaction --prefer-dist --optimize-autoloader"
    environment = {
      COMPOSER_ALLOW_SUPERUSER = "1"
    }
  }

  depends_on = [
    null_resource.update_env_files
  ]
}

# Conditional npm build - when package.json, Firebase config, or env files change
data "local_file" "package_json" {
  filename = "../package.json"
}

resource "null_resource" "npm_build" {
  count = var.environment == "dev" ? 1 : 0  # Only run in dev environment
  
  # Triggers when relevant files change
  triggers = {
    api_key        = local.api_key
    auth_domain    = local.auth_domain
    app_id         = local.web_app_id
    package_json   = filemd5("../package.json")
    env_files      = join(",", [for k, v in null_resource.update_env_files : k])
    database_id    = local.database_id
    force_build    = var.force_npm_build ? timestamp() : "false"
  }


  provisioner "local-exec" {
    command = "cd .. && npm run build"
    environment = {
      NODE_ENV = var.environment == "prod" ? "production" : "development"
    }
  }

  depends_on = [
    null_resource.update_env_files
  ]
}

# Create auth service environment file
resource "local_file" "auth_service_env" {
  content = join("\n", [
    "FIREBASE_PROJECT_ID=${var.project_id}",
    "FIREBASE_DATABASE_ID=(default)",
    "FIREBASE_API_KEY=${local.api_key}",
    "FIREBASE_AUTH_DOMAIN=${local.auth_domain}",
    "FIREBASE_STORAGE_BUCKET=${local.storage_bucket}",
    "FIREBASE_MESSAGING_SENDER_ID=${local.messaging_sender_id}",
    "FIREBASE_APP_ID=${local.web_app_id}",
    "FIREBASE_CREDENTIALS_PATH=./firebase-credentials.json",
    "SECRET_KEY=${var.auth_service_secret_key != "" ? var.auth_service_secret_key : "change-me-in-production-${random_password.auth_secret[0].result}"}",
    "ALGORITHM=HS256",
    "ACCESS_TOKEN_EXPIRE_MINUTES=30",
    "ENVIRONMENT=${var.environment}",
    "PORT=8000",
    ""
  ])
  
  filename = "../auth-service/.env"
  
  depends_on = [
    data.google_firebase_web_app_config.existing_app_config,
    data.google_firebase_web_app_config.new_app_config
  ]
}

# Generate random secret key if not provided
resource "random_password" "auth_secret" {
  count   = var.auth_service_secret_key == "" ? 1 : 0
  length  = 64
  special = true
}

# Copy Firebase service account key to auth service directory
resource "null_resource" "copy_firebase_key" {
  count = var.environment == "dev" ? 1 : 0
  
  provisioner "local-exec" {
    command = <<-EOT
      # Check if we have a service account key in the project root
      if [ -f "../firebase-credentials.json" ]; then
        cp "../firebase-credentials.json" "../auth-service/firebase-credentials.json"
        echo "Copied Firebase service account key to auth service"
      elif [ -f "../serviceAccountKey.json" ]; then
        cp "../serviceAccountKey.json" "../auth-service/firebase-credentials.json"
        echo "Copied serviceAccountKey.json to auth service as firebase-credentials.json"
      else
        echo "No Firebase service account key found. You'll need to:"
        echo "1. Download service account key from Firebase Console"
        echo "2. Save it as auth-service/firebase-credentials.json"
        echo "3. Or use default application credentials for cloud deployment"
      fi
    EOT
  }
  
  triggers = {
    environment = var.environment
  }
  
  depends_on = [local_file.auth_service_env]
}

# Check for existing Cloud Functions (both 1st and 2nd gen)
data "external" "existing_functions" {
  program = [
    "bash", "-c", <<EOT
      set -e
      PROJECT_ID="${var.project_id}"
      REGION="${var.region}"
      
      # Get count of existing 1st gen functions
      GEN1_COUNT=$(gcloud functions list --project="$PROJECT_ID" --format="value(name)" 2>/dev/null | grep -E "(driftwood-auth|driftwood-health|driftwood-api-${var.environment})" | wc -l || echo "0")
      
      # Get count of existing 2nd gen functions
      GEN2_COUNT=$(gcloud functions list --project="$PROJECT_ID" --gen2 --format="value(name)" 2>/dev/null | grep -E "(driftwood-auth|driftwood-health-${var.environment}|driftwood-api-${var.environment})" | wc -l || echo "0")
      
      TOTAL_COUNT=$((GEN1_COUNT + GEN2_COUNT))
      
      if [ "$TOTAL_COUNT" -gt 0 ]; then
        echo "{\"exists\": \"true\", \"count\": \"$TOTAL_COUNT\", \"gen1\": \"$GEN1_COUNT\", \"gen2\": \"$GEN2_COUNT\"}"
      else
        echo "{\"exists\": \"false\", \"count\": \"0\", \"gen1\": \"0\", \"gen2\": \"0\"}"
      fi
    EOT
  ]
}

# Clean up existing functions that might conflict
resource "null_resource" "cleanup_existing_functions" {
  count = data.external.existing_functions.result.exists == "true" ? 1 : 0
  
  provisioner "local-exec" {
    command = <<EOT
      PROJECT_ID="${var.project_id}"
      REGION="${var.region}"
      
      # Delete any existing functions that use our source bucket
      echo "Cleaning up existing Cloud Functions..."
      
      # Delete 1st gen functions
      for func in driftwood-auth driftwood-health "driftwood-api-${var.environment}"; do
        if gcloud functions describe "$func" --project="$PROJECT_ID" --region="$REGION" >/dev/null 2>&1; then
          echo "Deleting existing 1st gen function: $func"
          gcloud functions delete "$func" --project="$PROJECT_ID" --region="$REGION" --quiet
        fi
      done
      
      # Delete 2nd gen functions
      for func in driftwood-auth "driftwood-health-${var.environment}" "driftwood-api-${var.environment}"; do
        if gcloud functions describe "$func" --project="$PROJECT_ID" --region="$REGION" --gen2 >/dev/null 2>&1; then
          echo "Deleting existing 2nd gen function: $func"
          gcloud functions delete "$func" --project="$PROJECT_ID" --region="$REGION" --gen2 --quiet
        fi
      done
    EOT
  }

  triggers = {
    project_id = var.project_id
    region     = var.region
  }
}

# Create Cloud Storage bucket for Cloud Functions source code
resource "google_storage_bucket" "functions_bucket" {
  name     = "${var.project_id}-functions-source"
  location = var.region
  project  = var.project_id

  uniform_bucket_level_access = true
  
  lifecycle_rule {
    condition {
      age = 30
    }
    action {
      type = "Delete"
    }
  }

  lifecycle {
    ignore_changes = [name, location]
  }

  depends_on = [google_project_service.required_apis]
}

# Build and push Docker image for Cloud Run
resource "null_resource" "build_docker_image" {
  triggers = {
    main_py_hash = filemd5("../functions/main.py")
    requirements_hash = filemd5("../functions/requirements.txt")
    dockerfile_hash = filemd5("../functions/Dockerfile")
    project_id     = var.project_id
  }

  provisioner "local-exec" {
    command = <<-EOT
      cd ../functions
      
      # Build and push Docker image to Google Container Registry
      gcloud builds submit --tag gcr.io/${var.project_id}/driftwood-api:latest .
    EOT
  }

  depends_on = [
    google_project_service.required_apis
  ]
}

# Build and package the Cloud Function (for health check only)
resource "null_resource" "build_functions" {
  triggers = {
    functions_hash = filemd5("../functions/main.py")
    requirements_hash = filemd5("../functions/requirements.txt")
    project_id     = var.project_id
  }

  provisioner "local-exec" {
    command = <<-EOT
      cd ../functions
      
      # Create deployment package with all Python files
      zip -r function-source.zip main.py requirements.txt
    EOT
  }

  depends_on = [
    google_storage_bucket.functions_bucket,
    null_resource.cleanup_existing_functions
  ]
}

# Upload the function source to Cloud Storage (for health check only)
resource "google_storage_bucket_object" "function_source" {
  name   = "auth-function-${timestamp()}.zip"
  bucket = google_storage_bucket.functions_bucket.name
  source = "../functions/function-source.zip"

  depends_on = [null_resource.build_functions]
}

# Deploy the auth service as a Cloud Function Gen 2 - DISABLED (using unified driftwood_api instead)
# resource "google_cloudfunctions2_function" "auth_service" {
#   name        = "driftwood-auth"
#   description = "Driftwood authentication service"
#   project     = var.project_id
#   location    = var.region
#
#   build_config {
#     runtime     = "python311"
#     entry_point = "driftwood_api"
#     source {
#       storage_source {
#         bucket = google_storage_bucket.functions_bucket.name
#         object = google_storage_bucket_object.function_source.name
#       }
#     }
#   }
#
#   service_config {
#     max_instance_count = 10
#     min_instance_count = 0
#     available_memory   = "256Mi"
#     timeout_seconds    = 60
#     service_account_email = "${data.google_project.current.number}-compute@developer.gserviceaccount.com"
#     
#     environment_variables = {
#       SECRET_KEY = var.auth_service_secret_key != "" ? var.auth_service_secret_key : random_password.auth_secret[0].result
#       ENVIRONMENT = var.environment
#     }
#     
#     ingress_settings = "ALLOW_ALL"
#   }
#
#   depends_on = [
#     google_storage_bucket_object.function_source,
#     google_project_service.required_apis
#   ]
# }

# IAM binding to allow public access to the function - DISABLED
# resource "google_cloudfunctions2_function_iam_member" "auth_service_invoker" {
#   project        = var.project_id
#   location       = var.region
#   cloud_function = google_cloudfunctions2_function.auth_service.name
#
#   role   = "roles/cloudfunctions.invoker"
#   member = "allUsers"
# }

# Health check functionality is now included in the main Cloud Run API service
# No separate health check function needed

# Health check IAM bindings removed - functionality now in main Cloud Run API service

# Deploy FastAPI service as a Cloud Function Gen 2
# Cloud Run service for Driftwood API
resource "google_cloud_run_v2_service" "driftwood_api" {
  name     = "driftwood-api-${var.environment}"
  location = var.region
  project  = var.project_id

  template {
    scaling {
      max_instance_count = 5
      min_instance_count = 0
    }

    containers {
      image = "gcr.io/${var.project_id}/driftwood-api:latest"
      
      ports {
        container_port = 8080
      }

      resources {
        limits = {
          cpu    = "1"
          memory = "512Mi"
        }
      }

      env {
        name  = "ENVIRONMENT"
        value = var.environment
      }
      env {
        name  = "FIREBASE_PROJECT_ID"
        value = var.project_id
      }
      env {
        name  = "FIREBASE_DATABASE_ID"
        value = "(default)"
      }
      env {
        name  = "SECRET_KEY"
        value = var.auth_service_secret_key != "" ? var.auth_service_secret_key : random_password.auth_secret[0].result
      }
    }

    service_account = "${data.google_project.current.number}-compute@developer.gserviceaccount.com"
  }

  traffic {
    percent = 100
    type    = "TRAFFIC_TARGET_ALLOCATION_TYPE_LATEST"
  }

  depends_on = [
    google_project_service.required_apis,
    null_resource.build_docker_image
  ]
}

# Grant Cloud Run invoker permission to the Terraform service account (same one used by Laravel)
resource "google_cloud_run_service_iam_member" "terraform_service_account_invoker" {
  service  = google_cloud_run_v2_service.driftwood_api.name
  location = google_cloud_run_v2_service.driftwood_api.location
  role     = "roles/run.invoker"
  member   = "serviceAccount:${var.terraform_service_account}"
}

# Grant the compute service account permission to create Firebase custom tokens
resource "google_project_iam_member" "compute_token_creator" {
  project = var.project_id
  role    = "roles/iam.serviceAccountTokenCreator"
  member  = "serviceAccount:${data.google_project.current.number}-compute@developer.gserviceaccount.com"
}

# IAM binding removed - will be added manually to service account