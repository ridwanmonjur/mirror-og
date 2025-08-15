# Driftwood Terraform Configuration

This Terraform configuration sets up Firebase/Firestore infrastructure for the Driftwood esports platform.

## What it creates

- Firebase project integration
- Firestore database with security rules
- Firebase Authentication with Google OAuth
- Firebase Web App configuration

## Prerequisites

1. Google Cloud Project
2. Terraform installed
3. Google Cloud SDK installed and authenticated
4. Required APIs enabled (will be enabled automatically)

## Google Cloud Authentication

### Initial Setup
```bash
# Install Google Cloud SDK
curl https://sdk.cloud.google.com | bash
exec -l $SHELL

# Login to Google Cloud as as oceansgaming05@gmail.com
gcloud auth login
gcloud config set account oceansgaming05@gmail.com

# Set application default credentials (required for Terraform)
gcloud auth application-default login

# View current authentication
gcloud auth list


# Set your project (replace with your project ID)
gcloud config set project ocean-s-firebase  # for dev
gcloud config set project tf-driftwood-dev # for staging  
gcloud config set project turnkey-charter-428905-c8 # for prod

# View current project
gcloud config get-value project

# List available projects
gcloud projects list
```

## Complete List of gcloud Commands Used

### Authentication & Project Management
```bash
# Check current authentication status
gcloud auth list

# Check current project setting
gcloud config get-value project

# Set project for different environments
gcloud config set project ocean-s-firebase

# Set up application default credentials (browser-based)
gcloud auth application-default login

# Set up application default credentials (no browser)
gcloud auth application-default login --no-browser
```

### Service & API Management
```bash
# List all enabled APIs/services for the project
gcloud services list --enabled --project=ocean-s-firebase

# Enable specific APIs (if needed)
gcloud services enable firebase.googleapis.com --project=ocean-s-firebase
gcloud services enable firestore.googleapis.com --project=ocean-s-firebase
gcloud services enable identitytoolkit.googleapis.com --project=ocean-s-firebase
gcloud services enable cloudresourcemanager.googleapis.com --project=ocean-s-firebase
```

### Billing Management
```bash
# Check billing status for the project
gcloud billing projects describe ocean-s-firebase

# List available billing accounts
gcloud billing accounts list

# Link project to billing account (if needed)
gcloud billing projects link ocean-s-firebase --billing-account=XXXXXX-XXXXXX-XXXXXX
```

### Firestore Database Management
```bash
# List Firestore databases in the project
gcloud firestore databases list --project=ocean-s-firebase

# Describe the default Firestore database
gcloud firestore databases describe "(default)" --project=ocean-s-firebase

# Create Firestore database (if needed)
gcloud firestore databases create --location=asia-southeast1 --project=ocean-s-firebase
```

### Firebase Project Management
```bash
# List Firebase projects
gcloud firebase projects list

# Add Firebase to existing GCP project
gcloud firebase projects addfirebase ocean-s-firebase
```

### IAM & Service Accounts
```bash
# List service accounts
gcloud iam service-accounts list --project=ocean-s-firebase

# Create service account (if needed)
gcloud iam service-accounts create terraform-sa --display-name="Terraform Service Account" --project=ocean-s-firebase

# Grant roles to service account
gcloud projects add-iam-policy-binding ocean-s-firebase \
    --member="serviceAccount:terraform-sa@ocean-s-firebase.iam.gserviceaccount.com" \
    --role="roles/firebase.admin"

gcloud projects add-iam-policy-binding ocean-s-firebase \
    --member="serviceAccount:terraform-sa@ocean-s-firebase.iam.gserviceaccount.com" \
    --role="roles/serviceusage.serviceUsageAdmin"
```

### Diagnostics & Troubleshooting
```bash
# Run gcloud diagnostics
gcloud info --run-diagnostics

# Check gcloud configuration
gcloud config list

# Describe project details
gcloud projects describe ocean-s-firebase

# List project resources
gcloud asset search-all-resources --project=ocean-s-firebase
```

### Environment-specific Project Setup
```bash
# Development
gcloud config set project ocean-s-firebase

# Staging  
gcloud config set project tf-driftwood-dev

# Production
gcloud config set project turnkey-charter-428905-c8
```

## Terraform Commands Used

