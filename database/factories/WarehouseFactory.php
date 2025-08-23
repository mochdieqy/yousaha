<?php

namespace Database\Factories;

use App\Models\Company;
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
        $warehouseTypes = [
            'Main Warehouse',
            'Distribution Center',
            'Storage Facility',
            'Regional Warehouse',
            'Cold Storage',
            'Raw Materials Warehouse',
            'Finished Goods Warehouse',
            'Transit Warehouse',
            'Bulk Storage',
            'High Bay Warehouse'
        ];

        $warehouseCodes = [
            'WH001', 'WH002', 'WH003', 'WH004', 'WH005',
            'MAIN', 'DIST', 'STOR', 'REG', 'COLD',
            'RAW', 'FG', 'TRANS', 'BULK', 'HIGH',
            'TEST', 'DEV', 'PROD', 'BACKUP', 'ARCHIVE'
        ];

        return [
            'company_id' => Company::factory(),
            'code' => $this->faker->unique()->randomElement($warehouseCodes),
            'name' => $this->faker->unique()->randomElement($warehouseTypes) . ' ' . $this->faker->city(),
            'address' => $this->faker->optional(0.8)->address(),
        ];
    }

    /**
     * Indicate that the warehouse is a main warehouse.
     */
    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'MAIN',
            'name' => 'Main Warehouse',
        ]);
    }

    /**
     * Indicate that the warehouse is a distribution center.
     */
    public function distribution(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'DIST',
            'name' => 'Distribution Center',
        ]);
    }

    /**
     * Indicate that the warehouse is a cold storage facility.
     */
    public function coldStorage(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'COLD',
            'name' => 'Cold Storage Facility',
        ]);
    }

    /**
     * Indicate that the warehouse has no address.
     */
    public function noAddress(): static
    {
        return $this->state(fn (array $attributes) => [
            'address' => null,
        ]);
    }
}
