<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create global permissions
        $permissions = [
            'manage-halls',
            'manage-bookings',
            'manage-financials',
            'manage-employees',
            'manage-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Create global roles (team_id = null)
        $admin = Role::firstOrCreate(['name' => 'Tenant Admin', 'team_id' => null, 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'Manager', 'team_id' => null, 'guard_name' => 'web']);
        $manager->syncPermissions(['manage-halls', 'manage-bookings', 'manage-financials']);

        $staff = Role::firstOrCreate(['name' => 'Staff', 'team_id' => null, 'guard_name' => 'web']);
        $staff->syncPermissions(['manage-bookings']);
    }
}
