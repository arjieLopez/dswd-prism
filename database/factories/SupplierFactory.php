<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_name' => fake()->company(),
            'tin' => fake()->numerify('###-###-###-###'),
            'address' => fake()->address(),
            'contact_person' => fake()->name(),
            'contact_number' => fake()->numerify('09#########'),
            'email' => fake()->unique()->safeEmail(),
            'status_id' => Status::firstOrCreate(
                ['context' => 'supplier', 'name' => 'active'],
                ['display_name' => 'Active', 'color' => 'bg-green-100 text-green-800']
            )->id,
        ];
    }

    /**
     * Indicate that the supplier is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_id' => Status::firstOrCreate(
                ['name' => 'active', 'context' => 'supplier'],
                ['display_name' => 'Active', 'color' => 'bg-green-100 text-green-800']
            )->id,
        ]);
    }

    /**
     * Indicate that the supplier is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_id' => Status::firstOrCreate(
                ['name' => 'inactive', 'context' => 'supplier'],
                ['display_name' => 'Inactive', 'color' => 'bg-gray-100 text-gray-800']
            )->id,
        ]);
    }
}
