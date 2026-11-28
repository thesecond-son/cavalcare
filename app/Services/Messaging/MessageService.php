<?php

namespace App\Services\Messaging;

use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\User;
use App\Services\Notify\Notifier;
use Illuminate\Support\Str;

/** Posting messages and telling the other participants (spec 7.8, 9.2 MessageSent). */
class MessageService
{
    public function __construct(private Notifier $notifier)
    {
    }

    public function post(Thread $thread, ?User $sender, string $senderKind, ?string $text, ?string $clientId = null, ?string $attachment = null): Message
    {
        if ($clientId && ($existing = Message::where('client_id', $clientId)->first())) {
            return $existing;   // a retry from the phone
        }

        $message = Message::create([
            'client_id'       => $clientId,
            'thread_id'       => $thread->id,
            'sender_id'       => $sender?->id,
            'sender_kind'     => $senderKind,
            'text'            => $text,
            'attachment_path' => $attachment,
            'sent_at'         => now(),
        ]);
        $thread->update(['last_message_at' => now()]);
        $thread->participants()->where('user_id', $sender?->id)->update(['last_read_at' => now()]);

        // Push to everyone else in the thread (their own app), short preview only.
        $from = $senderKind === 'coordinator' || $senderKind === 'support' ? 'CavalCare' : ($sender?->name ?: 'New message');
        foreach ($thread->participants()->with('user.professional')->where('user_id', '!=', $sender?->id)->get() as $p) {
            if (! $p->user || $p->muted) {
                continue;
            }
            $app = $p->role_in_thread === 'professional' ? 'cavalpro' : 'cavalcare';
            $this->notifier->send($p->user, $app, 'messages', 'message_sent', $from, Str::limit((string) $text, 80) ?: 'Sent a photo', ['thread_id' => $thread->id]);
        }

        return $message;
    }
}
