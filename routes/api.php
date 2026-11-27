<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MeController;
use Illuminate\Support\Facades\Route;

/*
| Cavalcare app API, version 1. Served at /api/v1/*.
| Errors always use the { "error": { code, message, fields } } shape.
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Sign-in (no token) ────────────────────────────────────────────
    Route::post('auth/otp', [AuthController::class, 'sendOtp'])->middleware('throttle:otp-send')->name('auth.otp');
    Route::post('auth/verify', [AuthController::class, 'verifyOtp'])->middleware('throttle:otp-verify')->name('auth.verify');
    Route::post('auth/website-code', [AuthController::class, 'redeemWebsiteCode'])->middleware('throttle:website-code')->name('auth.website-code');

    // ── Signed in (Sanctum device token) ──────────────────────────────
    Route::middleware(['auth:sanctum', 'abilities:app:cavalcare', 'throttle:api-user'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('me', [MeController::class, 'show'])->name('me');
        Route::patch('me', [MeController::class, 'update'])->name('me.update');
        Route::get('me/website-bookings', [MeController::class, 'websiteBookings'])->name('me.website-bookings');
    });
});
