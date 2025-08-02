<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class EventSignupMail extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $body;

    /**
     * Create a new message instance.
     *
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
        return $this->replyTo(config('services.mail_address'))
            ->subject('The team has completed signup!')
            ->view('Email.event-signuped')
            ->with([
                'team' => $this->body['team'],
                'actionName' => 'Login and complete registration!',
                'actionUrl' => $this->body['link'],
                'text' => $this->body['text'],
            ]);
    }
}
