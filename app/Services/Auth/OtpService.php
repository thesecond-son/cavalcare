<?php

namespace App\Services\Auth;

use App\Mail\OtpCodeMail;
use App\Models\OtpCode;
use App\Services\Sms\SmsSender;
use Illuminate\Support\Facades\Mail;

/**
 * Six-digit sign-in codes for the apps.
 * Codes are stored as an HMAC (never in plain text), expire after
 * 10 minutes, work once, and lock after 5 wrong tries.
 */
class OtpService
{
    public const TTL_MINUTES  = 10;
    public const MAX_ATTEMPTS = 5;

    public function __construct(private SmsSender $sms)
    {
    }

    public static function hash(string $code): string
    {
        return hash_hmac('sha256', $code, config('app.key'));
    }

    public function smsAvailable(): bool
    {
        return $this->sms->delivers() || ! app()->environment('production');
    }

    /** Creates and sends a code. Earlier unused codes for the same destination stop working. */
    public function send(string $channel, string $destination, ?string $ip = null): void
    {
        $code = (string) random_int(100000, 999999);

        OtpCode::where('destination', $destination)
            ->where('purpose', 'login')
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        OtpCode::create([
            'channel'     => $channel,
            'destination' => $destination,
            'code_hash'   => self::hash($code),
            'purpose'     => 'login',
            'expires_at'  => now()->addMinutes(self::TTL_MINUTES),
            'ip'          => $ip,
        ]);

        if ($channel === 'email') {
            Mail::to($destination)->send(new OtpCodeMail($code, self::TTL_MINUTES));
        } else {
            $this->sms->send($destination, "{$code} is your Cavalcare code. It expires in " . self::TTL_MINUTES . ' minutes. Never share it.');
        }
    }

    /**
     * Checks a code. Returns 'ok', 'invalid' or 'locked'.
     * A correct code is consumed so it can't be used twice.
     */
    public function verify(string $destination, string $code): string
    {
        $otp = OtpCode::where('destination', $destination)
            ->where('purpose', 'login')
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->lockForUpdate()
            ->first();

        if (! $otp) {
            return 'invalid';
        }
        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            return 'locked';
        }

        if (! hash_equals($otp->code_hash, self::hash(preg_replace('/\D/', '', $code)))) {
            $otp->increment('attempts');
            return $otp->attempts >= self::MAX_ATTEMPTS ? 'locked' : 'invalid';
        }

        $otp->update(['consumed_at' => now()]);

        return 'ok';
    }
}
