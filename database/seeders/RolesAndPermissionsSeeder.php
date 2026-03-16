<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'super_admin', 'description' => 'Full system access'],
            ['name' => 'admin', 'description' => 'Administrative access'],
            ['name' => 'validate_payment', 'description' => 'Can validate and log payments'],
            ['name' => 'manage_members', 'description' => 'Can manage members'],
            ['name' => 'generate_reports', 'description' => 'Can generate reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->permissions()->sync(
            Permission::all()->pluck('id')
        );

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->permissions()->sync(
            Permission::whereIn('name', ['admin', 'manage_members', 'validate_payment', 'generate_reports'])->pluck('id')
        );

        $treasurerRole = Role::firstOrCreate(['name' => 'Treasurer']);
        $treasurerRole->permissions()->sync(
            Permission::whereIn('name', ['validate_payment', 'generate_reports'])->pluck('id')
        );

        $financialSecretaryRole = Role::firstOrCreate(['name' => 'Financial Secretary']);
        $financialSecretaryRole->permissions()->sync(
            Permission::whereIn('name', ['validate_payment', 'generate_reports'])->pluck('id')
        );

        Role::firstOrCreate(['name' => 'Member']);
    }
}
