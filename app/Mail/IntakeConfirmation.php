<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\PreBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IntakeConfirmation extends Mailable
{
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Booking    $booking,
        public readonly PreBooking $preBooking,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We\'ve received your care details — CavalCare',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.intake-confirmation',
            with: [
                'booking'    => $this->booking,
                'preBooking' => $this->preBooking,
                'firstName'  => explode(' ', $this->preBooking->booker_details['name'])[0],
                'startDate'  => \Carbon\Carbon::parse(
                    $this->booking->booking_details['start_date'] ?? null
                )->format('D, d M Y'),
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
        return [];
    }
}
