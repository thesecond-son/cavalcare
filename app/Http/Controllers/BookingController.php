<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\Booking;
use App\Models\PreBooking;
use App\Mail\BookingReceivedMail;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create(CarePlan $carePlan)
    {
        return view('bookings.create', [
            'carePlan'  => $carePlan,
            'careTypes' => CareType::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:30',
            'care_plan_id' => 'required|exists:care_plans,id',
            'care_type_id' => 'required|exists:care_types,id',
        ]);

        $preBooking = PreBooking::create([
            'reference'     => 'PB-' . strtoupper(Str::random(8)),
            'care_plan_id'  => $data['care_plan_id'],
            'care_type_id'  => $data['care_type_id'],
    
            'booker_details' => [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ],
        ]);

        Mail::to($data['email'])->send(
            new BookingReceivedMail($preBooking)
        );

        return redirect()
            ->route('booking.received', $preBooking->reference);
    }

    public function received(string $reference)
    {
        $preBooking = PreBooking::where('reference', $reference)->firstOrFail();

        return view('bookings.received', compact('preBooking'));
    }
}
