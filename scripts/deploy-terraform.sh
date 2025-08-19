#!/bin/bash

# Terraform deployment script for Driftwood Firebase infrastructure
# This script automates the deployment of Firebase/Firestore resources using Terraform

set -euo pipefail  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Script configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
TERRAFORM_DIR="${PROJECT_ROOT}/terraform"

# Default values
ENVIRONMENT="dev"
AUTO_APPROVE=""
PLAN_ONLY=false
DESTROY=false
INIT_ONLY=false
VALIDATE_ONLY=false

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to show usage
show_usage() {
    cat << EOF
Usage: $0 [OPTIONS]

Deploy Driftwood Firebase infrastructure using Terraform

OPTIONS:
    -e, --environment ENV    Environment to deploy (dev, staging, prod) [default: dev]
    -p, --plan-only         Only run terraform plan, don't apply
    -d, --destroy           Destroy infrastructure instead of creating it
    -i, --init-only         Only run terraform init
    -v, --validate-only     Only validate terraform configuration
    -y, --auto-approve      Skip interactive approval (use with caution)
    -h, --help              Show this help message

EXAMPLES:
    $0                                  # Deploy dev environment
    $0 -e prod                         # Deploy production environment
    $0 -e staging --plan-only          # Plan staging deployment without applying
    $0 -e prod --destroy --auto-approve # Destroy production (dangerous!)
    $0 --validate-only                 # Just validate configuration

ENVIRONMENT FILES:
    dev:     terraform/dev.tfvars
    staging: terraform/staging.tfvars  
    prod:    terraform/prod.tfvars

PREREQUISITES:
    1. Install Terraform >= 1.0
    2. Install Google Cloud SDK
    3. Install Firebase CLI
    4. Authenticate with: gcloud auth login && gcloud auth application-default login
    5. Get Firebase token: firebase login:ci
    6. Copy terraform.tfvars.example to terraform.tfvars and configure

EOF
}

# Function to check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    local errors=0
    
    # Check if Terraform is installed
    if ! command -v terraform &> /dev/null; then
        print_error "Terraform is not installed. Please install Terraform >= 1.0"
        ((errors++))
    else
        local tf_version=$(terraform version -json | jq -r .terraform_version)
        print_success "Terraform ${tf_version} is installed"
    fi
    
    # Check if Google Cloud SDK is installed
    if ! command -v gcloud &> /dev/null; then
        print_error "Google Cloud SDK is not installed"
        ((errors++))
    else
        local gcloud_version=$(gcloud version --format="value(Google Cloud SDK)" 2>/dev/null | head -n1)
        print_success "Google Cloud SDK ${gcloud_version} is installed"
    fi
    
    # Check if Firebase CLI is installed
    if ! command -v firebase &> /dev/null; then
        print_error "Firebase CLI is not installed. Install with: npm install -g firebase-tools"
        ((errors++))
    else
        local firebase_version=$(firebase --version)
        print_success "Firebase CLI ${firebase_version} is installed"
    fi
    
    # Check if jq is installed (for JSON parsing)
    if ! command -v jq &> /dev/null; then
        print_error "jq is not installed. Please install jq for JSON parsing"
        ((errors++))
    fi
    
    # Check if environment file exists
    local env_file="${TERRAFORM_DIR}/${ENVIRONMENT}.tfvars"
    if [[ ! -f "${env_file}" ]]; then
        print_error "Environment file not found: ${env_file}"
        ((errors++))
    else
        print_success "Environment file found: ${env_file}"
    fi
    
    # Check if terraform.tfvars exists
    local tfvars_file="${TERRAFORM_DIR}/terraform.tfvars"
    if [[ ! -f "${tfvars_file}" ]]; then
        print_warning "terraform.tfvars not found. Copy from terraform.tfvars.example and configure."
        print_warning "Some variables may not be set."
    else
        print_success "terraform.tfvars found"
    fi
    
    # Check if firestore.rules exists
    local rules_file="${PROJECT_ROOT}/firestore.rules"
    if [[ ! -f "${rules_file}" ]]; then
        print_error "Firestore rules file not found: ${rules_file}"
        ((errors++))
    else
        print_success "Firestore rules file found"
    fi
    
    if [[ ${errors} -gt 0 ]]; then
        print_error "Prerequisites check failed with ${errors} errors"
        exit 1
    fi
    
    print_success "All prerequisites met"
}

