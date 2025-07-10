<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class WithdrawalCsvExportMail extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3;

    public $userName;
    public $downloadLink;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userName, string $downloadLink)
    {
        $this->userName = $userName;
        $this->downloadLink = $downloadLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Withdrawal CSV Export Ready - Driftwood',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->view('Email.withdrawal-export')->with([
            'userName' => $this->userName,
            'downloadLink' => $this->downloadLink,
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
