<?php

namespace App\Providers;

use App\Events\JoinEventConfirmed;
use App\Events\TeamMemberCreated;
use App\Events\TeamMemberUpdated;
use App\Listeners\JoinEventConfirmation;
use App\Listeners\TeamMemberCreatedListener;
use App\Listeners\TeamMemberUpdatedListener;
use App\Models\EventDetail;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    // public function register(): void
    // {
    //     $this->app->bind('path.public', function() {
    //         return base_path('../public_html');
    //      });
    // }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            JoinEventConfirmed::class,
            JoinEventConfirmation::class,
        );

        Event::listen(
            TeamMemberUpdated::class,
            TeamMemberUpdatedListener::class
        );

        Event::listen(
            TeamMemberCreated::class,
            TeamMemberCreatedListener::class
        );
        // EventDetail::preventLazyLoading();
        Paginator::useBootstrapFive();
    }
}
