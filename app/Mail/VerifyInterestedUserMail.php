<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyInterestedUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $email;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct(string $email, string $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Confirm your Email for Driftwood's Closed Beta!",
        );
    }
    public function build()
    {
        return $this->view('Email.verifyInterestedUser')->with([
            'token' => $this->token,
            'email' => $this->email,
        ]);
    }
}
