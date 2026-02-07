<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commission>
 */
class CommissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Commission',
            'description' => $this->faker->paragraph(),
            'status' => 'active',
            'ordinance_number' => 'ORD-' . $this->faker->numberBetween(1000, 9999),
            'ordinance_date' => $this->faker->date(),
        ];
    }
}