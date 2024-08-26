<?php

namespace App\Listeners;

use App\Events\JoinEventConfirmed;
use App\Models\ActivityLogs;
use App\Notifications\EventJoinNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class JoinEventConfirmation implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
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
        Log::info($event->organizerNotification);
        // TODO: CREATE ACTIVITY
        ActivityLogs::insert($event->allEventLogs);
        Notification::send($event->memberList, new EventJoinNotification($event->memberNotification));
        Notification::send($event->organizerList, new EventJoinNotification($event->organizerNotification));
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
