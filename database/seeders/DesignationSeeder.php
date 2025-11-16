<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            ['name' => 'Administrative Officer V', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrative Officer IV', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrative Officer III', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrative Officer II', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrative Officer I', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrative Aide VI', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrative Aide IV', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrative Aide III', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Director', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supervisor', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('designations')->insert($designations);
    }
}
