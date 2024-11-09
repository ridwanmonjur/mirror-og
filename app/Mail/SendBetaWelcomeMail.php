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

class SendBetaWelcomeMail extends Mailable  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $tries = 3; 

    public function __construct(User $user)
    {
        $this->user = $user;
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
            'password' => $this->user->password,
            'email' => $this->user->email,
            'username' => $this->user->name,
        ]);
    }

}
