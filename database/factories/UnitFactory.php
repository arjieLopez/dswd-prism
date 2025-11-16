<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    protected $model = Unit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $units = [
            ['name' => 'piece', 'abbreviation' => 'pc'],
            ['name' => 'box', 'abbreviation' => 'box'],
            ['name' => 'ream', 'abbreviation' => 'rm'],
            ['name' => 'set', 'abbreviation' => 'set'],
            ['name' => 'pack', 'abbreviation' => 'pack'],
            ['name' => 'dozen', 'abbreviation' => 'doz'],
            ['name' => 'unit', 'abbreviation' => 'unit'],
            ['name' => 'bottle', 'abbreviation' => 'btl'],
            ['name' => 'gallon', 'abbreviation' => 'gal'],
            ['name' => 'kilogram', 'abbreviation' => 'kg'],
        ];

        // Find existing unit or create new one
        $unit = fake()->randomElement($units);
        $existingUnit = Unit::where('name', $unit['name'])->first();

        if ($existingUnit) {
            return $existingUnit->toArray();
        }

        return $unit;
    }
}
