<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\Company;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all companies
        $companies = Company::all();
        
        if ($companies->isEmpty()) {
            $this->command->warn('No companies found. Please run CompanySeeder first.');
            return;
        }

        foreach ($companies as $company) {
            $this->createWarehousesForCompany($company);
        }

        $this->command->info('Warehouse seeding completed successfully.');
    }

    /**
     * Create warehouses for a specific company.
     */
    private function createWarehousesForCompany(Company $company): void
    {
        $warehouses = [
            [
                'code' => 'MAIN',
                'name' => 'Main Warehouse',
                'address' => $company->address ?? 'Main Street, Downtown Area',
            ],
            [
                'code' => 'DIST',
                'name' => 'Distribution Center',
                'address' => 'Industrial Zone, Highway Access',
            ],
            [
                'code' => 'STOR',
                'name' => 'Storage Facility',
                'address' => 'Storage Complex, Business District',
            ],
            [
                'code' => 'REG',
                'name' => 'Regional Warehouse',
                'address' => 'Regional Business Park',
            ],
            [
                'code' => 'COLD',
                'name' => 'Cold Storage Facility',
                'address' => 'Refrigeration Complex, Industrial Area',
            ],
        ];

        foreach ($warehouses as $warehouseData) {
            Warehouse::create([
                'company_id' => $company->id,
                'code' => $warehouseData['code'],
                'name' => $warehouseData['name'],
                'address' => $warehouseData['address'],
            ]);
        }

        $this->command->info("Created " . count($warehouses) . " warehouses for company: {$company->name}");
    }
}
