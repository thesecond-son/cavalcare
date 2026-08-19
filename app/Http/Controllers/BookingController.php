<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Models\CareType;
use App\Models\CarePlan;
use App\Models\PreBooking;
use App\Models\AddOn;
use App\Mail\BookingReceivedMail;
use App\Mail\IntakeFormLinkMail;
use App\Mail\NewPreBookingPaymentMail;
use App\PreBookingStatus;
use App\BookingStatus;

class BookingController extends Controller
{
    /**
     * GET /book/{careType}
     *
     * The booking page is now anchored to a CareType (e.g. "Elderly Care").
     * All plans belonging to that type are loaded for the plan selector.
     * Add-ons are global and always shown.
     */
    public function create(CareType $careType)
    {
        $careType->load('carePlans'); // eager-load plans ordered by price (see model)

        return view('bookings.create', [
            'careType' => $careType,
            'plans'    => $careType->carePlans,
            'addOns'   => AddOn::where('is_active', true)->orderBy('price')->get(),
        ]);
    }

    /**
     * STEP 1 — POST /book/initiate
     *
     * Called by JS before Paystack opens.
     * Creates a PreBooking with status 'pending' and returns the reference
     * so the frontend can pass it to Paystack as metadata.
     *
     * Now accepts care_plan_id directly (the plan already knows its care type).
     */
    public function initiate(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:30',
            'care_plan_id' => 'required|exists:care_plans,id',
            'add_on_ids'   => 'nullable|array',
            'add_on_ids.*' => 'integer|exists:add_ons,id',
        ]);

        // Resolve the care type from the selected plan
        $carePlan = CarePlan::findOrFail($data['care_plan_id']);

        $preBooking = PreBooking::create([
            'reference'     => 'PB-' . strtoupper(Str::random(8)),
            'care_plan_id'  => $carePlan->id,
            'care_type_id'  => $carePlan->care_type_id, // derived — no separate input needed
            'status'        => PreBookingStatus::PENDING_PAYMENT,
            'booker_details' => [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ],
        ]);

        // Attach selected add-ons, snapshotting their price at booking time
        if (!empty($data['add_on_ids'])) {
            $addOns = AddOn::whereIn('id', $data['add_on_ids'])->get();

            $pivotData = $addOns->mapWithKeys(fn($addOn) => [
                $addOn->id => ['price_at_booking' => $addOn->price]
            ])->all();

            $preBooking->addOns()->attach($pivotData);
        }

        return response()->json([
            'reference' => $preBooking->reference,
        ]);
    }

    /**
     * STEP 2 — POST /book/store
     *
     * Called by JS after Paystack fires onSuccess.
     * Verifies the transaction server-side, then marks the PreBooking as paid
     * and sends the intake email.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'paystack_reference'    => 'required|string',
            'pre_booking_reference' => 'required|string|exists:pre_bookings,reference',
        ]);

        $preBooking = PreBooking::where('reference', $data['pre_booking_reference'])->firstOrFail();

        $verification = $this->verifyPaystack($data['paystack_reference']);

        if ($verification['status'] === 'paid') {
           
            // Generate the private intake token
            $preBooking->generateToken();

             /*
            |--------------------------------------------------------------------------
            | Payment metadata
            |--------------------------------------------------------------------------
            */

            $paymentData = $verification['data'] ?? [];

            $paymentMeta = [
                'transaction_id' => $paymentData['id'] ?? null,
                'status'         => $paymentData['status'] ?? null,
                'currency'       => $paymentData['currency'] ?? null,
                'channel'        => $paymentData['channel'] ?? null,
                'fees'           => $paymentData['fees'] ?? null,
                'paid_at'        => $paymentData['paid_at'] ?? null,
                'customer'       => $paymentData['customer'] ?? null,
            ];

            $preBooking->save();

            // Mark payment as successful
            $preBooking->update([
                'status'             => PreBookingStatus::PAID,
                'paystack_reference' => $data['paystack_reference'],
                'amount_paid'        => $verification['amount'],
                'payment_meta'       => $paymentMeta,
            ]);

            // Send the intake form email to the customer
            try {
                Mail::to($preBooking->booker_details['email'])
                    ->send(new IntakeFormLinkMail($preBooking));
            } catch (\Throwable $e) {
                report($e);
            }


            // Send payment notification to CavalCare admin
            try {
                Mail::to('cavalcareteam@gmail.com')
                    ->send(new NewPreBookingPaymentMail($preBooking));
            } catch (\Throwable $e) {
                report($e);
            }

            return response()->json([
                'success'  => true,
                'redirect' => route('booking.received', $preBooking->reference),
            ]);

        } else {
            $preBooking->update([
                'status' => $verification['error'] === 'connection'
                    ? PreBookingStatus::PENDING_PAYMENT
                    : PreBookingStatus::FAILED,
    
                'paystack_reference' => $data['paystack_reference'],
            ]);

            $message = $verification['error'] === 'connection'
                ? 'We could not reach Paystack to confirm your payment. Please try again in a moment — your card was not charged again if it already went through.'
                : 'Payment could not be verified. Please try again or contact support.';

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }
    }

    /**
     * GET /book/received/{reference}
     *
     * "Check your email" confirmation page shown after a successful payment.
     */
    public function received(string $reference)
    {
        $preBooking = PreBooking::where('reference', $reference)
            ->with([
                'carePlan',
                'careType',
                'booking.carePlan',
                'booking.careType',
            ])
            ->firstOrFail();

        return match ($preBooking->status) {

            PreBookingStatus::PENDING_PAYMENT =>
                response()->view(
                    'bookings.payment-not-completed',
                    compact('preBooking'),
                    400
                ),

            PreBookingStatus::PAID =>
                view(
                    'bookings.received',
                    compact('preBooking')
                ),

            PreBookingStatus::CONVERTED =>
                view(
                    'bookings.intake-already-submitted',
                    [
                        'preBooking' => $preBooking,
                        'booking' => $preBooking->booking,
                    ]
                ),

        };
    }

    // ── Private helper ────────────────────────────────────────────────────

    /**
     * Hit the Paystack verify endpoint and return a normalised result.
     * Returns ['status' => 'paid'|'failed', 'amount' => int (kobo), 'error' => string|null]
     */
    private function verifyPaystack(string $paystackReference): array
    {
        try {
            $request = Http::withToken(config('services.paystack.secret_key'))
                ->timeout(15);

            // Local/dev-only escape hatch for the "cURL error 60: SSL certificate
            // problem" issue. Never enable this in production — fix the server's
            // CA bundle instead (see notes below). Controlled by config so it can
            // never accidentally ship on: set PAYSTACK_INSECURE_SSL=true only in
            // your local .env.
            if (config('services.paystack.insecure_ssl', false)) {
                $request = $request->withOptions(['verify' => false]);
            }

            $response = $request->get("https://api.paystack.co/transaction/verify/{$paystackReference}");

            if (
                $response->successful() &&
                $response->json('data.status') === 'success'
            ) {
                return [
                    'status' => 'paid',
                    'amount' => $response->json('data.amount'),
                    'data' => $response->json('data'),
                    'error'  => null,
                ];
            }

            return ['status' => 'failed', 'amount' => 0, 'data' => [], 'error' => null];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Covers the SSL/cURL error 60 case and any other network failure.
            // We log it and tell the caller verification failed, instead of
            // letting a 500 bubble up and leave the frontend hanging forever.
            report($e);
            return ['status' => 'failed', 'amount' => 0, 'data' => [], 'error' => 'connection'];
        }
    }
}