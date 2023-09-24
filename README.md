# Splash

Depends on the team description 

# Admin Login for Test
```bash

Email: admin@example.com

password: 12345678

```

### Local Installation


```bash
# Composer dependencies
composer update

# Create a copy of your .env file
cp .env.example .env

# Generate an app encryption key
php artisan key:generate
```

### Migrate


```bash
# Seed
php artisan db:seed

# Run this one
php artisan db:seed --class=EventSeeder

# Changes
php artisan make:migration add_eventGroupStructure_to_events

# Development only reset database
php artisan migrate:reset

```




