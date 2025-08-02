<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('firebase', function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(config('credentials.file'));

            return $factory->createAuth();
        });

        $this->app->singleton('firebase.firestore', function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(config('credentials.file'));

            return $factory->createFirestore();
        });
    }
}
