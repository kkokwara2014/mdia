<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@mdia.com'],
            [
                'name' => 'MDIA Super Admin',
                'phone' => '00000000000',
                'password' => Hash::make('Admin@1234'),
                'user_image' => null,
            ]
        );

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $memberRole = Role::firstOrCreate(['name' => 'Member']);

        $roleIds = collect([$memberRole->id]);
        if ($superAdminRole) {
            $roleIds->push($superAdminRole->id);
        }
        $superAdmin->roles()->sync($roleIds->unique()->values()->all());
    }
}
