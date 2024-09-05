<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventJoinNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function build()
    {
        return $this->markdown('Email.event_joined')
            ->replyTo('mjrrdn@gmail.com')
            ->subject($this->body['subject'] ?? 'A subject')
            ->with([
                'greeting' => "Hello, there from us, Ocean's Gaming!",
                'body' => $this->body['text'],
                'actionText' => $this->body['links'][0]['name'] ?? null,
                'actionUrl' => $this->body['links'][0]['url'] ?? null,
                'salutation' => "Regards, \n Ocean's Gaming",
                'additionalLines' => ['Thank you for choosing us!'],
            ]);
    }
}
