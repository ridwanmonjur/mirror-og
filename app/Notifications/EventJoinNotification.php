<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class EventJoinNotification extends Notification
{
    use Queueable;

    public $body;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        $body
    ) {
        $this->body = $body;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->replyTo('mjrrdn@gmail.com')
            ->success()
            ->greeting("Welcome and greetings.")
            ->salutation("Regards, \n Driftwood")
            ->subject($this->body['subject'] ?? 'A subject')
            ->action($this->body['links'][0]['name'], $this->body['links'][0]['url'])
            ->line(new HtmlString ($this->body['text']));
    }

    public function toDatabase($notifiable)
    {
        // modify here
        return [
            'data' => $this->body['text'],
            'title' => $this->body['subject'],
            'links' => $this->body['links'],
            // Add any additional data you want to store in the database
        ];
    }

    // public function toArray($notifiable)
    // {
    //     // modify here
    //     return [
    //         'data' => $this->body['text'],
    //         'subject' => $this->body['subject'],
    //         'links' =>   $this->body['links']
    //         // Add any additional data you want to store in the database
    //     ];
    // }

    // public function markAsRead(){
    //     Auth::user()->unreadNotifications->markAsRead();
    //     return redirect()->back();
    // }
}
