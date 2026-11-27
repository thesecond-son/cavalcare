<?php

namespace Tests\Feature\Api;

use App\Mail\OtpCodeMail;
use App\Models\AppLinkCode;
use App\Models\Booking;
use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\PreBooking;
use App\Models\User;
use App\PreBookingStatus;
use App\Services\Auth\AppLinkCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AppAuthTest extends TestCase
{
    use RefreshDatabase;

    private function sentCode(string $email): string
    {
        $code = null;
        Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $mail) use ($email, &$code) {
            if ($mail->hasTo($email)) {
                $code = $mail->code;
                return true;
            }
            return false;
        });

        return $code;
    }

    private function paidBooking(array $booker = []): PreBooking
    {
        $type = CareType::create(['name' => 'Elderly Care']);
        $plan = CarePlan::create([
            'care_type_id' => $type->id, 'service_level' => 'nursing', 'name' => 'Day Care',
            'price' => 25000, 'billing_cycle' => 'per_day', 'meta' => ['duration_days' => 1],
        ]);

        return PreBooking::create([
            'reference'      => 'PB-TEST' . random_int(1000, 9999),
            'care_plan_id'   => $plan->id,
            'care_type_id'   => $type->id,
            'status'         => PreBookingStatus::PAID,
            'amount_paid'    => 2500000,
            'booker_details' => $booker + ['name' => 'Tolu Adeyemi', 'email' => 'tolu@example.com', 'phone' => '08031234567'],
        ]);
    }

    public function test_email_otp_signs_up_a_new_family_user(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/auth/otp', ['email' => 'Tolu@Example.com'])
            ->assertStatus(202)
            ->assertJsonPath('data.channel', 'email')
            ->assertJsonPath('data.sent_to', 't•••@example.com');

        $code = $this->sentCode('tolu@example.com');
        $this->assertDatabaseMissing('otp_codes', ['code_hash' => $code]); // stored hashed only

        $this->postJson('/api/v1/auth/verify', ['email' => 'tolu@example.com', 'code' => '000000'])
            ->assertStatus(422)->assertJsonPath('error.code', 'code_invalid');

        $res = $this->postJson('/api/v1/auth/verify', ['email' => 'tolu@example.com', 'code' => $code, 'device_name' => 'iPhone 15'])
            ->assertOk()
            ->assertJsonPath('data.is_new', true)
            ->assertJsonPath('data.user.email', 'tolu@example.com')
            ->assertJsonPath('data.user.email_verified', true)
            ->assertJsonPath('data.user.needs_profile', true);

        $token = $res->json('data.token');

        // The code works once.
        $this->postJson('/api/v1/auth/verify', ['email' => 'tolu@example.com', 'code' => $code])
            ->assertStatus(422);

        $this->withToken($token)->patchJson('/api/v1/me', ['name' => 'Tolu Adeyemi', 'time_zone' => 'Europe/London'])
            ->assertOk()->assertJsonPath('data.name', 'Tolu Adeyemi')->assertJsonPath('data.needs_profile', false);

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/me')->assertStatus(401)->assertJsonPath('error.code', 'unauthenticated');
    }

    public function test_otp_locks_after_five_wrong_tries(): void
    {
        Mail::fake();
        $this->postJson('/api/v1/auth/otp', ['email' => 'a@example.com'])->assertStatus(202);
        $code = $this->sentCode('a@example.com');

        for ($i = 0; $i < 4; $i++) {
            $this->postJson('/api/v1/auth/verify', ['email' => 'a@example.com', 'code' => '111111'])->assertStatus(422);
        }
        $this->postJson('/api/v1/auth/verify', ['email' => 'a@example.com', 'code' => '111111'])->assertStatus(429)
            ->assertJsonPath('error.code', 'code_locked');

        // Even the right code is refused now.
        $this->postJson('/api/v1/auth/verify', ['email' => 'a@example.com', 'code' => $code])->assertStatus(429);
    }

    public function test_sms_sign_in_is_refused_in_production_without_a_provider(): void
    {
        $this->app['env'] = 'production';

        $this->postJson('/api/v1/auth/otp', ['phone' => '+447700900123'])
            ->assertStatus(503)->assertJsonPath('error.code', 'sms_unavailable');
    }

    public function test_validation_errors_use_the_api_error_shape(): void
    {
        $this->postJson('/api/v1/auth/otp', [])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonStructure(['error' => ['code', 'message', 'fields' => ['email']]]);
    }

    public function test_website_code_opens_the_app_with_the_booking_filled_in(): void
    {
        Mail::fake();
        $pb = $this->paidBooking();
        Booking::create([
            'pre_booking_id' => $pb->id, 'care_plan_id' => $pb->care_plan_id, 'care_type_id' => $pb->care_type_id,
            'status' => 'intake_complete', 'booker_details' => $pb->booker_details,
            'patient_details' => [
                'full_name' => 'Folake Adeyemi', 'age' => 78, 'gender' => 'Female', 'mobility_level' => 'Assisted',
                'address' => '12 Admiralty Way, Lekki', 'diagnosis' => 'Hypertension',
                'allergies' => [['allergy' => 'Penicillin']], 'medications' => [['medication' => 'Amlodipine 5mg']],
            ],
            'booking_details' => ['start_date' => '2026-12-01'],
        ]);

        $code = app(AppLinkCodeService::class)->issue($pb);

        // Wrong identifier: refused without saying why.
        $this->postJson('/api/v1/auth/website-code', ['email' => 'someone@else.com', 'code' => $code])
            ->assertStatus(422)->assertJsonPath('error.code', 'code_invalid');

        // Right phone (booked as a local Nigerian number), code typed in lower case without the dash.
        $res = $this->postJson('/api/v1/auth/website-code', [
            'phone' => '+2348031234567', 'code' => strtolower(str_replace('-', '', $code)), 'device_name' => 'Pixel 8',
        ])->assertOk()
            ->assertJsonPath('data.is_new', true)
            ->assertJsonPath('data.user.name', 'Tolu Adeyemi')
            ->assertJsonPath('data.user.phone', '+2348031234567')
            ->assertJsonPath('data.website_booking.reference', $pb->reference)
            ->assertJsonPath('data.website_booking.care_plan.level_label', 'Licensed Nurse')
            ->assertJsonPath('data.website_booking.parent.full_name', 'Folake Adeyemi')
            ->assertJsonPath('data.website_booking.parent.sex', 'female')
            ->assertJsonPath('data.website_booking.parent.allergies.0', 'Penicillin');

        $this->assertNotNull($res->json('data.token'));
        $this->assertSame($res->json('data.user.id'), $pb->fresh()->user_id);

        // Works once.
        $this->postJson('/api/v1/auth/website-code', ['email' => 'tolu@example.com', 'code' => $code])->assertStatus(422);

        // The linked booking is listed for the account.
        $this->withToken($res->json('data.token'))->getJson('/api/v1/me/website-bookings')
            ->assertOk()->assertJsonPath('data.0.reference', $pb->reference);
    }

    public function test_website_code_signs_into_an_existing_account_with_the_same_email(): void
    {
        $existing = User::factory()->create(['email' => 'tolu@example.com', 'name' => 'Tolu A.']);
        $pb = $this->paidBooking();
        $code = app(AppLinkCodeService::class)->issue($pb);

        $this->postJson('/api/v1/auth/website-code', ['email' => 'TOLU@example.com', 'code' => $code])
            ->assertOk()
            ->assertJsonPath('data.is_new', false)
            ->assertJsonPath('data.user.id', $existing->id);
    }

    public function test_website_code_is_revoked_after_five_mismatches(): void
    {
        $pb = $this->paidBooking();
        $code = app(AppLinkCodeService::class)->issue($pb);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/website-code', ['email' => "wrong{$i}@example.com", 'code' => $code])->assertStatus(422);
        }

        $this->assertNotNull(AppLinkCode::first()->revoked_at);
        $this->postJson('/api/v1/auth/website-code', ['email' => 'tolu@example.com', 'code' => $code])->assertStatus(422);
    }

    public function test_tokens_from_the_api_cannot_reach_caval_os(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('x', ['app:cavalcare'])->plainTextToken;

        $this->withToken($token)->get('/adminos')->assertRedirect(route('os.login'));
    }
}
