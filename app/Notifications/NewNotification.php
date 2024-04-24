<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewNotification extends Notification
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
        Log::error('Notification join event ================>');
        Log::error($body); 
        Log::error('Notification join event ================>');
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
            ->line($this->body['text'])
            ->action('Notification Action', url('/'));
    }

    public function toDatabase($notifiable)
    {
        // modify here
        return [
            'title' => $this->body['title'] ?? 'Title',  
            'data' => $this->body['text'],
            
            // Add any additional data you want to store in the database
        ];
    }


    // public function markAsRead(){
    //     Auth::user()->unreadNotifications->markAsRead();
    //     return redirect()->back();
    // }
    

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->body['title'] ?? 'Title',  
            'data' => $this->body['text'],
            
            // Add any additional data you want to store in the database
        ];
    }
}
