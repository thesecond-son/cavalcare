<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminInviteMail extends Mailable
{
    public function __construct(public User $user, public string $link)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Caval OS admin access');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.os.admin-invite', with: [
            'firstName' => explode(' ', $this->user->name)[0],
            'link'      => $this->link,
        ]);
    }
}
