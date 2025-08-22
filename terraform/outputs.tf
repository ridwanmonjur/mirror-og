# Terraform outputs for Driftwood configuration
output "project_id" {
  description = "Google Cloud Project ID"
  value       = var.project_id
}

output "firebase_config" {
  description = "Firebase web app configuration"
  value = {
    api_key             = local.api_key
    auth_domain         = local.auth_domain
    project_id          = var.project_id
    storage_bucket      = local.storage_bucket
    messaging_sender_id = local.messaging_sender_id
    app_id              = local.web_app_id
  }
}

# output "firestore_database_name" {
#   description = "Firestore database name"
#   value       = google_firestore_database.default.name
# }

output "web_app_id" {
  description = "Firebase Web App ID"
  value       = local.web_app_id
}

output "app_check_config" {
  description = "App Check configuration status"
  sensitive   = true
  value = {
    firestore_enforcement = (var.environment == "prod" || var.enforce_app_check) ? "ENFORCED" : "UNENFORCED"
    auth_enforcement     = (var.environment == "prod" || var.enforce_app_check) ? "ENFORCED" : "UNENFORCED"
    environment          = var.environment
    debug_token_enabled  = var.environment != "prod"
    recaptcha_enabled    = var.environment == "prod" && var.recaptcha_site_key != ""
  }
}

# output "auth_service_env" {
#   description = "Environment variables for auth service"
#   sensitive   = true
#   value = {
#     FIREBASE_PROJECT_ID = var.project_id
#     FIREBASE_DATABASE_ID = local.database_id
#     FIREBASE_API_KEY = local.api_key
#     FIREBASE_AUTH_DOMAIN = local.auth_domain
#     FIREBASE_STORAGE_BUCKET = local.storage_bucket
#     FIREBASE_MESSAGING_SENDER_ID = local.messaging_sender_id
#     FIREBASE_APP_ID = local.web_app_id
#     SECRET_KEY = var.auth_service_secret_key != "" ? var.auth_service_secret_key : "change-me-in-production"
#     ALGORITHM = "HS256"
#     ACCESS_TOKEN_EXPIRE_MINUTES = "30"
#     ENVIRONMENT = var.environment
#   }
# }

output "cloud_function_urls" {
  description = "Cloud Function URLs"
  value = {
    # auth_service_url = google_cloudfunctions_function.auth_service.https_trigger_url
    health_check_url = google_cloudfunctions_function.health_check.https_trigger_url
    driftwood_api_url = google_cloudfunctions_function.driftwood_api.https_trigger_url
  }
}

