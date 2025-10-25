#!/bin/bash
# Wrapper script to run Terraform commands with environment-specific configuration
# Usage: docker-tf-wrapper.sh [dev|staging|prod] [plan|apply|destroy|...]

set -e

ENVIRONMENT=${1:-dev}
COMMAND=${2:-plan}

# Validate environment
case $ENVIRONMENT in
  dev|staging|prod)
    ;;
  *)
    echo "Error: Invalid environment '$ENVIRONMENT'"
    echo "Usage: $0 [dev|staging|prod] [terraform-command]"
    exit 1
    ;;
esac

echo "=========================================="
echo "Terraform Docker Wrapper"
echo "Environment: $ENVIRONMENT"
echo "Command: $COMMAND"
echo "=========================================="
echo ""

# Change to terraform directory
cd /app/terraform

# Source the environment loading script
echo "Loading environment variables..."
source scripts/load-env.sh "$ENVIRONMENT"

# Run Firebase authentication
echo ""
echo "Setting up Firebase authentication..."
bash scripts/firebase-login.sh

echo ""
echo "=========================================="
echo "Executing: terraform $COMMAND -var-file=\"${ENVIRONMENT}.tfvars\""
echo "=========================================="
echo ""

# Execute the terraform command with the environment-specific var file
terraform "$COMMAND" -var-file="${ENVIRONMENT}.tfvars"

echo ""
echo "=========================================="
echo "Terraform $COMMAND completed for $ENVIRONMENT"
echo "=========================================="
