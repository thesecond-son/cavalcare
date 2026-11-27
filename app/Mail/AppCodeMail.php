<?php

namespace App\Mail;

use App\Models\PreBooking;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Sends a family their app code on its own (resent from Caval OS). */
class AppCodeMail extends Mailable
{
    public function __construct(public PreBooking $preBooking, public string $code)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your code for the Cavalcare app');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.os.app-code', with: [
            'firstName' => explode(' ', $this->preBooking->booker_details['name'] ?? 'there')[0],
            'code'      => $this->code,
            'email'     => $this->preBooking->booker_details['email'] ?? null,
            'apps'      => config('cavalcare.app_links'),
        ]);
    }
}
