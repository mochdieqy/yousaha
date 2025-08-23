<?php

namespace Database\Factories;

use App\Models\Stock;
use App\Models\Company;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stock::class;

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
            'product_id' => Product::factory(),
            'quantity_total' => $this->faker->numberBetween(0, 1000),
            'quantity_reserve' => $this->faker->numberBetween(0, 100),
            'quantity_saleable' => function (array $attributes) {
                return $attributes['quantity_total'] - $attributes['quantity_reserve'];
            },
            'quantity_incoming' => $this->faker->numberBetween(0, 50),
        ];
    }

    /**
     * Indicate that the stock has low quantity.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity_total' => $this->faker->numberBetween(5, 10),
            'quantity_reserve' => 0,
            'quantity_saleable' => function (array $attributes) {
                return $attributes['quantity_total'];
            },
        ]);
    }

    /**
     * Indicate that the stock is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity_total' => 0,
            'quantity_reserve' => 0,
            'quantity_saleable' => 0,
            'quantity_incoming' => 0,
        ]);
    }

    /**
     * Indicate that the stock has high quantity.
     */
    public function highStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity_total' => $this->faker->numberBetween(100, 1000),
            'quantity_reserve' => $this->faker->numberBetween(0, 50),
            'quantity_saleable' => function (array $attributes) {
                return $attributes['quantity_total'] - $attributes['quantity_reserve'];
            },
        ]);
    }

    /**
     * Indicate that the stock has reserved quantity.
     */
    public function withReserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity_reserve' => $this->faker->numberBetween(10, 100),
            'quantity_saleable' => function (array $attributes) {
                return $attributes['quantity_total'] - $attributes['quantity_reserve'];
            },
        ]);
    }

    /**
     * Indicate that the stock has incoming quantity.
     */
    public function withIncoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity_incoming' => $this->faker->numberBetween(10, 100),
        ]);
    }
}
