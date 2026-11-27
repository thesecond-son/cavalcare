<?php

namespace App\Services\Sms;

interface SmsSender
{
    /** Sends $message to an E.164 number. Throws on failure. */
    public function send(string $toE164, string $message): void;

    /** False for drivers that don't really deliver (e.g. the log driver). */
    public function delivers(): bool;
}
