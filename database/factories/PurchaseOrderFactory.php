<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_request_id' => PurchaseRequest::factory(),
            'supplier_id' => Supplier::factory(),
            'po_number' => 'PO-' . date('Y') . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'mode_of_procurement' => fake()->randomElement([
                'Public Bidding',
                'Shopping',
                'Small Value Procurement',
                'Direct Contracting',
                'Negotiated Procurement'
            ]),
            'delivery_term' => fake()->randomElement(['7 days', '15 days', '30 days', '45 days', '60 days']),
            'payment_term' => fake()->randomElement(['Net 15', 'Net 30', 'Net 45', 'Net 60', 'COD']),
            'date_of_delivery' => fake()->dateTimeBetween('now', '+60 days'),
            'status_id' => Status::firstOrCreate(
                ['context' => 'purchase_request', 'name' => 'po_generated'],
                ['display_name' => 'PO Generated', 'color' => 'bg-blue-100 text-blue-800']
            )->id,
            'generated_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'generated_by' => User::factory(),
            'completed_at' => null,
        ];
    }

    /**
     * Indicate that the purchase order has been generated.
     */
    public function generated(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_id' => Status::firstOrCreate(
                ['name' => 'po_generated', 'context' => 'purchase_request'],
                ['display_name' => 'PO Generated', 'color' => 'bg-blue-100 text-blue-800']
            )->id,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the purchase order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_id' => Status::firstOrCreate(
                ['name' => 'completed', 'context' => 'purchase_request'],
                ['display_name' => 'Completed', 'color' => 'bg-purple-100 text-purple-800']
            )->id,
            'completed_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
