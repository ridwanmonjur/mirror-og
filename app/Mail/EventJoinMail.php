<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class EventJoinMail extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3; 

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
