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
        ActivityLogs::insert($event->allEventLogs);
        Notification::send($event->memberList, new EventJoinNotification($event->memberNotification));
        Notification::send($event->organizerList, new EventJoinNotification($event->organizerNotification));
    }

    public function failed(JoinEventConfirmed $event, Throwable $exception): void
    {
        Log::error($exception);
        throw new \Exception($exception->getMessage());
    }
}
