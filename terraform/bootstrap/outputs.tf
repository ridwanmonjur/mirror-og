# Outputs for Terraform state storage

output "bucket_name" {
  description = "Name of the Terraform state storage bucket"
  value       = google_storage_bucket.terraform_state.name
}

output "bucket_url" {
  description = "URL of the Terraform state storage bucket"
  value       = google_storage_bucket.terraform_state.url
}

output "backend_config" {
  description = "Backend configuration for Terraform"
  value = {
    bucket = google_storage_bucket.terraform_state.name
    prefix = "terraform/state"
  }
}

output "backend_configuration_block" {
  description = "Complete backend configuration block for copying"
  value = <<-EOT
  backend "gcs" {
    bucket = "${google_storage_bucket.terraform_state.name}"
    prefix = "terraform/state"
  }
  EOT
}