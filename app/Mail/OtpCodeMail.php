<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OtpCodeMail extends Mailable
{
    public function __construct(public string $code, public int $minutes)
    {
    }

    public function envelope(): Envelope
    {
        // Code in the subject so it shows in the notification without opening the email.
        return new Envelope(subject: "{$this->code} is your Cavalcare code");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.os.otp-code', with: ['code' => $this->code, 'minutes' => $this->minutes]);
    }
}
