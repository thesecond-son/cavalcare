<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AdminOs\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Step 1 of the Caval OS sign-in: email and password.
 * A correct password does not sign the admin in. It only opens step 2
 * (two-factor), handled by TwoFactorController.
 */
class AuthController extends Controller
{
    public const PENDING_KEY = 'os_2fa_pending';   // ['id' => int, 'at' => unix time]
    public const PENDING_TTL = 600;                // 10 minutes to finish step 2

    public function show(Request $request)
    {
        if ($request->user()?->isOsStaff()) {
            return redirect()->route('os.dashboard');
        }

        return view('adminos.auth.login'); // headers come from OsSecurityHeaders
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $email = Str::lower($data['email']);
        $key   = 'os-login:' . $email . '|' . $request->ip();
        $max   = config('adminos.max_login_attempts', 5);

        if (RateLimiter::tooManyAttempts($key, $max)) {
            $seconds = RateLimiter::availableIn($key);
            Audit::log('auth.locked_out', null, ['email' => $email]);

            throw ValidationException::withMessages([
                'email' => "Too many attempts. Try again in {$seconds} seconds.",
            ]);
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        // Hash::check runs even without a user so response time does not
        // reveal whether the email exists.
        $passwordOk = Hash::check($data['password'], $user?->password ?? '$2y$12$' . str_repeat('x', 53));
        $ok = $user && $passwordOk && $user->password && $user->isOsStaff();

        if (! $ok) {
            RateLimiter::hit($key, 15 * 60);
            Audit::log('auth.failed', null, ['email' => $email], $user?->id);

            throw ValidationException::withMessages([
                'email' => 'These details do not match an admin account.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->session()->put(self::PENDING_KEY, ['id' => $user->id, 'at' => time()]);
        Audit::log('auth.password_ok', null, [], $user->id);

        return redirect()->route($user->hasTwoFactorEnabled() ? 'os.2fa.challenge' : 'os.2fa.setup');
    }

    public function logout(Request $request)
    {
        Audit::log('auth.logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('os.login')->with('os_status', 'You have signed out.');
    }

    /** The admin who passed step 1 and is still within the time limit, or null. */
    public static function pendingUser(Request $request): ?User
    {
        $pending = $request->session()->get(self::PENDING_KEY);

        if (! $pending || (time() - (int) $pending['at']) > self::PENDING_TTL) {
            $request->session()->forget(self::PENDING_KEY);
            return null;
        }

        $user = User::find($pending['id']);

        return $user?->isOsStaff() ? $user : null;
    }

    /** Completes sign-in after step 2. */
    public static function completeLogin(Request $request, User $user): void
    {
        $request->session()->forget([self::PENDING_KEY, 'os_2fa_setup_secret']);
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('os_last_activity', time());
        $user->forceFill(['last_seen_at' => now()])->save();
        Audit::log('auth.login');
    }
}
