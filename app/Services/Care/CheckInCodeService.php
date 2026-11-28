<?php

namespace App\Services\Care;

use App\Models\Care\CheckInLink;
use App\Models\Care\Patient;
use App\Models\Care\Visit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * The rotating check-in code on the patient's phone (backend spec 13.3).
 *
 * code = HMAC-SHA256(link secret, visit id + 30-second step), cut to 6 digits.
 * Valid: the current step and the one before (up to 60 seconds). Offline scans
 * are checked against the time the caregiver's phone recorded. Proves the
 * caregiver was next to the patient's phone at that moment.
 */
class CheckInCodeService
{
    public const STEP_SECONDS = 30;

    /** The patient's active link, created if there is none. */
    public function linkFor(Patient $patient): CheckInLink
    {
        return CheckInLink::where('patient_id', $patient->id)->whereNull('revoked_at')->first()
            ?? CheckInLink::create([
                'patient_id' => $patient->id,
                'code'       => $this->newPublicCode(),
                'secret'     => base64_encode(random_bytes(32)),
            ]);
    }

    /** Revokes the current link and makes a new one (e.g. the old one was shared too widely). */
    public function rotate(Patient $patient): CheckInLink
    {
        CheckInLink::where('patient_id', $patient->id)->whereNull('revoked_at')->update(['revoked_at' => now()]);

        return $this->linkFor($patient);
    }

    public function codeAt(CheckInLink $link, Visit $visit, \DateTimeInterface $at): string
    {
        return $this->codeForStep($link, $visit, intdiv(Carbon::instance($at)->getTimestamp(), self::STEP_SECONDS));
    }

    /** Seconds until the code on screen changes. */
    public function secondsLeft(?\DateTimeInterface $at = null): int
    {
        $ts = ($at ? Carbon::instance($at) : now())->getTimestamp();

        return self::STEP_SECONDS - ($ts % self::STEP_SECONDS);
    }

    /** True when $code was on the patient's screen at $at (or one step before). */
    public function verify(Visit $visit, string $code, \DateTimeInterface $at): bool
    {
        $link = CheckInLink::where('patient_id', $visit->patient_id)->whereNull('revoked_at')->first();
        $code = preg_replace('/\D/', '', $code);
        if (! $link || strlen($code) !== 6 || ! $visit->inCheckInWindow($at)) {
            return false;
        }

        $step = intdiv(Carbon::instance($at)->getTimestamp(), self::STEP_SECONDS);
        foreach ([$step, $step - 1] as $s) {
            if (hash_equals($this->codeForStep($link, $visit, $s), $code)) {
                return true;
            }
        }

        return false;
    }

    /** The visit the check-in page should show now, if any. */
    public function activeVisit(CheckInLink $link): ?Visit
    {
        return Visit::with('professional.user')
            ->where('patient_id', $link->patient_id)
            ->whereIn('state', ['scheduled', 'en_route', 'checked_in'])
            ->where('scheduled_start', '<=', now()->addMinutes(30))
            ->where('scheduled_end', '>=', now())
            ->orderBy('scheduled_start')
            ->first();
    }

    private function codeForStep(CheckInLink $link, Visit $visit, int $step): string
    {
        $hmac = hash_hmac('sha256', $visit->id . '|' . $step, base64_decode($link->secret), true);
        $n = unpack('N', substr($hmac, 0, 4))[1] & 0x7fffffff;

        return str_pad((string) ($n % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function newPublicCode(): string
    {
        do {
            $code = Str::lower(Str::random(10));
        } while (CheckInLink::where('code', $code)->exists());

        return $code;
    }
}
