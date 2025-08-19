# Development environment variables
environment = "dev"
# project_id loaded from .env file via TF_VAR_project_id environment variable
# billing_account_id loaded from .env file via TF_VAR_billing_account_id environment variable
project_name = "driftwood"
region = "asia-southeast1"
firestore_location = "asia-southeast1"

# App Check configuration
enforce_app_check = false  # Set to true to enforce App Check in development

# Sensitive values loaded from .env files via environment variables
# Use: source scripts/load-env.sh dev