<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\WebsiteBookingResource;
use App\Models\PreBooking;
use App\Models\User;
use App\Services\Auth\AppLinkCodeService;
use App\Services\Auth\OtpService;
use App\Support\ApiError;
use App\Support\Phone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Sign-in for the Cavalcare (family) app.
 *
 *  POST /api/v1/auth/otp           send a 6-digit code by email (or SMS once a provider exists)
 *  POST /api/v1/auth/verify        check the code, returns a device token
 *  POST /api/v1/auth/website-code  "I booked on the website": email or phone + the code from the booking email
 *  POST /api/v1/auth/logout        revoke this device's token
 */
class AuthController extends Controller
{
    public function __construct(private OtpService $otp, private AppLinkCodeService $links)
    {
    }

    public function sendOtp(Request $request): JsonResponse
    {
        [$channel, $destination] = $this->identifier($request);

        if ($channel === 'sms' && ! $this->otp->smsAvailable()) {
            return ApiError::make('sms_unavailable', 'Text message codes aren\'t available yet. Please use your email address.', 503);
        }

        try {
            $this->otp->send($channel, $destination, $request->ip());
        } catch (\Throwable $e) {
            report($e);
            return ApiError::make('delivery_failed', 'We couldn\'t send your code just now. Please try again in a moment.', 503);
        }

        return response()->json(['data' => [
            'sent'       => true,
            'channel'    => $channel,
            'sent_to'    => $channel === 'email' ? $this->maskEmail($destination) : Phone::mask($destination),
            'expires_in' => OtpService::TTL_MINUTES * 60,
        ]], 202);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'code'        => ['required', 'string', 'max:10'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);
        [$channel, $destination] = $this->identifier($request);

        return DB::transaction(function () use ($request, $channel, $destination) {
            $result = $this->otp->verify($destination, $request->input('code'));

            if ($result === 'locked') {
                return ApiError::make('code_locked', 'Too many wrong tries. Ask for a new code.', 429);
            }
            if ($result !== 'ok') {
                return ApiError::make('code_invalid', 'That code isn\'t right or has expired.', 422, ['code' => ['That code isn\'t right or has expired.']]);
            }

            $column = $channel === 'email' ? 'email' : 'phone_e164';
            $user   = $channel === 'email'
                ? User::whereRaw('LOWER(email) = ?', [$destination])->lockForUpdate()->first()
                : User::where('phone_e164', $destination)->lockForUpdate()->first();
            $isNew  = false;

            if (! $user) {
                $user  = new User([$column => $destination, 'name' => '']);
                $user->forceFill(['type' => self::app($request) === 'cavalpro' ? User::TYPE_PROFESSIONAL : User::TYPE_FAMILY]);
                $isNew = true;
            }

            $user->forceFill([$channel === 'email' ? 'email_verified_at' : 'phone_verified_at' => now()])->save();

            return $this->issueToken($user, $request, $isNew || blank($user->name), null);
        });
    }