# Function to authenticate with Google Cloud and Firebase
check_authentication() {
    print_status "Checking authentication..."
    
    # Check Google Cloud authentication
    if ! gcloud auth list --filter=status:ACTIVE --format="value(account)" &> /dev/null; then
        print_error "Not authenticated with Google Cloud. Run: gcloud auth login"
        exit 1
    else
        local active_account=$(gcloud auth list --filter=status:ACTIVE --format="value(account)")
        print_success "Authenticated with Google Cloud as: ${active_account}"
    fi
    
    # Check if application default credentials are set
    if ! gcloud auth application-default print-access-token &> /dev/null; then
        print_warning "Application Default Credentials not set. Run: gcloud auth application-default login"
    fi
    
    # Check Firebase authentication (if firebase_token is set)
    local env_file="${TERRAFORM_DIR}/${ENVIRONMENT}.tfvars"
    local tfvars_file="${TERRAFORM_DIR}/terraform.tfvars"
    
    # Try to extract project_id from environment file
    local project_id=""
    if [[ -f "${env_file}" ]]; then
        project_id=$(grep '^project_id' "${env_file}" | cut -d'"' -f2 || echo "")
    fi
    
    if [[ -n "${project_id}" ]]; then
        print_success "Target project: ${project_id}"
    else
        print_warning "Could not determine project_id from environment file"
    fi
}

# Function to initialize Terraform
terraform_init() {
    print_status "Initializing Terraform..."
    
    cd "${TERRAFORM_DIR}"
    
    terraform init \
        -upgrade \
        -input=false
    
    print_success "Terraform initialized successfully"
}

# Function to validate Terraform configuration
terraform_validate() {
    print_status "Validating Terraform configuration..."
    
    cd "${TERRAFORM_DIR}"
    
    terraform validate
    
    print_success "Terraform configuration is valid"
}

# Function to plan Terraform deployment
terraform_plan() {
    print_status "Planning Terraform deployment for environment: ${ENVIRONMENT}"
    
    cd "${TERRAFORM_DIR}"
    
    local plan_args=(
        "-var-file=${ENVIRONMENT}.tfvars"
        "-out=tfplan-${ENVIRONMENT}"
    )
    
    # Add terraform.tfvars if it exists
    if [[ -f "terraform.tfvars" ]]; then
        plan_args+=("-var-file=terraform.tfvars")
    fi
    
    if [[ "${DESTROY}" == "true" ]]; then
        plan_args+=("-destroy")
        print_warning "Planning DESTROY operation!"
    fi
    
    terraform plan "${plan_args[@]}"
    
    if [[ "${DESTROY}" == "true" ]]; then
        print_warning "Destroy plan created. Review carefully before applying!"
    else
        print_success "Plan created successfully"
    fi
}

