<?php

namespace App\Http\Middleware\AdminOs;

use App\Support\AdminOs\Audit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards every Caval OS page.
 *
 * - Guests are sent to the OS login.
 * - Signed-in users without the admin role get a 404, so the OS does not
 *   reveal that it exists to customers or caregivers.
 * - Sessions end after OS_IDLE_MINUTES of inactivity (default 30).
 */
class EnsureOsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('os.login'));
        }

        if (! $user->isOsStaff()) {
            Audit::log('auth.denied', null, ['path' => $request->path()]);
            abort(404);
        }

        // A website session (e.g. the old Filament admin) is not enough:
        // Caval OS needs the two-step sign-in, recorded as os_last_activity.
        if (! $user->hasTwoFactorEnabled() || ! $request->session()->has('os_last_activity')) {
            return redirect()->guest(route('os.login'))
                ->with('os_status', 'Please sign in to Caval OS with your two-factor code.');
        }

        $idleLimit =(int) config('adminos.idle_minutes', 30) * 60;
        $lastSeen  = (int) $request->session()->get('os_last_activity', 0);

        if ($lastSeen && (time() - $lastSeen) > $idleLimit) {
            Audit::log('auth.idle_timeout');
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('os.login')
                ->with('os_status', 'You were signed out after a period of inactivity.');
        }

        $request->session()->put('os_last_activity', time());

        $response = $next($request);

        // Admin pages must never be cached by the browser or a proxy.
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $response->headers->set('Referrer-Policy', 'same-origin');

        return $response;
    }
}
