<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => '2024-10-14 06:33:44',
            'password' => '$2y$10$CSYf/w9dP0ipYijlUpMFN.FBTNjmphK3eH7gmxGDJwmFvejbEfXzS',
            'role_id' => 1
        ]);
    }
}
