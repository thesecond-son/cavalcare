<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PreBooking;

class BookingReceivedMail extends Mailable
{
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public PreBooking $preBooking)
    {
       
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your CavalCare booking is confirmed — complete your care details',
        );
    }

    public function build()
    {
        return new Content(
            view: 'emails.booking-received',  // your existing template
            with: [
                'preBooking' => $this->preBooking,
                'firstName'  => explode(' ', $this->preBooking->booker_details['name'])[0],
                'planName'   => $this->preBooking->carePlan->name,
                'careType'   => $this->preBooking->careType->name,
                // ── NEW: pass the intake URL into your template ──
                'intakeUrl'  => route('booking.intake', $this->preBooking->token),
            ],
        );
    }
}
