<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AdminOs\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

/**
 * New admins choose their own password from a signed, time-limited link,
 * so nobody else ever knows it. The link stops working once used, because
 * it carries a fingerprint of the current password.
 */
class PasswordSetupController extends Controller
{
    public static function linkFor(User $user, int $minutes = 60): string
    {
        return URL::temporarySignedRoute('os.password.setup', now()->addMinutes($minutes), [
            'user' => $user->id,
            'v'    => self::fingerprint($user),
        ]);
    }

    private static function fingerprint(User $user): string
    {
        return substr(hash_hmac('sha256', (string) $user->getAuthPassword() . '|' . $user->id, config('app.key')), 0, 20);
    }

    private function resolve(Request $request): User
    {
        $user = User::find($request->route('user'));

        abort_unless(
            $user && $user->isOsStaff() && hash_equals(self::fingerprint($user), (string) $request->query('v')),
            403,
            'This link has expired or was already used. Ask a super admin for a new one.'
        );

        return $user;
    }

    public function show(Request $request)
    {
        $user = $this->resolve($request);

        return view('adminos.auth.set-password', ['user' => $user, 'action' => $request->fullUrl()]);
    }

    public function store(Request $request)
    {
        $user = $this->resolve($request);

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->uncompromised()],
        ]);

        $user->forceFill(['password' => $data['password']])->save();
        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Audit::log('auth.password_set', null, [], $user->id);

        return redirect()->route('os.login')->with('os_status', 'Password saved. Sign in to set up two-factor.');
    }
}
