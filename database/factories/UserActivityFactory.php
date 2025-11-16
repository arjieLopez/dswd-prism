<?php

namespace Database\Factories;

use App\Models\UserActivity;
use App\Models\User;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserActivity>
 */
class UserActivityFactory extends Factory
{
    protected $model = UserActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = [
            'pr_created',
            'pr_submitted',
            'pr_approved',
            'pr_rejected',
            'po_generated',
            'pr_completed',
        ];

        $descriptions = [
            'Created a new Purchase Request',
            'Submitted Purchase Request for review',
            'Approved Purchase Request',
            'Rejected Purchase Request',
            'Generated Purchase Order',
            'Completed Purchase Request',
        ];

        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement($actions),
            'description' => fake()->randomElement($descriptions),
            'pr_number' => fake()->optional()->numerify('PR-####-####'),
            'document_name' => fake()->optional()->word() . '.pdf',
            'details' => fake()->optional()->passthrough(json_encode([
                'key' => fake()->word(),
                'value' => fake()->sentence(),
            ])),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the activity is for a specific PR.
     */
    public function forPurchaseRequest(PurchaseRequest $pr): static
    {
        return $this->state(fn(array $attributes) => [
            'pr_number' => $pr->pr_number,
            'description' => 'Activity for PR ' . $pr->pr_number,
        ]);
    }

    /**
     * Indicate that the activity is a PR submission notification.
     */
    public function prSubmitted(): static
    {
        return $this->state(fn(array $attributes) => [
            'action' => 'pr_submitted',
            'description' => 'New Purchase Request submitted for review',
        ]);
    }

    /**
     * Indicate that the activity is a PR approval notification.
     */
    public function prApproved(): static
    {
        return $this->state(fn(array $attributes) => [
            'action' => 'pr_approved',
            'description' => 'Your Purchase Request has been approved',
        ]);
    }
}
