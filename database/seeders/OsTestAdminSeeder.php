<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Local-only Caval OS test admin. Credentials come from .env
 * (OS_TEST_ADMIN_EMAIL / OS_TEST_ADMIN_PASSWORD), never from code.
 *
 *   php artisan db:seed --class=OsTestAdminSeeder
 */
class OsTestAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            $this->command?->warn('Skipped: OsTestAdminSeeder only runs when APP_ENV=local.');
            return;
        }

        $email    = env('OS_TEST_ADMIN_EMAIL');
        $password = env('OS_TEST_ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('Skipped: set OS_TEST_ADMIN_EMAIL and OS_TEST_ADMIN_PASSWORD in .env.');
            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => 'OS Test Admin', 'password' => Hash::make($password)]
        );
        $user->syncRoles(['admin']);
    }
}
