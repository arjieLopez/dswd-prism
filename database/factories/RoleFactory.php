<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['admin', 'staff', 'user']),
        ];
    }

    /**
     * State for admin role
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'admin',
        ]);
    }

    /**
     * State for staff role
     */
    public function staff(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'staff',
        ]);
    }

    /**
     * State for user role
     */
    public function user(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'user',
        ]);
    }
}
