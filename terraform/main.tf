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
    "storage.googleapis.com"
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



# IAM binding for user account Firestore access
resource "google_project_iam_member" "user_datastore_owner" {
  project = var.project_id
  role    = "roles/datastore.owner"
  member  = "user:oceansgaming05@gmail.com"
}

# Additional IAM binding for user account to create Firestore databases
resource "google_project_iam_member" "user_firebase_admin" {
  project = var.project_id
  role    = "roles/firebase.admin"
  member  = "user:oceansgaming05@gmail.com"
}

# IAM binding for user account to manage project services
resource "google_project_iam_member" "user_service_admin" {
  project = var.project_id
  role    = "roles/serviceusage.serviceUsageAdmin"
  member  = "user:oceansgaming05@gmail.com"
}

# IAM binding for user account as project editor (broader permissions)
resource "google_project_iam_member" "user_editor" {
  project = var.project_id
  role    = "roles/editor"
  member  = "user:oceansgaming05@gmail.com"
}






############################################################
# 1. Check if Firestore already exists
############################################################
data "external" "firestore_exists" {
  program = [
    "bash", "-c", <<EOT
      set -e
      PROJECT_ID="${var.project_id}"
      DB_NAME="${var.project_name}-${var.environment}"

      if gcloud firestore databases describe --project="$PROJECT_ID" --database="$DB_NAME" >/dev/null 2>&1; then
        echo '{"exists": "true"}'
      else
        echo '{"exists": "false"}'
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
      DB_NAME="${var.project_name}-${var.environment}"

      # Check if any indexes exist
      if gcloud firestore indexes list --project="$PROJECT_ID" --database="$DB_NAME" --format="value(name)" 2>/dev/null | grep -q "."; then
        echo '{"exists": "true"}'
      else
        echo '{"exists": "false"}'
      fi
    EOT
  ]
}

############################################################
# Deploy Firebase rules using CLI commands to specific database
############################################################
resource "null_resource" "deploy_firestore_rules" {
  provisioner "local-exec" {
    command = <<EOT
      set -e
      PROJECT_ID="${var.project_id}"
      DATABASE_ID="${var.project_name}-${var.environment}"
      
      # Change to project root directory where firestore.rules is located
      cd ..
      
      # Deploy rules using Firebase CLI to specific database
      firebase use "$PROJECT_ID"
      firebase deploy --only firestore:$DATABASE_ID --project="$PROJECT_ID"
    EOT
  }

  # Trigger redeployment when rules content changes
  triggers = {
    rules_hash = filemd5("../firestore.rules")
    project_id = var.project_id
    database_id = "${var.project_name}-${var.environment}"
  }

  depends_on = [
    google_firestore_database.default,
    google_firebase_project.default
  ]
}

