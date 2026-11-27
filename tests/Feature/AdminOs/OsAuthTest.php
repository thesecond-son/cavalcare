<?php

namespace Tests\Feature\AdminOs;

use App\Http\Controllers\AdminOs\PasswordSetupController;
use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\PreBooking;
use App\Models\User;
use App\PreBookingStatus;
use Database\Seeders\AdminRolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class OsAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminRolesSeeder::class);
    }

    private function staff(string $role, bool $twoFactor = true): User
    {
        $user = User::factory()->create(['password' => 'Correct-horse-9']);
        $user->assignRole($role);

        if ($twoFactor) {
            $user->forceFill([
                'two_factor_secret'       => (new Google2FA())->generateSecretKey(32),
                'two_factor_confirmed_at' => now(),
            ])->save();
        }

        return $user;
    }

    private function totp(string $secret): string
    {
        return (new Google2FA())->getCurrentOtp($secret);
    }

    private function signIn(User $user): void
    {
        $this->post('/adminos/login', ['email' => $user->email, 'password' => 'Correct-horse-9'])
            ->assertRedirect(route('os.2fa.challenge'));
        $this->post('/adminos/two-factor', ['code' => $this->totp($user->two_factor_secret)])
            ->assertRedirect(route('os.dashboard'));
    }

    public function test_guests_are_sent_to_the_login(): void
    {
        $this->get('/adminos')->assertRedirect(route('os.login'));
    }

    public function test_password_alone_does_not_sign_in(): void
    {
        $user = $this->staff('super_admin');

        $this->post('/adminos/login', ['email' => $user->email, 'password' => 'Correct-horse-9'])
            ->assertRedirect(route('os.2fa.challenge'));

        $this->assertGuest();
        $this->get('/adminos')->assertRedirect(route('os.login'));
    }

    public function test_wrong_code_fails_right_code_signs_in_and_cannot_be_replayed(): void
    {
        $user = $this->staff('coordinator');

        $this->post('/adminos/login', ['email' => $user->email, 'password' => 'Correct-horse-9']);
        $this->post('/adminos/two-factor', ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertGuest();

        $code = $this->totp($user->two_factor_secret);
        $this->post('/adminos/two-factor', ['code' => $code])->assertRedirect(route('os.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get('/adminos')->assertOk()->assertSee('Latest website bookings');

        // Same code again in a new sign-in is refused.
        $this->post('/adminos/logout');
        $this->post('/adminos/login', ['email' => $user->email, 'password' => 'Correct-horse-9']);
        $this->post('/adminos/two-factor', ['code' => $code])->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_first_sign_in_requires_two_factor_setup(): void
    {
        $user = $this->staff('super_admin', twoFactor: false);

        $this->post('/adminos/login', ['email' => $user->email, 'password' => 'Correct-horse-9'])
            ->assertRedirect(route('os.2fa.setup'));
        $this->get('/adminos/two-factor/setup')->assertOk()->assertSee('Scan this QR code');

        $secret = session('os_2fa_setup_secret');
        $this->post('/adminos/two-factor/setup', ['code' => $this->totp($secret)])
            ->assertRedirect(route('os.2fa.recovery'));

        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());
        $this->get('/adminos/two-factor/recovery-codes')->assertOk()->assertSee('only time they are shown');
    }

    public function test_recovery_code_works_once(): void
    {
        $user = $this->staff('super_admin', twoFactor: false);
        $this->post('/adminos/login', ['email' => $user->email, 'password' => 'Correct-horse-9']);
        $this->get('/adminos/two-factor/setup');
        $this->post('/adminos/two-factor/setup', ['code' => $this->totp(session('os_2fa_setup_secret'))]);
        $codes = session('os_recovery_codes');
        $this->post('/adminos/logout');

        $this->post('/adminos/login', ['email' => $user->email, 'password' => 'Correct-horse-9']);
        $this->post('/adminos/two-factor', ['recovery_code' => strtolower($codes[0])])->assertRedirect(route('os.dashboard'));
        $this->post('/adminos/logout');

        $this->post('/adminos/login', ['email' => $user->email, 'password' => 'Correct-horse-9']);
        $this->post('/adminos/two-factor', ['recovery_code' => $codes[0]])->assertSessionHasErrors('recovery_code');
        $this->assertCount(7, $user->fresh()->two_factor_recovery_codes);
    }

    public function test_non_staff_get_the_same_error_as_a_wrong_password(): void
    {
        $customer = User::factory()->create(['password' => 'Correct-horse-9']);

        $this->post('/adminos/login', ['email' => $customer->email, 'password' => 'Correct-horse-9'])
            ->assertSessionHasErrors(['email' => 'These details do not match an admin account.']);
    }

    public function test_a_website_session_without_the_os_sign_in_is_not_enough(): void
    {
        $admin = $this->staff('admin');
        $this->actingAs($admin)->get('/adminos')->assertRedirect(route('os.login'));
    }

    public function test_roles_limit_what_staff_can_do(): void
    {
        Mail::fake();
        $type = CareType::create(['name' => 'Elderly Care']);
        $plan = CarePlan::create(['care_type_id' => $type->id, 'name' => 'Day Care', 'price' => 15000, 'billing_cycle' => 'per_day', 'service_level' => 'caretaker']);
        $pb = PreBooking::create([
            'reference' => 'PB-ROLE1', 'care_plan_id' => $plan->id, 'care_type_id' => $type->id,
            'status' => PreBookingStatus::PAID, 'booker_details' => ['name' => 'A', 'email' => 'a@example.com'],
        ]);

        $support = $this->staff('support');
        $this->signIn($support);
        $this->get('/adminos/bookings')->assertOk();
        $this->post("/adminos/bookings/{$pb->reference}/app-code")->assertForbidden();
        $this->post('/adminos/logout');

        $coordinator = $this->staff('coordinator');
        $this->signIn($coordinator);
        $this->post("/adminos/bookings/{$pb->reference}/app-code")->assertRedirect();
        Mail::assertSent(\App\Mail\AppCodeMail::class, fn ($m) => $m->hasTo('a@example.com'));
        $this->assertDatabaseHas('os_audit_logs', ['action' => 'booking.app_code_sent', 'actor_id' => $coordinator->id]);
    }

    public function test_password_setup_link_works_once(): void
    {
        $user = User::factory()->create(['password' => null]);
        $user->assignRole('finance');
        $link = PasswordSetupController::linkFor($user);

        $this->get($link)->assertOk()->assertSee('Choose a password');
        $this->post($link, ['password' => 'weak', 'password_confirmation' => 'weak'])->assertSessionHasErrors('password');

        // Strong password (avoids the breached-password check needing the network).
        \Illuminate\Support\Facades\Http::fake(['api.pwnedpasswords.com/*' => \Illuminate\Support\Facades\Http::response('', 200)]);
        $this->post($link, ['password' => 'Bright-River-Lagos-2026', 'password_confirmation' => 'Bright-River-Lagos-2026'])
            ->assertRedirect(route('os.login'));

        $this->get($link)->assertForbidden();
    }
}
