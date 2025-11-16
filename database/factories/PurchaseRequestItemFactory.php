<?php

namespace Database\Factories;

use App\Models\PurchaseRequestItem;
use App\Models\PurchaseRequest;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseRequestItem>
 */
class PurchaseRequestItemFactory extends Factory
{
    protected $model = PurchaseRequestItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $items = [
            'Bond Paper (A4)',
            'Ballpen (Black)',
            'Ballpen (Blue)',
            'Folder (Long)',
            'Stapler',
            'Staple Wire',
            'Paper Clip',
            'Envelope (Long)',
            'Correction Tape',
            'Highlighter',
            'Notebook',
            'Calculator',
            'Tape Dispenser',
            'Scissors',
            'Glue Stick',
        ];

        $quantity = fake()->numberBetween(1, 100);
        $unitCost = fake()->randomFloat(2, 10, 500);
        $totalCost = $quantity * $unitCost;

        // Get a random unit from the seeded units, or create if none exist
        $unit = Unit::inRandomOrder()->first() ?? Unit::create([
            'name' => 'piece',
            'abbreviation' => 'pc'
        ]);

        return [
            'purchase_request_id' => PurchaseRequest::factory(),
            'unit_id' => $unit->id,
            'item_description' => fake()->randomElement($items),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
        ];
    }
}
