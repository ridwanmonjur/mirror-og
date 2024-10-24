<?php

namespace App\Listeners;

use App\Events\JoinEventSignuped;
use App\Models\ActivityLogs;
use App\Notifications\EventJoinNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;


class JoinEventSignupListener implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = config('constants.QUEUE_TRIES'); 

    /**
     * 
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(JoinEventSignuped $event): void
    {
        ActivityLogs::insert($event->allEventLogs);
        Notification::send($event->memberList, new EventJoinNotification($event->memberNotification));
        Notification::send($event->organizerList, new EventJoinNotification($event->organizerNotification));
    }

    public function failed(): void
    {
    }
}
