<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Status>
 */
class StatusFactory extends Factory
{
    protected $model = Status::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contexts = ['purchase_request', 'purchase_order', 'supplier', 'approval'];
        $statuses = [
            ['name' => 'draft', 'display_name' => 'Draft', 'color' => 'bg-gray-100 text-gray-800', 'context' => 'purchase_request'],
            ['name' => 'pending', 'display_name' => 'Pending', 'color' => 'bg-yellow-100 text-yellow-800', 'context' => 'purchase_request'],
            ['name' => 'approved', 'display_name' => 'Approved', 'color' => 'bg-green-100 text-green-800', 'context' => 'purchase_request'],
            ['name' => 'rejected', 'display_name' => 'Rejected', 'color' => 'bg-red-100 text-red-800', 'context' => 'purchase_request'],
            ['name' => 'po_generated', 'display_name' => 'PO Generated', 'color' => 'bg-blue-100 text-blue-800', 'context' => 'purchase_request'],
            ['name' => 'completed', 'display_name' => 'Completed', 'color' => 'bg-purple-100 text-purple-800', 'context' => 'purchase_request'],
            ['name' => 'active', 'display_name' => 'Active', 'color' => 'bg-green-100 text-green-800', 'context' => 'supplier'],
            ['name' => 'inactive', 'display_name' => 'Inactive', 'color' => 'bg-gray-100 text-gray-800', 'context' => 'supplier'],
        ];

        $status = fake()->randomElement($statuses);

        return [
            'context' => $status['context'],
            'name' => $status['name'],
            'display_name' => $status['display_name'],
            'color' => $status['color'],
        ];
    }
}
