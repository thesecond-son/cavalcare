<?php

namespace Database\Seeders;

use App\Support\AdminOs\OsRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates the Caval OS roles and permissions from App\Support\AdminOs\OsRoles.
 * Safe to run again: it only adds what is missing and resyncs each role's grants.
 *
 *   php artisan db:seed --class=AdminRolesSeeder --force
 */
class AdminRolesSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (array_keys(OsRoles::PERMISSIONS) as $name) {
            Permission::findOrCreate($name, 'web');
        }

        foreach (OsRoles::all() as $roleName) {
            $role = Role::findOrCreate($roleName, 'web');

            if (isset(OsRoles::GRANTS[$roleName])) {
                $role->syncPermissions(OsRoles::GRANTS[$roleName]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
