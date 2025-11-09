<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed designations
        $designations = [
            ['name' => 'Administrative Officer'],
            ['name' => 'Procurement Officer'],
            ['name' => 'Budget Officer'],
            ['name' => 'General Services Officer'],
            ['name' => 'Staff'],
            ['name' => 'Supervisor'],
        ];
        foreach ($designations as $designation) {
            DB::table('designations')->updateOrInsert(
                ['name' => $designation['name']],
                $designation + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // Seed offices
        $offices = [
            ['name' => 'DSWD Main Office'],
            ['name' => 'Regional Office'],
            ['name' => 'Provincial Office'],
            ['name' => 'City Office'],
            ['name' => 'Municipal Office'],
        ];
        foreach ($offices as $office) {
            DB::table('offices')->updateOrInsert(
                ['name' => $office['name']],
                $office + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // Seed roles
        $roles = [
            ['name' => 'admin'],
            ['name' => 'staff'],
            ['name' => 'user'],
        ];
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                $role + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // Seed statuses
        $statuses = [
            // Purchase Request statuses
            ['context' => 'purchase_request', 'name' => 'draft', 'display_name' => 'Draft', 'color' => 'bg-gray-100 text-gray-800'],
            ['context' => 'purchase_request', 'name' => 'pending', 'display_name' => 'Pending', 'color' => 'bg-yellow-100 text-yellow-800'],
            ['context' => 'purchase_request', 'name' => 'approved', 'display_name' => 'Approved', 'color' => 'bg-green-100 text-green-800'],
            ['context' => 'purchase_request', 'name' => 'rejected', 'display_name' => 'Rejected', 'color' => 'bg-red-100 text-red-800'],
            ['context' => 'purchase_request', 'name' => 'po_generated', 'display_name' => 'PO Generated', 'color' => 'bg-blue-100 text-blue-800'],
            ['context' => 'purchase_request', 'name' => 'completed', 'display_name' => 'Completed', 'color' => 'bg-purple-100 text-purple-800'],

            // Supplier statuses
            ['context' => 'supplier', 'name' => 'active', 'display_name' => 'Active', 'color' => 'bg-green-100 text-green-800'],
            ['context' => 'supplier', 'name' => 'inactive', 'display_name' => 'Inactive', 'color' => 'bg-gray-100 text-gray-800'],

            // Approval statuses
            ['context' => 'approval', 'name' => 'approved', 'display_name' => 'Approved', 'color' => 'bg-green-100 text-green-800'],
            ['context' => 'approval', 'name' => 'rejected', 'display_name' => 'Rejected', 'color' => 'bg-red-100 text-red-800'],

            // Purchase Order statuses
            ['context' => 'purchase_order', 'name' => 'generated', 'display_name' => 'PO Generated', 'color' => 'bg-blue-100 text-blue-800'],
            ['context' => 'purchase_order', 'name' => 'completed', 'display_name' => 'Completed', 'color' => 'bg-purple-100 text-purple-800'],
        ];
        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['context' => $status['context'], 'name' => $status['name']],
                $status + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // Seed units
        $units = [
            ['name' => 'Piece', 'abbreviation' => 'pcs'],
            ['name' => 'Box', 'abbreviation' => 'bx'],
            ['name' => 'Set', 'abbreviation' => 'set'],
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Liter', 'abbreviation' => 'L'],
            ['name' => 'Meter', 'abbreviation' => 'm'],
            ['name' => 'Pack', 'abbreviation' => 'pk'],
            ['name' => 'Bundle', 'abbreviation' => 'bdl'],
        ];
        foreach ($units as $unit) {
            DB::table('units')->updateOrInsert(
                ['name' => $unit['name']],
                $unit + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // Seed procurement modes
        $procurementModes = [
            ['name' => 'Public Bidding', 'description' => 'Competitive bidding process open to all qualified suppliers'],
            ['name' => 'Small Value Procurement', 'description' => 'Simplified procurement for low-value purchases'],
            ['name' => 'Direct Contracting', 'description' => 'Direct purchase from a specific supplier'],
            ['name' => 'Repeat Order', 'description' => 'Additional order from previously awarded supplier'],
        ];
        foreach ($procurementModes as $mode) {
            DB::table('procurement_modes')->updateOrInsert(
                ['name' => $mode['name']],
                $mode + ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
