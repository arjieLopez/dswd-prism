<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSelection;

class SystemSelectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $selections = [
            // Metric Units
            ['type' => 'metric_units', 'name' => 'pcs'],
            ['type' => 'metric_units', 'name' => 'set'],
            ['type' => 'metric_units', 'name' => 'pair'],
            ['type' => 'metric_units', 'name' => 'dozen'],
            ['type' => 'metric_units', 'name' => 'lot'],
            ['type' => 'metric_units', 'name' => 'box'],
            ['type' => 'metric_units', 'name' => 'pack'],
            ['type' => 'metric_units', 'name' => 'carton'],
            ['type' => 'metric_units', 'name' => 'case'],
            ['type' => 'metric_units', 'name' => 'roll'],
            ['type' => 'metric_units', 'name' => 'ream'],
            ['type' => 'metric_units', 'name' => 'bundle'],
            ['type' => 'metric_units', 'name' => 'tube'],
            ['type' => 'metric_units', 'name' => 'bottle'],
            ['type' => 'metric_units', 'name' => 'can'],
            ['type' => 'metric_units', 'name' => 'jar'],
            ['type' => 'metric_units', 'name' => 'sachet'],
            ['type' => 'metric_units', 'name' => 'drum'],
            ['type' => 'metric_units', 'name' => 'barrel'],
            ['type' => 'metric_units', 'name' => 'bag'],
            ['type' => 'metric_units', 'name' => 'g'],
            ['type' => 'metric_units', 'name' => 'kg'],
            ['type' => 'metric_units', 'name' => 'lb'],
            ['type' => 'metric_units', 'name' => 'ton'],
            ['type' => 'metric_units', 'name' => 'ml'],
            ['type' => 'metric_units', 'name' => 'L'],
            ['type' => 'metric_units', 'name' => 'gal'],
            ['type' => 'metric_units', 'name' => 'mm'],
            ['type' => 'metric_units', 'name' => 'cm'],
            ['type' => 'metric_units', 'name' => 'm'],
            ['type' => 'metric_units', 'name' => 'km'],
            ['type' => 'metric_units', 'name' => 'sqm'],

            // Other system selections (placeholders)

            // Entity Name
            ['type' => 'entity', 'name' => 'Department of Social Welfare and Development'],
            ['type' => 'entity', 'name' => 'DSWD Field Office XI'],
            ['type' => 'entity', 'name' => 'DSWD Central Office'],
            ['type' => 'entity', 'name' => 'DSWD Regional Office'],

            // Fund Cluster
            ['type' => 'fund_cluster', 'name' => '01 - Regular Agency Fund'],
            ['type' => 'fund_cluster', 'name' => '02 - Continuing Fund'],
            ['type' => 'fund_cluster', 'name' => '03 - Foreign-Assisted Projects'],
            ['type' => 'fund_cluster', 'name' => '04 - Special Account in the General Fund'],

            // Responsibility Code
            ['type' => 'responsibility_code', 'name' => 'Administrative Division'],
            ['type' => 'responsibility_code', 'name' => 'Finance Division'],
            ['type' => 'responsibility_code', 'name' => 'Human Resource Management Division'],
            ['type' => 'responsibility_code', 'name' => 'General Services Office'],
            ['type' => 'responsibility_code', 'name' => 'Supply Management Unit'],

            // Delivery Period
            ['type' => 'delivery_period', 'name' => '7 days'],
            ['type' => 'delivery_period', 'name' => '15 days'],
            ['type' => 'delivery_period', 'name' => '30 days'],
            ['type' => 'delivery_period', 'name' => '45 days'],
            ['type' => 'delivery_period', 'name' => '60 days'],
            ['type' => 'delivery_period', 'name' => '90 days'],

            // Delivery Address
            ['type' => 'delivery_address', 'name' => 'DSWD Field Office XI, Ramon Magsaysay Ave, D Suazo St, Davao City'],
            ['type' => 'delivery_address', 'name' => 'DSWD Central Office, IBP Road, Constitution Hills, Batasan Complex, Quezon City'],
            ['type' => 'delivery_address', 'name' => 'Department of Social Welfare and Development, IBP Road, Batasan Hills, Quezon City'],

            // Delivery Term
            ['type' => 'delivery_term', 'name' => 'FOB (Free on Board) - Supplier\'s Warehouse'],
            ['type' => 'delivery_term', 'name' => 'FOB (Free on Board) - Destination'],
            ['type' => 'delivery_term', 'name' => 'CIF (Cost, Insurance, and Freight)'],
            ['type' => 'delivery_term', 'name' => 'Ex Works (EXW)'],
            ['type' => 'delivery_term', 'name' => 'Delivered at Place (DAP)'],

            // Payment Term
            ['type' => 'payment_term', 'name' => 'Net 30 days'],
            ['type' => 'payment_term', 'name' => 'Net 45 days'],
            ['type' => 'payment_term', 'name' => 'Net 60 days'],
            ['type' => 'payment_term', 'name' => '50% Down Payment, 50% Upon Delivery'],
            ['type' => 'payment_term', 'name' => 'Full Payment Upon Delivery'],
            ['type' => 'payment_term', 'name' => 'Payment After Inspection'],
        ];

        foreach ($selections as $selection) {
            SystemSelection::create($selection);
        }
    }
}
