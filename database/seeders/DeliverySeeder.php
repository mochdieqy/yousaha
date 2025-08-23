<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Delivery;
use App\Models\DeliveryProductLine;
use App\Models\DeliveryStatusLog;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $warehouses = Warehouse::where('company_id', $company->id)->get();
            $products = Product::where('company_id', $company->id)->get();
            
            if ($warehouses->isEmpty() || $products->isEmpty()) {
                continue;
            }
            
            // Create sample deliveries
            for ($i = 1; $i <= 5; $i++) {
                $warehouse = $warehouses->random();
                $status = $this->getRandomStatus();
                
                $delivery = Delivery::create([
                    'company_id' => $company->id,
                    'warehouse_id' => $warehouse->id,
                    'delivery_address' => $this->getRandomAddress(),
                    'scheduled_at' => now()->addDays(rand(1, 30))->addHours(rand(9, 17)),
                    'reference' => 'DEL-' . strtoupper(substr(md5($i . $company->id), 0, 6)),
                    'status' => $status,
                ]);
                
                // Create product lines
                $numProducts = rand(1, 3);
                $selectedProducts = $products->random($numProducts);
                
                foreach ($selectedProducts as $product) {
                    DeliveryProductLine::create([
                        'delivery_id' => $delivery->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 10),
                    ]);
                }
                
                // Create status logs
                $this->createStatusLogs($delivery, $status);
            }
        }
    }
    
    /**
     * Get a random delivery status with weighted distribution
     */
    private function getRandomStatus(): string
    {
        $statuses = [
            'draft' => 30,      // 30% chance
            'waiting' => 25,    // 25% chance
            'ready' => 20,      // 20% chance
            'done' => 20,       // 20% chance
            'cancel' => 5,      // 5% chance
        ];
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuses as $status => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $status;
            }
        }
        
        return 'draft';
    }
    
    /**
     * Get a random delivery address
     */
    private function getRandomAddress(): string
    {
        $addresses = [
            'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta',
            'Jl. Thamrin No. 45, Jakarta Pusat, DKI Jakarta',
            'Jl. Gatot Subroto No. 67, Jakarta Selatan, DKI Jakarta',
            'Jl. Rasuna Said No. 89, Jakarta Selatan, DKI Jakarta',
            'Jl. Jenderal Ahmad Yani No. 12, Jakarta Timur, DKI Jakarta',
            'Jl. Raya Bekasi No. 34, Jakarta Timur, DKI Jakarta',
            'Jl. Raya Bogor No. 56, Jakarta Selatan, DKI Jakarta',
            'Jl. Raya Depok No. 78, Jakarta Selatan, DKI Jakarta',
            'Jl. Raya Tangerang No. 90, Jakarta Barat, DKI Jakarta',
            'Jl. Raya Serpong No. 11, Tangerang Selatan, Banten',
        ];
        
        return $addresses[array_rand($addresses)];
    }
    
    /**
     * Create status logs for the delivery
     */
    private function createStatusLogs(Delivery $delivery, string $currentStatus): void
    {
        $statuses = ['draft', 'waiting', 'ready', 'done', 'cancel'];
        $currentIndex = array_search($currentStatus, $statuses);
        
        // Create logs for all statuses up to the current one
        for ($i = 0; $i <= $currentIndex; $i++) {
            $status = $statuses[$i];
            $timestamp = $delivery->created_at->addMinutes($i * 30);
            
            DeliveryStatusLog::create([
                'delivery_id' => $delivery->id,
                'status' => $status,
                'changed_at' => $timestamp,
            ]);
        }
    }
}
