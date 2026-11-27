<?php

use App\Http\Controllers\AdminOs\AppCodeController;
use App\Http\Controllers\AdminOs\AuthController;
use App\Http\Controllers\AdminOs\BookingController;
use App\Http\Controllers\AdminOs\DashboardController;
use App\Http\Controllers\AdminOs\PasswordSetupController;
use App\Http\Controllers\AdminOs\SectionController;
use App\Http\Controllers\AdminOs\TwoFactorController;
use Illuminate\Support\Facades\Route;

/*
| Caval OS routes. Loaded from bootstrap/app.php under the /adminos prefix,
| named "os.*". Sign-in is two steps: password, then two-factor.
*/

// ── Sign-in (guests) ───────────────────────────────────────────────────
Route::get('login', [AuthController::class, 'show'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:20,1')->name('login.attempt');

Route::get('two-factor', [TwoFactorController::class, 'challenge'])->name('2fa.challenge');
Route::post('two-factor', [TwoFactorController::class, 'verify'])->middleware('throttle:20,1')->name('2fa.verify');
Route::get('two-factor/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
Route::post('two-factor/setup', [TwoFactorController::class, 'confirm'])->middleware('throttle:20,1')->name('2fa.confirm');

Route::get('set-password/{user}', [PasswordSetupController::class, 'show'])->middleware('signed')->name('password.setup');
Route::post('set-password/{user}', [PasswordSetupController::class, 'store'])->middleware(['signed', 'throttle:10,1'])->name('password.store');

// ── Signed-in staff ────────────────────────────────────────────────────
Route::middleware('os.admin')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('two-factor/recovery-codes', [TwoFactorController::class, 'recovery'])->name('2fa.recovery');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::middleware('can:bookings.view')->group(function () {
        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{preBooking:reference}', [BookingController::class, 'show'])->name('bookings.show');
    });

    Route::post('bookings/{preBooking:reference}/app-code', [AppCodeController::class, 'store'])
        ->middleware(['can:bookings.manage', 'throttle:10,1'])
        ->name('bookings.app-code');

    // Sections that arrive with the app backend. Shown as planned, not faked.
    Route::get('{section}', SectionController::class)
        ->whereIn('section', ['schedule', 'clients', 'caregivers', 'visits', 'billing', 'messages', 'analytics', 'settings'])
        ->name('section');
});
