<?php

namespace Database\Factories;

use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\Office;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseRequest>
 */
class PurchaseRequestFactory extends Factory
{
    protected $model = PurchaseRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'office_id' => Office::factory(),
            'pr_number' => 'PR-' . date('Y') . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'entity_name' => 'DSWD',
            'fund_cluster' => fake()->randomElement(['01', '02', '03']),
            'responsibility_center_code' => 'RC-' . fake()->randomNumber(3),
            'date' => fake()->date(),
            'submitted_at' => null,
            'stoc_property_no' => fake()->optional()->numerify('STOC-####'),
            'total' => fake()->randomFloat(2, 1000, 100000),
            'delivery_period' => fake()->randomElement(['7 days', '15 days', '30 days', '45 days']),
            'delivery_address' => fake()->address(),
            'purpose' => fake()->sentence(10),
            'status_id' => Status::firstOrCreate(
                ['context' => 'purchase_request', 'name' => 'draft'],
                ['display_name' => 'Draft', 'color' => 'bg-gray-100 text-gray-800']
            )->id,
            'procurement_mode_id' => null,
            'remarks' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the purchase request is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_id' => Status::firstOrCreate(
                ['name' => 'draft', 'context' => 'purchase_request'],
                ['display_name' => 'Draft', 'color' => 'bg-gray-100 text-gray-800']
            )->id,
            'submitted_at' => null,
        ]);
    }

    /**
     * Indicate that the purchase request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_id' => Status::firstOrCreate(
                ['name' => 'pending', 'context' => 'purchase_request'],
                ['display_name' => 'Pending', 'color' => 'bg-yellow-100 text-yellow-800']
            )->id,
            'submitted_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Indicate that the purchase request is approved.
     */
    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_id' => Status::firstOrCreate(
                ['name' => 'approved', 'context' => 'purchase_request'],
                ['display_name' => 'Approved', 'color' => 'bg-green-100 text-green-800']
            )->id,
            'submitted_at' => fake()->dateTimeBetween('-14 days', '-7 days'),
        ]);
    }

    /**
     * Indicate that the purchase request is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_id' => Status::firstOrCreate(
                ['name' => 'rejected', 'context' => 'purchase_request'],
                ['display_name' => 'Rejected', 'color' => 'bg-red-100 text-red-800']
            )->id,
            'submitted_at' => fake()->dateTimeBetween('-14 days', '-7 days'),
            'remarks' => fake()->sentence(),
        ]);
    }
}
