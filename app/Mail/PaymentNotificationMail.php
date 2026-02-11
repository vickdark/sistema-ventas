<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PaymentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $messageText;
    public $attachmentPath;
    public $clientEmail;

    /**
     * Create a new message instance.
     */
    public function __construct($tenant, $messageText = null, $attachmentPath = null, $clientEmail = null)
    {
        $this->tenant = $tenant;
        $this->messageText = $messageText;
        $this->attachmentPath = $attachmentPath;
        $this->clientEmail = $clientEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 NUEVO COMPROBANTE DE PAGO - Cliente: ' . ($this->tenant->business_name ?? $this->tenant->id),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-notification',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        if ($this->attachmentPath && file_exists($this->attachmentPath)) {
            return [
                Attachment::fromPath($this->attachmentPath)
                    ->as('comprobante_pago.' . pathinfo($this->attachmentPath, PATHINFO_EXTENSION))
            ];
        }

        return [];
    }
}
