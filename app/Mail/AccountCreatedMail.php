<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AccountCreatedMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $resetUrl
    ) {}

    public function build()
    {
        return $this->subject('Your Caval Care Account Is Ready')
            ->view('emails.account-created')
            ->with([
                'user' => $this->user,
                'resetUrl' => $this->resetUrl,
            ]);
    }
}
