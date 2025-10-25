#!/bin/bash
# Firebase authentication script for Docker Terraform container
# This script authenticates Firebase CLI using the service account credentials

set -e

echo "=== Firebase Authentication Setup ==="

# Check if GOOGLE_APPLICATION_CREDENTIALS is set
if [ -z "$GOOGLE_APPLICATION_CREDENTIALS" ]; then
    echo "ERROR: GOOGLE_APPLICATION_CREDENTIALS environment variable not set"
    exit 1
fi

# Check if service account file exists
if [ ! -f "$GOOGLE_APPLICATION_CREDENTIALS" ]; then
    echo "ERROR: Service account file not found at: $GOOGLE_APPLICATION_CREDENTIALS"
    exit 1
fi

echo "Using service account: $GOOGLE_APPLICATION_CREDENTIALS"

# Activate gcloud service account
echo "Activating gcloud service account..."
gcloud auth activate-service-account --key-file="$GOOGLE_APPLICATION_CREDENTIALS"

# Get the project ID from the service account
PROJECT_ID=$(gcloud config get-value project 2>/dev/null || echo "")

if [ -z "$PROJECT_ID" ]; then
    # Extract project_id from service account JSON if gcloud config doesn't have it
    PROJECT_ID=$(cat "$GOOGLE_APPLICATION_CREDENTIALS" | grep -o '"project_id"[[:space:]]*:[[:space:]]*"[^"]*"' | cut -d'"' -f4)

    if [ -n "$PROJECT_ID" ]; then
        echo "Setting gcloud project to: $PROJECT_ID"
        gcloud config set project "$PROJECT_ID"
    else
        echo "ERROR: Could not determine project ID from service account"
        exit 1
    fi
fi

echo "Project ID: $PROJECT_ID"

# Use Firebase CLI with service account (no interactive login needed)
# Firebase CLI will automatically use GOOGLE_APPLICATION_CREDENTIALS
echo "Firebase CLI is configured to use service account credentials"
echo "The following commands will now work:"
echo "  - firebase use <project-id>"
echo "  - firebase deploy --only firestore"

# Test Firebase authentication
echo ""
echo "Testing Firebase authentication..."
if firebase projects:list --json >/dev/null 2>&1; then
    echo "✅ Firebase authentication successful!"
    echo ""
    echo "Available projects:"
    firebase projects:list
else
    echo "⚠️  Firebase CLI authentication may have issues"
    echo "The service account must have the following roles:"
    echo "  - Firebase Admin"
    echo "  - Cloud Datastore Owner (for Firestore)"
    echo ""
    echo "Add these roles in GCP Console:"
    echo "https://console.cloud.google.com/iam-admin/iam?project=$PROJECT_ID"
fi

echo ""
echo "=== Authentication setup complete ==="
