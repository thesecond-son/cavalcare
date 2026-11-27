<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // SMS driver (config/sms.php). Only "log" exists until a provider is chosen.
        $this->app->bind(\App\Services\Sms\SmsSender::class, function () {
            return match (config('sms.driver')) {
                default => new \App\Services\Sms\LogSmsSender(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── App API rate limits (backend spec, section 6.5) ──────────────
        $identity = fn ($request) => sha1(strtolower(trim((string) ($request->input('email') ?: $request->input('phone')))));

        \Illuminate\Support\Facades\RateLimiter::for('otp-send', fn ($request) => [
            \Illuminate\Cache\RateLimiting\Limit::perMinutes(10, 3)->by('otp-send:' . $identity($request)),
            \Illuminate\Cache\RateLimiting\Limit::perHour(20)->by('otp-send-ip:' . $request->ip()),
        ]);
        \Illuminate\Support\Facades\RateLimiter::for('otp-verify', fn ($request) => [
            \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by('otp-verify:' . $identity($request)),
            \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by('otp-verify-ip:' . $request->ip()),
        ]);
        \Illuminate\Support\Facades\RateLimiter::for('website-code', fn ($request) => [
            \Illuminate\Cache\RateLimiting\Limit::perHour(10)->by('website-code:' . $identity($request)),
            \Illuminate\Cache\RateLimiting\Limit::perMinutes(10, 10)->by('website-code-ip:' . $request->ip()),
        ]);
        \Illuminate\Support\Facades\RateLimiter::for('api-user', fn ($request) =>
            \Illuminate\Cache\RateLimiting\Limit::perMinute(120)->by('api-user:' . ($request->user()?->id ?: $request->ip()))
        );

        // Caval OS: super admins and the existing "admin" role pass every permission check.
        \Illuminate\Support\Facades\Gate::before(function ($user, string $ability) {
            return $user->hasAnyRole(\App\Support\AdminOs\OsRoles::fullAccess()) ? true : null;
        });

        // Caval OS sidebar: paid bookings still waiting for the intake form.
        \Illuminate\Support\Facades\View::composer('adminos.layout', function ($view) {
            $count = \App\Models\PreBooking::where('status', \App\PreBookingStatus::PAID)->count();
            $view->with('navAwaiting', $count ?: null);
        });

        // FilamentAsset::register([
        //     asset('css/app.css'),
        // ], panel: 'admin');
    }
}
