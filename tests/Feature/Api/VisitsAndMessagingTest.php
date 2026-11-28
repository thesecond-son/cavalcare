<?php

namespace Tests\Feature\Api;

use App\Models\Care\CareBooking;
use App\Models\Care\CheckInLink;
use App\Models\Care\Patient;
use App\Models\Care\Professional;
use App\Models\Care\ShiftOffer;
use App\Models\Care\Visit;
use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\User;
use App\Services\Care\CheckInCodeService;
use App\Services\Care\MatchingService;
use Database\Seeders\AdminRolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class VisitsAndMessagingTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private string $ownerToken;
    private Professional $nurse;
    private string $nurseToken;
    private Patient $patient;
    private CareBooking $booking;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminRolesSeeder::class);

        $type = CareType::create(['name' => 'Elderly Care']);
        $plan = CarePlan::create(['care_type_id' => $type->id, 'service_level' => 'nursing', 'name' => 'Day Care', 'price' => 25000, 'billing_cycle' => 'per_day', 'meta' => ['daily_hours' => 9]]);

        $this->owner = User::factory()->create(['name' => 'Tolu Adeyemi']);
        $this->ownerToken = $this->owner->createToken('t', ['app:cavalcare'])->plainTextToken;
        $this->patient = Patient::create(['full_name' => 'Folake Adeyemi', 'address_as' => 'Mama', 'lat' => 6.447, 'lng' => 3.472, 'source' => 'app']);
        $this->patient->members()->create(['user_id' => $this->owner->id, 'role' => 'owner']);

        $nurseUser = User::factory()->create(['name' => 'Adaeze Okafor']);
        $this->nurse = Professional::create(['user_id' => $nurseUser->id, 'title' => 'rn', 'status' => 'active', 'lat' => 6.45, 'lng' => 3.47]);
        foreach ($this->nurse->requiredCredentialTypes() as $t) {
            $this->nurse->credentials()->create(['type' => $t, 'status' => 'verified']);
        }
        $this->nurseToken = $nurseUser->createToken('t', ['app:cavalpro'])->plainTextToken;

        // A paid booking starting today at the current Lagos hour, so it is inside its check-in window.
        $this->booking = CareBooking::create([
            'patient_id' => $this->patient->id, 'booked_by' => $this->owner->id, 'care_plan_id' => $plan->id, 'care_type_id' => $type->id,
            'service_level' => 'nursing', 'plan_name' => 'Day Care', 'billing_cycle' => 'per_day', 'start_date' => now('Africa/Lagos')->toDateString(),
            'start_hour' => (int) now('Africa/Lagos')->format('G'), 'units' => 3, 'end_date' => now('Africa/Lagos')->addDays(2)->toDateString(),
            'price_per_unit_kobo' => 2500000, 'total_kobo' => 7500000, 'status' => 'matching', 'paid_at' => now(),
        ]);
        $offer = app(MatchingService::class)->offerTo($this->booking, $this->nurse);
        app(MatchingService::class)->accept($offer, $this->nurse->fresh(['credentials', 'availabilityRules']));
    }

    private function as(string $token): static
    {
        $this->app['auth']->forgetGuards();

        return $this->withToken($token);
    }

    private function todaysVisit(): Visit
    {
        return Visit::where('care_booking_id', $this->booking->id)->orderBy('scheduled_start')->firstOrFail();
    }

    private function qrCheckIn(Visit $visit): void
    {
        $link = app(CheckInCodeService::class)->linkFor($this->patient);
        $code = app(CheckInCodeService::class)->codeAt($link, $visit, now());
        $this->as($this->nurseToken)->postJson("/api/v1/pro/visits/{$visit->id}/check-in", [
            'method' => 'qr', 'code' => $code, 'lat' => 6.4471, 'lng' => 3.4721, 'recorded_at' => now()->toIso8601String(),
        ])->assertOk()->assertJsonPath('data.state', 'checked_in');
    }

    private function item(Visit $v, string $type, array $payload, ?string $clientId = null, $at = null): array
    {
        return ['client_id' => $clientId ?? (string) Str::uuid(), 'type' => $type, 'visit_id' => $v->id, 'recorded_at' => ($at ?? now())->toIso8601String(), 'payload' => $payload];
    }

    // ── Matching creates the visit plan ─────────────────────────────────

    public function test_accepting_creates_visits_care_plan_thread_and_tells_the_family(): void
    {
        $this->assertSame(3, Visit::where('care_booking_id', $this->booking->id)->count());
        $this->assertDatabaseHas('patient_care_plans', ['patient_id' => $this->patient->id]);

        $thread = Thread::where('care_booking_id', $this->booking->id)->firstOrFail();
        $this->assertEqualsCanonicalizing([$this->owner->id, $this->nurse->user_id], $thread->participants->pluck('user_id')->all());
        $this->assertTrue(DB::table('notifications')->where('notifiable_id', $this->owner->id)->where('type', 'professional_matched')->exists());

        $this->as($this->nurseToken)->getJson('/api/v1/pro/visits')->assertOk()->assertJsonPath('data.0.patient_name', 'Folake Adeyemi');
    }

    // ── Proving presence ────────────────────────────────────────────────

    public function test_rotating_code_proves_the_caregiver_is_with_the_patient(): void
    {
        $visit = $this->todaysVisit();
        $codes = app(CheckInCodeService::class);
        $link = $codes->linkFor($this->patient);

        // An old code (two steps ago) is refused; so is a made-up one.
        $old = $codes->codeAt($link, $visit, now()->subSeconds(65));
        $this->as($this->nurseToken)->postJson("/api/v1/pro/visits/{$visit->id}/check-in", ['method' => 'qr', 'code' => $old, 'recorded_at' => now()->toIso8601String()])
            ->assertStatus(422)->assertJsonPath('error.code', 'validation_failed');
        $this->as($this->nurseToken)->postJson("/api/v1/pro/visits/{$visit->id}/check-in", ['method' => 'qr', 'code' => '000000', 'recorded_at' => now()->toIso8601String()])
            ->assertStatus(422);

        // The previous 30-second step still works (a slow scan).
        $prev = $codes->codeAt($link, $visit, now()->subSeconds(30));
        $this->as($this->nurseToken)->postJson("/api/v1/pro/visits/{$visit->id}/check-in", ['method' => 'qr', 'code' => $prev, 'lat' => 6.4471, 'lng' => 3.4721, 'recorded_at' => now()->toIso8601String()])
            ->assertOk()->assertJsonPath('data.check_in_method', 'qr');

        $ci = $visit->fresh()->checkIn;
        $this->assertTrue($ci->code_valid);
        $this->assertLessThan(100, $ci->distance_m);   // a few metres from the home
    }

    public function test_the_code_page_shows_nothing_outside_a_visit_and_the_code_during_one(): void
    {
        $codes = app(CheckInCodeService::class);
        $link = $codes->linkFor($this->patient);
        $current = $codes->codeAt($link, $this->todaysVisit(), now());
        $page = $this->get('/c/' . $link->code)->assertOk()->assertSee('Adaeze Okafor')->assertSee('changes every 30 seconds')->assertDontSee('Folake');
        // The big number on screen is the rotating 6-digit code, not the link's own id.
        $this->assertMatchesRegularExpression('/id="code"[^>]*>' . $current . '</', $page->getContent());
        $this->get('/c/abcdefghij')->assertNotFound();

        Visit::where('care_booking_id', $this->booking->id)->update(['scheduled_start' => now()->addDays(5), 'scheduled_end' => now()->addDays(5)->addHours(9)]);
        $this->get('/c/' . $link->code)->assertOk()->assertSee('No visit right now');
    }

    public function test_manual_check_in_asks_the_family_and_a_no_disputes_it(): void
    {
        $visit = $this->todaysVisit();
        $this->as($this->nurseToken)->postJson("/api/v1/pro/visits/{$visit->id}/check-in", ['method' => 'manual', 'reason' => 'phone_off', 'recorded_at' => now()->toIso8601String()])->assertOk();

        $this->assertTrue(DB::table('notifications')->where('notifiable_id', $this->owner->id)->where('type', 'arrival_confirmation_requested')->exists());
        $this->as($this->ownerToken)->getJson("/api/v1/family/visits/{$visit->id}/live")->assertOk()->assertJsonPath('data.check_in.needs_your_answer', true);

        $this->as($this->ownerToken)->postJson("/api/v1/family/visits/{$visit->id}/confirm-arrival", ['answer' => 'no'])->assertOk();
        $ci = $visit->fresh()->checkIn;
        $this->assertTrue($ci->disputed);
        $this->assertNotNull($visit->fresh()->pay_hold_reason);
    }

    public function test_unanswered_arrival_alerts_coordinators_once(): void
    {
        $coordinator = User::factory()->create();
        $coordinator->assignRole('coordinator');
        $visit = $this->todaysVisit();
        $this->as($this->nurseToken)->postJson("/api/v1/pro/visits/{$visit->id}/check-in", ['method' => 'manual', 'reason' => 'no_smartphone', 'recorded_at' => now()->toIso8601String()]);

        $this->travel(16)->minutes();
        $this->artisan('visits:watch')->assertSuccessful();
        $this->artisan('visits:watch')->assertSuccessful();

        $this->assertSame(1, DB::table('notifications')->where('notifiable_id', $coordinator->id)->where('type', 'arrival_unanswered')->count());
    }

    // ── Recording and the report ────────────────────────────────────────

    public function test_sync_records_flags_and_raises_status_without_leaking_flags_to_family(): void
    {
        $visit = $this->todaysVisit();
        $this->qrCheckIn($visit);

        $same = (string) Str::uuid();
        $res = $this->as($this->nurseToken)->postJson('/api/v1/pro/sync', ['items' => [
            $this->item($visit, 'task', ['task_id' => 'vitals', 'state' => 'done'], $same),
            $this->item($visit, 'task', ['task_id' => 'vitals', 'state' => 'done'], $same),                      // repeat
            $this->item($visit, 'vital', ['type' => 'bp', 'value' => '172/96', 'unit' => 'mmHg']),                 // out of range
            $this->item($visit, 'medication', ['medication_name' => 'Amlodipine', 'outcome' => 'given']),
            $this->item($visit, 'observation', ['mood' => 'calm', 'pain_score' => 2], null, now()->subMinutes(25)), // clock drift
        ]])->assertOk()->json('data');

        $this->assertSame(['accepted', 'duplicate', 'accepted', 'accepted', 'accepted'], array_column($res, 'result'));
        $this->assertSame('attention', $visit->fresh()->status);
        $this->assertTrue(\App\Models\Care\Observation::first()->clock_drift_flag);

        $live = $this->as($this->ownerToken)->getJson("/api/v1/family/visits/{$visit->id}/live")->assertOk()->json('data');
        $this->assertSame('172/96', $live['latest_vitals'][0]['value']);
        $this->assertStringNotContainsString('out_of_range', json_encode($live));
        $this->assertStringNotContainsString('pay_kobo', json_encode($live));

        // An urgent concern raises it further.
        $this->as($this->nurseToken)->postJson('/api/v1/pro/sync', ['items' => [$this->item($visit, 'concern', ['kind' => 'chest_pain', 'severity' => 'urgent', 'description' => 'Tight chest'])]])->assertOk();
        $this->assertSame('urgent', $visit->fresh()->status);
    }

    public function test_carers_cannot_mark_medicines_given_and_others_cannot_write_to_the_visit(): void
    {
        $visit = $this->todaysVisit();
        $this->qrCheckIn($visit);
        $this->nurse->update(['title' => 'carer']);

        $r = $this->as($this->nurseToken)->postJson('/api/v1/pro/sync', ['items' => [
            $this->item($visit, 'medication', ['medication_name' => 'Amlodipine', 'outcome' => 'given']),
            $this->item($visit, 'vital', ['type' => 'bp', 'value' => '120/80']),
        ]])->json('data');
        $this->assertSame('role_not_allowed', $r[0]['code']);
        $this->assertSame('role_not_allowed', $r[1]['code']);

        $otherUser = User::factory()->create();
        Professional::create(['user_id' => $otherUser->id, 'title' => 'rn', 'status' => 'active']);
        $other = $otherUser->createToken('t', ['app:cavalpro'])->plainTextToken;
        $this->as($other)->postJson('/api/v1/pro/sync', ['items' => [$this->item($visit, 'care_note', ['text' => 'x'])]])
            ->assertOk()->assertJsonPath('data.0.code', 'not_assigned');
        $this->as($other)->getJson("/api/v1/pro/visits/{$visit->id}/brief")->assertNotFound();
    }

    public function test_submit_needs_every_task_then_locks_the_report(): void
    {
        $visit = $this->todaysVisit();
        $this->qrCheckIn($visit);

        $this->as($this->nurseToken)->postJson("/api/v1/pro/visits/{$visit->id}/submit", ['recorded_at' => now()->toIso8601String()])
            ->assertStatus(422)->assertJsonPath('error.code', 'report_incomplete');

        $tasks = collect(\App\Models\Care\PatientCarePlan::where('patient_id', $this->patient->id)->value('tasks'))->pluck('id');
        $items = $tasks->map(fn ($id) => $this->item($visit, 'task', ['task_id' => $id, 'state' => 'done']))->all();
        $items[] = $this->item($visit, 'care_note', ['text' => 'Mama was cheerful and ate well.']);
        $this->as($this->nurseToken)->postJson('/api/v1/pro/sync', ['items' => $items])->assertOk();

        $this->as($this->nurseToken)->postJson("/api/v1/pro/visits/{$visit->id}/submit", ['lat' => 6.4471, 'lng' => 3.4721, 'recorded_at' => now()->toIso8601String()])
            ->assertOk()->assertJsonPath('data.report_submitted', true);
        $this->assertSame('ready', $visit->fresh()->pay_state);
        $this->assertSame('active', $this->booking->fresh()->status);

        // Locked: new records are refused; amendments are kept with a reason.
        $r = $this->as($this->nurseToken)->postJson('/api/v1/pro/sync', ['items' => [$this->item($visit, 'care_note', ['text' => 'late'])]])->json('data.0');
        $this->assertSame('visit_already_submitted', $r['code']);
        $note = \App\Models\Care\CareNote::first();
        $this->as($this->nurseToken)->postJson('/api/v1/pro/sync', ['items' => [$this->item($visit, 'amendment', [
            'record_type' => 'care_note', 'record_id' => $note->id, 'field' => 'text', 'new_value' => 'Mama was cheerful and ate all her lunch.', 'reason' => 'Typo',
        ])]])->assertJsonPath('data.0.result', 'accepted');

        $report = $this->as($this->ownerToken)->getJson("/api/v1/family/visits/{$visit->id}/report")->assertOk()
            ->assertJsonPath('data.updated', true)->assertJsonPath('data.care_note', 'Mama was cheerful and ate well.')->json();
        $this->assertStringNotContainsString('out_of_range', json_encode($report));
    }

    // ── Messages and notifications ──────────────────────────────────────

    public function test_care_team_messaging_with_roles_and_safe_retries(): void
    {
        $thread = Thread::where('care_booking_id', $this->booking->id)->firstOrFail();
        $cid = (string) Str::uuid();

        $this->as($this->ownerToken)->postJson("/api/v1/threads/{$thread->id}/messages", ['client_id' => $cid, 'text' => 'Please remind Mama to drink water'])->assertCreated();
        $this->as($this->ownerToken)->postJson("/api/v1/threads/{$thread->id}/messages", ['client_id' => $cid, 'text' => 'Please remind Mama to drink water'])->assertCreated();
        $this->assertSame(1, Message::where('client_id', $cid)->count());

        $this->as($this->nurseToken)->getJson('/api/v1/threads')->assertOk()->assertJsonPath('data.0.unread', 2);   // system "matched" line + the message

        $viewer = User::factory()->create();
        $this->patient->members()->create(['user_id' => $viewer->id, 'role' => 'viewer']);
        $thread->participants()->create(['user_id' => $viewer->id, 'role_in_thread' => 'family', 'read_only' => true]);
        $vt = $viewer->createToken('t', ['app:cavalcare'])->plainTextToken;
        $this->as($vt)->getJson("/api/v1/threads/{$thread->id}/messages")->assertOk();
        $this->as($vt)->postJson("/api/v1/threads/{$thread->id}/messages", ['client_id' => (string) Str::uuid(), 'text' => 'hi'])->assertStatus(403);

        $stranger = User::factory()->create()->createToken('t', ['app:cavalcare'])->plainTextToken;
        $this->as($stranger)->getJson("/api/v1/threads/{$thread->id}/messages")->assertNotFound();
    }

    public function test_push_preferences_respect_mutes_but_not_for_urgent(): void
    {
        $this->as($this->ownerToken)->postJson('/api/v1/devices', ['fcm_token' => str_repeat('a', 150), 'platform' => 'ios'])->assertCreated()->assertJsonPath('data.app', 'cavalcare');
        $this->as($this->ownerToken)->putJson('/api/v1/me/notification-preferences', ['push' => ['visits' => false]])->assertOk();

        $sent = [];
        $this->app->instance(\App\Services\Notify\PushSender::class, new class($sent) extends \App\Services\Notify\PushSender {
            public function __construct(public array &$log) {}
            public function send($device, string $title, string $body, array $data = [], bool $urgent = false): void { $this->log[] = $title; }
        });
        $notifier = $this->app->make(\App\Services\Notify\Notifier::class);
        $notifier->send($this->owner, 'cavalcare', 'visits', 'x', 'Muted one', 'b');
        $notifier->send($this->owner, 'cavalcare', 'visits', 'y', 'Urgent one', 'b', [], true);

        $this->assertSame(['Urgent one'], $sent);
        $this->as($this->ownerToken)->getJson('/api/v1/notifications')->assertOk()->assertJsonPath('meta.unread', 3);  // matched + both
    }

    // ── Caval OS ────────────────────────────────────────────────────────

    public function test_coordinator_resolving_a_concern_brings_the_status_down(): void
    {
        $visit = $this->todaysVisit();
        $this->qrCheckIn($visit);
        $this->as($this->nurseToken)->postJson('/api/v1/pro/sync', ['items' => [$this->item($visit, 'concern', ['kind' => 'fall', 'severity' => 'urgent'])]]);
        $concern = \App\Models\Care\Concern::firstOrFail();

        $sign = function (string $role) {
            $u = User::factory()->create(['password' => 'Correct-horse-9']);
            $u->assignRole($role);
            $u->forceFill(['two_factor_secret' => (new \PragmaRX\Google2FA\Google2FA())->generateSecretKey(32), 'two_factor_confirmed_at' => now()])->save();
            // Earlier API calls in this test switched the default guard to Sanctum; a browser session starts fresh.
            $this->flushHeaders();
            $this->app['auth']->forgetGuards();
            $this->app['auth']->shouldUse('web');
            $this->post('/adminos/login', ['email' => $u->email, 'password' => 'Correct-horse-9']);
            $this->post('/adminos/two-factor', ['code' => (new \PragmaRX\Google2FA\Google2FA())->getCurrentOtp($u->two_factor_secret)]);
        };

        $sign('support');
        $this->post("/adminos/concerns/{$concern->id}/resolve", ['resolution_note' => 'x'])->assertForbidden();
        $this->post('/adminos/logout');

        $sign('coordinator');
        $this->get('/adminos/visits')->assertOk()->assertSee('Needs a coordinator');
        $this->post("/adminos/concerns/{$concern->id}/acknowledge")->assertSessionHas('os_flash');
        $this->assertTrue(DB::table('notifications')->where('notifiable_id', $this->owner->id)->where('type', 'concern_acknowledged')->exists());
        $this->post("/adminos/concerns/{$concern->id}/resolve", ['resolution_note' => 'Doctor checked, no injury'])->assertSessionHas('os_flash');
        $this->assertSame('stable', $visit->fresh()->status);
    }
}
