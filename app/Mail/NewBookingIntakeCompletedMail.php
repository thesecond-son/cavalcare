<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewBookingIntakeCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public string $adminUrl;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;

        $this->adminUrl = url(
            'secure-cavalcareadmin/bookings/' . $booking->id
        );
    }

    public function build()
    {
        return $this
            ->subject(
                'New Booking — Intake Form Completed · ' .
                $this->booking->reference
            )
            ->view('emails.new-booking-intake-completed');
    }
}