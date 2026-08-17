<?php

namespace App\Mail;

use App\Models\PreBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IntakeFormLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PreBooking $preBooking
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your CavalCare payment is confirmed — complete your care details',
        );
    }

    public function content(): Content
    {
        $this->preBooking->loadMissing([
            'carePlan',
            'careType',
        ]);

        return new Content(
            view: 'emails.intake-form-link',
            with: [
                'preBooking' => $this->preBooking,

                'firstName' => explode(
                    ' ',
                    $this->preBooking->booker_details['name'] ?? 'Customer'
                )[0],

                'planName' => $this->preBooking->carePlan?->name ?? 'Care Plan',

                'careType' => $this->preBooking->careType?->name ?? 'Care',

                'intakeUrl' => route(
                    'booking.intake',
                    ['token' => $this->preBooking->token]
                ),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}