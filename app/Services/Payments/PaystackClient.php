<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/** Minimal Paystack API client: start a transaction and verify one. Amounts in kobo. */
class PaystackClient
{
    private function http()
    {
        $req = Http::withToken((string) config('services.paystack.secret_key'))
            ->acceptJson()
            ->timeout(15)
            ->baseUrl('https://api.paystack.co');

        // Local-only escape hatch, same as the website's checkout (never on in production).
        if (config('services.paystack.insecure_ssl', false) && ! app()->environment('production')) {
            $req = $req->withOptions(['verify' => false]);
        }

        return $req;
    }

    /** @return array{authorization_url:string, access_code:string, reference:string} */
    public function initialize(string $email, int $amountKobo, string $reference, array $metadata = [], ?string $callbackUrl = null): array
    {
        $res = $this->http()->post('/transaction/initialize', array_filter([
            'email'        => $email,
            'amount'       => $amountKobo,
            'currency'     => 'NGN',
            'reference'    => $reference,
            'metadata'     => $metadata,
            'callback_url' => $callbackUrl,
        ]));

        if (! $res->successful() || ! $res->json('status')) {
            throw new RuntimeException('Paystack initialize failed: ' . $res->status() . ' ' . $res->json('message'));
        }

        return [
            'authorization_url' => $res->json('data.authorization_url'),
            'access_code'       => $res->json('data.access_code'),
            'reference'         => $res->json('data.reference'),
        ];
    }

    /** @return array{paid:bool, amount:int, currency:?string, channel:?string, paid_at:?string, raw:array} */
    public function verify(string $reference): array
    {
        $res = $this->http()->get('/transaction/verify/' . rawurlencode($reference));
        $data = $res->json('data') ?? [];

        return [
            'paid'     => $res->successful() && ($data['status'] ?? null) === 'success',
            'amount'   => (int) ($data['amount'] ?? 0),
            'currency' => $data['currency'] ?? null,
            'channel'  => $data['channel'] ?? null,
            'paid_at'  => $data['paid_at'] ?? null,
            // Keep what helps support; never card numbers or authorization codes.
            'raw'      => array_intersect_key($data, array_flip(['id', 'status', 'reference', 'amount', 'currency', 'channel', 'paid_at', 'gateway_response', 'fees'])),
        ];
    }

    /** Checks the x-paystack-signature header (HMAC-SHA512 of the raw body with the secret key). */
    public static function validSignature(string $rawBody, ?string $signature): bool
    {
        $secret = (string) config('services.paystack.secret_key');

        return $secret !== '' && $signature !== null
            && hash_equals(hash_hmac('sha512', $rawBody, $secret), $signature);
    }
}
