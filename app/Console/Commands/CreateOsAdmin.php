<?php

namespace App\Console\Commands;

use App\Http\Controllers\AdminOs\PasswordSetupController;
use App\Mail\AdminInviteMail;
use App\Models\User;
use App\Support\AdminOs\Audit;
use App\Support\AdminOs\OsRoles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Adds (or updates) a Caval OS admin and emails them a link to choose
 * their own password. Nobody, including whoever runs this, sees it.
 *
 *   php artisan os:admin jane@usecavalcare.com --name="Jane Doe" --role=coordinator
 */
class CreateOsAdmin extends Command
{
    protected $signature = 'os:admin
        {email : The admin\'s email address}
        {--name= : Full name (required for a new admin)}
        {--role=super_admin : One of: super_admin, admin, coordinator, clinical_lead, compliance, finance, support}
        {--show-link : Print the password link instead of emailing it (local testing only)}';

    protected $description = 'Add a Caval OS admin and send them a set-password link';

    public function handle(): int
    {
        $email = Str::lower(trim($this->argument('email')));
        $role  = $this->option('role');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('That is not a valid email address.');
            return self::FAILURE;
        }
        if (! in_array($role, OsRoles::all(), true)) {
            $this->error('Unknown role. Use one of: ' . implode(', ', OsRoles::all()));
            return self::FAILURE;
        }

        $this->call('db:seed', ['--class' => 'AdminRolesSeeder', '--force' => true]);

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if (! $user) {
            if (! $this->option('name')) {
                $this->error('New admin: pass --name="Full Name".');
                return self::FAILURE;
            }
            $user = new User(['email' => $email, 'name' => $this->option('name')]);
        } elseif ($this->option('name')) {
            $user->name = $this->option('name');
        }

        $user->forceFill(['type' => User::TYPE_ADMIN])->save();
        $user->assignRole($role);
        Audit::log('admin.created_or_updated', $user, ['role' => $role, 'via' => 'console']);

        $link = PasswordSetupController::linkFor($user, 24 * 60);

        if ($this->option('show-link')) {
            if (app()->environment('production')) {
                $this->error('--show-link is disabled in production. The link is emailed instead.');
                return self::FAILURE;
            }
            $this->line($link);
        } else {
            Mail::to($user->email)->send(new AdminInviteMail($user, $link));
            $this->info("Set-password link emailed to {$user->email}. It works once, for 24 hours.");
        }

        $this->info("{$user->name} has the " . OsRoles::LABELS[$role] . ' role.');

        return self::SUCCESS;
    }
}
