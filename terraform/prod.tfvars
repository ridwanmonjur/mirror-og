# Production environment variables
environment = "prod"
# project_id loaded from .env.prod file via TF_VAR_project_id environment variable
# billing_account_id loaded from .env.prod file via TF_VAR_billing_account_id environment variable
project_name = "driftwood"
region = "asia-southeast1"
firestore_location = "asia-southeast1"

# Sensitive values loaded from .env files via environment variables
# Use: source scripts/load-env.sh prod