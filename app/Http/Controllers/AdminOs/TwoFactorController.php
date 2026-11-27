<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Support\AdminOs\Audit;
use App\Support\AdminOs\TwoFactor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Step 2 of the Caval OS sign-in. Two-factor is required for every admin:
 * those without it set it up here before they can get in.
 */
class TwoFactorController extends Controller
{
    public function __construct(private TwoFactor $twoFactor)
    {
    }

    // ── Challenge (2FA already set up) ───────────────────────────────────

    public function challenge(Request $request)
    {
        $user = AuthController::pendingUser($request);
        if (! $user) {
            return $this->restart();
        }
        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route('os.2fa.setup');
        }

        return view('adminos.auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $user = AuthController::pendingUser($request);
        if (! $user || ! $user->hasTwoFactorEnabled()) {
            return $this->restart();
        }

        $data = $request->validate([
            'code'          => ['nullable', 'string', 'max:12'],
            'recovery_code' => ['nullable', 'string', 'max:20'],
        ]);

        $this->throttle("os-2fa:{$user->id}");

        if (filled($data['recovery_code'] ?? null)) {
            if (! $this->twoFactor->useRecoveryCode($user, $data['recovery_code'])) {
                $this->fail($user, 'recovery_code', 'That recovery code is not valid.');
            }
            Audit::log('auth.2fa_recovery_used', null, ['remaining' => count($user->two_factor_recovery_codes ?? [])], $user->id);
        } else {
            $step = $this->twoFactor->verify($user->two_factor_secret, (string) ($data['code'] ?? ''), $user->two_factor_last_step);
            if ($step === null) {
                $this->fail($user, 'code', 'That code is not right. Check your authenticator app and try again.');
            }
            $user->forceFill(['two_factor_last_step' => $step])->save();
        }

        RateLimiter::clear("os-2fa:{$user->id}");
        AuthController::completeLogin($request, $user);

        return redirect()->intended(route('os.dashboard'));
    }

    // ── Setup (first sign-in, or after a reset) ──────────────────────────

    public function setup(Request $request)
    {
        $user = AuthController::pendingUser($request);
        if (! $user) {
            return $this->restart();
        }
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('os.2fa.challenge');
        }

        // The secret lives in the server-side session until it is confirmed.
        $secret = $request->session()->get('os_2fa_setup_secret');
        if (! $secret) {
            $secret = $this->twoFactor->newSecret();
            $request->session()->put('os_2fa_setup_secret', $secret);
        }

        return view('adminos.auth.two-factor-setup', [
            'qr'     => $this->twoFactor->qrSvg($user, $secret),
            'secret' => trim(chunk_split($secret, 4, ' ')),
        ]);
    }

    public function confirm(Request $request)
    {
        $user   = AuthController::pendingUser($request);
        $secret = $request->session()->get('os_2fa_setup_secret');
        if (! $user || ! $secret || $user->hasTwoFactorEnabled()) {
            return $this->restart();
        }

        $data = $request->validate(['code' => ['required', 'string', 'max:12']]);
        $this->throttle("os-2fa:{$user->id}");

        $step = $this->twoFactor->verify($secret, $data['code']);
        if ($step === null) {
            $this->fail($user, 'code', 'That code is not right. Make sure you scanned the code above, then try again.');
        }

        $codes = $this->twoFactor->newRecoveryCodes();
        $user->forceFill([
            'two_factor_secret'         => $secret,
            'two_factor_confirmed_at'   => now(),
            'two_factor_last_step'      => $step,
            'two_factor_recovery_codes' => $this->twoFactor->hashCodes($codes),
        ])->save();

        Audit::log('auth.2fa_enabled', null, [], $user->id);
        RateLimiter::clear("os-2fa:{$user->id}");
        AuthController::completeLogin($request, $user);

        // Shown once on the next page, never stored in plain text.
        return redirect()->route('os.2fa.recovery')->with('os_recovery_codes', $codes);
    }

    public function recovery(Request $request)
    {
        $codes = $request->session()->get('os_recovery_codes');
        if (! $codes) {
            return redirect()->route('os.dashboard');
        }

        return view('adminos.auth.recovery-codes', compact('codes'));
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function throttle(string $key): void
    {
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'code' => 'Too many attempts. Wait ' . RateLimiter::availableIn($key) . ' seconds and try again.',
            ]);
        }
    }

    private function fail($user, string $field, string $message): never
    {
        RateLimiter::hit("os-2fa:{$user->id}", 300);
        Audit::log('auth.2fa_failed', null, [], $user->id);

        throw ValidationException::withMessages([$field => $message]);
    }

    private function restart()
    {
        return redirect()->route('os.login')->with('os_status', 'Please sign in again.');
    }
}
