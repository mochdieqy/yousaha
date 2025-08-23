<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
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
            'number' => 'PO-' . fake()->unique()->numerify('######'),
            'supplier_id' => Supplier::factory(),
            'requestor' => fake()->name(),
            'activities' => fake()->sentence(),
            'total' => fake()->randomFloat(2, 100, 10000),
            'status' => fake()->randomElement(['draft', 'accepted', 'sent', 'done', 'cancel']),
            'deadline' => fake()->dateTimeBetween('now', '+30 days'),
        ];
    }
}
