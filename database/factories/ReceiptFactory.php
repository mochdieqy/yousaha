<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Receipt>
 */
class ReceiptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'warehouse_id' => Warehouse::factory(),
            'receive_from' => Supplier::factory(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+7 days'),
            'reference' => fake()->unique()->numerify('REF-######'),
            'status' => fake()->randomElement(['draft', 'waiting', 'ready', 'done', 'cancel']),
        ];
    }
}
