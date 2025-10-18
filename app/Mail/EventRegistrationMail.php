<?php

namespace App\Mail;

use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class EventRegistrationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Confirmation d\'inscription - ' . $this->registration->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.event-registration',
            with: [
                'registration' => $this->registration,
                'event' => $this->registration->event,
                'user' => $this->registration->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        // Si un QR Code existe, l'attacher
        if ($this->registration->qr_code_path) {
            $qrCodePath = storage_path('app/public/' . $this->registration->qr_code_path);
            if (file_exists($qrCodePath)) {
                $attachments[] = Attachment::fromPath($qrCodePath)
                    ->as('billet-qrcode.png')
                    ->withMime('image/png');
            }
        }
        
        return $attachments;
    }
}
