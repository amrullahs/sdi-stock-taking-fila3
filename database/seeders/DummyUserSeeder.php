<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'Admin')->first();
        $stockTakerRole = Role::where('name', 'Stock Taker')->first();
        $viewerRole = Role::where('name', 'Viewer')->first();

        // Create Admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@stocktaking.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole($adminRole);

        // Create Stock Taker user
        $stockTakerUser = User::firstOrCreate(
            ['email' => 'stocktaker@stocktaking.com'],
            [
                'name' => 'Stock Taker User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $stockTakerUser->assignRole($stockTakerRole);

        // Create Viewer user
        $viewerUser = User::firstOrCreate(
            ['email' => 'viewer@stocktaking.com'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $viewerUser->assignRole($viewerRole);

        $this->command->info('Dummy users created successfully!');
        $this->command->info('Admin: admin@stocktaking.com (password: password)');
        $this->command->info('Stock Taker: stocktaker@stocktaking.com (password: password)');
        $this->command->info('Viewer: viewer@stocktaking.com (password: password)');
    }
}
