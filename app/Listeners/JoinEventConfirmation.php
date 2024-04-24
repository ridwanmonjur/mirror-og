<?php

namespace App\Listeners;

use App\Events\JoinEventConfirmed;
use App\Notifications\NewNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use ReflectionObject;
use ReflectionProperty;
use Throwable;

class JoinEventConfirmation
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JoinEventConfirmed $event): void
    {
        // $user = $event->user;
        Log::error('Join event ================>');
        Log::error($event->memberList); 
        Log::error($event->organizerList); 
        Log::error($event->memberNotification); 
        Log::error($event->organizerNotificatio); 
        Notification::send($event->memberList, new NewNotification($event->memberNotification));
        Notification::send($event->organizerList, new NewNotification($event->organizerNotificatio));
        Log::error('Join event ================>');

        // Blade here
        //  {{ $notification->data['message'] }}
    }

    public function failed(JoinEventConfirmed $event, Throwable $exception): void
    {
        Log::error('User logged in');
        Log::error($exception);
    }

   
}