# Function to apply Terraform deployment
terraform_apply() {
    print_status "Applying Terraform deployment for environment: ${ENVIRONMENT}"
    
    cd "${TERRAFORM_DIR}"
    
    local apply_args=("tfplan-${ENVIRONMENT}")
    
    if [[ "${AUTO_APPROVE}" == "true" ]]; then
        print_warning "Auto-approve enabled, applying without confirmation"
    else
        if [[ "${DESTROY}" == "true" ]]; then
            print_warning "This will DESTROY infrastructure in ${ENVIRONMENT} environment!"
            read -p "Are you sure you want to continue? (yes/no): " confirm
            if [[ "${confirm}" != "yes" ]]; then
                print_status "Deployment cancelled"
                exit 0
            fi
        else
            print_status "About to apply changes to ${ENVIRONMENT} environment"
            read -p "Do you want to continue? (yes/no): " confirm
            if [[ "${confirm}" != "yes" ]]; then
                print_status "Deployment cancelled"
                exit 0
            fi
        fi
    fi
    
    terraform apply "${apply_args[@]}"
    
    if [[ "${DESTROY}" == "true" ]]; then
        print_success "Infrastructure destroyed successfully"
    else
        print_success "Infrastructure deployed successfully"
    fi
}

# Function to show outputs
show_outputs() {
    if [[ "${DESTROY}" == "true" ]]; then
        return 0
    fi
    
    print_status "Terraform outputs:"
    
    cd "${TERRAFORM_DIR}"
    terraform output
}

# Function to cleanup plan files
cleanup() {
    print_status "Cleaning up temporary files..."
    
    cd "${TERRAFORM_DIR}"
    
    if [[ -f "tfplan-${ENVIRONMENT}" ]]; then
        rm -f "tfplan-${ENVIRONMENT}"
        print_success "Cleaned up plan file"
    fi
}

# Function to show deployment summary
show_summary() {
    print_status "Deployment Summary"
    echo "=================="
    echo "Environment: ${ENVIRONMENT}"
    echo "Action: $(if [[ "${DESTROY}" == "true" ]]; then echo "DESTROY"; else echo "DEPLOY"; fi)"
    echo "Terraform Directory: ${TERRAFORM_DIR}"
    echo "Time: $(date)"
    
    if [[ "${DESTROY}" == "false" ]]; then
        echo ""
        print_status "Next steps:"
        echo "1. Verify deployment in Firebase Console"
        echo "2. Test Firestore security rules"
        echo "3. Update application configuration"
        echo "4. Set up monitoring and alerts"
    fi
}

# Main function
main() {
    print_status "Driftwood Firebase Terraform Deployment"
    print_status "========================================"
    
    # Change to project root
    cd "${PROJECT_ROOT}"
    
    # Run deployment steps
    if [[ "${VALIDATE_ONLY}" == "true" ]]; then
        check_prerequisites
        terraform_validate
        print_success "Validation completed successfully"
        return 0
    fi
    
    if [[ "${INIT_ONLY}" == "true" ]]; then
        check_prerequisites
        terraform_init
        print_success "Initialization completed successfully"
        return 0
    fi
    
    check_prerequisites
    check_authentication
    terraform_init
    terraform_validate
    terraform_plan
    
    if [[ "${PLAN_ONLY}" == "false" ]]; then
        terraform_apply
        show_outputs
    fi
    
    cleanup
    show_summary
    
    print_success "Deployment script completed successfully"
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -e|--environment)
            ENVIRONMENT="$2"
            shift 2
            ;;
        -p|--plan-only)
            PLAN_ONLY=true
            shift
            ;;
        -d|--destroy)
            DESTROY=true
            shift
            ;;
        -i|--init-only)
            INIT_ONLY=true
            shift
            ;;
        -v|--validate-only)
            VALIDATE_ONLY=true
            shift
            ;;
        -y|--auto-approve)
            AUTO_APPROVE=true
            shift
            ;;
        -h|--help)
            show_usage
            exit 0
            ;;
        *)
            print_error "Unknown option: $1"
            show_usage
            exit 1
            ;;
    esac
done

# Validate environment
if [[ ! "${ENVIRONMENT}" =~ ^(dev|staging|prod)$ ]]; then
    print_error "Invalid environment: ${ENVIRONMENT}. Must be one of: dev, staging, prod"
    exit 1
fi

# Set error handler
trap 'cleanup; print_error "Script failed on line $LINENO"' ERR

# Run main function
main

exit 0