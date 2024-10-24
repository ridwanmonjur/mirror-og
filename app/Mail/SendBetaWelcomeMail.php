<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendBetaWelcomeMail extends Mailable  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    private $email;
    private $password;
    public $tries = 3; 

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

        /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Driftwood\'s Closed Beta!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->view('Email.sendBetaWelcome')->with([
            'password' => $this->password,
            'email' => $this->email,
        ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
  
}
