<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\StockHistory;
use Carbon\Carbon;

class InitialStockSeeder extends Seeder
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

        $this->command->info('Creating initial stock data for company: ' . $company->name);

        // Get required data
        $products = Product::where('company_id', $company->id)->where('is_track_inventory', true)->get();
        $warehouses = Warehouse::where('company_id', $company->id)->get();

        if ($products->isEmpty() || $warehouses->isEmpty()) {
            $this->command->error('Required data not found. Please run ProductSeeder and WarehouseSeeder first.');
            return;
        }

        // Clear existing stock data
        $this->clearExistingStock($company);

        // Create initial stock for each warehouse and product
        foreach ($warehouses as $warehouse) {
            foreach ($products as $product) {
                $this->createInitialStock($company, $warehouse, $product);
            }
        }
        
        // Set initial account balances
        $this->setInitialAccountBalances($company);

        $this->command->info('Initial stock seeding completed successfully!');
    }

    private function clearExistingStock($company)
    {
        $this->command->info('Clearing existing stock data...');
        
        StockHistory::whereHas('stock', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        
        StockDetail::whereHas('stock', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        
        Stock::where('company_id', $company->id)->delete();
    }

    private function createInitialStock($company, $warehouse, $product)
    {
        // Generate realistic initial stock quantities
        $initialQuantity = $this->generateInitialQuantity($product);
        
        // Create stock record
        $stock = Stock::create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => $initialQuantity,
            'quantity_reserve' => 0,
            'quantity_saleable' => $initialQuantity,
            'quantity_incoming' => 0,
        ]);

        // Create stock detail
        StockDetail::create([
            'stock_id' => $stock->id,
            'quantity' => $initialQuantity,
            'cost' => $product->cost,
            'expiration_date' => $this->generateExpiryDate($product),
            'code' => 'BATCH-' . strtoupper(substr(md5(rand()), 0, 8)),
            'reference' => 'INITIAL-STOCK',
        ]);

        // Create stock history for initial stock
        StockHistory::create([
            'stock_id' => $stock->id,
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total_before' => 0,
            'quantity_total_after' => $initialQuantity,
            'quantity_reserve_before' => 0,
            'quantity_reserve_after' => 0,
            'quantity_saleable_before' => 0,
            'quantity_saleable_after' => $initialQuantity,
            'quantity_incoming_before' => 0,
            'quantity_incoming_after' => 0,
            'type' => 'in',
            'reference' => 'INITIAL-STOCK',
            'date' => Carbon::create(2024, 1, 1),
            'quantity' => $initialQuantity,
            'reference_type' => 'initial',
            'reference_id' => null,
            'notes' => 'Initial stock setup',
        ]);

        $this->command->info("Created initial stock for {$product->name} in {$warehouse->name}: {$initialQuantity} units");
    }

    private function generateInitialQuantity($product)
    {
        // Generate realistic quantities based on product type and cost
        $baseQuantity = 0;
        
        if ($product->type === 'goods') {
            if ($product->cost <= 100000) { // Low cost items
                $baseQuantity = rand(50, 200);
            } elseif ($product->cost <= 1000000) { // Medium cost items
                $baseQuantity = rand(20, 100);
            } else { // High cost items
                $baseQuantity = rand(5, 25);
            }
        } elseif ($product->type === 'combo') {
            $baseQuantity = rand(10, 50);
        }
        
        return $baseQuantity;
    }

    private function generateExpiryDate($product)
    {
        // Generate expiry dates for perishable products
        if ($product->is_shrink) {
            // Perishable products get expiry dates within 1 year
            return Carbon::now()->addDays(rand(30, 365));
        }
        
        // Non-perishable products get expiry dates within 3-5 years
        return Carbon::now()->addYears(rand(3, 5));
    }
    
    /**
     * Set realistic initial account balances
     */
    private function setInitialAccountBalances($company)
    {
        $this->command->info('Setting initial account balances...');
        
        $accounts = \App\Models\Account::where('company_id', $company->id)->get();
        
        foreach ($accounts as $account) {
            $initialBalance = $this->getInitialBalanceForAccount($account);
            
            $this->command->info("Set {$account->name} ({$account->code}) initial balance: IDR " . number_format($initialBalance, 0, ',', '.'));
        }
        
        $this->command->info('Initial account balances set successfully!');
    }
    
    /**
     * Get realistic initial balance for an account
     */
    private function getInitialBalanceForAccount($account)
    {
        switch ($account->code) {
            case '1000': // Cash
                return rand(50000000, 200000000); // 50M - 200M IDR
                
            case '1100': // Accounts Receivable
                return rand(10000000, 50000000); // 10M - 50M IDR
                
            case '1200': // Inventory
                return rand(100000000, 300000000); // 100M - 300M IDR
                
            case '1300': // Prepaid Expenses
                return rand(5000000, 20000000); // 5M - 20M IDR
                
            case '1400': // Fixed Assets
                return rand(200000000, 500000000); // 200M - 500M IDR
                
            case '1500': // Accumulated Depreciation
                return -rand(50000000, 150000000); // Negative: 50M - 150M IDR
                
            case '2000': // Accounts Payable
                return -rand(20000000, 80000000); // Negative: 20M - 80M IDR
                
            case '2100': // Accrued Expenses
                return -rand(10000000, 30000000); // Negative: 10M - 30M IDR
                
            case '2200': // Short-term Loans
                return -rand(50000000, 150000000); // Negative: 50M - 150M IDR
                
            case '2300': // Long-term Loans
                return -rand(100000000, 300000000); // Negative: 100M - 300M IDR
                
            case '3000': // Owner's Equity
                return rand(300000000, 800000000); // 300M - 300M IDR
                
            case '3100': // Retained Earnings
                return rand(100000000, 300000000); // 100M - 300M IDR
                
            case '3200': // Current Year Earnings
                return rand(50000000, 150000000); // 50M - 150M IDR
                
            case '4000': // Sales Revenue
                return rand(500000000, 1500000000); // 500M - 1.5B IDR
                
            case '4100': // Other Income
                return rand(20000000, 80000000); // 20M - 80M IDR
                
            case '5000': // Cost of Goods Sold
                return -rand(300000000, 800000000); // Negative: 300M - 800M IDR
                
            case '5100': // Operating Expenses
                return -rand(100000000, 300000000); // Negative: 100M - 300M IDR
                
            case '5200': // Payroll Expenses
                return -rand(80000000, 200000000); // Negative: 80M - 200M IDR
                
            case '5300': // Marketing Expenses
                return -rand(30000000, 100000000); // Negative: 30M - 100M IDR
                
            case '5400': // Administrative Expenses
                return -rand(50000000, 150000000); // Negative: 50M - 150M IDR
                
            case '5500': // Depreciation Expense
                return -rand(20000000, 60000000); // Negative: 20M - 60M IDR
                
            default:
                return 0;
        }
    }
}
