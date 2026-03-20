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
            ['name' => 'validate_payment', 'description' => 'Can validate and add payments'],
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

        foreach (['Chairman', 'Financial Secretary', 'Secretary', 'Treasurer'] as $name) {
            Role::firstOrCreate(['name' => $name]);
        }

        Role::firstOrCreate(['name' => 'Member']);
    }
}
