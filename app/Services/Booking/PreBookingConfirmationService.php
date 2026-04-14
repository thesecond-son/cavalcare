<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\PreBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\PaymentConfirmedMail;
use Illuminate\Support\Facades\Mail;
use App\Services\Booking\CustomerAccountCreator;

class PreBookingConfirmationService
{
    public function confirm(PreBooking $preBooking): Booking
    {
        return DB::transaction(function () use ($preBooking) {

            $booking = Booking::create([
                'reference'      => 'CC-' . strtoupper(Str::random(8)),
                'care_plan_id'   => $preBooking->care_plan_id,
                'care_type_id'   => $preBooking->care_type_id,
                'booker_details'=> $preBooking->booker_details,
                'status'         => 'confirmed',
            ]);

            $preBooking->update([
                'status' => 'converted',
            ]);

            $user = app(CustomerAccountCreator::class)
                ->createFromBooking($booking);

            Mail::to($user->email)
                ->send(new PaymentConfirmedMail($booking, $user));

            return $booking;
        });
    }
}
