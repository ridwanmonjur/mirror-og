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


# making migration
php artisan make:migration create_oganizer_table
php artisan make:migration create_participant_table
php artisan make:migration add_eventGroupStructure_to_events
php artisan make:migration add_tokens_to_users

# edit tables in those files, then run:
php artisan migrate
php artisan make:model Organizer
php artisan make:model Participant

# Changes Development only reset database
php artisan migrate:reset

# Seed
php artisan make:seeder EventSeeder
# Run this one
php artisan db:seed --class=EventSeeder

```

```bash

# docs
https://realrashid.github.io/sweet-alert/config

# Include 'sweetalert::alert' in master layout
@include('sweetalert::alert')
# and run the below command to publish the package assets.
php artisan sweetalert:publish

bash```
php artisan make:migration add_fkkeys_to_organizers
php artisan make:migration add_fkkeys_to_participants

# http://localhost:8000/organizerSignin
# http://localhost:8000/organizerSignup

php artisan make:middleware CheckPermission

http://localhost:8000/participant/signin
http://localhost:8000/participant/signup
http://localhost:8000/organizer/siginin
http://localhost:8000/organizer/signup


http://localhost:8000/authenticated
http://localhost:8000/showNoAuth
http://localhost:8000/permissions-admin-participant
http://localhost:8000/permissions-admin-organizer
```
