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

    public function __construct(
        public PreBooking $preBooking
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your CavalCare booking is confirmed — complete your care details',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.intake-form-link',
            with: [
                'preBooking' => $this->preBooking,

                'firstName' => explode(
                    ' ',
                    $this->preBooking->booker_details['name']
                )[0],

                'planName' => $this->preBooking->carePlan->name,

                'careType' => $this->preBooking->careType->name,

                'intakeUrl' => route('booking.intake', [
                    'preBooking' => $this->preBooking->token,
                ]),
            ],
        );
    }
}