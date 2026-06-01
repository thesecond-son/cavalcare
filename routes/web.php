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

Route::get('/book/{carePlan}', [BookingController::class, 'create'])->name('booking.create');

// Step 1 (POST): Paystack callback — creates booking + sends intake email
Route::post('/book/store', [BookingController::class, 'store'])
     ->name('booking.store');

// Step 1 (confirmation): "Check your email" page shown after payment
Route::get('/book/confirmation/{booking}', [BookingController::class, 'confirmation'])
     ->name('booking.confirmation');

// Private intake form (link is sent inside BookingReceivedMail)
// {preBooking} resolves by the token column via PreBooking::getRouteKeyName()
Route::get('/intake/{preBooking}',  [BookingIntakeController::class, 'show'])
     ->name('booking.intake');
 
Route::post('/intake/{preBooking}', [BookingIntakeController::class, 'submit'])
     ->name('booking.intake.submit');

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
