<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingIntakeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AboutController;

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/services', [ServiceController::class, 'index'])->name('services');

Route::get('/about', [AboutController::class, 'index'])->name('about');

// ── Booking ───────────────────────────────────────────────────────────────
// {carePlan} now resolves by the `slug` column (see CarePlan::getRouteKeyName).
// URLs look like /book/full-care-plan-abc12 instead of /book/3.
// Laravel still returns 404 automatically when the slug isn't found.
 
Route::get('/book/{careType:slug}', [BookingController::class, 'create'])
     ->name('booking.create');

Route::get('/book/service/{careType}', [BookingController::class, 'create'])
     ->name('booking.create.service');

Route::post('/book/calculate-price', [BookingController::class, 'calculatePrice'])
     ->name('booking.calculate-price');

Route::post('/book/initiate', [BookingController::class, 'initiate'])->name('booking.initiate');

// Step 1 (POST): Paystack callback — creates booking + sends intake email
Route::post('/book/store', [BookingController::class, 'store'])
     ->name('booking.store');

Route::get('/book/received/{reference}', [BookingController::class, 'received'])->name('booking.received');

Route::get('/intake/confirmation/{booking}', [BookingIntakeController::class, 'confirmation'])
     ->name('booking.intake.confirmation');

// Private intake form (link is sent inside BookingReceivedMail)
// {preBooking} resolves by the token column via PreBooking::getRouteKeyName()
Route::get('/intake/{preBooking:token}', [BookingIntakeController::class, 'show'])
    ->name('booking.intake');

Route::post('/intake/{preBooking:token}', [BookingIntakeController::class, 'submit'])
    ->name('booking.intake.submit');

// Already submitted
Route::get('/intake/{preBooking}/already-submitted', [BookingIntakeController::class, 'alreadySubmitted'])
->name('booking.intake.already-submitted');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-tailwind', function () {
    return view('test-tailwind');
});

require __DIR__.'/auth.php';
