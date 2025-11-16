<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            // Procurement Statuses (shared by Purchase Requests and Purchase Orders)
            ['context' => 'procurement', 'name' => 'draft', 'display_name' => 'Draft', 'color' => 'bg-gray-100 text-gray-800', 'created_at' => now(), 'updated_at' => now()],
            ['context' => 'procurement', 'name' => 'pending', 'display_name' => 'Pending Review', 'color' => 'bg-yellow-100 text-yellow-800', 'created_at' => now(), 'updated_at' => now()],
            ['context' => 'procurement', 'name' => 'approved', 'display_name' => 'Approved', 'color' => 'bg-green-100 text-green-800', 'created_at' => now(), 'updated_at' => now()],
            ['context' => 'procurement', 'name' => 'rejected', 'display_name' => 'Rejected', 'color' => 'bg-red-100 text-red-800', 'created_at' => now(), 'updated_at' => now()],
            ['context' => 'procurement', 'name' => 'po_generated', 'display_name' => 'PO Generated', 'color' => 'bg-purple-100 text-purple-800', 'created_at' => now(), 'updated_at' => now()],
            ['context' => 'procurement', 'name' => 'completed', 'display_name' => 'Completed', 'color' => 'bg-indigo-100 text-indigo-800', 'created_at' => now(), 'updated_at' => now()],

            // Supplier Statuses
            ['context' => 'supplier', 'name' => 'active', 'display_name' => 'Active', 'color' => 'bg-green-100 text-green-800', 'created_at' => now(), 'updated_at' => now()],
            ['context' => 'supplier', 'name' => 'inactive', 'display_name' => 'Inactive', 'color' => 'bg-gray-100 text-gray-800', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('statuses')->insert($statuses);
    }
}
