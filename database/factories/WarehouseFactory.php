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
            'High Bay Warehouse',
            'Production Warehouse',
            'Quality Control Warehouse',
            'Shipping Warehouse',
            'Receiving Warehouse',
            'Cross Dock Warehouse',
            'Automated Warehouse',
            'Manual Warehouse',
            'Multi-Temperature Warehouse',
            'Hazardous Materials Warehouse',
            'Pharmaceutical Warehouse',
            'Food Grade Warehouse',
            'Textile Warehouse',
            'Electronics Warehouse',
            'Automotive Warehouse',
            'Chemical Warehouse'
        ];

        // Generate dynamic codes to avoid unique conflicts
        $code = 'WH' . str_pad($this->faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Generate dynamic names to avoid unique conflicts
        $type = $this->faker->randomElement($warehouseTypes);
        $city = $this->faker->city();
        $suffix = $this->faker->optional(0.7)->randomElement(['Branch', 'Facility', 'Center', 'Hub', 'Station', 'Point']);
        
        $name = $suffix ? "{$type} - {$city} {$suffix}" : "{$type} - {$city}";

        return [
            'company_id' => Company::factory(),
            'code' => $code,
            'name' => $name,
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
