# Driftwood Installation Guide

## Prerequisites
- PHP 8.2
- Composer
- Node.js & npm
- Apache2
- SQLite3

## Admin Credentials
```
Email: admin@example.com
Password: 12345678
```

## Installation Steps

1. First, install PHP 8.2 and required extensions:
```bash
sudo apt install php8.2-sqlite3
service apache2 restart
```

2. Install project dependencies:
```bash
# Install Composer dependencies
composer update

# Install npm dependencies
npm i
npm i -g vite
npm run build
```

3. Set up environment:
```bash
# Create environment file
cp .env.prod .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret
```

4. Set up storage:
```bash
# Remove existing storage symlink if exists
rm -rf public/storage

# Create new storage symlink
php artisan storage:link

# Copy storage assets
cp -r public/assets/images/storage/* public/storage/
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

