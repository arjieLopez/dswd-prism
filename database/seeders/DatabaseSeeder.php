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
        // Seed lookup tables first (no foreign keys)
        $this->call([
            RoleSeeder::class,
            DesignationSeeder::class,
            OfficeSeeder::class,
            StatusSeeder::class,
            UnitSeeder::class,
            ProcurementModeSeeder::class,
            SystemSelectionSeeder::class,
        ]);

        // Seed users (depends on roles, designations, offices)
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}
