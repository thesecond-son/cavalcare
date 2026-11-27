<?php

namespace App\Support;

/**
 * Normalises phone numbers to E.164 (+2348012345678).
 *
 * The apps send E.164 already. Website bookings hold whatever the family
 * typed, usually a Nigerian local number (08012345678), so a leading 0
 * with 11 digits is read as Nigerian. Anything that can't be read safely
 * returns null rather than a guess.
 */
final class Phone
{
    public static function toE164(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $raw    = trim($raw);
        $plus   = str_starts_with($raw, '+') || str_starts_with($raw, '00');
        $digits = preg_replace('/\D/', '', $raw);

        if (str_starts_with($raw, '00')) {
            $digits = substr($digits, 2);
        }

        if ($plus) {
            return (strlen($digits) >= 8 && strlen($digits) <= 15) ? '+' . $digits : null;
        }

        // Nigerian local format: 0 + 10 digits.
        if (strlen($digits) === 11 && $digits[0] === '0') {
            return '+234' . substr($digits, 1);
        }

        // Nigerian number typed with country code but no plus: 234 + 10 digits.
        if (strlen($digits) === 13 && str_starts_with($digits, '234')) {
            return '+' . $digits;
        }

        return null;
    }

    /** "+44 •••• ••• 123" style masking for messages and logs. */
    public static function mask(string $e164): string
    {
        return substr($e164, 0, 4) . ' •••• ' . substr($e164, -3);
    }
}
