<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;

class EventJoinMail extends Mailable
{
    use Queueable, SerializesModels;

    public $body;

    /**
     * Create a new message instance.
     *
     * @param array $body
     * @return void
     */
    public function __construct(array $body)
    {
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    
    public function build()
    {
        return $this->replyTo(env('MAIL_CC_ADDRESS'))
            ->subject($this->body['subject'] ?? 'A subject')
            ->view('Email.event-joined')
            ->with([
                'team' => $this->body['team'],
                'bannerPath' => $this->body['banner'],
                'actionName' => $this->body['links'][0]['name'],
                'actionUrl' => $this->body['links'][0]['url'],
                'text' => $this->body['text'],  
            ]);
    }
}
