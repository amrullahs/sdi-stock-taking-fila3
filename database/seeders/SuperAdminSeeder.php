<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super-admin role if not exists
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);

        // Get all permissions and assign to super-admin role
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        // Create super-admin user
        $superAdminUser = User::firstOrCreate(
            ['email' => 'amrullah@sankei-dharma.com'],
            [
                'name' => 'Amrullah',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign super-admin role to user
        $superAdminUser->assignRole($superAdminRole);

        $this->command->info('Super Admin user created successfully!');
        $this->command->info('Email: amrullah@sankei-dharma.com');
        $this->command->info('Password: password');
        $this->command->info('Role: super-admin with ' . $superAdminRole->permissions->count() . ' permissions');
    }
}
