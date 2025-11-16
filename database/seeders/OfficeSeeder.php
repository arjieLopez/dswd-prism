<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            ['name' => 'Office of the Regional Director', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrative Division', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance Division', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Human Resource Management Section', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'General Services Section', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supply Management Section', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Planning Section', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ICT Section', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Legal Section', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Records Section', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('offices')->insert($offices);
    }
}
