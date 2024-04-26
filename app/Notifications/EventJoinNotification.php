<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class EventJoinNotification extends Notification
{
    use Queueable;

    public $body;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        // decide parameters
        // get table parameters
        // get mail parameters
        $body 
    )
    {
        Log::info('Notification join event ================>');
        Log::info($body); 
        Log::info('Notification join event ================>');
        $this->body = $body;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        // customize this notification
        // Mail::to($order->customer_email)->send(new OrderConfirmation($order));
        return (new MailMessage)
            ->mailer("Ocean's Gaming")
            ->replyTo('oceansgamingmy@gmail.com')
            ->success()
            ->greeting("Hello, there from us, Ocean's Gaming!")
            ->salutation("Regards, \n Ocean's Gaming")
            ->subject($this->body['subject'] ?? 'A subject')
            ->action($this->body['links'][0]['name'], $this->body['links'][0]['url'])
            ->line($this->body['text']);
    }

    public function toDatabase($notifiable)
    {
        // modify here
        return [
            'data' => $this->body['text'],
            'title' => $this->body['subject'],
            'links' =>   $this->body['links']
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
