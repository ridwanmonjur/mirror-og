<?php

namespace App\Notifications;

use App\Mail\EventConfirmMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventConfirmNotification extends Notification
{
    use Queueable;
    public $body;

    /**
     * Create a new notification instance.
     */
    public function __construct($body)
    {
        $this->body = $body;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new EventConfirmMail($this->body))->to($notifiable->email);
    }

    public function toDatabase($notifiable)
    {
        return [
            'data' => $this->body['textFirstPart'] 
                . $this->body['text'],
            'title' => $this->body['subject'],
            'links' => $this->body['links'],
        ];
    }

}
