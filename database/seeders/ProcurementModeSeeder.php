<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $procurementModes = [
            ['name' => 'Public Bidding', 'description' => 'Open competitive bidding for procurement', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shopping', 'description' => 'Shopping for small value procurement', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Direct Contracting', 'description' => 'Direct contracting with supplier', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Negotiated Procurement', 'description' => 'Negotiated procurement method', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('procurement_modes')->insert($procurementModes);
    }
}
