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
    recaptcha_enabled    = true
    recaptcha_site_key   = google_recaptcha_enterprise_key.driftwood_recaptcha.name
  }
}

output "recaptcha_config" {
  description = "Enterprise reCAPTCHA configuration"
  sensitive   = true
  value = {
    site_key     = google_recaptcha_enterprise_key.driftwood_recaptcha.name
    display_name = google_recaptcha_enterprise_key.driftwood_recaptcha.display_name
    domain       = local.recaptcha_domain
    environment  = var.environment
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

output "service_urls" {
  description = "Service URLs"
  value = {
    driftwood_api_url = google_cloud_run_v2_service.driftwood_api.uri
    health_check_url = "${google_cloud_run_v2_service.driftwood_api.uri}/health"
    client_auth_url = google_cloudfunctions_function.client_auth_service.https_trigger_url
    client_auth_token_url = "${google_cloudfunctions_function.client_auth_service.https_trigger_url}/auth/token"
  }
}

# Using existing Firebase service account from FIREBASE_CREDENTIALS
# No additional service account outputs needed

