<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use App\Models\User;

class PaymentConfirmedMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Booking $booking,
        public User $user
    ) {}

    public function build()
    {

        // Ensure relations are loaded
        $this->booking->loadMissing(['carePlan', 'careType']);
        
        return $this
            ->subject('Payment Confirmed – Your Caval Care Booking Is Active')
            ->view('emails.payment-confirmed', [
                'booking' => $this->booking,
                'user' => $this->user,
            ]);
    }
}
