<?php

use App\Http\Controllers\AdminOs\AppBookingController;
use App\Http\Controllers\AdminOs\AppCodeController;
use App\Http\Controllers\AdminOs\AuthController;
use App\Http\Controllers\AdminOs\BookingController;
use App\Http\Controllers\AdminOs\CaregiverController;
use App\Http\Controllers\AdminOs\ClientController;
use App\Http\Controllers\AdminOs\DashboardController;
use App\Http\Controllers\AdminOs\PasswordSetupController;
use App\Http\Controllers\AdminOs\SectionController;
use App\Http\Controllers\AdminOs\TwoFactorController;
use App\Http\Controllers\AdminOs\VisitController as OsVisitController;
use App\Http\Controllers\AdminOs\MessageController as OsMessageController;
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

    Route::middleware('can:bookings.view')->group(function () {
        Route::get('app-bookings', [AppBookingController::class, 'index'])->name('app-bookings.index');
        Route::get('app-bookings/{booking}', [AppBookingController::class, 'show'])->whereUuid('booking')->name('app-bookings.show');
    });
    Route::middleware(['can:schedule.manage', 'throttle:30,1'])->group(function () {
        Route::post('app-bookings/{booking}/offer', [AppBookingController::class, 'offer'])->whereUuid('booking')->name('app-bookings.offer');
        Route::post('app-bookings/{booking}/open', [AppBookingController::class, 'open'])->whereUuid('booking')->name('app-bookings.open');
    });

    // Visits & notes; concerns, disputed check-ins and safety alerts
    Route::middleware('can:clients.view')->group(function () {
        Route::get('visits', [OsVisitController::class, 'index'])->name('visits.index');
        Route::get('visits/{visit}', [OsVisitController::class, 'show'])->whereUuid('visit')->name('visits.show');
    });
    Route::middleware(['can:concerns.manage', 'throttle:60,1'])->group(function () {
        Route::post('concerns/{concern}/acknowledge', [OsVisitController::class, 'acknowledgeConcern'])->whereUuid('concern')->name('concerns.acknowledge');
        Route::post('concerns/{concern}/resolve', [OsVisitController::class, 'resolveConcern'])->whereUuid('concern')->name('concerns.resolve');
        Route::post('check-ins/{checkIn}/resolve', [OsVisitController::class, 'resolveCheckIn'])->whereUuid('checkIn')->name('check-ins.resolve');
        Route::post('safety-alerts/{alert}/acknowledge', [OsVisitController::class, 'acknowledgeSafety'])->whereUuid('alert')->name('safety.acknowledge');
    });

    // Family messages
    Route::middleware('can:messages.manage')->group(function () {
        Route::get('messages', [OsMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{thread}', [OsMessageController::class, 'show'])->whereUuid('thread')->name('messages.show');
        Route::post('messages/{thread}', [OsMessageController::class, 'reply'])->whereUuid('thread')->middleware('throttle:60,1')->name('messages.reply');
    });

    Route::middleware('can:clients.view')->group(function () {
        Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/{patient}', [ClientController::class, 'show'])->whereUuid('patient')->name('clients.show');
    });

    Route::middleware('can:caregivers.view')->group(function () {
        Route::get('caregivers', [CaregiverController::class, 'index'])->name('caregivers.index');
        Route::get('caregivers/{professional}', [CaregiverController::class, 'show'])->whereUuid('professional')->name('caregivers.show');
        Route::get('caregivers/{professional}/credentials/{credential}/document', [CaregiverController::class, 'document'])
            ->whereUuid(['professional', 'credential'])->name('caregivers.credentials.document');
    });
    Route::middleware(['can:caregivers.verify', 'throttle:30,1'])->group(function () {
        Route::post('caregivers/{professional}/credentials/{credential}', [CaregiverController::class, 'reviewCredential'])
            ->whereUuid(['professional', 'credential'])->name('caregivers.credentials.review');
        Route::post('caregivers/{professional}/status', [CaregiverController::class, 'updateStatus'])
            ->whereUuid('professional')->name('caregivers.status');
    });

    // Sections that arrive with the app backend. Shown as planned, not faked.
    Route::get('{section}', SectionController::class)
        ->whereIn('section', ['schedule', 'billing', 'analytics', 'settings'])
        ->name('section');
});
