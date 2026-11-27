<?php

namespace App\Services\Auth;

use App\Mail\AppCodeMail;
use App\Models\AppLinkCode;
use App\Models\PreBooking;
use App\Models\User;
use App\Support\Phone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * The code a family gets after paying on the website, typed once into the
 * Cavalcare app to open their account with the booking already filled in.
 *
 * Security:
 * - 8 characters from a 31-letter alphabet without look-alikes (~8.5e11 combinations).
 * - Stored only as an HMAC. Works once, expires after 30 days.
 * - Only works together with the email or phone used to book, so a code
 *   seen by someone else is useless on its own. 5 mismatches revoke it.
 */
class AppLinkCodeService
{
    public const TTL_DAYS       = 30;
    public const MAX_ATTEMPTS   = 5;
    private const ALPHABET      = 'ABCDEFGHJKLMNPQRSTUVWXYZ2345679';

    public static function normalise(string $code): string
    {
        return preg_replace('/[^A-Z0-9]/', '', Str::upper($code));
    }

    public static function hash(string $code): string
    {
        return hash_hmac('sha256', 'app-link|' . self::normalise($code), config('app.key'));
    }

    public static function format(string $code): string
    {
        return substr($code, 0, 4) . '-' . substr($code, 4, 4);
    }

    /** Issues a fresh code (earlier unused ones stop working) and returns it, formatted. */
    public function issue(PreBooking $preBooking, ?User $issuedBy = null): string
    {
        return DB::transaction(function () use ($preBooking, $issuedBy) {
            AppLinkCode::where('pre_booking_id', $preBooking->id)->active()->update(['revoked_at' => now()]);

            do {
                $code = '';
                for ($i = 0; $i < 8; $i++) {
                    $code .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
                }
            } while (AppLinkCode::where('code_hash', self::hash($code))->exists());

            AppLinkCode::create([
                'pre_booking_id' => $preBooking->id,
                'code_hash'      => self::hash($code),
                'expires_at'     => now()->addDays(self::TTL_DAYS),
                'issued_by'      => $issuedBy?->id,
            ]);

            return self::format($code);
        });
    }

    /** Issues a code and emails it to the person who booked. Returns false if there is no email. */
    public function issueAndEmail(PreBooking $preBooking, ?User $issuedBy = null): bool
    {
        $email = $preBooking->booker_details['email'] ?? null;
        if (! $email) {
            return false;
        }

        $code = $this->issue($preBooking, $issuedBy);
        Mail::to($email)->send(new AppCodeMail($preBooking, $code));
        AppLinkCode::where('pre_booking_id', $preBooking->id)->active()->update(['sent_at' => now()]);

        return true;
    }

    /**
     * Finds the booking for a code + identifier, or null. Never says which part was wrong.
     * Must run inside a transaction (the code row is locked).
     */
    public function match(string $code, ?string $email, ?string $phoneE164): ?AppLinkCode
    {
        $link = AppLinkCode::with('preBooking')
            ->where('code_hash', self::hash($code))
            ->lockForUpdate()
            ->first();

        if (! $link || ! $link->isUsable() || $link->attempts >= self::MAX_ATTEMPTS) {
            return null;
        }

        $booker      = $link->preBooking->booker_details ?? [];
        $emailOk     = $email && isset($booker['email']) && hash_equals(Str::lower(trim($booker['email'])), Str::lower(trim($email)));
        $bookerPhone = Phone::toE164($booker['phone'] ?? null);
        $phoneOk     = $phoneE164 && $bookerPhone && hash_equals($bookerPhone, $phoneE164);

        if (! $emailOk && ! $phoneOk) {
            $link->increment('attempts');
            if ($link->attempts >= self::MAX_ATTEMPTS) {
                $link->update(['revoked_at' => now()]);
            }
            return null;
        }

        return $link;
    }
}
