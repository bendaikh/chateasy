<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create SuperAdmin
        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@chateasy.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Customer
        \App\Models\User::create([
            'name' => 'Test Customer',
            'email' => 'customer@chateasy.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'company_name' => 'Test Company',
            'phone' => '+1234567890',
            'subscription_plan' => 'advanced',
            'subscription_ends_at' => now()->addMonth(),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
