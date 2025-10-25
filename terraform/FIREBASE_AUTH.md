# Firebase Authentication in Docker Terraform

This guide explains how Firebase authentication works in the Docker Terraform setup for deploying Firestore rules.

## Overview

The Terraform configuration uses Firebase CLI to deploy Firestore rules. Firebase CLI requires authentication, which is handled automatically using service account credentials.

## Authentication Methods

### Method 1: Service Account (Automatic - Recommended)

The Docker container is pre-configured to use service account authentication:

1. **Service account file**: `firebase/gcloud_service_account_dev.json` is mounted into the container
2. **Environment variable**: `GOOGLE_APPLICATION_CREDENTIALS` points to the service account file
3. **Automatic authentication**: Both `gcloud` and Firebase CLI use this service account automatically

**No manual login required!** Just run your Terraform commands:

```bash
# Using composer shortcuts
composer tf:dev:apply
composer tf:staging:apply
composer tf:prod:apply
```

### Method 2: Manual Firebase Login (Development Only)

If you need to use a different Google account for testing:

1. **Start the container**:
   ```bash
   docker-compose -f docker-compose.terraform.yml run terraform bash
   ```

2. **Inside the container, run the helper script**:
   ```bash
   firebase-login
   ```

   Or use the full path:
   ```bash
   bash /app/terraform/scripts/firebase-login.sh
   ```

   This will:
   - Activate the service account with gcloud
   - Verify Firebase authentication
   - List available projects

3. **Alternative: Interactive login** (if needed):
   ```bash
   # Authenticate gcloud
   gcloud auth login --no-launch-browser

   # Follow the prompts to authenticate
   ```

## How It Works

### In docker-compose.terraform.yml

```yaml
environment:
  - GOOGLE_APPLICATION_CREDENTIALS=/app/firebase/gcloud_service_account_dev.json
  - FIREBASE_CREDENTIALS=firebase/gcloud_service_account_dev.json

volumes:
  - ./firebase:/app/firebase:ro  # Service account mounted
  - gcloud-config:/root/.config/gcloud  # Persisted auth state
```

### In terraform/main.tf (Line 163-197)

The `null_resource.deploy_firestore_rules` resource:

1. **Authenticates gcloud** using the service account
2. **Creates firebase.json** with Firestore rules configuration
3. **Deploys rules** using `firebase deploy --only firestore`

Firebase CLI automatically uses `GOOGLE_APPLICATION_CREDENTIALS` - no manual login needed!

## Environment-Specific Service Accounts

Switch between environments by changing the service account file:

```yaml
# Development
GOOGLE_APPLICATION_CREDENTIALS=/app/firebase/gcloud_service_account_dev.json

# Staging
GOOGLE_APPLICATION_CREDENTIALS=/app/firebase/gcloud_service_account_staging.json

# Production
GOOGLE_APPLICATION_CREDENTIALS=/app/firebase/gcloud_service_account_prod.json
```

## Required Service Account Permissions

Your service account must have these IAM roles:

- **Firebase Admin** (`roles/firebase.admin`)
- **Cloud Datastore Owner** (`roles/datastore.owner`)
- **Service Account Token Creator** (`roles/iam.serviceAccountTokenCreator`)

Add these roles in GCP Console:
```
https://console.cloud.google.com/iam-admin/iam?project=YOUR_PROJECT_ID
```

## Troubleshooting

### "Permission denied" when deploying rules

**Solution**: Add the required IAM roles to your service account (see above).

### "Could not authenticate"

**Solution**:
1. Verify the service account file exists: `ls -la firebase/gcloud_service_account_dev.json`
2. Check environment variable: `echo $GOOGLE_APPLICATION_CREDENTIALS`
3. Run the helper script: `firebase-login`

### "Project not found"

**Solution**: Ensure the service account belongs to the correct GCP project.

## Testing Authentication

Run this inside the container to test:

```bash
# Test gcloud authentication
gcloud auth list

# Test Firebase authentication
firebase projects:list

# Test Firestore access
gcloud firestore databases describe --database="(default)"
```

## Files Modified

1. **docker-compose.terraform.yml**: Added `FIREBASE_TOKEN_FILE` environment variable
2. **terraform/main.tf**: Added gcloud authentication before Firebase deploy (lines 171-176)
3. **terraform/scripts/firebase-login.sh**: Helper script for manual authentication
4. **terraform/FIREBASE_AUTH.md**: This documentation

## Summary

✅ **Automatic authentication** using service account - no manual login needed!
✅ **Persisted credentials** via Docker volumes
✅ **Environment-specific** service accounts for dev/staging/prod
✅ **Helper script** available for troubleshooting

Just run `composer tf:dev:apply` and Terraform will handle Firebase authentication automatically!
