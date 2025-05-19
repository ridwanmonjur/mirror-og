<?php

namespace App\Providers;

use App\Events\JoinEventSignuped;
use App\Events\TeamMemberCreated;
use App\Events\TeamMemberUpdated;
use App\Listeners\JoinEventSignupListener;
use App\Listeners\TeamMemberCreatedListener;
use App\Listeners\TeamMemberUpdatedListener;
use App\Services\BracketDataService;
use App\Models\StripePayment;
use App\Models\User;
use App\Services\EventMatchService;
use App\Services\PaymentService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentService::class, function ($app) {
            return new PaymentService($app->make(StripePayment::class));
        });

        $this->app->bind(EventMatchService::class, function ($app) {
            return new EventMatchService($app->make(BracketDataService::class));
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $allowedEmails = [
            'mjrrdn@gmail.com',
            'mjrrdnasm@gmail.com',
            'ridwan@driftwood.gg',
            'zach@driftwood.gg',
            'admin@driftwood.gg',
            'leigh@driftwood.gg',
            config('services.mail_address'),
            'justforus@driftwood.gg'
        ];

        Gate::define('viewPulse', function (?User $user) use ($allowedEmails) {
           
            
            return $user && in_array($user->email, $allowedEmails);
        });

        View::share('USER_ACCESS', config('constants.USER_ACCESS'));
        LogViewer::auth(function ($request) use ($allowedEmails) {
            if (app()->environment('production')) {
                return $request->user()
                && in_array($request->user()->email, $allowedEmails);
            }

            return true;
        });

        Event::listen(
            JoinEventSignuped::class,
            JoinEventSignupListener::class,
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
