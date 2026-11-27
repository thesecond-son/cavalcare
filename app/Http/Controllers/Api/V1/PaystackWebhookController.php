<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Care\Payment;
use App\Services\Care\PaymentService;
use App\Services\Payments\PaystackClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * POST /api/v1/webhooks/paystack
 *
 * Paystack calls this when a charge succeeds. The signature is checked, then
 * the payment is re-verified with Paystack's API before anything changes.
 * Repeated deliveries are harmless. Only app payments (CVLP-...) are handled;
 * website checkouts keep their own flow.
 *
 * Set the webhook URL in the Paystack dashboard (test and live separately).
 */
class PaystackWebhookController extends Controller
{
    public function __invoke(Request $request, PaymentService $payments)
    {
        if (! PaystackClient::validSignature($request->getContent(), $request->header('x-paystack-signature'))) {
            Log::warning('[Paystack webhook] bad signature', ['ip' => $request->ip()]);
            return response()->json(['ok' => false], 401);
        }

        $event = $request->input('event');
        $reference = (string) $request->input('data.reference');

        if ($event === 'charge.success' && str_starts_with($reference, 'CVLP-')) {
            $payment = Payment::where('reference', $reference)->first();
            if ($payment) {
                try {
                    $payments->confirm($payment);
                } catch (\Throwable $e) {
                    report($e);
                    return response()->json(['ok' => false], 500);   // Paystack retries later
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}
