<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendBetaWelcomeMail extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $passText;

    public $tries = 3;

    public function __construct(User $user, string $passText)
    {
        $this->user = $user;
        $this->passText = $passText;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to OW Gaming\'s Closed Beta!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->view('Email.sendBetaWelcome')->with([
            'password' => $this->passText,
            'email' => $this->user->email,
            'username' => $this->user->name,
        ]);
    }
}
