<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerifyUserMailChange extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $user;

    public $token;

    public $newEmail;

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Change your email address for OW Gaming.',
        );
    }

    public function __construct(User $user, string $token, string $newEmail)
    {
        $this->user = $user;
        $this->token = $token;
        $this->newEmail = $newEmail;
    }

    public function build()
    {
        return $this->view('Email.verifyEmailChange')->with([
            'token' => $this->token,
            'user' => $this->user,
            'newEmai' => $this->newEmail,
        ]);
    }
}
