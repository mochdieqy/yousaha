<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => null, // Will be set in tests
            'code' => fake()->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'name' => fake()->words(2, true) . ' Warehouse',
            'address' => fake()->address(),
        ];
    }
}
