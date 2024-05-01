<?php

namespace App\Listeners;

use App\Events\JoinEventConfirmed;
use App\Models\ActivityLogs;
use App\Notifications\EventJoinNotification;
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
        Log::info('Join event ================>');
        Log::error($event->memberList); 
        Log::info($event->organizerList); 
        Log::info($event->memberNotification); 
        Log::info($event->organizerNotificatio); 
        // TODO: CREATE ACTIVITY
        ActivityLogs::insert($event->allEventLogs);
        Notification::send($event->memberList, new EventJoinNotification($event->memberNotification));
        Notification::send($event->organizerList, new EventJoinNotification($event->organizerNotificatio));
        Log::info('Join event ================>');

        // Blade here
        //  {{ $notification->data['message'] }}
    }

    public function failed(JoinEventConfirmed $event, Throwable $exception): void
    {
        Log::error('User logged in');
        Log::error($exception);
    }

   
}
