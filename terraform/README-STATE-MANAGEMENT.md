# Terraform State Management

This document explains how to set up and use remote state storage for the Driftwood Terraform infrastructure.

## Overview

Remote state storage allows multiple team members to collaborate on infrastructure changes without conflicts. The state is stored in Google Cloud Storage with versioning and lifecycle management.

## Initial Setup (Bootstrap)

### 1. Bootstrap the State Storage

Before using remote state, you need to create the storage bucket:

```bash
# For development environment
composer tf:bootstrap:dev:init
composer tf:bootstrap:dev:plan
composer tf:bootstrap:dev:apply

# For staging environment  
composer tf:bootstrap:staging:plan
composer tf:bootstrap:staging:apply

# For production environment
composer tf:bootstrap:prod:plan
composer tf:bootstrap:prod:apply
```

### 2. Get the Bucket Name

After bootstrap, get the bucket name:

```bash
composer tf:bootstrap:output
```

This will output something like:
```
bucket_name = "ocean-s-firebase-terraform-state-abc12345"
backend_configuration_block = <<EOT
  backend "gcs" {
    bucket = "ocean-s-firebase-terraform-state-abc12345"
    prefix = "terraform/state"
  }
EOT
```

### 3. Update Backend Configuration

Update the appropriate backend file with the bucket name:

- **Development**: `terraform/backend-dev.tf`
- **Staging**: `terraform/backend-staging.tf`  
- **Production**: `terraform/backend-prod.tf`

Uncomment and update the backend configuration:

```hcl
terraform {
  backend "gcs" {
    bucket = "ocean-s-firebase-terraform-state-abc12345"
    prefix = "terraform/state/dev"  # Use dev/staging/prod as appropriate
  }
}
```

### 4. Initialize with Remote State

```bash
# Reinitialize Terraform to use remote backend
composer tf:init

# Terraform will ask if you want to copy existing state to the new backend
# Answer "yes" if you have existing local state
```

## Environment Structure

Each environment uses a separate state file prefix:

- **Development**: `terraform/state/dev/default.tfstate`
- **Staging**: `terraform/state/staging/default.tfstate`
- **Production**: `terraform/state/prod/default.tfstate`

## Storage Bucket Features

### Versioning
- All state file versions are preserved
- Allows rollback to previous states
- Automatic recovery from corruption

### Lifecycle Management
- Files older than 90 days are deleted
- Files older than 7 days are moved to Archive storage
- Archived files older than 30 days are deleted

### Security
- Public access is blocked
- Uniform bucket-level access enabled
- Optional IAM binding for service accounts

## Team Collaboration

### Best Practices

1. **Always use remote state** - Never work with local state files
2. **Use terraform plan** - Always review changes before applying
3. **Use plan files** - Save plans and apply them for reproducibility
4. **Coordinate deployments** - Only one person should deploy at a time per environment

### Recommended Workflow

```bash
# 1. Plan and save
composer tf:dev:plan-save

# 2. Review the plan file
cat terraform/dev.tfplan

# 3. Apply the saved plan
composer tf:dev:apply-plan
```

### State Locking

Google Cloud Storage provides automatic state locking to prevent concurrent modifications.

## Troubleshooting

### State Lock Issues

If state is locked due to interrupted operations:

```bash
# Force unlock (use with caution)
cd terraform
terraform force-unlock LOCK_ID
```

### Backend Initialization Issues

If backend initialization fails:

```bash
# Remove backend and reinitialize
rm -rf terraform/.terraform
composer tf:init
```

### Missing State File

If state file is missing or corrupted:

1. Check bucket versions in Google Cloud Console
2. Restore from a previous version
3. Contact team members for backup state files

## Security Considerations

### Service Account Access

If using service accounts, ensure they have proper IAM roles:

```hcl
# In bootstrap/main.tf
resource "google_storage_bucket_iam_member" "terraform_state_admin" {
  bucket = google_storage_bucket.terraform_state.name
  role   = "roles/storage.admin"
  member = "serviceAccount:terraform@project.iam.gserviceaccount.com"
}
```

### Sensitive Data

State files contain sensitive information:
- Keep bucket access restricted
- Use sensitive = true for sensitive variables
- Monitor bucket access logs

## Monitoring

### State File Size

Monitor state file size growth:
- Large states can indicate resource drift
- Consider splitting large configurations

### Access Patterns

Monitor who is accessing state files:
- Enable Cloud Audit Logs
- Review access patterns regularly

## Migration

### From Local to Remote State

If migrating existing infrastructure:

1. Bootstrap storage bucket
2. Update backend configuration
3. Run `terraform init`
4. Confirm state migration when prompted

### Between Environments

To copy state between environments:

```bash
# Export state from source
terraform state pull > source.tfstate

# Import to target (use with extreme caution)
terraform state push source.tfstate
```

## Commands Reference

### Bootstrap Commands
```bash
composer tf:bootstrap:dev:init      # Initialize bootstrap
composer tf:bootstrap:dev:plan      # Plan bootstrap changes
composer tf:bootstrap:dev:apply     # Apply bootstrap changes
composer tf:bootstrap:output        # Show bootstrap outputs
```

### Main Infrastructure Commands
```bash
composer tf:init                    # Initialize Terraform
composer tf:dev:plan-save          # Plan and save to file
composer tf:dev:apply-plan         # Apply saved plan
composer tf:state:list             # List resources in state
```

## Support

For issues with state management:
1. Check this documentation
2. Review Terraform logs
3. Contact the infrastructure team
4. Check Google Cloud Console for bucket status