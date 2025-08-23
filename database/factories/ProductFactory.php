<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->regexify('[A-Z]{2}[0-9]{4}'),
            'type' => fake()->randomElement(['goods', 'service', 'combo']),
            'is_track_inventory' => fake()->boolean(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'taxes' => fake()->randomFloat(2, 0, 50),
            'cost' => fake()->randomFloat(2, 5, 500),
            'barcode' => fake()->ean13(),
            'reference' => fake()->optional()->words(2, true),
            'is_shrink' => fake()->boolean(),
        ];
    }
}
