# Driftwood Installation Guide

## Prerequisites
- PHP 8.2
- Composer
- Node.js & npm
- Hostinger
- Docker
- Hostinger MYSQL and local MYSQL with UTC timezone.  
- PHP extensions for GRPC and social auth
- An un-restricted php environment 
- Firebase project must be created for each .env. This allows 3 necessary APIs to be enabled by default
(along with 35 others)
- Don't create your own Google Cloud Service Account. Use firebases's Service Account so you have firestore's policies.
- Add policies: Service Account Admin, Project IAM Admin, Firebase Admin, and Editor

## Admin Credentials
```
Email: admin@driftwood.gg
Password: 12345678
```

## Installation Steps

1. First, install PHP 8.2 and required extensions:

2. Configure Google Cloud SDK:
```bash
# Set the project
gcloud config set project ocean-s-firebase

# Authenticate with Google Cloud
gcloud auth login

# Set application default credentials
gcloud auth application-default login

# Verify configuration
gcloud config get-value project
gcloud auth list
gcloud projects list

```

3. Install project dependencies:
```bash
# Create environment file for Driftwood
cp .env.prod .env


# Create environment file for Oceans
cp .env.staging .env

# Generate application key
php artisan key:generate

# Install Composer dependencies
composer update

# Install npm dependencies
npm i
npm i -g vite
npm run build
```

4. Set up environment:
```bash

# Generate JWT secret
php artisan jwt:secret
```

5. Set up storage:
```bash
# Remove existing storage symlink if exists
rm -rf public/storage
mkdir storage/app/
mkdir storage/app/public
mkdir storage/app/public/images

# Create new storage symlink
php artisan storage:link
ln -s storage/app/public public/storage

# Copy storage assets

cp -r public/assets/images/storage/images/* storage/app/public/images

# Backup
php artisan db:backup --path=database/backups/dev.sql
php artisan db:backup --path=database/backups/prod.sql

# Restore
php artisan db:restore --path=database/backups/dev.sql

```

6. Database setup:
```bash

# Run migrations
php artisan migrate

# Create factory
php artisan generate:factory

# Import SQL data (if needed)
# The data is located in database/migrations/data.sql
```

7. Set up Filament admin:
```bash
# Create admin user
php artisan make:filament-user

# IMPORTANT: After creating the user, manually update the database:
# Set role='admin' for the created user in the users table
```

8. Clear application cache:
```bash
php artisan optimize:clear
```

## Terraform Infrastructure Setup

### Quick Start with Composer Scripts (Recommended)

Configure and deploy Firebase and Google Cloud resources using composer scripts:

```bash
# Initialize Terraform
composer tf:init

# Validate configuration
composer tf:validate

# Format terraform files
composer tf:fmt

# Basic operations for each environment
# Development
composer tf:dev:plan        # Plan changes
composer tf:dev:apply       # Apply changes

# Staging
composer tf:staging:plan    # Plan changes
composer tf:staging:apply   # Apply changes


# Production
composer tf:prod:plan       # Plan changes
composer tf:prod:apply      # Apply changes
```

### Environment Configuration

**Development Environment:**
- Project: `ocean-s-firebase`
- Uses `.env.example` configuration
- Debug mode enabled
- Firebase project for development

**Staging Environment:**
- Project: `tf-driftwood-dev`
- Uses `.env.staging` configuration
- Production-like setup with debug features

**Production Environment:**
- Project: `turnkey-charter-428905-c8`
- Uses `.env.prod` configuration
- Optimized for performance and security

### Infrastructure Components

The terraform configuration manages:
- **Firebase Project**: Automated project setup
- **Firestore Database**: NoSQL database with security rules
- **Firebase Authentication**: Google OAuth integration
- **Firebase Web App**: Application configuration
- **Security Rules**: Comprehensive access control
- **Identity Platform**: Authentication services

## Composer Scripts Reference

### Environment Management
```bash
# Switch to production environment
composer env

# Switch to staging environment  
composer env:staging

# Setup local development environment
composer local
```

### Development & Build Scripts
```bash
# Code formatting with Laravel Pint
composer pint
composer pint:fix

# Static analysis with PHPStan
composer stan

# Code quality insights
composer insights
composer insights:fix

# Clear development caches
composer dev:clear

# Build for production
composer build

# Build for staging
composer staging

# Build frontend assets only
composer npm-build

# Start partial Docker environment
composer docker:partial
```


## Bracket System Abbreviations

The application uses specific abbreviations in the `BracketDataService` class and bracket components:

| Abbreviation | Meaning      | Description                              |
|--------------|--------------|------------------------------------------|
| U            | Upper Bracket| Primary bracket path for winners         |
| L            | Lower Bracket| Secondary bracket path for those who lost once |
| pre          | Pre-finals   | Matches that occur before the final round|
| fin          | Finals       | Final championship matches               |

For example, in the code you might see:
- `U1` - Upper bracket round 1
- `L2` - Lower bracket round 2
- `preFin` - Pre-finals match
- `Fin` - Finals match

## Development Commands

### Creating New Components
```bash
# Create migrations
php artisan make:migration [migration_name]

# Create models
php artisan make:model [ModelName]

# Create seeders
php artisan make:seeder [SeederName]
```

### Database Management
```bash
# Reset database (development only)
php artisan migrate:reset

# Run specific seeder
php artisan db:seed --class=EventSeeder

# Rollback specific migration
php artisan migrate:rollback --path=/database/migrations/[migration_file].php
```

### Additional Setup

1. SweetAlert Integration:
```bash
# Publish SweetAlert assets
php artisan sweetalert:publish

# Add to layout
# Include '@include('sweetalert::alert')' in your master layout
```

2. Mail Configuration:
```bash
# Publish mail configuration
php artisan vendor:publish --tag=laravel-mail
```



3. Log Viewer:
```bash
php artisan vendor:publish --tag=log-viewer-assets --force
```

## Debugging
- Use `dd()` for debugging JSON requests instead of Postman
- Check storage permissions if file-related operations fail
- Verify database role settings for admin users
- When working with brackets, ensure proper abbreviation usage in the BracketDataService