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


### Admin Filament Installation
```bash
# Create Admin Credentials
php artisan make:filament-user

#update Images
php artisan storage:link
```
