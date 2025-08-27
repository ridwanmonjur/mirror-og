# Variables for Driftwood Terraform configuration
variable "project_id" {
  description = "Google Cloud Project ID"
  type        = string
}

variable "project_name" {
  description = "Project name for resource naming"
  type        = string
  default     = "driftwood"
}

variable "environment" {
  description = "Environment (dev, staging, prod)"
  type        = string
  
  validation {
    condition     = contains(["dev", "staging", "prod"], var.environment)
    error_message = "Environment must be dev, staging, or prod."
  }
}

variable "region" {
  description = "Google Cloud region"
  type        = string
  default     = "asia-southeast1"
}

variable "firestore_location" {
  description = "Firestore database location"
  type        = string
  default     = "asia-southeast1"
}

variable "billing_account_id" {
  description = "Google Cloud Billing Account ID"
  type        = string
  default     = ""
}




variable "force_composer_update" {
  description = "Force composer update even if files haven't changed"
  type        = bool
  default     = false
}

variable "force_npm_build" {
  description = "Force npm build even if files haven't changed"
  type        = bool
  default     = false
}

variable "skip_identity_platform" {
  description = "Skip creating Identity Platform config if already exists"
  type        = bool
  default     = false
}

variable "recaptcha_site_key" {
  description = "reCAPTCHA Enterprise site key for App Check (production)"
  type        = string
  default     = ""
  sensitive   = true
}

variable "debug_token" {
  description = "Firebase App Check debug token for development"
  type        = string
  default     = ""
  sensitive   = true
}

variable "enforce_app_check" {
  description = "Enforce App Check for all environments (default: only prod)"
  type        = bool
  default     = false
}

variable "auth_service_secret_key" {
  description = "Secret key for JWT token signing in auth service"
  type        = string
  default     = ""
  sensitive   = true
}

variable "terraform_service_account" {
  description = "Service account email used by Terraform and Laravel app"
  type        = string
}