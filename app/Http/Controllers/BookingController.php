<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\PreBooking;
use App\Mail\BookingReceivedMail;

class BookingController extends Controller
{
    public function create(CarePlan $carePlan)
    {
        return view('bookings.create', [
            'carePlan'  => $carePlan,
            'careTypes' => CareType::orderBy('name')->get(),
        ]);
    }

    /**
     * STEP 1 — Called when the user submits the form (before Paystack opens).
     *
     * Creates the PreBooking with status 'pending' so we have a record
     * to attach the Paystack reference to. Returns the reference back to
     * the JS so it can be passed to Paystack as the metadata reference.
     */

     public function initiate(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:30',
            'care_plan_id' => 'required|exists:care_plans,id',
            'care_type_id' => 'required|exists:care_types,id',
        ]);
 
        $preBooking = PreBooking::create([
            'reference'      => 'PB-' . strtoupper(Str::random(8)),
            'care_plan_id'   => $data['care_plan_id'],
            'care_type_id'   => $data['care_type_id'],
            'status'         => 'pending',
            'booker_details' => [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ],
        ]);
 
        // Return the reference to the frontend — the JS passes this to
        // Paystack as `metadata.pre_booking_reference` so we can look it
        // up when Paystack calls our callback.
        return response()->json([
            'reference' => $preBooking->reference,
        ]);
    }

    /**
     * STEP 2 — Called by YOUR JS after Paystack fires its onSuccess callback.
     *
     * The JS sends us the Paystack transaction reference. We verify it
     * server-side against the Paystack API, then update the PreBooking
     * status to 'paid' or 'failed' accordingly.
     *
     * We always save the attempt — paid AND failed — so nothing is lost.
     */
    
     public function store(Request $request)
     {
         $data = $request->validate([
             'paystack_reference'   => 'required|string',
             'pre_booking_reference' => 'required|string|exists:pre_bookings,reference',
         ]);
  
         $preBooking = PreBooking::where('reference', $data['pre_booking_reference'])->firstOrFail();
  
         // ── Verify with Paystack ──────────────────────────────────────────
         $verification = $this->verifyPaystack($data['paystack_reference']);
  
         if ($verification['status'] === 'paid') {
             // Payment confirmed — generate intake token and send the email
             $preBooking->generateToken()->save();
  
             $preBooking->update([
                 'status'             => 'paid',
                 'paystack_reference' => $data['paystack_reference'],
                 'amount_paid'        => $verification['amount'], // in kobo
             ]);
  
             Mail::to($preBooking->booker_details['email'])->send(
                 new BookingReceivedMail($preBooking)
             );
  
             return response()->json([
                 'success'  => true,
                 'redirect' => route('booking.received', $preBooking->reference),
             ]);
  
         } else {
             // Payment failed — record it but do NOT send the intake email
             // and do NOT generate the intake token
             $preBooking->update([
                 'status'             => 'failed',
                 'paystack_reference' => $data['paystack_reference'],
             ]);
  
             return response()->json([
                 'success' => false,
                 'message' => 'Payment could not be verified. Please try again or contact support.',
             ], 422);
         }
     }
  
     /**
     * The "check your email" page shown after a successful payment.
     */

     public function received(string $reference)
    {
        $preBooking = PreBooking::where('reference', $reference)->firstOrFail();
 
        // Guard: don't show the confirmation page for failed payments
        abort_if($preBooking->status !== 'paid', 403, 'Payment not completed.');
 
        return view('bookings.received', compact('preBooking'));
    }

    // ── Private helper ────────────────────────────────────────────────────
 
    /**
     * Hit the Paystack verify endpoint and return a normalised result.
     * Returns ['status' => 'paid'|'failed', 'amount' => int (kobo)]
     */
    private function verifyPaystack(string $paystackReference): array
    {
        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get("https://api.paystack.co/transaction/verify/{$paystackReference}");
 
        if (
            $response->successful() &&
            $response->json('data.status') === 'success'
        ) {
            return [
                'status' => 'paid',
                'amount' => $response->json('data.amount'), // in kobo
            ];
        }
 
        return ['status' => 'failed', 'amount' => 0];
    }
}
