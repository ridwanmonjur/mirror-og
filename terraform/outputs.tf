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

