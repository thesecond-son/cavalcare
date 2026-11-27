<?php

namespace Tests\Feature\Api;

use App\Models\Care\CareBooking;
use App\Models\Care\Patient;
use App\Models\Care\Professional;
use App\Models\Care\ShiftOffer;
use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FamilyAndProApiTest extends TestCase
{
    use RefreshDatabase;

    private CareType $type;
    private CarePlan $nursingDay;
    private CarePlan $caretakerDay;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.paystack.secret_key' => 'sk_test_x', 'services.paystack.public_key' => 'pk_test_x']);
        $this->type = CareType::create(['name' => 'Elderly Care']);
        $this->nursingDay = CarePlan::create(['care_type_id' => $this->type->id, 'service_level' => 'nursing', 'name' => 'Day Care', 'price' => 25000, 'billing_cycle' => 'per_day']);
        $this->caretakerDay = CarePlan::create(['care_type_id' => $this->type->id, 'service_level' => 'caretaker', 'name' => 'Day Care', 'price' => 15000, 'billing_cycle' => 'per_day']);
    }

    private function family(): array
    {
        $u = User::factory()->create(['email' => 'tolu' . random_int(1, 99999) . '@example.com']);
        return [$u, $u->createToken('t', ['app:cavalcare'])->plainTextToken];
    }

    private function pro(string $title = 'rn', array $attrs = []): array
    {
        $u = User::factory()->create(['name' => 'Nurse ' . random_int(1, 9999)]);
        $p = Professional::create(array_merge(['user_id' => $u->id, 'title' => $title, 'home_area' => 'Lekki', 'lat' => 6.45, 'lng' => 3.47, 'max_travel_km' => 20, 'status' => 'active'], $attrs));
        foreach ($p->requiredCredentialTypes() as $t) {
            $p->credentials()->create(['type' => $t, 'status' => 'verified', 'verified_at' => now()]);
        }
        return [$p->fresh(), $u->createToken('t', ['app:cavalpro'])->plainTextToken];
    }

    private function patient(string $token): string
    {
        $id = $this->withToken($token)->postJson('/api/v1/family/patients', [
            'full_name' => 'Folake Adeyemi', 'display_name' => 'Mama Folake', 'sex' => 'female', 'age' => 78,
            'address_line' => '12 Admiralty Way', 'area' => 'Lekki Phase 1', 'city' => 'Lagos', 'lat' => 6.447, 'lng' => 3.472,
            'relationship' => 'daughter',
        ])->assertCreated()->json('data.id');
        $this->app['auth']->forgetGuards();

        return $id;
    }

    private function fakePaystack(int $amount, string $status = 'success'): void
    {
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response(['status' => true, 'data' => ['authorization_url' => 'https://checkout.paystack.com/abc', 'access_code' => 'abc', 'reference' => 'x']]),
            'api.paystack.co/transaction/verify/*'   => Http::response(['status' => true, 'data' => ['status' => $status, 'amount' => $amount, 'currency' => 'NGN', 'channel' => 'card', 'paid_at' => now()->toIso8601String(), 'reference' => 'x']]),
        ]);
    }

    private function bookAndPay(string $token, string $patientId, CarePlan $plan, array $extra = []): CareBooking
    {
        $this->fakePaystack($plan->price * 100 * ($extra['units'] ?? 1));
        $id = $this->withToken($token)->postJson('/api/v1/family/bookings', array_merge([
            'patient_id' => $patientId, 'care_plan_id' => $plan->id, 'start_date' => now()->addDay()->toDateString(),
        ], $extra))->assertCreated()->json('data.booking.id');
        $this->withToken($token)->postJson("/api/v1/family/bookings/{$id}/verify-payment")->assertOk();
        $this->app['auth']->forgetGuards();

        return CareBooking::find($id);
    }

    // ── Family: patients ─────────────────────────────────────────────────

    public function test_add_parent_steps_and_family_roles(): void
    {
        [$owner, $token] = $this->family();
        $id = $this->patient($token);

        $this->withToken($token)->putJson("/api/v1/family/patients/{$id}/health", [
            'conditions' => ['Hypertension'],
            'allergies' => [['name' => 'Penicillin', 'reaction' => 'Rash', 'severe' => true]],
            'medications' => [['name' => 'Amlodipine', 'dose' => '5 mg', 'times' => ['08:00']]],
            'mobility_level' => 'assisted',
        ])->assertOk()->assertJsonPath('data.allergies.0.severe', true)->assertJsonPath('data.medications.0.times.0', '08:00');

        $this->withToken($token)->putJson("/api/v1/family/patients/{$id}/emergency-contacts", ['contacts' => [['name' => 'Kunle', 'phone' => '08031112222']]])
            ->assertOk()->assertJsonPath('data.emergency_contacts.0.phone', '+2348031112222');
        $this->withToken($token)->postJson("/api/v1/family/patients/{$id}/consent", ['agreed' => true, 'text_version' => '2026-11'])
            ->assertOk()->assertJsonPath('data.consent_given', true)->assertJsonPath('data.my_role', 'owner');

        // A viewer can read but not edit; a stranger can't see it at all.
        [$viewer, $vToken] = $this->family();
        Patient::find($id)->members()->create(['user_id' => $viewer->id, 'role' => 'viewer']);
        $this->app['auth']->forgetGuards();
        $this->withToken($vToken)->getJson("/api/v1/family/patients/{$id}")->assertOk()->assertJsonPath('data.can_edit', false);
        $this->withToken($vToken)->patchJson("/api/v1/family/patients/{$id}", ['area' => 'Ikoyi'])->assertStatus(403)->assertJsonPath('error.code', 'role_not_allowed');

        [, $sToken] = $this->family();
        $this->app['auth']->forgetGuards();
        $this->withToken($sToken)->getJson("/api/v1/family/patients/{$id}")->assertNotFound();
    }

    public function test_tokens_only_reach_their_own_app(): void
    {
        [, $famToken] = $this->family();
        [, $proToken] = $this->pro();

        $this->withToken($proToken)->getJson('/api/v1/family/patients')->assertForbidden();
        $this->app['auth']->forgetGuards();
        $this->withToken($famToken)->getJson('/api/v1/pro/profile')->assertForbidden();
        $this->app['auth']->forgetGuards();
        $this->withToken($proToken)->getJson('/api/v1/me')->assertOk();
    }

    public function test_catalogue_lists_plans_by_level(): void
    {
        [, $token] = $this->family();
        $this->withToken($token)->getJson("/api/v1/family/care-types/{$this->type->slug}/plans?level=nursing")
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.price_kobo', 2500000)->assertJsonPath('data.0.level_label', 'Licensed Nurse');
    }

    // ── CavalPro onboarding ──────────────────────────────────────────────

    public function test_caregiver_onboarding_upload_and_checklist(): void
    {
        Storage::fake('local');
        $u = User::factory()->create();
        $token = $u->createToken('t', ['app:cavalpro'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/pro/profile')->assertNotFound()->assertJsonPath('error.code', 'profile_missing');
        $this->withToken($token)->postJson('/api/v1/pro/profile/about', [
            'name' => 'Adaeze Okafor', 'title' => 'rn', 'sex' => 'female', 'home_area' => 'Lekki', 'id_number' => 'NMCN-778812', 'languages' => ['English', 'Igbo'],
        ])->assertCreated()->assertJsonPath('data.status', 'applicant');

        $pdf = UploadedFile::fake()->createWithContent('licence.pdf', "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");
        $upload = $this->withToken($token)->post('/api/v1/uploads', ['file' => $pdf], ['Accept' => 'application/json'])
            ->assertCreated()->json('data.upload_id');

        $this->withToken($token)->postJson('/api/v1/pro/credentials', ['type' => 'nmcn', 'upload_id' => $upload, 'valid_to' => now()->addYear()->toDateString()])
            ->assertOk()->assertJsonPath('data.status', 'verifying')
            ->assertJsonPath('data.items.0.type', 'nmcn')->assertJsonPath('data.items.0.status', 'in_progress')->assertJsonPath('data.items.0.number_last4', '8812');

        // The same upload can't be reused.
        $this->withToken($token)->postJson('/api/v1/pro/credentials', ['type' => 'bls', 'upload_id' => $upload])->assertStatus(422);

        $this->withToken($token)->putJson('/api/v1/pro/availability', ['rules' => [['weekday' => 1, 'start_time' => '08:00', 'end_time' => '17:00']], 'open_to_extra_shifts' => true])
            ->assertOk()->assertJsonPath('data.availability.0.start_time', '08:00')->assertJsonPath('data.open_to_extra_shifts', true);
    }

    // ── Bookings and payment ─────────────────────────────────────────────

    public function test_booking_quote_payment_and_matching(): void
    {
        [, $token] = $this->family();
        $patientId = $this->patient($token);
        [$nurse] = $this->pro('rn');
        [$carer] = $this->pro('carer');

        $this->withToken($token)->postJson('/api/v1/family/bookings/quote', [
            'patient_id' => $patientId, 'care_plan_id' => $this->nursingDay->id, 'start_date' => now()->addDay()->toDateString(), 'units' => 3,
        ])->assertOk()->assertJsonPath('data.total_kobo', 7500000)->assertJsonPath('data.total_display', '₦75,000');

        $booking = $this->bookAndPay($token, $patientId, $this->nursingDay, ['units' => 3]);

        $this->assertSame('matching', $booking->status);
        $this->assertNotNull($booking->paid_at);
        $offer = ShiftOffer::where('care_booking_id', $booking->id)->firstOrFail();
        $this->assertSame($nurse->id, $offer->professional_id);       // nursing plan: never the carer
        $this->assertNotSame($carer->id, $offer->professional_id);
        $this->assertSame(1750000, $offer->pay_per_visit_kobo);       // 70% of ₦25,000 = ₦17,500
    }

    public function test_payment_is_not_trusted_when_paystack_amount_differs(): void
    {
        [, $token] = $this->family();
        $patientId = $this->patient($token);
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response(['status' => true, 'data' => ['authorization_url' => 'u', 'access_code' => 'a', 'reference' => 'x']]),
            'api.paystack.co/transaction/verify/*'   => Http::response(['status' => true, 'data' => ['status' => 'success', 'amount' => 100, 'currency' => 'NGN']]),
        ]);
        $id = $this->withToken($token)->postJson('/api/v1/family/bookings', ['patient_id' => $patientId, 'care_plan_id' => $this->nursingDay->id, 'start_date' => now()->addDay()->toDateString()])->json('data.booking.id');
        $this->withToken($token)->postJson("/api/v1/family/bookings/{$id}/verify-payment")->assertOk()->assertJsonPath('data.status', 'pending_payment');
    }

    public function test_webhook_checks_the_signature_then_confirms(): void
    {
        [, $token] = $this->family();
        $patientId = $this->patient($token);
        $this->fakePaystack(2500000);
        $id = $this->withToken($token)->postJson('/api/v1/family/bookings', ['patient_id' => $patientId, 'care_plan_id' => $this->nursingDay->id, 'start_date' => now()->addDay()->toDateString()])->json('data.booking.id');
        $ref = CareBooking::find($id)->payments()->first()->reference;

        $body = json_encode(['event' => 'charge.success', 'data' => ['reference' => $ref]]);
        $this->call('POST', '/api/v1/webhooks/paystack', [], [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_X_PAYSTACK_SIGNATURE' => 'forged'], $body)->assertStatus(401);
        $this->assertSame('pending_payment', CareBooking::find($id)->status);

        $sig = hash_hmac('sha512', $body, 'sk_test_x');
        $this->call('POST', '/api/v1/webhooks/paystack', [], [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_X_PAYSTACK_SIGNATURE' => $sig], $body)->assertOk();
        $this->assertSame('confirmed', CareBooking::find($id)->fresh()->paid_at ? 'confirmed' : 'no');
    }

    // ── Shift offers ─────────────────────────────────────────────────────

    public function test_offer_privacy_accept_and_unlock(): void
    {
        [, $token] = $this->family();
        $patientId = $this->patient($token);
        [$nurse, $nurseToken] = $this->pro('rn');
        $booking = $this->bookAndPay($token, $patientId, $this->nursingDay);

        $offers = $this->withToken($nurseToken)->getJson('/api/v1/pro/shift-offers?source=assigned')->assertOk()->json('data');
        $this->assertCount(1, $offers);
        $json = json_encode($offers);
        $this->assertStringNotContainsString('Folake', $json);           // no name
        $this->assertStringNotContainsString('Admiralty', $json);        // no street address
        $this->assertStringNotContainsString('2500000', $json);          // no family price
        $this->assertSame('Lekki Phase 1', $offers[0]['area']);

        $this->withToken($nurseToken)->postJson("/api/v1/pro/shift-offers/{$offers[0]['id']}/accept")
            ->assertOk()->assertJsonPath('data.patient.full_name', 'Folake Adeyemi');
        $this->assertSame('matched', $booking->fresh()->status);

        // The family sees the caregiver but never their pay.
        $this->app['auth']->forgetGuards();
        $fam = $this->withToken($token)->getJson("/api/v1/family/bookings/{$booking->id}")->assertOk()
            ->assertJsonPath('data.caregiver.id', $nurse->id)->json();
        $this->assertStringNotContainsString('pay_per_visit', json_encode($fam));
    }

    public function test_decline_moves_on_then_opens_and_only_one_can_take_it(): void
    {
        [, $token] = $this->family();
        $patientId = $this->patient($token);
        $pros = collect(range(1, 4))->map(fn () => $this->pro('rn'));
        $booking = $this->bookAndPay($token, $patientId, $this->nursingDay);

        // Three assigned offers declined one after another...
        for ($i = 0; $i < 3; $i++) {
            $offer = ShiftOffer::where('care_booking_id', $booking->id)->where('status', 'open')->where('source', 'assigned')->firstOrFail();
            $tok = $pros->first(fn ($p) => $p[0]->id === $offer->professional_id)[1];
            $this->withToken($tok)->postJson("/api/v1/pro/shift-offers/{$offer->id}/decline", ['reason' => 'Too far'])->assertOk();
            $this->app['auth']->forgetGuards();
        }

        // ...then it is an open shift. The first accept wins, the second gets shift_taken.
        $open = ShiftOffer::where('care_booking_id', $booking->id)->where('source', 'open')->firstOrFail();
        [$a, $b] = [$pros[0][1], $pros[3][1]];
        $this->withToken($b)->postJson("/api/v1/pro/shift-offers/{$open->id}/accept")->assertOk();
        $this->app['auth']->forgetGuards();
        $this->withToken($a)->postJson("/api/v1/pro/shift-offers/{$open->id}/accept")->assertStatus(409)->assertJsonPath('error.code', 'shift_taken');
    }

    public function test_unanswered_offers_expire_and_move_on(): void
    {
        [, $token] = $this->family();
        $patientId = $this->patient($token);
        $this->pro('rn');
        $this->pro('rn');
        $booking = $this->bookAndPay($token, $patientId, $this->nursingDay);

        $first = ShiftOffer::where('care_booking_id', $booking->id)->firstOrFail();
        $this->travel(16)->minutes();
        $this->artisan('offers:expire')->assertSuccessful();

        $this->assertSame('expired', $first->fresh()->status);
        $this->assertSame(1, ShiftOffer::where('care_booking_id', $booking->id)->where('status', 'open')->where('source', 'assigned')->count());
    }

    public function test_unverified_caregivers_cannot_accept(): void
    {
        [, $token] = $this->family();
        $patientId = $this->patient($token);
        $booking = $this->bookAndPay($token, $patientId, $this->caretakerDay);   // nobody qualifies: open shift
        $open = ShiftOffer::where('care_booking_id', $booking->id)->where('source', 'open')->firstOrFail();

        [$newbie, $tok] = $this->pro('carer', ['status' => 'verifying']);
        $newbie->credentials()->update(['status' => 'in_progress']);
        $this->withToken($tok)->postJson("/api/v1/pro/shift-offers/{$open->id}/accept")->assertStatus(403)->assertJsonPath('error.code', 'not_verified');
    }
}
