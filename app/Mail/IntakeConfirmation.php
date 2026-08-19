<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\PreBooking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IntakeConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Booking $booking,
        public readonly PreBooking $preBooking,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your care details have been received — CavalCare',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $bookerName = $this->preBooking->booker_details['name'] ?? 'there';

        $firstName = trim(
            explode(' ', $bookerName)[0] ?? 'there'
        );

        $startDate = $this->booking->booking_details['start_date'] ?? null;

        return new Content(
            view: 'emails.intake-confirmation',
            with: [
                'booking'    => $this->booking,
                'preBooking' => $this->preBooking,
                'firstName'  => $firstName,
                'startDate'  => $startDate
                    ? Carbon::parse($startDate)->format('D, d M Y')
                    : null,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}