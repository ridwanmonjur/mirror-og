# Terraform outputs for Driftwood configuration
output "project_id" {
  description = "Google Cloud Project ID"
  value       = var.project_id
}

output "firebase_config" {
  description = "Firebase web app configuration"
  value = {
    api_key             = data.google_firebase_web_app_config.driftwood_app_config.api_key
    auth_domain         = data.google_firebase_web_app_config.driftwood_app_config.auth_domain
    project_id          = var.project_id
    storage_bucket      = data.google_firebase_web_app_config.driftwood_app_config.storage_bucket
    messaging_sender_id = data.google_firebase_web_app_config.driftwood_app_config.messaging_sender_id
    app_id              = google_firebase_web_app.driftwood_app.app_id
  }
}

# output "firestore_database_name" {
#   description = "Firestore database name"
#   value       = google_firestore_database.default.name
# }

output "web_app_id" {
  description = "Firebase Web App ID"
  value       = google_firebase_web_app.driftwood_app.app_id
}

output "oauth_client_id" {
  description = "Generated Google OAuth client ID"
  value       = local.oauth_client_id
  sensitive   = true
}

output "oauth_client_secret" {
  description = "Generated Google OAuth client secret"
  value       = local.oauth_client_secret
  sensitive   = true
}