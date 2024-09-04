<?php

namespace App\Notifications;

use App\Mail\EventJoinNotificationMail;
use Illuminate\Bus\Queueable;
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
        $body
    ) {
        Log::info('Notification join event ================>');
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
        return (new EventJoinNotificationMail($this->body));


            // php artisan make:mail EventJoinNotificationMail --markdown=Email.event-joined.blade.php

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
