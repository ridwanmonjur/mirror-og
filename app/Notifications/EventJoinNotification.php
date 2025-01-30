<?php

namespace App\Notifications;

use App\Mail\EventJoinMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class EventJoinNotification extends Notification
{
    use Queueable;

    public $body;

    public function __construct(
        $body
    ) {
        $this->body = $body;
    }

    public function via($notifiable)
    {
        return [ 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new EventJoinMail($this->body))->to($notifiable->email);
    }

}
