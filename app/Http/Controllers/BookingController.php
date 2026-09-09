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

            // Meta Conversions API — server-side Purchase event, mirroring the
            // browser-side pixel fired on bookings.received. Same event_id
            // (the pre-booking reference) so Meta deduplicates the two.
            try {
                $this->sendMetaPurchaseEvent($preBooking, $request);
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
     * Send a server-side "Purchase" event to Meta's Conversions API.
     *
     * Uses the same event_id (pre-booking reference) as the browser-side
     * pixel call on bookings.received.blade.php so Meta deduplicates the
     * two instead of double-counting the conversion. PII is SHA-256 hashed
     * before it ever leaves the server, per Meta's requirements.
     */
    private function sendMetaPurchaseEvent(PreBooking $preBooking, Request $request): void
    {
        // Unconditional, level-agnostic entry marker. Uses error() on
        // purpose (not info/warning) so it can never be silently dropped by
        // a stricter LOG_LEVEL in .env or config/logging.php. If this line
        // alone doesn't show up in the log after a payment, this method is
        // not being reached at all — the problem is upstream in store(),
        // not in this method.
        \Illuminate\Support\Facades\Log::error('[MetaCAPI] sendMetaPurchaseEvent() invoked', [
            'reference' => $preBooking->reference,
        ]);

        try {
            $pixelId     = config('services.meta.pixel_id', '1111010055204698');
            $accessToken = config('services.meta.capi_token');

            \Illuminate\Support\Facades\Log::error('[MetaCAPI] config resolved', [
                'pixel_id_present'  => !empty($pixelId),
                'token_present'     => !empty($accessToken),
                'test_code_present' => !empty(config('services.meta.test_event_code')),
            ]);

            if (empty($accessToken)) {
                \Illuminate\Support\Facades\Log::error(
                    '[MetaCAPI] Skipped — META_CAPI_TOKEN resolved empty. Check .env and run php artisan config:clear.',
                    ['reference' => $preBooking->reference]
                );
                return;
            }

            $email = $preBooking->booker_details['email'] ?? null;
            $phone = $preBooking->booker_details['phone'] ?? null;

            $userData = array_filter([
                'em' => $email ? [hash('sha256', strtolower(trim($email)))] : null,
                'ph' => $phone ? [hash('sha256', preg_replace('/\D+/', '', $phone))] : null,
                'client_ip_address' => $request->ip(),
                'client_user_agent' => $request->userAgent(),
                'fbc' => $request->cookie('_fbc'),
                'fbp' => $request->cookie('_fbp'),
            ]);

            $payload = [
                'data' => [[
                    'event_name'       => 'Purchase',
                    'event_time'       => now()->timestamp,
                    'event_id'         => $preBooking->reference, // matches pixel eventID
                    'event_source_url' => route('booking.received', $preBooking->reference),
                    'action_source'    => 'website',
                    'user_data'        => $userData,
                    'custom_data'      => [
                        'currency'     => 'NGN',
                        'value'        => $preBooking->amount_paid ? $preBooking->amount_paid / 100 : 0,
                        'content_name' => $preBooking->carePlan->name ?? 'CavalCare Booking',
                        'content_type' => 'product',
                    ],
                ]],
            ];

            // Only present in Meta's Test Events tab if this is included — a
            // real successful call WITHOUT this still lands in Events
            // Manager as a production event, it just won't show in the Test
            // Events panel. Set META_CAPI_TEST_EVENT_CODE in .env while
            // testing; leave unset in production.
            $testEventCode = config('services.meta.test_event_code');
            if (!empty($testEventCode)) {
                $payload['test_event_code'] = $testEventCode;
            }

            \Illuminate\Support\Facades\Log::error('[MetaCAPI] Sending request to Meta', [
                'reference' => $preBooking->reference,
                'url'       => "https://graph.facebook.com/v20.0/{$pixelId}/events",
                'payload'   => $payload, // hashed PII only — safe to log
            ]);

            $response = Http::asJson()
                ->timeout(10)
                ->post("https://graph.facebook.com/v20.0/{$pixelId}/events", array_merge(
                    $payload,
                    ['access_token' => $accessToken]
                ));

            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error('[MetaCAPI] Request failed', [
                    'reference' => $preBooking->reference,
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                ]);
            } else {
                \Illuminate\Support\Facades\Log::error('[MetaCAPI] Request succeeded', [
                    'reference' => $preBooking->reference,
                    'response'  => $response->json(),
                ]);
            }
        } catch (\Throwable $e) {
            // Deliberately caught and logged HERE, inside the method itself,
            // rather than relying on the caller's catch block. This is the
            // fix for the "no log at all" symptom: if the exception was
            // being caught upstream by report($e) and that channel/level
            // wasn't visible to you, this guarantees an error-level entry
            // regardless of where else it's reported.
            \Illuminate\Support\Facades\Log::error('[MetaCAPI] Exception thrown', [
                'reference' => $preBooking->reference,
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);
        }
    }

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