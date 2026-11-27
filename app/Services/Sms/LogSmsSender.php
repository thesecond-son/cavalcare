<?php

namespace App\Services\Sms;

use App\Support\Phone;
use Illuminate\Support\Facades\Log;

/** Development stand-in until an SMS provider is chosen. Never delivers. */
class LogSmsSender implements SmsSender
{
    public function send(string $toE164, string $message): void
    {
        // Only outside production: sign-in codes must never reach production logs.
        if (! app()->environment('production')) {
            Log::info('[SMS:log] to ' . Phone::mask($toE164) . ': ' . $message);
        }
    }

    public function delivers(): bool
    {
        return false;
    }
}
