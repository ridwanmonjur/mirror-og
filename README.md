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
cp .env.prod .env

# Generate an app encryption key
php artisan key:generate

# Install npm using wget 

npm i 
npm i -g vite
npm run build

rm -rf public/storage
php artisan storage:link
php artisan migrate
Raw sql data in database/migrations/data.sql
Copy file from public/assets/images/storage to public/storage (new symlink folder)
npm install selenium-standalone chromedriver -g
sudo apt install openjdk-18-jre 
sudo apt-get --only-upgrade install google-chrome-stable
selenium-standalone install
php vendor/bin/codecept bootstrap
php vendor/bin/codecept generate:cest Acceptance Signin
php vendor/bin/codecept generate:scenarios Acceptance
php vendor/bin/codecept run Acceptance CreateEventCest.php
selenium-standalone start

actor: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: 'http://myappurl.local'
 php vendor/bin/codecept run

# Clear All Caches (Combined)

php artisan optimize:clear


```
### Admin Filament Installation
```bash
# Create Admin Credentials
php artisan make:filament-user
GO TO DATABASE
AND GIVE USER ROLE=ADMIN (compulsory!!!)
#update Images

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
php artisan make:seeder UserSeeder
# Run this one
php artisan db:seed --class=EventSeeder

```

```bash

# docs

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
bash```


bash```
Mail
php artisan vendor:publish --tag=laravel-mail

bash```

Rollback specific migration
bash```
php artisan migrate:rollback --path=/database/migrations/your-specific-migration.php
php artisan migrate
bash```