### Basic Terraform Operations
```bash
# Initialize Terraform (download providers and modules)
terraform init

# Validate configuration syntax
terraform validate

# Format configuration files
terraform fmt

# Generate and display execution plan
terraform plan -var-file="dev.tfvars"

# Generate plan and save to file
terraform plan -var-file="dev.tfvars" -out=dev.tfplan

# Apply configuration changes
terraform apply -var-file="dev.tfvars"

# Apply with auto-approval (use with caution)
terraform apply -var-file="dev.tfvars" -auto-approve

# Show current state
terraform show

# List resources in state
terraform state list

# Show specific resource details
terraform state show google_firebase_project.default

# Destroy all resources (use with extreme caution)
terraform destroy -var-file="dev.tfvars"
```

### Import Existing Resources
```bash
# Import existing Firestore rules release
terraform import -var-file='dev.tfvars' google_firebaserules_release.firestore_release projects/ocean-s-firebase/releases/cloud.firestore

# Import existing Firebase project
terraform import -var-file='dev.tfvars' google_firebase_project.default projects/ocean-s-firebase

# Import existing Firestore database
terraform import -var-file='dev.tfvars' google_firestore_database.default projects/ocean-s-firebase/databases/(default)
```

### Environment-Specific Commands
```bash
# Development environment
source scripts/load-env.sh dev && terraform plan -var-file='dev.tfvars' -out=dev.tfplan
source scripts/load-env.sh dev && terraform apply -var-file='dev.tfvars' -auto-approve

# Staging environment
source scripts/load-env.sh staging && terraform plan -var-file='staging.tfvars' -out=staging.tfplan
source scripts/load-env.sh staging && terraform apply -var-file='staging.tfvars' -auto-approve

# Production environment
source scripts/load-env.sh prod && terraform plan -var-file='prod.tfvars' -out=prod.tfplan
source scripts/load-env.sh prod && terraform apply -var-file='prod.tfvars' -auto-approve
```

### State Management
```bash
# Backup current state
cp terraform.tfstate terraform.tfstate.backup.$(date +%Y%m%d_%H%M%S)

# Remove resource from state (without destroying)
terraform state rm google_firestore_database.default

# Move resource in state
terraform state mv google_firebase_project.default google_firebase_project.renamed

# Refresh state from real infrastructure
terraform refresh -var-file="dev.tfvars"
```

## Usage

### Option 1: Using Composer Scripts (Recommended)

The easiest way to run terraform commands is through the configured composer scripts:

```bash

# Init
cd terraform && terraform init

# Validate configuration
composer tf:validate

# Plan deployments
composer tf:dev:plan      # Plan dev environment
composer tf:staging:plan  # Plan staging environment  
composer tf:prod:plan     # Plan prod environment

# Apply deployments
composer tf:dev:apply     # Deploy dev environment
composer tf:staging:apply # Deploy staging environment
composer tf:prod:apply    # Deploy prod environment

# View outputs
composer tf:dev:output    # Show dev outputs
composer tf:staging:output # Show staging outputs
composer tf:prod:output   # Show prod outputs

# Destroy infrastructure (use with caution)
composer tf:dev:destroy    # Destroy dev environment
composer tf:staging:destroy # Destroy staging environment
composer tf:prod:destroy   # Destroy prod environment
```

### Option 2: Manual Commands

#### 1. Set up variables

Copy the example file and fill in your values:
```bash
cp terraform.tfvars.example terraform.tfvars
```

Edit `terraform.tfvars` with your project details.

#### 2. Load environment variables from .env files

Use the provided script to securely load values from your existing .env files:

For development:
```bash
source scripts/load-env.sh dev
terraform init
terraform plan -var-file="dev.tfvars"
terraform apply -var-file="dev.tfvars"
```

For staging:
```bash
source scripts/load-env.sh staging
terraform plan -var-file="staging.tfvars"
terraform apply -var-file="staging.tfvars"
```

For production:
```bash
source scripts/load-env.sh prod
terraform plan -var-file="prod.tfvars"
terraform apply -var-file="prod.tfvars"
```

#### 3. Alternative: Manual environment variables

You can also manually set environment variables:

