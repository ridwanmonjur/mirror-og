<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ErrorCommandMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $className;

    public string $errorMessage;

    public string $errorDate;

    public string $errorTime;

    public string $stackTrace;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $className,
        string $errorMessage,
        string $errorDate,
        string $errorTime,
        string $stackTrace
    ) {
        $this->className = $className;
        $this->errorMessage = $errorMessage;
        $this->errorDate = $errorDate;
        $this->errorTime = $errorTime;
        $this->stackTrace = $stackTrace;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "First Error Alert: {$this->className} Command Failed - {$this->errorDate}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Email.command-error',
            with: [
                'class_name' => $this->className,
                'error_message' => $this->errorMessage,
                'error_date' => $this->errorDate,
                'error_time' => $this->errorTime,
                'stack_trace' => $this->stackTrace,
            ]
        );
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
