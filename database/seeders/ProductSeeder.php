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
                'price' => 1299.99,
                'cost' => 950.00,
                'taxes' => 65.00,
                'barcode' => '123456789012',
                'reference' => 'Electronics - Computers',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'PHONE001',
                'price' => 999.99,
                'cost' => 750.00,
                'taxes' => 50.00,
                'barcode' => '123456789013',
                'reference' => 'Electronics - Mobile',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Office Chair Premium',
                'sku' => 'CHAIR001',
                'price' => 299.99,
                'cost' => 180.00,
                'taxes' => 15.00,
                'barcode' => '123456789014',
                'reference' => 'Furniture - Office',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Wireless Mouse Logitech',
                'sku' => 'MOUSE001',
                'price' => 49.99,
                'cost' => 25.00,
                'taxes' => 2.50,
                'barcode' => '123456789015',
                'reference' => 'Electronics - Accessories',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Coffee Beans Premium',
                'sku' => 'COFFEE001',
                'price' => 24.99,
                'cost' => 12.00,
                'taxes' => 1.25,
                'barcode' => '123456789016',
                'reference' => 'Food & Beverage',
                'is_track_inventory' => true,
                'is_shrink' => true,
            ],
            [
                'name' => 'Notebook A4 Spiral',
                'sku' => 'NOTE001',
                'price' => 5.99,
                'cost' => 2.50,
                'taxes' => 0.30,
                'barcode' => '123456789017',
                'reference' => 'Stationery',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'USB Cable Type-C',
                'sku' => 'USB001',
                'price' => 19.99,
                'cost' => 8.00,
                'taxes' => 1.00,
                'barcode' => '123456789018',
                'reference' => 'Electronics - Cables',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Desk Lamp LED',
                'sku' => 'LAMP001',
                'price' => 79.99,
                'cost' => 45.00,
                'taxes' => 4.00,
                'barcode' => '123456789019',
                'reference' => 'Furniture - Lighting',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Printer Paper A4',
                'sku' => 'PAPER001',
                'price' => 12.99,
                'cost' => 6.00,
                'taxes' => 0.65,
                'barcode' => '123456789020',
                'reference' => 'Office Supplies',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Monitor 24 inch 4K',
                'sku' => 'MONITOR001',
                'price' => 399.99,
                'cost' => 280.00,
                'taxes' => 20.00,
                'barcode' => '123456789021',
                'reference' => 'Electronics - Displays',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'sku' => 'KEYBOARD001',
                'price' => 149.99,
                'cost' => 85.00,
                'taxes' => 7.50,
                'barcode' => '123456789022',
                'reference' => 'Electronics - Input',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Headphones Wireless',
                'sku' => 'HEADPHONE001',
                'price' => 199.99,
                'cost' => 120.00,
                'taxes' => 10.00,
                'barcode' => '123456789023',
                'reference' => 'Electronics - Audio',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Water Bottle Steel',
                'sku' => 'BOTTLE001',
                'price' => 29.99,
                'cost' => 15.00,
                'taxes' => 1.50,
                'barcode' => '123456789024',
                'reference' => 'Lifestyle',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Backpack Laptop',
                'sku' => 'BACKPACK001',
                'price' => 89.99,
                'cost' => 50.00,
                'taxes' => 4.50,
                'barcode' => '123456789025',
                'reference' => 'Accessories',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Power Bank 20000mAh',
                'sku' => 'POWERBANK001',
                'price' => 59.99,
                'cost' => 30.00,
                'taxes' => 3.00,
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
                'price' => 2500.00,
                'cost' => 1200.00,
                'taxes' => 125.00,
                'reference' => 'IT Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Digital Marketing Consultation',
                'sku' => 'MARKETING001',
                'price' => 500.00,
                'cost' => 200.00,
                'taxes' => 25.00,
                'reference' => 'Marketing Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'IT Support Monthly',
                'sku' => 'SUPPORT001',
                'price' => 300.00,
                'cost' => 150.00,
                'taxes' => 15.00,
                'reference' => 'IT Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Graphic Design Service',
                'sku' => 'DESIGN001',
                'price' => 150.00,
                'cost' => 75.00,
                'taxes' => 7.50,
                'reference' => 'Creative Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Data Analytics Consultation',
                'sku' => 'ANALYTICS001',
                'price' => 800.00,
                'cost' => 400.00,
                'taxes' => 40.00,
                'reference' => 'Consulting Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Cloud Migration Service',
                'sku' => 'CLOUD001',
                'price' => 1500.00,
                'cost' => 800.00,
                'taxes' => 75.00,
                'reference' => 'IT Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'SEO Optimization Service',
                'sku' => 'SEO001',
                'price' => 400.00,
                'cost' => 180.00,
                'taxes' => 20.00,
                'reference' => 'Marketing Services',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Training Session - 1 Day',
                'sku' => 'TRAINING001',
                'price' => 250.00,
                'cost' => 100.00,
                'taxes' => 12.50,
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
                'price' => 1599.99,
                'cost' => 1100.00,
                'taxes' => 80.00,
                'reference' => 'Hardware + Software',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Office Setup Package',
                'sku' => 'COMBO002',
                'price' => 899.99,
                'cost' => 600.00,
                'taxes' => 45.00,
                'reference' => 'Furniture + Installation',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Website + Hosting Package',
                'sku' => 'COMBO003',
                'price' => 1200.00,
                'cost' => 700.00,
                'taxes' => 60.00,
                'reference' => 'Service + Product',
                'is_track_inventory' => false,
                'is_shrink' => false,
            ],
            [
                'name' => 'Smart Home Starter Kit',
                'sku' => 'COMBO004',
                'price' => 799.99,
                'cost' => 500.00,
                'taxes' => 40.00,
                'reference' => 'IoT Devices + Setup',
                'is_track_inventory' => true,
                'is_shrink' => false,
            ],
            [
                'name' => 'Marketing Campaign Package',
                'sku' => 'COMBO005',
                'price' => 2000.00,
                'cost' => 1200.00,
                'taxes' => 100.00,
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
