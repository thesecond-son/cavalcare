<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountCreatedMail;

class CustomerAccountCreator
{
    public function createFromBooking(Booking $booking): User
    {
        $details = $booking->booker_details;

        $user = User::firstOrCreate(
            ['email' => $details['email']],
            [
                'name'     => $details['name'],
                'password' => bcrypt(str()->random(32)),
            ]
        );

        $user->assignRole('customer');

        $booking->update([
            'user_id' => $user->id,
        ]);
    
        $token = Password::createToken($user);
    
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));
    
        Mail::to($user->email)
            ->send(new AccountCreatedMail($user, $resetUrl));

        return $user;
    }
}