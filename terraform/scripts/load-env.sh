#!/bin/bash
# Script to load environment variables from .env files for Terraform

set -e

ENVIRONMENT=${1:-dev}

case $ENVIRONMENT in
  "dev")
    ENV_FILE="../.env"
    ;;
  "staging")
    ENV_FILE="../.env.staging"
    ;;
  "prod")
    ENV_FILE="../.env.prod"
    ;;
  *)
    echo "Usage: $0 [dev|staging|prod]"
    exit 1
    ;;
esac

if [ ! -f "$ENV_FILE" ]; then
  echo "Environment file $ENV_FILE not found"
  exit 1
fi

echo "Loading environment variables from $ENV_FILE for $ENVIRONMENT environment..."

# Extract values from .env file and export as TF_VAR_* environment variables
export TF_VAR_project_id=$(grep "FIREBASE_PROJECT_ID" $ENV_FILE | cut -d '=' -f2)
export TF_VAR_bucket_name=$(grep "TERRAFORM_STATE_BUCKET" $ENV_FILE | cut -d '=' -f2)
export TF_VAR_billing_account_id=$(grep "BILLING_ACCOUNT_ID" $ENV_FILE | cut -d '=' -f2)
export TF_VAR_environment=$ENVIRONMENT

# Set Google Application Credentials for Terraform
FIREBASE_CREDENTIALS_PATH=$(grep "^FIREBASE_CREDENTIALS=" $ENV_FILE | cut -d '=' -f2)
export GOOGLE_APPLICATION_CREDENTIALS="../$FIREBASE_CREDENTIALS_PATH"

echo "Environment variables set:"
echo "TF_VAR_project_id=$TF_VAR_project_id"
echo "TF_VAR_bucket_name=$TF_VAR_bucket_name"
echo "TF_VAR_billing_account_id=$TF_VAR_billing_account_id"
echo "TF_VAR_environment=$TF_VAR_environment"
echo "GOOGLE_APPLICATION_CREDENTIALS=$GOOGLE_APPLICATION_CREDENTIALS"

echo ""
echo "Now run your terraform commands, e.g.:"
echo "terraform plan -var-file=\"environments/${ENVIRONMENT}.tfvars\""
echo "terraform apply -var-file=\"environments/${ENVIRONMENT}.tfvars\""