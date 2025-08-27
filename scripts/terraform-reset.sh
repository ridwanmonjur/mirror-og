#!/bin/bash

# Terraform Reset Script - Handles proper destroy/apply sequence
# This script ensures APIs are properly managed during Terraform operations

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ENVIRONMENT=${1:-dev}
PROJECT_ID="ocean-s-firebase"

echo -e "${YELLOW}Starting Terraform reset for environment: $ENVIRONMENT${NC}"

# Change to terraform directory
cd "$(dirname "$0")/../terraform"

# Function to check if APIs are enabled
check_apis() {
    echo -e "${YELLOW}Checking required APIs...${NC}"
    
    # APIs that need to be enabled
    APIS=(
        "firebase.googleapis.com"
        "firestore.googleapis.com"
        "identitytoolkit.googleapis.com"
        "cloudresourcemanager.googleapis.com"
        "billingbudgets.googleapis.com"
        "iam.googleapis.com"
    )
    
    for api in "${APIS[@]}"; do
        if gcloud services list --enabled --project=$PROJECT_ID --format="value(name)" | grep -q "$api"; then
            echo -e "${GREEN}✓ $api is enabled${NC}"
        else
            echo -e "${RED}✗ $api is not enabled${NC}"
            echo -e "${YELLOW}Enabling $api...${NC}"
            gcloud services enable "$api" --project=$PROJECT_ID
        fi
    done
}

# Function to handle Terraform destroy
terraform_destroy() {
    echo -e "${YELLOW}Running Terraform destroy...${NC}"
    
    # Initialize Terraform
    terraform init
    
    # Import existing state if needed
    # terraform import google_project_service.required_apis[\"firebase.googleapis.com\"] $PROJECT_ID/firebase.googleapis.com || true
    
    # Run destroy with auto-approve
    if [ "$ENVIRONMENT" = "dev" ]; then
        TF_VAR_project_id=$PROJECT_ID \
        terraform destroy -var-file="environments/dev.tfvars" -auto-approve
    elif [ "$ENVIRONMENT" = "staging" ]; then
        TF_VAR_project_id=$PROJECT_ID \
        terraform destroy -var-file="environments/staging.tfvars" -auto-approve
    elif [ "$ENVIRONMENT" = "prod" ]; then
        TF_VAR_project_id=$PROJECT_ID \
        terraform destroy -var-file="environments/prod.tfvars" -auto-approve
    fi
}

# Function to handle Terraform apply
terraform_apply() {
    echo -e "${YELLOW}Running Terraform apply...${NC}"
    
    # Make sure APIs are enabled before applying
    check_apis
    
    # Wait a bit for API propagation
    echo -e "${YELLOW}Waiting for API propagation...${NC}"
    sleep 30
    
    # Initialize Terraform
    terraform init
    
    # Run apply with auto-approve
    if [ "$ENVIRONMENT" = "dev" ]; then
        TF_VAR_project_id=$PROJECT_ID \
        terraform apply -var-file="environments/dev.tfvars" -auto-approve
    elif [ "$ENVIRONMENT" = "staging" ]; then
        TF_VAR_project_id=$PROJECT_ID \
        terraform apply -var-file="environments/staging.tfvars" -auto-approve
    elif [ "$ENVIRONMENT" = "prod" ]; then
        TF_VAR_project_id=$PROJECT_ID \
        terraform apply -var-file="environments/prod.tfvars" -auto-approve
    fi
}

# Main execution
case "${2:-apply}" in
    "destroy")
        terraform_destroy
        ;;
    "apply")
        terraform_apply
        ;;
    "reset")
        terraform_destroy
        echo -e "${YELLOW}Waiting before apply...${NC}"
        sleep 10
        terraform_apply
        ;;
    *)
        echo "Usage: $0 <environment> [destroy|apply|reset]"
        echo "  environment: dev, staging, prod"
        echo "  action: destroy, apply, reset (default: apply)"
        exit 1
        ;;
esac

echo -e "${GREEN}Terraform operation completed successfully!${NC}"