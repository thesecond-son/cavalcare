<?php

namespace App\Services\Care;

use App\Models\Care\CareBooking;
use App\Models\Care\Payment;
use App\Services\Payments\PaystackClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Confirms app payments. A payment is marked paid only after Paystack's own
 * verify endpoint says so, for the exact amount and currency: never on the
 * app's word (spec 10.1). Safe to call repeatedly (webhook + app check).
 */
class PaymentService
{
    public function __construct(private PaystackClient $paystack, private MatchingService $matching)
    {
    }

    /** Returns the booking's state after checking Paystack. */
    public function confirm(Payment $payment): CareBooking
    {
        $booking = $payment->booking;
        if ($payment->status === 'success') {
            return $booking->fresh();
        }

        $result = $this->paystack->verify($payment->reference);

        $justPaid = DB::transaction(function () use ($payment, $result) {
            $payment = Payment::lockForUpdate()->find($payment->id);
            $booking = CareBooking::lockForUpdate()->find($payment->care_booking_id);

            if ($payment->status === 'success') {
                return false;   // another request got here first
            }

            if (! $result['paid']) {
                return false;   // still pending (or abandoned): leave it
            }

            if ($result['amount'] !== $payment->amount_kobo || $result['currency'] !== 'NGN') {
                Log::error('[Payments] amount mismatch', ['reference' => $payment->reference, 'expected' => $payment->amount_kobo, 'got' => $result['amount']]);
                $payment->update(['status' => 'failed', 'raw' => $result['raw']]);
                return false;
            }

            $paidAt = $result['paid_at'] ? Carbon::parse($result['paid_at']) : now();
            $payment->update(['status' => 'success', 'channel' => $result['channel'], 'paid_at' => $paidAt, 'raw' => $result['raw']]);

            if ($booking->status === 'pending_payment') {
                $booking->update(['status' => 'confirmed', 'paid_at' => $paidAt]);
                return true;
            }

            return false;
        });

        if ($justPaid) {
            // No queue worker yet: matching runs straight away, after the payment is safely recorded.
            try {
                $this->matching->start($booking->fresh());
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $booking->fresh();
    }
}
