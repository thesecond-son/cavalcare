<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;

class PaymentConfirmationService
{
    public function confirm(Booking $booking): User
    {
        return DB::transaction(function () use ($booking) {

            $email = data_get($booking->booker_details, 'email');

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => data_get($booking->booker_details, 'name'),
                    'password' => bcrypt(str()->random(16)),
                ]
            );

            if (! $user->hasRole('customer')) {
                $user->assignRole('customer');
            }

            $booking->update([
                'user_id' => $user->id,
                'status'  => 'confirmed',
                'payment_details' => [
                    'confirmed_at' => now(),
                ],
            ]);

            Password::sendResetLink(['email' => $user->email]);

            return $user;
        });
    }
}
