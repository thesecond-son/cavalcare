<?php

namespace Tests\Feature\AdminOs;

use App\Models\Booking;
use App\Models\Care\Patient;
use App\Models\Care\Professional;
use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\PreBooking;
use App\Models\User;
use App\PreBookingStatus;
use App\Services\Care\WebsiteIntakeImporter;
use Database\Seeders\AdminRolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class ClientsAndCaregiversTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminRolesSeeder::class);
    }

    private function signInAs(string $role): User
    {
        $user = User::factory()->create(['password' => 'Correct-horse-9']);
        $user->assignRole($role);
        $user->forceFill(['two_factor_secret' => (new Google2FA())->generateSecretKey(32), 'two_factor_confirmed_at' => now()])->save();

        $this->post('/adminos/login', ['email' => $user->email, 'password' => 'Correct-horse-9']);
        $this->post('/adminos/two-factor', ['code' => (new Google2FA())->getCurrentOtp($user->two_factor_secret)])
            ->assertRedirect(route('os.dashboard'));

        return $user;
    }

    private function websiteIntake(?int $userId = null): Booking
    {
        $type = CareType::create(['name' => 'Elderly Care']);
        $plan = CarePlan::create(['care_type_id' => $type->id, 'name' => 'Day Care', 'price' => 25000, 'billing_cycle' => 'per_day', 'service_level' => 'nursing']);
        $booker = ['name' => 'Tolu Adeyemi', 'email' => 'tolu@example.com', 'phone' => '08031234567', 'state' => 'Lagos'];
        $pb = PreBooking::create([
            'reference' => 'PB-IMP' . random_int(100, 999), 'care_plan_id' => $plan->id, 'care_type_id' => $type->id,
            'status' => PreBookingStatus::CONVERTED, 'booker_details' => $booker,
        ]);
        if ($userId) {
            $pb->forceFill(['user_id' => $userId])->save();
        }

        return Booking::create([
            'pre_booking_id' => $pb->id, 'care_plan_id' => $plan->id, 'care_type_id' => $type->id, 'status' => 'intake_complete',
            'booker_details' => $booker,
            'patient_details' => [
                'full_name' => 'Folake Adeyemi', 'age' => 78, 'gender' => 'Female', 'mobility_level' => 'Assisted',
                'address' => '12 Admiralty Way, Lekki', 'diagnosis' => 'Hypertension', 'emergency_contact' => '08039998888',
                'allergies' => [['allergy' => 'Penicillin'], ['allergy' => '']], 'medications' => [['medication' => 'Amlodipine 5mg']],
            ],
            'booking_details' => ['start_date' => '2026-12-01'],
        ]);
    }

    public function test_website_intake_becomes_a_client_once(): void
    {
        $owner = User::factory()->create();
        $booking = $this->websiteIntake($owner->id);
        $importer = app(WebsiteIntakeImporter::class);

        $patient = $importer->import($booking);
        $importer->import($booking);           // repeat: no duplicate
        $this->assertSame(0, $importer->importAll());

        $this->assertSame(1, Patient::count());
        $this->assertSame('Folake Adeyemi', $patient->full_name);
        $this->assertSame('Woman, 78', $patient->age_sex);
        $this->assertSame(['Penicillin'], $patient->allergies->pluck('name')->all()); // blank rows skipped
        $this->assertSame('+2348039998888', $patient->emergencyContacts->first()->phone_e164);
        $this->assertSame($patient->id, $booking->preBooking->fresh()->patient_id);
        $this->assertDatabaseHas('patient_members', ['patient_id' => $patient->id, 'user_id' => $owner->id, 'role' => 'owner']);
    }

    public function test_dashboard_hides_client_names_from_roles_without_client_access(): void
    {
        $patient = app(WebsiteIntakeImporter::class)->import($this->websiteIntake());
        $patient->update(['display_name' => 'Mama Folake']);
        \App\Models\Care\Concern::unguard();

        $this->signInAs('finance');
        $this->get('/adminos')->assertOk()->assertSee('Client details are for coordinators')->assertDontSee('Mama Folake');
    }

    public function test_clients_pages_need_permission_and_log_views(): void
    {
        $patient = app(WebsiteIntakeImporter::class)->import($this->websiteIntake());

        $this->signInAs('finance');            // finance has no clients.view
        $this->get('/adminos/clients')->assertForbidden();
        $this->post('/adminos/logout');

        $coordinator = $this->signInAs('coordinator');
        $this->get('/adminos/clients')->assertOk()->assertSee('Folake Adeyemi')->assertSee('1 allergy');
        $this->get("/adminos/clients/{$patient->id}")->assertOk()->assertSee('Penicillin')->assertSee('Hypertension')->assertSee('Tolu Adeyemi');
        $this->assertDatabaseHas('os_audit_logs', ['action' => 'client.viewed', 'actor_id' => $coordinator->id, 'subject_id' => $patient->id]);
    }

    private function applicant(): Professional
    {
        $user = User::factory()->create(['name' => 'Adaeze Okafor']);
        $pro = Professional::create(['user_id' => $user->id, 'title' => 'rn', 'home_area' => 'Lekki']);
        $pro->credentials()->create(['type' => 'nmcn', 'status' => 'in_progress', 'number' => 'NMCN-123456']);
        $pro->credentials()->create(['type' => 'background', 'status' => 'in_progress']);
        $pro->credentials()->create(['type' => 'training', 'status' => 'verified', 'verified_at' => now()]);
        $pro->syncStatusWithCredentials();

        return $pro->fresh();
    }

    public function test_verifying_every_required_check_makes_a_caregiver_active(): void
    {
        $pro = $this->applicant();
        $this->assertSame('verifying', $pro->status);

        $officer = $this->signInAs('compliance');
        $this->get('/adminos/caregivers')->assertOk()->assertSee('Adaeze Okafor')->assertSee('1/3');
        $this->get("/adminos/caregivers/{$pro->id}")->assertOk()->assertSee('•••• 3456')->assertDontSee('NMCN-123456');

        $nmcn = $pro->credentials->firstWhere('type', 'nmcn');
        $bg   = $pro->credentials->firstWhere('type', 'background');

        // Rejecting needs a reason.
        $this->post("/adminos/caregivers/{$pro->id}/credentials/{$bg->id}", ['decision' => 'reject'])->assertSessionHasErrors('rejection_reason');

        $this->post("/adminos/caregivers/{$pro->id}/credentials/{$nmcn->id}", ['decision' => 'approve', 'valid_to' => now()->addYear()->toDateString()]);
        $this->assertSame('verifying', $pro->fresh()->status);

        $this->post("/adminos/caregivers/{$pro->id}/credentials/{$bg->id}", ['decision' => 'approve'])
            ->assertSessionHas('os_flash');
        $this->assertSame('active', $pro->fresh()->status);
        $this->assertSame($officer->id, $bg->fresh()->verified_by);
        $this->assertDatabaseHas('os_audit_logs', ['action' => 'caregiver.credential_verified', 'actor_id' => $officer->id]);
    }

    public function test_suspension_needs_a_reason_and_holds_until_reinstated(): void
    {
        $pro = $this->applicant();
        $this->signInAs('compliance');

        $this->post("/adminos/caregivers/{$pro->id}/status", ['action' => 'suspend'])->assertSessionHasErrors('reason');
        $this->post("/adminos/caregivers/{$pro->id}/status", ['action' => 'suspend', 'reason' => 'Missed visits']);
        $this->assertSame('suspended', $pro->fresh()->status);

        // Approving a credential while suspended does not lift the suspension.
        $nmcn = $pro->credentials->firstWhere('type', 'nmcn');
        $this->post("/adminos/caregivers/{$pro->id}/credentials/{$nmcn->id}", ['decision' => 'approve']);
        $this->assertSame('suspended', $pro->fresh()->status);

        $this->post("/adminos/caregivers/{$pro->id}/status", ['action' => 'reinstate']);
        $this->assertSame('verifying', $pro->fresh()->status);   // background check still pending
    }

    public function test_support_can_see_caregivers_but_not_verify_them(): void
    {
        $pro = $this->applicant();
        $this->signInAs('support');

        $this->get("/adminos/caregivers/{$pro->id}")->assertOk()->assertDontSee('Verify</button>', false);
        $cred = $pro->credentials->first();
        $this->post("/adminos/caregivers/{$pro->id}/credentials/{$cred->id}", ['decision' => 'approve'])->assertForbidden();
        $this->post("/adminos/caregivers/{$pro->id}/status", ['action' => 'suspend', 'reason' => 'x'])->assertForbidden();
    }

    public function test_coordinator_sees_app_booking_and_sends_an_offer(): void
    {
        $type = CareType::create(['name' => 'Elderly Care']);
        $plan = CarePlan::create(['care_type_id' => $type->id, 'name' => 'Day Care', 'price' => 15000, 'billing_cycle' => 'per_day', 'service_level' => 'caretaker']);
        $family = User::factory()->create();
        $patient = Patient::create(['full_name' => 'Folake Adeyemi', 'area' => 'Lekki', 'source' => 'app']);
        $patient->members()->create(['user_id' => $family->id, 'role' => 'owner']);
        $booking = \App\Models\Care\CareBooking::create([
            'patient_id' => $patient->id, 'booked_by' => $family->id, 'care_plan_id' => $plan->id, 'care_type_id' => $type->id,
            'service_level' => 'caretaker', 'plan_name' => 'Day Care', 'billing_cycle' => 'per_day', 'start_date' => now()->addDays(5)->toDateString(),
            'units' => 1, 'price_per_unit_kobo' => 1500000, 'total_kobo' => 1500000, 'status' => 'matching', 'paid_at' => now(),
        ]);
        $carer = Professional::create(['user_id' => User::factory()->create(['name' => 'Halima Sule'])->id, 'title' => 'carer', 'status' => 'active']);
        foreach ($carer->requiredCredentialTypes() as $t) {
            $carer->credentials()->create(['type' => $t, 'status' => 'verified']);
        }

        $this->signInAs('support');
        $this->get("/adminos/app-bookings/{$booking->id}")->assertOk()->assertSee($booking->reference)->assertDontSee('Send offer');
        $this->post("/adminos/app-bookings/{$booking->id}/offer", ['professional_id' => $carer->id])->assertForbidden();
        $this->post('/adminos/logout');

        $this->signInAs('coordinator');
        $this->get("/adminos/app-bookings/{$booking->id}")->assertOk()->assertSee('Halima Sule')->assertSee('Send offer');
        $this->post("/adminos/app-bookings/{$booking->id}/offer", ['professional_id' => $carer->id])->assertSessionHas('os_flash');
        $this->assertDatabaseHas('shift_offers', ['care_booking_id' => $booking->id, 'professional_id' => $carer->id, 'status' => 'open', 'source' => 'assigned']);
    }

    public function test_expiring_and_expired_checks_are_marked_daily_and_expired_ones_stop_matching(): void
    {
        $user = User::factory()->create();
        $pro = Professional::create(['user_id' => $user->id, 'title' => 'rn', 'status' => 'active']);
        $pro->credentials()->create(['type' => 'nmcn', 'status' => 'verified', 'valid_to' => now()->addDays(10)->toDateString()]);
        $pro->credentials()->create(['type' => 'background', 'status' => 'verified', 'valid_to' => now()->subDay()->toDateString()]);
        $pro->credentials()->create(['type' => 'training', 'status' => 'verified']);

        $this->artisan('credentials:expire')->assertSuccessful();

        $this->assertSame('expiring', $pro->credentials()->where('type', 'nmcn')->value('status'));
        $this->assertSame('expired', $pro->credentials()->where('type', 'background')->value('status'));
        $this->assertSame('verifying', $pro->fresh()->status);   // no longer able to take shifts
    }

    public function test_a_credential_from_another_caregiver_cannot_be_reviewed_through_this_one(): void
    {
        $a = $this->applicant();
        $b = $this->applicant();
        $this->signInAs('compliance');

        $foreign = $b->credentials->first();
        $this->post("/adminos/caregivers/{$a->id}/credentials/{$foreign->id}", ['decision' => 'approve'])->assertNotFound();
    }
}
