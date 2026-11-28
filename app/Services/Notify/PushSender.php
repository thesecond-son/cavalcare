<?php

namespace App\Services\Notify;

use App\Models\Device;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends push notifications through Firebase Cloud Messaging (HTTP v1).
 *
 * Driver "log" (default) writes them to the log instead, until Firebase is
 * set up: FCM_DRIVER=fcm, FIREBASE_PROJECT_ID and FIREBASE_CREDENTIALS (path
 * to the service-account JSON, kept outside the web root, never in git).
 *
 * Payloads carry an id and a short message only, never health data
 * (spec 9.3). The app fetches details after it opens.
 */
class PushSender
{
    public function send(Device $device, string $title, string $body, array $data = [], bool $urgent = false): void
    {
        if (config('push.driver') !== 'fcm') {
            if (! app()->environment('production')) {
                Log::info('[Push:log] ' . $device->app . ' ' . $device->platform . ': ' . $title . ' | ' . $body, $data);
            }
            return;
        }

        $token = $this->accessToken();
        $message = [
            'token'        => $device->fcm_token,
            'notification' => ['title' => $title, 'body' => $body],
            'data'         => array_map('strval', $data),
            'android'      => ['priority' => $urgent ? 'HIGH' : 'NORMAL'],
            'apns'         => ['headers' => ['apns-priority' => $urgent ? '10' : '5']],
        ];

        $res = Http::withToken($token)->timeout(10)
            ->post('https://fcm.googleapis.com/v1/projects/' . config('push.firebase.project_id') . '/messages:send', ['message' => $message]);

        // The app was removed or the token replaced: forget this device.
        if (in_array($res->status(), [404, 400], true) && str_contains((string) $res->body(), 'UNREGISTERED')) {
            $device->delete();
            return;
        }
        if (! $res->successful()) {
            Log::warning('[Push] FCM send failed', ['status' => $res->status(), 'device' => $device->id]);
        }
    }

    /** OAuth access token from the service account (signed JWT), cached for 50 minutes. */
    private function accessToken(): string
    {
        return Cache::remember('fcm:access_token', now()->addMinutes(50), function () {
            $sa = json_decode((string) file_get_contents((string) config('push.firebase.credentials')), true);
            $now = time();
            $b64 = fn ($v) => rtrim(strtr(base64_encode(is_string($v) ? $v : json_encode($v)), '+/', '-_'), '=');

            $unsigned = $b64(['alg' => 'RS256', 'typ' => 'JWT']) . '.' . $b64([
                'iss'   => $sa['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]);
            openssl_sign($unsigned, $sig, $sa['private_key'], 'sha256WithRSAEncryption');

            $res = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $unsigned . '.' . $b64($sig),
            ]);

            return (string) $res->json('access_token');
        });
    }
}
