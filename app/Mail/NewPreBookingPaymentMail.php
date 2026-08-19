<?php

namespace App\Mail;

use App\Models\PreBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPreBookingPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public PreBooking $preBooking;

    public string $adminUrl;

    public function __construct(PreBooking $preBooking)
    {
        $this->preBooking = $preBooking;
    
        $this->adminUrl = url(
            'secure-cavalcareadmin/pre-bookings/' . $preBooking->id
        );
    }

    public function build()
    {
        return $this
            ->subject(
                'New PreBooking & Payment Received — ' .
                $this->preBooking->reference
            )
            ->view('emails.new-prebooking-payment');
    }
}