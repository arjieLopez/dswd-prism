<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->randomElement([fake()->firstName(), null]),
            'last_name' => fake()->lastName(),
            'designation_id' => null, // Allow tests to set this if needed
            'office_id' => null, // Allow tests to set this if needed
            'employee_id' => fake()->unique()->numerify('EMP###'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // password
            'role_id' => null, // Allow tests to set this if needed
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'email' => 'khalid.a.ambobot@gmail.com',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'employee_id' => 'EMP001',
        ]);
    }

    public function staff(): static
    {
        return $this->state(fn(array $attributes) => [
            'email' => 'arjiepelimerlopez@gmail.com',
            'first_name' => 'Staff',
            'last_name' => 'User',
            'employee_id' => 'EMP002',
        ]);
    }

    public function user(): static
    {
        return $this->state(fn(array $attributes) => [
            'email' => 'crisostomoarnaldopilipinoleand@gmail.com',
            'first_name' => 'User',
            'last_name' => 'User',
            'employee_id' => 'EMP003',
        ]);
    }
}
