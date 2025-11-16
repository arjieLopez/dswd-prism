<?php

namespace Database\Factories;

use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Office>
 */
class OfficeFactory extends Factory
{
    protected $model = Office::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $offices = [
            'Central Office',
            'Regional Office I',
            'Regional Office II',
            'Regional Office III',
            'Regional Office IV-A',
            'Regional Office IV-B',
            'Regional Office V',
            'Field Office',
            'Division Office',
            'Administrative Services',
            'Finance Division',
            'Human Resource Management',
            'Procurement Unit',
            'Planning and Development',
        ];

        return [
            'name' => fake()->unique()->randomElement($offices),
        ];
    }
}
