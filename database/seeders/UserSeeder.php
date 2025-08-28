<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // // Create admin user
        // User::create([
        //     'name' => 'Administrator',
        //     'email' => 'admin@stocktaking.com',
        //     'password' => Hash::make('password'),
        //     'email_verified_at' => now(),
        // ]);
    }
}