############################################################
# 2. Create Firestore only if it doesn't exist
############################################################
resource "google_firestore_database" "default" {
  count    = data.external.firestore_exists.result.exists == "true" ? 0 : 1
  provider = google-beta
  project  = var.project_id
  name     = "${var.project_name}-${var.environment}"

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
      DB_NAME="${var.project_name}-${var.environment}"
      echo "Waiting for Firestore database to become ACTIVE..."
      for i in {1..30}; do
        STATUS=$(gcloud firestore databases describe "$DB_NAME" --project="$PROJECT" --format="value(state)" || echo "MISSING")
        if [ "$STATUS" = "ACTIVE" ]; then
          echo "Firestore is ready!"
          exit 0
        fi
        echo "Status: $STATUS — retrying in 10s..."
        sleep 10
      done
      echo "Timed out waiting for Firestore!"
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
    database_name  = "${var.project_name}-${var.environment}"
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







# Local variable for database reference
locals {
  # If database is created by terraform, use it; otherwise use the existing database name
  database_id = length(google_firestore_database.default) > 0 ? google_firestore_database.default[0].name : "${var.project_name}-${var.environment}"
}

# Firebase Auth configuration - conditional creation
resource "google_identity_platform_config" "auth_config" {
  count    = var.skip_identity_platform ? 0 : 1
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

# Create OAuth client configuration script
resource "local_file" "oauth_setup_script" {
  filename = "../create-oauth-client.sh"
  file_permission = "0755"
  content = <<-EOT
#!/bin/bash
# Script to create Google OAuth client for ${var.project_name}-${var.environment}

echo "Creating OAuth client for project: ${var.project_id}"
echo "Environment: ${var.environment}"
echo ""
echo "Please create an OAuth client in Google Cloud Console with these settings:"
echo ""
echo "Project: ${var.project_id}"
echo "Application Type: Web application"
echo "Name: ${var.project_name}-${var.environment}-web-client"
echo ""
echo "Authorized JavaScript origins:"
echo "- http://localhost:8000"
echo "- https://oceansgaming.gg"
echo "- https://driftwood.gg"
echo ""
echo "Authorized redirect URIs:"
echo "- http://localhost:8000/auth/google/callback"
echo "- https://driftwood.gg/auth/google/callback"
echo "- https://oceansgaming.gg/auth/google/callback"
echo ""
echo "After creating the OAuth client:"
echo "1. Copy the Client ID and Client Secret"
echo "2. Update your .env file with the credentials:"
echo "   GOOGLE_CLIENT_ID=your_client_id_here"
echo "   GOOGLE_CLIENT_SECRET=your_client_secret_here"
echo ""
echo "Google Cloud Console URL:"
echo "https://console.cloud.google.com/apis/credentials?project=${var.project_id}"
EOT

  depends_on = [google_firebase_project.default]
}

# Read OAuth credentials from current .env files
data "local_file" "current_env_for_oauth" {
  filename = var.environment == "dev" ? "../.env" : var.environment == "staging" ? "../.env.staging" : "../.env.prod"
}

# Extract OAuth credentials from .env file
locals {
  env_lines = split("\n", data.local_file.current_env_for_oauth.content)
  
  # Find GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET from .env
  oauth_client_id = try(
    regex("GOOGLE_CLIENT_ID=(.+)", join("\n", local.env_lines))[0],
    "your_google_oauth_client_id_here"
  )
  
  oauth_client_secret = try(
    regex("GOOGLE_CLIENT_SECRET=(.+)", join("\n", local.env_lines))[0], 
    "your_google_oauth_client_secret_here"
  )
}



# Firestore security rules are deployed via Firebase CLI in null_resource.deploy_firestore_rules
# Terraform ruleset management is disabled to avoid conflicts

# Firebase rules release is handled by null_resource.force_deploy_rules using Firebase CLI
# This avoids the "Resource already exists" error with Terraform provider

# Firebase Web App
resource "google_firebase_web_app" "driftwood_app" {
  provider = google-beta
  project  = var.project_id

  display_name = "${var.project_name}-${var.environment}-web"

  depends_on = [google_firebase_project.default]
}

# Firebase Web App configuration (data source)
data "google_firebase_web_app_config" "driftwood_app_config" {
  provider   = google-beta
  project    = var.project_id
  web_app_id = google_firebase_web_app.driftwood_app.app_id

  depends_on = [google_firebase_web_app.driftwood_app]
}

# Firebase App Check configuration for production (reCAPTCHA Enterprise)
resource "google_firebase_app_check_recaptcha_enterprise_config" "driftwood_app_check" {
  count    = var.environment == "prod" ? 1 : 0
  provider = google-beta
  project  = var.project_id
  app_id   = google_firebase_web_app.driftwood_app.app_id

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
  app_id       = google_firebase_web_app.driftwood_app.app_id
  display_name = "${var.environment}-debug-token"
  token        = var.debug_token != "" ? var.debug_token : "12345678-1234-4567-8901-123456789abc"

  depends_on = [google_firebase_web_app.driftwood_app]
}

# App Check Service Config for Firestore
resource "google_firebase_app_check_service_config" "firestore" {
  provider     = google-beta
  project      = var.project_id
  service_id   = "firestore.googleapis.com"
  enforcement_mode = var.environment == "prod" ? "ENFORCED" : "UNENFORCED"

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
  enforcement_mode = var.environment == "prod" ? "ENFORCED" : "UNENFORCED"

  depends_on = [
    google_project_service.required_apis
  ]
}

# Note: Cloud Functions and Storage App Check configs removed as they're not supported yet
# Only Firestore and Auth (Identity Toolkit) support App Check service configs currently

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

resource "google_firestore_document" "collection_init" {
  for_each = local.collections
  
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
      startswith(line, "FIREBASE_API_KEY=") ? "FIREBASE_API_KEY=${data.google_firebase_web_app_config.driftwood_app_config.api_key}" :
      startswith(line, "FIREBASE_DATABASE_ID=") ? "FIREBASE_DATABASE_ID=${local.database_id}" :
      startswith(line, "VITE_FIREBASE_API_KEY=") ? "VITE_FIREBASE_API_KEY=${data.google_firebase_web_app_config.driftwood_app_config.api_key}" :
      startswith(line, "VITE_AUTH_DOMAIN=") ? "VITE_AUTH_DOMAIN=${data.google_firebase_web_app_config.driftwood_app_config.auth_domain}" :
      startswith(line, "VITE_STORAGE_BUCKET=") ? "VITE_STORAGE_BUCKET=${data.google_firebase_web_app_config.driftwood_app_config.storage_bucket}" :
      startswith(line, "VITE_MESSAGE_SENDER_ID=") ? "VITE_MESSAGE_SENDER_ID=${data.google_firebase_web_app_config.driftwood_app_config.messaging_sender_id}" :
      startswith(line, "VITE_APP_ID=") ? "VITE_APP_ID=${google_firebase_web_app.driftwood_app.app_id}" :
      startswith(line, "VITE_FIREBASE_DATABASE_ID=") ? "VITE_FIREBASE_DATABASE_ID=${local.database_id}" :
      startswith(line, "GOOGLE_CLIENT_ID=") ? "GOOGLE_CLIENT_ID=${local.oauth_client_id}" :
      startswith(line, "GOOGLE_CLIENT_SECRET=") ? "GOOGLE_CLIENT_SECRET=${local.oauth_client_secret}" :
      line
    ])
  }
}

# Backup original .env files before making changes (only once)
resource "local_file" "env_backups" {
  for_each = local.env_files_to_update
  
  content  = each.value
  filename = "../${each.key}.terraform.backup"
  
  # Only create backup if it doesn't exist
  lifecycle {
    ignore_changes = [content]
  }
}

# Update .env files using null_resource to avoid destruction issues
resource "null_resource" "update_env_files" {
  for_each = local.update_env_content
  
  # Triggers when Firebase config changes
  triggers = {
    api_key        = data.google_firebase_web_app_config.driftwood_app_config.api_key
    auth_domain    = data.google_firebase_web_app_config.driftwood_app_config.auth_domain
    app_id         = google_firebase_web_app.driftwood_app.app_id
    database_id    = local.database_id
    oauth_client_id = local.oauth_client_id
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
    data.google_firebase_web_app_config.driftwood_app_config,
    local_file.env_backups
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
    api_key        = data.google_firebase_web_app_config.driftwood_app_config.api_key
    auth_domain    = data.google_firebase_web_app_config.driftwood_app_config.auth_domain
    app_id         = google_firebase_web_app.driftwood_app.app_id
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