```bash
export TF_VAR_project_id="your-project-id"
export TF_VAR_google_oauth_client_id="your-client-id"
export TF_VAR_google_oauth_client_secret="your-client-secret"

terraform plan -var-file="dev.tfvars"
terraform apply -var-file="dev.tfvars"
```

## Firestore Security Rules

The configuration automatically deploys the `firestore.rules` file from the project root. Make sure this file exists and contains your Firestore security rules.

## Outputs

After successful deployment, Terraform will output:
- Firebase configuration details for your web app
- Firestore database name
- Web app ID

These values can be used to update your `.env` files.

## Project IDs by Environment

Based on your `.env` files:
- **Development**: `ocean-s-firebase`
- **Staging**: `tf-driftwood-dev`  
- **Production**: `turnkey-charter-428905-c8`

## Generated Terraform Plans

### Current Plan Status (dev.tfplan)

The latest terraform plan shows the following planned changes:

**Plan Summary:** 1 to add, 0 to change, 1 to destroy

**Resources to be replaced:**
- `google_firebaserules_release.firestore_release` - Will be replaced due to ruleset update

**Reason for replacement:** The Firestore security rules have been updated with a new ruleset, requiring the release to be replaced to activate the new rules.

### Plan Generation Commands
```bash
# Generate and save development plan
source scripts/load-env.sh dev && terraform plan -var-file='dev.tfvars' -out=dev.tfplan

# View saved plan
terraform show dev.tfplan

# Apply the saved plan
terraform apply dev.tfplan

# Generate other environment plans
source scripts/load-env.sh staging && terraform plan -var-file='staging.tfvars' -out=staging.tfplan
source scripts/load-env.sh prod && terraform plan -var-file='prod.tfvars' -out=prod.tfplan
```

### Plan File Benefits
- **Consistency**: Ensures what you review is exactly what gets applied
- **Safety**: Prevents changes from occurring between plan and apply
- **Auditing**: Provides a record of intended changes
- **Automation**: Enables safe CI/CD pipeline implementations

### Auto-Update Capability
The terraform configuration includes lifecycle management that automatically:
- Updates Firestore security rules when `../firestore.rules` changes
- Replaces releases with new rulesets when rules are modified  
- Maintains proper dependencies between Firebase resources
- Handles resource imports for existing infrastructure

### Current Infrastructure State
```bash
# View all managed resources
terraform state list

# Show detailed resource information
terraform show

# Get output values
terraform output
```

**Managed Resources:**
- `google_firebase_project.default` - Firebase project integration
- `google_firebase_web_app.driftwood_app` - Web application configuration  
- `google_firebaserules_ruleset.firestore_rules` - Current security rules
- `google_firebaserules_release.firestore_release` - Active rules deployment
- `data.google_firebase_web_app_config.driftwood_app_config` - App configuration data

### Current Terraform Outputs

The following configuration values are available after successful deployment:

```bash
firebase_config = {
  "api_key" = "AIzaSyDoDYqdF9D-3tp4QhjXPLsDwbLLFQcGKmk"
  "app_id" = "1:959438861084:web:67f02581424528c46100d9"
  "auth_domain" = "ocean-s-firebase.firebaseapp.com"
  "messaging_sender_id" = "959438861084"
  "project_id" = "ocean-s-firebase"
  "storage_bucket" = "ocean-s-firebase.firebasestorage.app"
}
project_id = "ocean-s-firebase"
web_app_id = "1:959438861084:web:67f02581424528c46100d9"
```

These values can be used to configure your frontend application's Firebase integration.

## Summary

This terraform configuration successfully manages:

✅ **Deployed Successfully:**
- Firebase project integration for `ocean-s-firebase`
- Firebase Web App (`driftwood-dev-web`)
- Firestore security rules with comprehensive access control
- Firebase configuration for web applications

⚠️ **Commented Out (Due to Service Account Permissions):**
- API enablement resources (APIs already enabled manually)
- Firestore database creation (database already exists)
- Identity Platform configuration (requires billing)
- Google OAuth provider setup (requires billing)

🔧 **Next Steps:**
1. Enable billing on the Google Cloud project to unlock Identity Platform features
2. Grant additional IAM roles to service account for full automation
3. Configure frontend applications using the terraform outputs
4. Set up CI/CD pipelines using the generated terraform plans

The infrastructure is functional and ready for use with the Driftwood esports platform.