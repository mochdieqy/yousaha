<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Company;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the demo company
        $company = Company::where('name', 'Yousaha Demo Company')->first();
        
        if (!$company) {
            $this->command->error('Demo company not found. Please run UserSeeder first.');
            return;
        }

        $this->command->info('Creating products for company: ' . $company->name);

        // Create goods products
        $this->createGoodsProducts($company);
        
        // Create service products
        $this->createServiceProducts($company);
        
        // Create combo products
        $this->createComboProducts($company);

        $this->command->info('Product seeding completed successfully!');
    }

    private function createGoodsProducts($company)
    {
        $goodsProducts = [
            [
                'name' => 'Dell Laptop XPS 13',
                'sku' => 'LAPTOP001',
                'price' => 25000000,
                'cost' => 19000000,
                'taxes' => 1250000,
                'barcode' => '123456789012',
                'reference' => 'Electronics - Computers',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'PHONE001',
                'price' => 18000000,
                'cost' => 13500000,
                'taxes' => 900000,
                'barcode' => '123456789013',
                'reference' => 'Electronics - Mobile',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Office Chair Premium',
                'sku' => 'CHAIR001',
                'price' => 2500000,
                'cost' => 1500000,
                'taxes' => 125000,
                'barcode' => '123456789014',
                'reference' => 'Furniture - Office',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Wireless Mouse Logitech',
                'sku' => 'MOUSE001',
                'price' => 450000,
                'cost' => 225000,
                'taxes' => 22500,
                'barcode' => '123456789015',
                'reference' => 'Electronics - Accessories',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Coffee Beans Premium',
                'sku' => 'COFFEE001',
                'price' => 125000,
                'cost' => 60000,
                'taxes' => 6250,
                'barcode' => '123456789016',
                'reference' => 'Food & Beverage',
                'is_track_inventory' => true,
                'is_shrink' => true,
            ],
            [
                'name' => 'Notebook A4 Spiral',
                'sku' => 'NOTE001',
                'price' => 25000,
                'cost' => 12500,
                'taxes' => 1250,
                'barcode' => '123456789017',
                'reference' => 'Stationery',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'USB Cable Type-C',
                'sku' => 'USB001',
                'price' => 150000,
                'cost' => 60000,
                'taxes' => 7500,
                'barcode' => '123456789018',
                'reference' => 'Electronics - Cables',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Desk Lamp LED',
                'sku' => 'LAMP001',
                'price' => 750000,
                'cost' => 450000,
                'taxes' => 37500,
                'barcode' => '123456789019',
                'reference' => 'Furniture - Lighting',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Printer Paper A4',
                'sku' => 'PAPER001',
                'price' => 65000,
                'cost' => 30000,
                'taxes' => 3250,
                'barcode' => '123456789020',
                'reference' => 'Office Supplies',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Monitor 24 inch 4K',
                'sku' => 'MONITOR001',
                'price' => 4500000,
                'cost' => 3150000,
                'taxes' => 225000,
                'barcode' => '123456789021',
                'reference' => 'Electronics - Displays',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'sku' => 'KEYBOARD001',
                'price' => 1500000,
                'cost' => 850000,
                'taxes' => 75000,
                'barcode' => '123456789022',
                'reference' => 'Electronics - Input',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Headphones Wireless',
                'sku' => 'HEADPHONE001',
                'price' => 2000000,
                'cost' => 1200000,
                'taxes' => 100000,
                'barcode' => '123456789023',
                'reference' => 'Electronics - Audio',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Water Bottle Steel',
                'sku' => 'BOTTLE001',
                'price' => 300000,
                'cost' => 150000,
                'taxes' => 15000,
                'barcode' => '123456789024',
                'reference' => 'Lifestyle',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Backpack Laptop',
                'sku' => 'BACKPACK001',
                'price' => 900000,
                'cost' => 500000,
                'taxes' => 45000,
                'barcode' => '123456789025',
                'reference' => 'Accessories',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Power Bank 20000mAh',
                'sku' => 'POWERBANK001',
                'price' => 600000,
                'cost' => 300000,
                'taxes' => 30000,
                'barcode' => '123456789026',
                'reference' => 'Electronics - Power',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
        ];

        foreach ($goodsProducts as $productData) {
            Product::firstOrCreate([
                'sku' => $productData['sku'],
                'company_id' => $company->id,
            ], array_merge($productData, [
                'company_id' => $company->id,
                'type' => 'goods',
            ]));
        }

        $this->command->info('Created ' . count($goodsProducts) . ' goods products');
    }

    private function createServiceProducts($company)
    {
        $serviceProducts = [
            [
                'name' => 'Web Development Service',
                'sku' => 'WEBDEV001',
                'price' => 25000000,
                'cost' => 12000000,
                'taxes' => 1250000,
                'reference' => 'IT Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Digital Marketing Consultation',
                'sku' => 'MARKETING001',
                'price' => 5000000,
                'cost' => 2000000,
                'taxes' => 250000,
                'reference' => 'Marketing Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'IT Support Monthly',
                'sku' => 'SUPPORT001',
                'price' => 3000000,
                'cost' => 1500000,
                'taxes' => 150000,
                'reference' => 'IT Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Graphic Design Service',
                'sku' => 'DESIGN001',
                'price' => 1500000,
                'cost' => 750000,
                'taxes' => 75000,
                'reference' => 'Creative Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Data Analytics Consultation',
                'sku' => 'ANALYTICS001',
                'price' => 8000000,
                'cost' => 4000000,
                'taxes' => 400000,
                'reference' => 'Consulting Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Cloud Migration Service',
                'sku' => 'CLOUD001',
                'price' => 15000000,
                'cost' => 8000000,
                'taxes' => 750000,
                'reference' => 'IT Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'SEO Optimization Service',
                'sku' => 'SEO001',
                'price' => 4000000,
                'cost' => 1800000,
                'taxes' => 200000,
                'reference' => 'Marketing Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Training Session - 1 Day',
                'sku' => 'TRAINING001',
                'price' => 2500000,
                'cost' => 1000000,
                'taxes' => 125000,
                'reference' => 'Training Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
        ];

        foreach ($serviceProducts as $productData) {
            Product::firstOrCreate([
                'sku' => $productData['sku'],
                'company_id' => $company->id,
            ], array_merge($productData, [
                'company_id' => $company->id,
                'type' => 'service',
            ]));
        }

        $this->command->info('Created ' . count($serviceProducts) . ' service products');
    }

    private function createComboProducts($company)
    {
        $comboProducts = [
            [
                'name' => 'Laptop + Software Bundle',
                'sku' => 'COMBO001',
                'price' => 30000000,
                'cost' => 22000000,
                'taxes' => 1500000,
                'reference' => 'Hardware + Software',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Office Setup Package',
                'sku' => 'COMBO002',
                'price' => 9000000,
                'cost' => 6000000,
                'taxes' => 450000,
                'reference' => 'Furniture + Installation',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Website + Hosting Package',
                'sku' => 'COMBO003',
                'price' => 12000000,
                'cost' => 7000000,
                'taxes' => 600000,
                'reference' => 'Service + Product',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Smart Home Starter Kit',
                'sku' => 'COMBO004',
                'price' => 8000000,
                'cost' => 5000000,
                'taxes' => 400000,
                'reference' => 'IoT Devices + Setup',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Marketing Campaign Package',
                'sku' => 'COMBO005',
                'price' => 20000000,
                'cost' => 12000000,
                'taxes' => 1000000,
                'reference' => 'Service + Materials',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
        ];

        foreach ($comboProducts as $productData) {
            Product::firstOrCreate([
                'sku' => $productData['sku'],
                'company_id' => $company->id,
            ], array_merge($productData, [
                'company_id' => $company->id,
                'type' => 'combo',
            ]));
        }

        $this->command->info('Created ' . count($comboProducts) . ' combo products');
    }
}
