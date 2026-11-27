<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One users table for everyone (families, professionals, admins), as in the
 * backend spec. App users sign in with a one-time code, so email and password
 * become optional; phone_e164 is the second unique identity.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type', 20)->default('family')->after('id')->index()
                  ->comment('family | professional | admin');
            $table->string('phone_e164', 20)->nullable()->unique()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('phone_e164');
            $table->string('country_code', 2)->nullable()->after('phone_verified_at');
            $table->string('time_zone', 64)->default('Africa/Lagos')->after('country_code');
            $table->string('preferred_currency', 3)->default('NGN')->after('time_zone');
            $table->timestamp('last_seen_at')->nullable()->after('remember_token');

            // Caval OS two-factor sign-in (TOTP). Secret and codes are encrypted.
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->unsignedBigInteger('two_factor_last_step')->nullable()
                  ->comment('Last accepted TOTP time step, blocks code replay');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });

        // Existing admins become type "admin"; everyone else stays "family".
        $adminIds = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'admin')
            ->where('model_has_roles.model_type', \App\Models\User::class)
            ->pluck('model_has_roles.model_id');

        DB::table('users')->whereIn('id', $adminIds)->update(['type' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone_e164']);
            $table->dropIndex(['type']);
            $table->dropColumn([
                'type', 'phone_e164', 'phone_verified_at', 'country_code', 'time_zone',
                'preferred_currency', 'last_seen_at', 'two_factor_secret',
                'two_factor_recovery_codes', 'two_factor_confirmed_at', 'two_factor_last_step',
            ]);
        });
    }
};
