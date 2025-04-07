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

## Admin Credentials
```
Email: admin@example.com
Password: 12345678
```

## Installation Steps

1. First, install PHP 8.2 and required extensions:

2. Install project dependencies:
```bash
# Create environment file for Driftwood
cp .env.prod .env

# Create environment file for Oceans
cp .env.staging .env

# Install Composer dependencies
composer update

# Install npm dependencies
npm i
npm i -g vite
npm run build
```

3. Set up environment:
```bash

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret
```

4. Set up storage:
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
```

5. Database setup:
```bash
# Run migrations
php artisan migrate

# Import SQL data (if needed)
# The data is located in database/migrations/data.sql
```

6. Set up Filament admin:
```bash
# Create admin user
php artisan make:filament-user

# IMPORTANT: After creating the user, manually update the database:
# Set role='admin' for the created user in the users table
```

7. Clear application cache:
```bash
php artisan optimize:clear
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

3. Horizon Setup:
```bash
php artisan horizon:install
```

4. Log Viewer:
```bash
php artisan vendor:publish --tag=log-viewer-assets --force
```

## Debugging
- Use `dd()` for debugging JSON requests instead of Postman
- Check storage permissions if file-related operations fail
- Verify database role settings for admin users
- When working with brackets, ensure proper abbreviation usage in the BracketDataService