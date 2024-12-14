<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use packages\domain\model\email\SendEmailDto;

class EmailHandler extends Mailable
{
    use Queueable, SerializesModels;

    private SendEmailDto $sendEmailDto;

    /**
     * Create a new message instance.
     */
    public function __construct(SendEmailDto $sendEmailDto)
    {   
        $this->sendEmailDto = $sendEmailDto;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->sendEmailDto->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->sendEmailDto->mailFilePath,
            with: $this->sendEmailDto->templateVariables,
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

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->from($this->sendEmailDto->fromAddress, $this->sendEmailDto->systemName);
    }
}
