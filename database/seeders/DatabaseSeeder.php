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
        // Create admin
        \App\Models\User::factory()->admin()->create([
            'password' => bcrypt('12345678'), // Set a known password
        ]);

        // Create staff
        \App\Models\User::factory()->staff()->create([
            'password' => bcrypt('12345678'),
        ]);

        // Create user
        \App\Models\User::factory()->user()->create([
            'password' => bcrypt('12345678'),
        ]);
    }
}
