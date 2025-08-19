# Staging environment variables
environment = "staging"
# project_id loaded from .env.staging file via TF_VAR_project_id environment variable
# billing_account_id loaded from .env.staging file via TF_VAR_billing_account_id environment variable
project_name = "oceansgaming"
region = "asia-southeast1"
firestore_location = "asia-southeast1"

# Skip Identity Platform creation since it's already enabled
skip_identity_platform = true

# Sensitive values loaded from .env files via environment variables
# Use: source scripts/load-env.sh staging