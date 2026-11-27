<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Family\BookingController as FamilyBookingController;
use App\Http\Controllers\Api\V1\Family\CatalogueController;
use App\Http\Controllers\Api\V1\Family\PatientController;
use App\Http\Controllers\Api\V1\Family\ProfessionalController as FamilyProfessionalController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\PaystackWebhookController;
use App\Http\Controllers\Api\V1\Pro\ProfileController as ProProfileController;
use App\Http\Controllers\Api\V1\Pro\ShiftOfferController;
use App\Http\Controllers\Api\V1\UploadController;
use Illuminate\Support\Facades\Route;

/*
| Cavalcare and CavalPro API, version 1. Served at /api/v1/*.
| Errors always use the { "error": { code, message, fields } } shape.
| Tokens are tied to one app: family tokens (X-App: cavalcare) reach
| /family/*, caregiver tokens (X-App: cavalpro) reach /pro/*.
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Sign-in (no token) ────────────────────────────────────────────
    Route::post('auth/otp', [AuthController::class, 'sendOtp'])->middleware('throttle:otp-send')->name('auth.otp');
    Route::post('auth/verify', [AuthController::class, 'verifyOtp'])->middleware('throttle:otp-verify')->name('auth.verify');
    Route::post('auth/website-code', [AuthController::class, 'redeemWebsiteCode'])->middleware('throttle:website-code')->name('auth.website-code');

    // ── Paystack calls this (signature-checked, no token) ─────────────
    Route::post('webhooks/paystack', PaystackWebhookController::class)->middleware('throttle:120,1')->name('webhooks.paystack');

    // ── Shared: either app ────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'ability:app:cavalcare,app:cavalpro', 'throttle:api-user'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('me', [MeController::class, 'show'])->name('me');
        Route::patch('me', [MeController::class, 'update'])->name('me.update');
        Route::post('uploads', [UploadController::class, 'store'])->middleware('throttle:30,1')->name('uploads.store');
    });

    // ── Family app ────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'abilities:app:cavalcare', 'throttle:api-user'])->group(function () {
        Route::get('me/website-bookings', [MeController::class, 'websiteBookings'])->name('me.website-bookings');

        Route::prefix('family')->name('family.')->group(function () {
            Route::get('care-types', [CatalogueController::class, 'careTypes'])->name('care-types');
            Route::get('care-types/{slug}/plans', [CatalogueController::class, 'plans'])->name('care-types.plans');
            Route::get('add-ons', [CatalogueController::class, 'addOns'])->name('add-ons');

            Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
            Route::post('patients', [PatientController::class, 'store'])->name('patients.store');
            Route::get('patients/{id}', [PatientController::class, 'show'])->name('patients.show');
            Route::patch('patients/{id}', [PatientController::class, 'update'])->name('patients.update');
            Route::put('patients/{id}/health', [PatientController::class, 'health'])->name('patients.health');
            Route::put('patients/{id}/emergency-contacts', [PatientController::class, 'emergencyContacts'])->name('patients.emergency-contacts');
            Route::post('patients/{id}/consent', [PatientController::class, 'consent'])->name('patients.consent');
            Route::get('patients/{id}/members', [PatientController::class, 'members'])->name('patients.members');

            Route::get('professionals', [FamilyProfessionalController::class, 'index'])->name('professionals.index');
            Route::get('professionals/{id}', [FamilyProfessionalController::class, 'show'])->name('professionals.show');

            Route::post('bookings/quote', [FamilyBookingController::class, 'quote'])->name('bookings.quote');
            Route::post('bookings', [FamilyBookingController::class, 'store'])->middleware('throttle:10,1')->name('bookings.store');
            Route::get('bookings', [FamilyBookingController::class, 'index'])->name('bookings.index');
            Route::get('bookings/{id}', [FamilyBookingController::class, 'show'])->name('bookings.show');
            Route::post('bookings/{id}/verify-payment', [FamilyBookingController::class, 'verifyPayment'])->middleware('throttle:20,1')->name('bookings.verify-payment');
            Route::post('bookings/{id}/cancel', [FamilyBookingController::class, 'cancel'])->name('bookings.cancel');
        });
    });

    // ── CavalPro (caregiver app) ──────────────────────────────────────
    Route::middleware(['auth:sanctum', 'abilities:app:cavalpro', 'throttle:api-user'])->prefix('pro')->name('pro.')->group(function () {
        Route::post('profile/about', [ProProfileController::class, 'about'])->name('profile.about');
        Route::get('profile', [ProProfileController::class, 'show'])->name('profile.show');
        Route::patch('profile', [ProProfileController::class, 'update'])->name('profile.update');
        Route::get('verification', [ProProfileController::class, 'verification'])->name('verification');
        Route::post('credentials', [ProProfileController::class, 'credential'])->name('credentials.store');
        Route::post('background-check/consent', [ProProfileController::class, 'backgroundConsent'])->name('background.consent');
        Route::post('training/complete', [ProProfileController::class, 'trainingComplete'])->name('training.complete');
        Route::put('availability', [ProProfileController::class, 'availability'])->name('availability');

        Route::get('shift-offers', [ShiftOfferController::class, 'index'])->name('shift-offers.index');
        Route::get('shift-offers/{id}', [ShiftOfferController::class, 'show'])->name('shift-offers.show');
        Route::post('shift-offers/{id}/accept', [ShiftOfferController::class, 'accept'])->middleware('throttle:30,1')->name('shift-offers.accept');
        Route::post('shift-offers/{id}/decline', [ShiftOfferController::class, 'decline'])->name('shift-offers.decline');
        Route::get('assignments', [ShiftOfferController::class, 'assignments'])->name('assignments');
    });
});
