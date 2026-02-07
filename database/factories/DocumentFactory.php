<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_number' => $this->faker->numberBetween(1, 1000),
            'document_number' => $this->faker->unique()->bothify('DOC-####'),
            'title' => $this->faker->sentence(),
            'code' => $this->faker->bothify('??-###'),
            'descriptor' => $this->faker->word(),
            'document_date' => $this->faker->date('m/Y'),
            'confidentiality' => 'Público',
            'version' => '1.0',
            'is_copy' => 'Não',
        ];
    }
}