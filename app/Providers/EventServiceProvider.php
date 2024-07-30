<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
  
    public function boot(): void
    {
        //
    }

    
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