    public function redeemWebsiteCode(Request $request): JsonResponse
    {
        $request->validate([
            'code'        => ['required', 'string', 'max:20'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);
        [$channel, $destination] = $this->identifier($request);

        return DB::transaction(function () use ($request, $channel, $destination) {
            $link = $this->links->match(
                $request->input('code'),
                $channel === 'email' ? $destination : null,
                $channel === 'sms' ? $destination : null,
            );

            if (! $link) {
                return ApiError::make(
                    'code_invalid',
                    'That code doesn\'t match. Check the code in your booking email and use the same email or phone you booked with, or sign in with a one-time code instead.',
                    422,
                    ['code' => ['That code doesn\'t match.']]
                );
            }

            $booking = $link->preBooking;
            $booker  = $booking->booker_details ?? [];
            $email   = isset($booker['email']) ? Str::lower(trim($booker['email'])) : null;
            $phone   = Phone::toE164($booker['phone'] ?? null);

            if ($booking->user_id) {
                $user = User::lockForUpdate()->find($booking->user_id);
            } else {
                $user = ($email ? User::whereRaw('LOWER(email) = ?', [$email])->lockForUpdate()->first() : null)
                    ?? ($phone ? User::where('phone_e164', $phone)->lockForUpdate()->first() : null);
            }

            $isNew = false;
            if (! $user) {
                $user = new User(['name' => $booker['name'] ?? '', 'email' => $email]);
                $user->forceFill(['type' => User::TYPE_FAMILY]);
                $isNew = true;
            }

            // The code was emailed to the booker, so holding it proves the email.
            if ($email && Str::lower((string) $user->email) === $email) {
                $user->forceFill(['email_verified_at' => $user->email_verified_at ?? now()]);
            }
            if ($phone && ! $user->phone_e164 && ! User::where('phone_e164', $phone)->exists()) {
                $user->phone_e164 = $phone;
            }
            if (blank($user->name) && ! empty($booker['name'])) {
                $user->name = $booker['name'];
            }
            $user->save();

            // Link this booking, and any other website bookings made with the same email.
            $booking->forceFill(['user_id' => $user->id])->save();
            if ($email) {
                PreBooking::whereNull('user_id')
                    ->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(booker_details, "$.email"))) = ?', [$email])
                    ->update(['user_id' => $user->id]);
            }

            $link->forceFill(['redeemed_at' => now(), 'redeemed_by' => $user->id])->save();

            // Make this account the Owner of every client from its website bookings.
            $importer = app(\App\Services\Care\WebsiteIntakeImporter::class);
            PreBooking::with('booking')->where('user_id', $user->id)->get()
                ->each(function (PreBooking $pb) use ($importer) {
                    if ($pb->booking) {
                        $importer->import($pb->booking);
                    }
                });

            return $this->issueToken($user, $request, $isNew, $booking);
        });
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['data' => ['signed_out' => true]]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /** Reads either "email" or "phone" from the request. Returns [channel, normalised destination]. */
    private function identifier(Request $request): array
    {
        $request->validate([
            'email' => ['nullable', 'required_without:phone', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:20'],
        ]);

        if ($request->filled('email')) {
            return ['email', Str::lower(trim($request->input('email')))];
        }

        $phone = Phone::toE164($request->input('phone'));
        if (! $phone) {
            abort(ApiError::make('validation_failed', 'Enter your number with its country code, e.g. +44 7700 900123.', 422, [
                'phone' => ['Enter your number with its country code, e.g. +44 7700 900123.'],
            ]));
        }

        return ['sms', $phone];
    }

    private function issueToken(User $user, Request $request, bool $isNew, ?PreBooking $booking): JsonResponse
    {
        $device = Str::limit((string) ($request->input('device_name') ?: 'Cavalcare app'), 100, '');

        // One token per device: signing in again on the same device replaces the old token.
        $app = self::app($request, $booking);
        $user->tokens()->where('name', $device)->delete();
        $token = $user->createToken($device, ['app:' . $app], now()->addDays(90))->plainTextToken;

        $user->forceFill(['last_seen_at' => now()])->save();

        return response()->json(['data' => array_filter([
            'token'           => $token,
            'token_type'      => 'Bearer',
            'is_new'          => $isNew,
            'user'            => new UserResource($user->fresh()),
            'website_booking' => $booking ? new WebsiteBookingResource($booking->fresh(['carePlan', 'careType', 'addOns', 'booking'])) : null,
        ], fn ($v) => $v !== null)]);
    }

    /**
     * Which app is signing in, from the X-App header: "cavalpro" (caregivers)
     * or "cavalcare" (families, the default). Website codes are family-only.
     * The token can only call that app's endpoints.
     */
    private static function app(Request $request, ?PreBooking $booking = null): string
    {
        return ! $booking && $request->header('X-App') === 'cavalpro' ? 'cavalpro' : 'cavalcare';
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);

        return mb_substr($local, 0, 1) . str_repeat('•', max(2, min(6, mb_strlen($local) - 1))) . '@' . $domain;
    }
}
