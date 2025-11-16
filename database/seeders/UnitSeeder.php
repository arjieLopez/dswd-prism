<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'abbreviation' => 'pc', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Set', 'abbreviation' => 'set', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Unit', 'abbreviation' => 'unit', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Box', 'abbreviation' => 'box', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pack', 'abbreviation' => 'pack', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ream', 'abbreviation' => 'ream', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bottle', 'abbreviation' => 'btl', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gallon', 'abbreviation' => 'gal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Liter', 'abbreviation' => 'ltr', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kilogram', 'abbreviation' => 'kg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meter', 'abbreviation' => 'm', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Roll', 'abbreviation' => 'roll', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pair', 'abbreviation' => 'pair', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dozen', 'abbreviation' => 'dz', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Carton', 'abbreviation' => 'ctn', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Can', 'abbreviation' => 'can', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sack', 'abbreviation' => 'sack', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bag', 'abbreviation' => 'bag', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bundle', 'abbreviation' => 'bdl', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Length', 'abbreviation' => 'lgth', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('units')->insert($units);
    }
}
