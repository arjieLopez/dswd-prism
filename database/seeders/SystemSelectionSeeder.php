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
            ['type' => 'entity', 'name' => 'DSWD'],
            ['type' => 'fund_cluster', 'name' => '01'],
            ['type' => 'responsibility_code', 'name' => 'RC-001'],
            ['type' => 'delivery_period', 'name' => '7 days'],
            ['type' => 'delivery_period', 'name' => '14 days'],
            ['type' => 'delivery_period', 'name' => '30 days'],
            ['type' => 'delivery_address', 'name' => 'DSWD Main Office'],
            ['type' => 'mode_of_procurement', 'name' => 'Competitive Bidding'],
            ['type' => 'mode_of_procurement', 'name' => 'Limited Source Bidding'],
            ['type' => 'mode_of_procurement', 'name' => 'Negotiated Procurement'],
            ['type' => 'mode_of_procurement', 'name' => 'Direct Contracting'],
            ['type' => 'mode_of_procurement', 'name' => 'Repeat Order'],
            ['type' => 'mode_of_procurement', 'name' => 'Small Value Procurement'],
            ['type' => 'delivery_term', 'name' => 'FOB'],
            ['type' => 'delivery_term', 'name' => 'CIF'],
            ['type' => 'payment_term', 'name' => 'COD'],
            ['type' => 'payment_term', 'name' => '30 days'],
            ['type' => 'payment_term', 'name' => '60 days'],
        ];

        foreach ($selections as $selection) {
            SystemSelection::create($selection);
        }
    }
}
