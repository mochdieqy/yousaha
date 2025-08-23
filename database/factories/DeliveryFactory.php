<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['draft', 'waiting', 'ready', 'done', 'cancel'];
        
        return [
            'company_id' => Company::factory(),
            'warehouse_id' => Warehouse::factory(),
            'delivery_address' => $this->faker->address(),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+30 days'),
            'reference' => $this->faker->optional()->regexify('DEL-[A-Z0-9]{6}'),
            'status' => $this->faker->randomElement($statuses),
        ];
    }

    /**
     * Indicate that the delivery is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the delivery is in waiting status.
     */
    public function waiting(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'waiting',
        ]);
    }

    /**
     * Indicate that the delivery is in ready status.
     */
    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ready',
        ]);
    }

    /**
     * Indicate that the delivery is completed.
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'done',
        ]);
    }

    /**
     * Indicate that the delivery is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancel',
        ]);
    }

    /**
     * Indicate that the delivery is scheduled for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => $this->faker->dateTimeBetween('today', 'today +23 hours'),
        ]);
    }

    /**
     * Indicate that the delivery is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'status' => $this->faker->randomElement(['draft', 'waiting', 'ready']),
        ]);
    }
}
