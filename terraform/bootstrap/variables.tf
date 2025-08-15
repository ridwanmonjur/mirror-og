# Variables for Terraform state storage bootstrap

variable "project_id" {
  description = "Google Cloud Project ID"
  type        = string
}

variable "project_name" {
  description = "Project name for resource naming"
  type        = string
  default     = "driftwood"
}

variable "region" {
  description = "Google Cloud region"
  type        = string
  default     = "asia-southeast1"
}

variable "bucket_location" {
  description = "Location for the Terraform state storage bucket"
  type        = string
  default     = "ASIA"
}

variable "terraform_service_account_email" {
  description = "Email of the service account used by Terraform (optional)"
  type        = string
  default     = ""
}

variable "bucket_name" {
  description = "Name for the Terraform state bucket (from .env)"
  type        = string
}

variable "environment" {
  description = "Environment (dev, staging, prod)"
  type        = string
  
  validation {
    condition     = contains(["dev", "staging", "prod"], var.environment)
    error_message = "Environment must be dev, staging, or prod."
  }
}