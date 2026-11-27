<?php

namespace App\Http\Controllers\Api\V1\Family;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CareBookingResource;
use App\Models\Care\CareBooking;
use App\Models\Care\Patient;
use App\Models\Care\Payment;
use App\Models\Care\Professional;
use App\Models\CarePlan;
use App\Services\Care\BookingPricing;
use App\Services\Care\PaymentService;
use App\Services\Payments\PaystackClient;
use App\Support\ApiError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Booking care in the family app (backend spec 7.4, 10.1):
 * quote -> create (returns the Paystack checkout) -> pay in Paystack ->
 * server confirms (webhook, or the app calls verify-payment on return).
 */
class BookingController extends Controller
{
    public function __construct(private BookingPricing $pricing)
    {
    }

    private function rules(): array
    {
        return [
            'patient_id'       => ['required', 'uuid'],
            'care_plan_id'     => ['required', 'integer'],
            'start_date'       => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before:' . now()->addMonths(6)->toDateString()],
            'start_hour'       => ['nullable', 'integer', 'between:0,23'],
            'units'            => ['nullable', 'integer', 'between:1,' . BookingPricing::MAX_UNITS],
            'add_on_ids'       => ['nullable', 'array', 'max:10'],
            'add_on_ids.*'     => ['integer'],
            'caregiver_choice' => ['nullable', Rule::in(['match_me', 'chosen'])],
            'professional_id'  => ['nullable', 'required_if:caregiver_choice,chosen', 'uuid'],
            'preferences'      => ['nullable', 'array'],
            'preferences.sex'  => ['nullable', Rule::in(['female', 'male'])],
            'preferences.language' => ['nullable', 'string', 'max:40'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** POST /family/bookings/quote: price and summary for step 5 (review). Nothing is saved. */
    public function quote(Request $request)
    {
        $data = $request->validate($this->rules());
        [, $plan] = $this->resolve($request, $data);
        $q = $this->pricing->quote($plan, $data['start_date'], $data['units'] ?? null, $data['add_on_ids'] ?? []);

        return response()->json(['data' => [
            'plan'          => ['id' => $plan->id, 'name' => $plan->name, 'level_label' => $plan->service_level_label, 'display' => $plan->formatted_price],
            'start_date'    => $data['start_date'],
            'end_date'      => $q['end_date'],
            'units'         => $q['units'],
            'plan_kobo'     => $q['plan_kobo'],
            'add_ons'       => $q['add_ons'],
            'add_ons_kobo'  => $q['add_ons_kobo'],
            'total_kobo'    => $q['total_kobo'],
            'total_display' => BookingPricing::naira($q['total_kobo']),
            'currency'      => 'NGN',
            'price_note'    => 'Care and nursing prices may vary depending on the complexity of the patient\'s needs.',
        ]]);
    }

    /**
     * POST /family/bookings: creates the booking (pending_payment) and a Paystack
     * transaction. Send an Idempotency-Key header: a retry returns the same booking.
     */
    public function store(Request $request, PaystackClient $paystack)
    {
        $key = $request->header('Idempotency-Key');
        $cacheKey = $key ? 'idem:booking:' . $request->user()->id . ':' . sha1($key) : null;
        if ($cacheKey && ($cached = Cache::get($cacheKey))) {
            return response()->json($cached, 201);
        }

        $data = $request->validate($this->rules());
        [$patient, $plan] = $this->resolve($request, $data, edit: true);

        $email = $request->user()->email;
        if (! $email) {
            return ApiError::make('email_required', 'Add your email address to your profile to pay.', 422);
        }

        $chosen = null;
        if (($data['caregiver_choice'] ?? 'match_me') === 'chosen') {
            $chosen = Professional::where('id', $data['professional_id'])->where('status', 'active')->first();
            if (! $chosen || ($plan->service_level === 'nursing' && $chosen->title !== 'rn')) {
                return ApiError::make('validation_failed', 'Some details need checking.', 422, ['professional_id' => ['That caregiver isn\'t available for this plan. Choose another or use Match me.']]);
            }
        }

        $q = $this->pricing->quote($plan, $data['start_date'], $data['units'] ?? null, $data['add_on_ids'] ?? []);

        [$booking, $payment] = DB::transaction(function () use ($request, $data, $patient, $plan, $q, $chosen) {
            $booking = CareBooking::create([
                'patient_id'               => $patient->id,
                'booked_by'                => $request->user()->id,
                'care_plan_id'             => $plan->id,
                'care_type_id'             => $plan->care_type_id,
                'service_level'            => $plan->service_level,
                'plan_name'                => $plan->name,
                'billing_cycle'            => $plan->billing_cycle,
                'start_date'               => $data['start_date'],
                'start_hour'               => $data['start_hour'] ?? null,
                'units'                    => $q['units'],
                'end_date'                 => $q['end_date'],
                'caregiver_choice'         => $chosen ? 'chosen' : 'match_me',
                'requested_professional_id' => $chosen?->id,
                'preferences'              => array_filter($data['preferences'] ?? []),
                'notes'                    => $data['notes'] ?? null,
                'price_per_unit_kobo'      => $q['unit_kobo'],
                'add_ons'                  => $q['add_ons'],
                'add_ons_total_kobo'       => $q['add_ons_kobo'],
                'total_kobo'               => $q['total_kobo'],
                'status'                   => 'pending_payment',
            ]);

            $payment = $booking->payments()->create([
                'payer_id'    => $request->user()->id,
                'reference'   => 'CVLP-' . Str::upper(Str::random(12)),
                'amount_kobo' => $q['total_kobo'],
                'status'      => 'pending',
            ]);

            return [$booking, $payment];
        });

        try {
            $checkout = $paystack->initialize($email, $payment->amount_kobo, $payment->reference, [
                'booking_reference' => $booking->reference,
                'source'            => 'cavalcare_app',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return ApiError::make('payment_unavailable', 'We couldn\'t start the payment. Please try again in a moment.', 503);
        }

        $body = ['data' => [
            'booking' => new CareBookingResource($booking->load(['careType', 'payments'])),
            'payment' => [
                'reference'         => $payment->reference,
                'amount_kobo'       => $payment->amount_kobo,
                'authorization_url' => $checkout['authorization_url'],
                'access_code'       => $checkout['access_code'],
                'public_key'        => config('services.paystack.public_key'),
            ],
        ]];
        $body = json_decode(json_encode($body), true);

        if ($cacheKey) {
            Cache::put($cacheKey, $body, now()->addDay());
        }

        return response()->json($body, 201);
    }

    /** POST /family/bookings/{id}/verify-payment: call when Paystack checkout closes. */
    public function verifyPayment(Request $request, string $id, PaymentService $payments)
    {
        $booking = $this->booking($request, $id);
        $payment = $booking->payments()->latest()->first();

        if ($payment && $payment->status === 'pending') {
            try {
                $booking = $payments->confirm($payment);
            } catch (\Throwable $e) {
                report($e);
                return ApiError::make('payment_check_failed', 'We couldn\'t check your payment just now. If you paid, it will update shortly.', 503);
            }
        }

        return $this->show($request, $id);
    }

    /** GET /family/bookings?patient_id=&status= */
    public function index(Request $request)
    {
        $patientIds = Patient::whereHas('members', fn ($q) => $q->where('user_id', $request->user()->id))->pluck('id');

        $bookings = CareBooking::with(['careType', 'professional.user', 'payments'])
            ->whereIn('patient_id', $patientIds)
            ->when($request->query('patient_id'), fn ($q, $p) => $q->where('patient_id', $p))
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('start_date')
            ->limit(100)
            ->get();

        return response()->json(['data' => CareBookingResource::collection($bookings)]);
    }

    /** GET /family/bookings/{id} */
    public function show(Request $request, string $id)
    {
        return response()->json(['data' => new CareBookingResource($this->booking($request, $id)->load(['careType', 'professional.user', 'payments']))]);
    }

    /**
     * POST /family/bookings/{id}/cancel: before a caregiver is confirmed.
     * Paid bookings are flagged for a refund review by Finance (policy not final, spec 10.3).
     */
    public function cancel(Request $request, string $id)
    {
        $booking = $this->booking($request, $id, edit: true);
        $data = $request->validate(['reason' => ['required', 'string', 'max:255']]);

        if (! in_array($booking->status, ['pending_payment', 'confirmed', 'matching'], true)) {
            return ApiError::make('cannot_cancel', 'This booking can\'t be cancelled in the app now. Please contact CavalCare.', 409);
        }

        DB::transaction(function () use ($booking, $data, $request) {
            $booking->update([
                'status' => 'cancelled', 'cancelled_at' => now(), 'cancel_reason' => $data['reason'], 'cancelled_by' => $request->user()->id,
            ]);
            $booking->offers()->where('status', 'open')->update(['status' => 'withdrawn', 'responded_at' => now()]);
        });

        return $this->show($request, $id);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /** @return array{0: Patient, 1: CarePlan} */
    private function resolve(Request $request, array $data, bool $edit = false): array
    {
        $patient = Patient::with('members')->find($data['patient_id']);
        $member = $patient?->members->firstWhere('user_id', $request->user()->id);
        if (! $member) {
            abort(ApiError::make('validation_failed', 'Some details need checking.', 422, ['patient_id' => ['Choose one of your family members.']]));
        }
        if ($edit && ! in_array($member->role, ['owner', 'caregiver'], true)) {
            abort(ApiError::make('role_not_allowed', 'Only the owner or a family caregiver can book care.', 403));
        }

        $plan = CarePlan::find($data['care_plan_id']);   // soft-deleted (retired) plans can't be booked
        if (! $plan) {
            abort(ApiError::make('validation_failed', 'Some details need checking.', 422, ['care_plan_id' => ['That plan is no longer available.']]));
        }

        return [$patient, $plan];
    }

    private function booking(Request $request, string $id, bool $edit = false): CareBooking
    {
        $booking = CareBooking::with('patient.members')->find($id);
        $member = $booking?->patient?->members->firstWhere('user_id', $request->user()->id);
        abort_unless($member, 404);
        if ($edit && ! in_array($member->role, ['owner', 'caregiver'], true)) {
            abort(ApiError::make('role_not_allowed', 'Only the owner or a family caregiver can change this booking.', 403));
        }

        return $booking;
    }
}
