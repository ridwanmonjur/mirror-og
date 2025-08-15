# Bootstrap Terraform configuration for Driftwood state storage
# This must be run first to create the storage bucket for remote state

terraform {
  required_version = ">= 1.0"
  required_providers {
    google = {
      source  = "hashicorp/google"
      version = "~> 5.0"
    }
    random = {
      source  = "hashicorp/random"
      version = "~> 3.0"
    }
    local = {
      source  = "hashicorp/local"
      version = "~> 2.0"
    }
    null = {
      source  = "hashicorp/null"
      version = "~> 3.0"
    }
  }
}

# Provider configuration
provider "google" {
  project = var.project_id
  region  = var.region
}

# Google Cloud Storage bucket for Terraform state
resource "google_storage_bucket" "terraform_state" {
  name          = var.bucket_name
  location      = var.bucket_location
  force_destroy = false

  # Enable versioning for state file recovery
  versioning {
    enabled = true
  }

  # Prevent public access
  public_access_prevention = "enforced"

  # Enable uniform bucket-level access
  uniform_bucket_level_access = true

  # Lifecycle management
  lifecycle_rule {
    condition {
      age = 90
    }
    action {
      type = "Delete"
    }
  }

  lifecycle_rule {
    condition {
      age                = 30
      with_state         = "ARCHIVED"
    }
    action {
      type = "Delete"
    }
  }

  lifecycle_rule {
    condition {
      age = 7
    }
    action {
      type          = "SetStorageClass"
      storage_class = "ARCHIVE"
    }
  }

  # Labels for organization
  labels = {
    purpose     = "terraform-state"
    project     = var.project_name
    managed-by  = "terraform"
  }
}

# Enable APIs required for storage
resource "google_project_service" "storage_api" {
  service = "storage.googleapis.com"
  project = var.project_id

  disable_dependent_services = false
}

# IAM binding for Terraform service account (if using one)
resource "google_storage_bucket_iam_member" "terraform_state_admin" {
  bucket = google_storage_bucket.terraform_state.name
  role   = "roles/storage.admin"
  member = "serviceAccount:${var.terraform_service_account_email}"

  depends_on = [google_storage_bucket.terraform_state]
}

# Read current .env files based on environment
data "local_file" "current_env_dev" {
  count    = var.environment == "dev" ? 1 : 0
  filename = "../../.env"
}

data "local_file" "current_env_example" {
  count    = var.environment == "dev" ? 1 : 0
  filename = "../../.env.example"
}

data "local_file" "current_env_staging" {
  count    = var.environment == "staging" ? 1 : 0
  filename = "../../.env.staging"
}

data "local_file" "current_env_prod" {
  count    = var.environment == "prod" ? 1 : 0
  filename = "../../.env.prod"
}

# Update .env files with the actual bucket name
locals {
  env_files_to_update = var.environment == "dev" ? {
    "../../.env" = data.local_file.current_env_dev[0].content,
    "../../.env.example" = data.local_file.current_env_example[0].content
  } : var.environment == "staging" ? {
    "../../.env.staging" = data.local_file.current_env_staging[0].content
  } : var.environment == "prod" ? {
    "../../.env.prod" = data.local_file.current_env_prod[0].content
  } : {}

  update_env_content = { 
    for env_file, env_content in local.env_files_to_update : env_file => join("\n", [
      for line in split("\n", env_content) : 
      startswith(line, "TERRAFORM_STATE_BUCKET=") ? "TERRAFORM_STATE_BUCKET=${google_storage_bucket.terraform_state.name}" :
      line
    ])
  }
}

# Backup original .env files before making changes (only once)
resource "local_file" "env_backups" {
  for_each = local.env_files_to_update
  
  content  = each.value
  filename = "${each.key}.terraform.backup"
  
  # Only create backup if it doesn't exist
  lifecycle {
    ignore_changes = [content]
  }
}

# Update .env files using null_resource to avoid destruction issues
resource "null_resource" "update_env_files" {
  for_each = local.update_env_content
  
  # Triggers when bucket config changes
  triggers = {
    bucket_name  = google_storage_bucket.terraform_state.name
    content_hash = md5(each.value)
  }

  # Update .env files only on apply/refresh, not on destroy
  provisioner "local-exec" {
    when    = create
    command = <<-EOT
      cat > "${each.key}" << 'EOF'
${each.value}
EOF
    EOT
  }
  
  depends_on = [
    google_storage_bucket.terraform_state,
    local_file.env_backups
  ]
}