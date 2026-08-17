<?php

namespace App\Mail;

use App\Models\PreBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public PreBooking $preBooking
    ) {
    }

    /**
     * Email subject.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your CavalCare booking is confirmed — complete your care details',
        );
    }

    /**
     * Email content.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.intake-form',
            with: [
                'preBooking' => $this->preBooking,

                'firstName' => explode(
                    ' ',
                    $this->preBooking->booker_details['name'] ?? ''
                )[0],

                'planName' => $this->preBooking->carePlan?->name ?? 'N/A',

                'careType' => $this->preBooking->careType?->name ?? 'N/A',

                'intakeUrl' => route(
                    'booking.intake',
                    $this->preBooking->token
                ),
            ],
        );
    }

    /**
     * Email attachments.
     */
    public function attachments(): array
    {
        return [];
    }
}