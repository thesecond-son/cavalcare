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

    public function build()
    {
        return $this
            ->subject('We’ve received your care booking')
            ->view('emails.booking-received');
    }
}
