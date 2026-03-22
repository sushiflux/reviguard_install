<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'RGAdmin'],
            [
                'vorname'         => 'System',
                'nachname'        => 'Administrator',
                'username'        => 'RGAdmin',
                'email'           => 'rgadmin@reviguard.local',
                'password'        => Hash::make('RGAdmin'),
                'is_active'       => true,
                'is_system_admin' => true,
            ]
        );

        $systemAdminRole = Role::where('name', 'system_admin')->first();
        $adminRole       = Role::where('name', 'administrator')->first();

        if ($systemAdminRole && !$admin->roles()->where('role_id', $systemAdminRole->id)->exists()) {
            $admin->roles()->attach($systemAdminRole->id);
        }

        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
        }
    }
}
