<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProductLine;
use App\Models\PurchaseOrderStatusLog;
use App\Models\Receipt;
use App\Models\ReceiptProductLine;
use App\Models\ReceiptStatusLog;
use App\Models\SalesOrder;
use App\Models\SalesOrderProductLine;
use App\Models\SalesOrderStatusLog;
use App\Models\Delivery;
use App\Models\DeliveryProductLine;
use App\Models\DeliveryStatusLog;
use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\StockHistory;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerDetail;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\Income;
use App\Models\IncomeDetail;
use App\Models\InternalTransfer;
use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionDataSeeder extends Seeder
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

        $this->command->info('Creating transaction data for company: ' . $company->name);

        // Get required data
        $products = Product::where('company_id', $company->id)->get();
        $warehouses = Warehouse::where('company_id', $company->id)->get();
        $suppliers = Supplier::where('company_id', $company->id)->get();
        $customers = Customer::where('company_id', $company->id)->get();
        $accounts = Account::where('company_id', $company->id)->get();

        if ($products->isEmpty() || $warehouses->isEmpty() || $suppliers->isEmpty() || $customers->isEmpty()) {
            $this->command->error('Required data not found. Please run ProductSeeder, WarehouseSeeder, SupplierSeeder, and CustomerSeeder first.');
            return;
        }

        // Clear existing transaction data
        $this->clearExistingData($company);

        // Seed data for each month of 2024
        for ($month = 1; $month <= 12; $month++) {
            $this->seedMonthData($company, $month, 2024, $products, $warehouses, $suppliers, $customers, $accounts);
        }

        $this->command->info('Transaction data seeding completed successfully!');
        
        // Log summary statistics
        $this->logSummaryStatistics($company);
        
        // Show final account balances
        $this->showFinalAccountBalances($company, $accounts);
    }

    /**
     * Safely add time to a date without exceeding 2024
     */
    private function safeAddTime($date, $unit, $amount)
    {
        $maxDate = Carbon::create(2024, 12, 31, 23, 59, 59);
        $newDate = $date->copy()->add($unit, $amount);
        
        // If the new date exceeds 2024, cap it at December 31, 2024
        if ($newDate->year > 2024) {
            return $maxDate;
        }
        
        return $newDate;
    }

    /**
     * Log summary statistics for the seeded data
     */
    private function logSummaryStatistics($company)
    {
        $this->command->info('=== SEEDING SUMMARY STATISTICS ===');
        
        // Count transactions
        $purchaseOrders = PurchaseOrder::where('company_id', $company->id)->count();
        $salesOrders = SalesOrder::where('company_id', $company->id)->count();
        $receipts = Receipt::where('company_id', $company->id)->count();
        $deliveries = Delivery::where('company_id', $company->id)->count();
        $internalTransfers = InternalTransfer::where('company_id', $company->id)->count();
        $assets = Asset::where('company_id', $company->id)->count();
        $expenses = Expense::where('company_id', $company->id)->count();
        $incomes = Income::where('company_id', $company->id)->count();
        
        $this->command->info("Purchase Orders: {$purchaseOrders}");
        $this->command->info("Sales Orders: {$salesOrders}");
        $this->command->info("Receipts: {$receipts}");
        $this->command->info("Deliveries: {$deliveries}");
        $this->command->info("Internal Transfers: {$internalTransfers} (Financial transfers between accounts)");
        $this->command->info("Assets: {$assets}");
        $this->command->info("Expenses: {$expenses}");
        $this->command->info("Incomes: {$incomes}");
        
        // Calculate total asset value (assets don't store value in table, so skip this)
        $assetCount = Asset::where('company_id', $company->id)->count();
        $this->command->info("Total Assets: {$assetCount}");
        $this->command->info("Note: Asset values are tracked in General Ledger entries");
        
        // Calculate total transaction values
        $totalPurchaseValue = PurchaseOrder::where('company_id', $company->id)->sum('total');
        $totalSalesValue = SalesOrder::where('company_id', $company->id)->sum('total');
        $totalExpenseValue = Expense::where('company_id', $company->id)->sum('total');
        $totalIncomeValue = Income::where('company_id', $company->id)->sum('total');
        $totalTransferValue = InternalTransfer::where('company_id', $company->id)->sum('value');
        
        $this->command->info("Total Purchase Value: IDR " . number_format($totalPurchaseValue, 0, ',', '.'));
        $this->command->info("Total Sales Value: IDR " . number_format($totalSalesValue, 0, ',', '.'));
        $this->command->info("Total Expense Value: IDR " . number_format($totalExpenseValue, 0, ',', '.'));
        $this->command->info("Total Income Value: IDR " . number_format($totalIncomeValue, 0, ',', '.'));
        $this->command->info("Total Transfer Value: IDR " . number_format($totalTransferValue, 0, ',', '.'));
        
        // Count automatic cash balancing transactions
        $autoTransfers = InternalTransfer::where('company_id', $company->id)
            ->where('note', 'like', '%Automatic cash balancing%')
            ->count();
        $emergencyIncomes = Income::where('company_id', $company->id)
            ->where('note', 'like', '%Emergency income%')
            ->count();
        
        if ($autoTransfers > 0 || $emergencyIncomes > 0) {
            $this->command->info("=== CASH BALANCING SUMMARY ===");
            $this->command->info("Automatic Cash Transfers: {$autoTransfers} (from Accounts Receivable to Cash)");
            $this->command->info("Emergency Income Transactions: {$emergencyIncomes} (when both cash and receivables were low)");
            
            // Calculate total value of automatic transfers
            $totalAutoTransferValue = InternalTransfer::where('company_id', $company->id)
                ->where('note', 'like', '%Automatic cash balancing%')
                ->sum('value');
            $this->command->info("Total Auto-Transfer Value: IDR " . number_format($totalAutoTransferValue, 0, ',', '.'));
            
            // Calculate total value of emergency incomes
            $totalEmergencyIncomeValue = Income::where('company_id', $company->id)
                ->where('note', 'like', '%Emergency income%')
                ->sum('total');
            $this->command->info("Total Emergency Income Value: IDR " . number_format($totalEmergencyIncomeValue, 0, ',', '.'));
        }
        
        $this->command->info('=== END SUMMARY ===');
    }

    private function clearExistingData($company)
    {
        $this->command->info('Clearing existing transaction data...');
        
        // Clear in reverse order of dependencies
        GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        
        GeneralLedger::where('company_id', $company->id)->delete();
        
        IncomeDetail::whereHas('income', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        Income::where('company_id', $company->id)->delete();
        
        ExpenseDetail::whereHas('expense', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        Expense::where('company_id', $company->id)->delete();
        
        StockHistory::whereHas('stock', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        StockDetail::whereHas('stock', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        Stock::where('company_id', $company->id)->delete();
        
        DeliveryStatusLog::whereHas('delivery', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        DeliveryProductLine::whereHas('delivery', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        Delivery::where('company_id', $company->id)->delete();
        
        ReceiptStatusLog::whereHas('receipt', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        ReceiptProductLine::whereHas('receipt', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        Receipt::where('company_id', $company->id)->delete();
        
        SalesOrderStatusLog::whereHas('salesOrder', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        SalesOrderProductLine::whereHas('salesOrder', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        SalesOrder::where('company_id', $company->id)->delete();
        
        PurchaseOrderStatusLog::whereHas('purchaseOrder', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        PurchaseOrderProductLine::whereHas('purchaseOrder', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->delete();
        PurchaseOrder::where('company_id', $company->id)->delete();
        
        // Clear internal transfers
        InternalTransfer::where('company_id', $company->id)->delete();
        
        // Clear assets
        Asset::where('company_id', $company->id)->delete();
    }

    private function seedMonthData($company, $month, $year, $products, $warehouses, $suppliers, $customers, $accounts)
    {
        $this->command->info("Seeding data for {$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT));
        
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Create 10-15 records per month
        $recordsCount = rand(10, 15);
        
        // Track available stock for each product in each warehouse
        $availableStock = [];
        foreach ($warehouses as $warehouse) {
            $availableStock[$warehouse->id] = [];
            foreach ($products as $product) {
                $availableStock[$warehouse->id][$product->id] = 0;
            }
        }
        
        for ($i = 0; $i < $recordsCount; $i++) {
            $date = $this->safeAddTime($startDate, 'days', rand(0, $endDate->day - 1));
            
            // Validate that the date is within 2024
            if ($date->year !== 2024) {
                $this->command->warn("Generated date {$date->format('Y-m-d H:i:s')} is outside 2024. Adjusting to December 31, 2024.");
                $date = Carbon::create(2024, 12, 31, 23, 59, 59);
            }
            
            // Create purchase order
            $purchaseOrder = $this->createPurchaseOrder($company, $date, $warehouses, $suppliers, $products);
            
            // Create receipt for purchase order
            $receipt = $this->createReceipt($company, $date, $warehouses, $suppliers, $purchaseOrder);
            
            // Update available stock after receipt
            $this->updateAvailableStock($availableStock, $receipt, $purchaseOrder);
            
            // Create sales order only for products with available stock
            $salesOrder = $this->createSalesOrder($company, $date, $warehouses, $customers, $products, $availableStock);
            
            // Create delivery for sales order only if sales order was created
            $delivery = null;
            if ($salesOrder) {
                $delivery = $this->createDelivery($company, $date, $warehouses, $salesOrder);
                
                // Update available stock after delivery
                $this->updateAvailableStock($availableStock, null, null, $delivery, $salesOrder);
            }
            
            // Create additional transactions (expenses, incomes)
            $this->createAdditionalTransactions($company, $date, $accounts, $suppliers, $customers);
            
            // Create internal transfers (20% chance)
            if (rand(1, 100) <= 20) {
                $internalTransfer = $this->createInternalTransfer($company, $date, $accounts);
                
                // Create inventory movements between warehouses (separate from financial transfers)
                $inventoryMovementValue = $this->createInventoryMovement($company, $date, $warehouses, $products, $availableStock);
                
                if ($inventoryMovementValue > 0) {
                    $this->command->info("Created inventory movement with value: IDR " . number_format($inventoryMovementValue, 0, ',', '.'));
                }
            }
            
            // Create assets (15% chance)
            if (rand(1, 100) <= 15) {
                $asset = $this->createAsset($company, $date, $accounts, $suppliers);
                
                if ($asset) {
                    $this->command->info("Created asset: {$asset->name} with value IDR " . number_format($asset->amount, 0, ',', '.'));
                }
            }
            
            // Update stock
            $this->updateStock($company, $date, $warehouses, $products, $purchaseOrder, $receipt, $salesOrder, $delivery, $availableStock);
            
            // Create general ledger entries
            $this->createGeneralLedgerEntries($company, $date, $accounts, $purchaseOrder, $receipt, $salesOrder, $delivery);
            
            // Check and balance cash account after each transaction to prevent negative balance
            $this->checkAndBalanceCash($company, $accounts, $date);
        }
        
        // Update account balances after all transactions are created
        $this->updateAccountBalances($company, $accounts, $endDate);
        
        // Log final stock levels for this month
        $this->logFinalStockLevels($availableStock, $warehouses, $products, $month, $year);
    }

    /**
     * Log final stock levels for the month
     */
    private function logFinalStockLevels($availableStock, $warehouses, $products, $month, $year)
    {
        $this->command->info("Final stock levels for {$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . ":");
        
        foreach ($warehouses as $warehouse) {
            $this->command->info("  Warehouse: {$warehouse->name}");
            foreach ($products->where('is_track_inventory', true) as $product) {
                $stock = $availableStock[$warehouse->id][$product->id] ?? 0;
                if ($stock > 0) {
                    $this->command->info("    {$product->name}: {$stock} units");
                }
            }
        }
    }

    /**
     * Update available stock tracking array
     */
    private function updateAvailableStock(&$availableStock, $receipt = null, $purchaseOrder = null, $delivery = null, $salesOrder = null)
    {
        // Add stock from receipt
        if ($receipt && $purchaseOrder) {
            foreach ($purchaseOrder->productLines as $poLine) {
                $availableStock[$receipt->warehouse_id][$poLine->product_id] += $poLine->quantity;
                $this->command->info("Added {$poLine->quantity} units of product {$poLine->product_id} to warehouse {$receipt->warehouse_id}. New stock: {$availableStock[$receipt->warehouse_id][$poLine->product_id]}");
            }
        }
        
        // Subtract stock from delivery
        if ($delivery && $salesOrder) {
            foreach ($salesOrder->productLines as $soLine) {
                $availableStock[$delivery->warehouse_id][$soLine->product_id] -= $soLine->quantity;
                // Ensure stock doesn't go negative
                if ($availableStock[$delivery->warehouse_id][$soLine->product_id] < 0) {
                    $availableStock[$delivery->warehouse_id][$soLine->product_id] = 0;
                }
                $this->command->info("Subtracted {$soLine->quantity} units of product {$soLine->product_id} from warehouse {$delivery->warehouse_id}. New stock: {$availableStock[$delivery->warehouse_id][$soLine->product_id]}");
            }
        }
    }

    /**
     * Check if a product has sufficient stock in a warehouse
     */
    private function hasSufficientStock($availableStock, $warehouseId, $productId, $requiredQuantity)
    {
        return isset($availableStock[$warehouseId][$productId]) && 
               $availableStock[$warehouseId][$productId] >= $requiredQuantity;
    }

    private function createPurchaseOrder($company, $date, $warehouses, $suppliers, $products)
    {
        $warehouse = $warehouses->random();
        $supplier = $suppliers->random();
        
        // Add seasonal variation to purchase quantities
        $month = $date->month;
        $seasonalMultiplier = 1.0;
        
        // Higher purchases in certain months (e.g., before holidays, year-end)
        if (in_array($month, [11, 12])) { // November, December (holiday season)
            $seasonalMultiplier = 1.3;
        } elseif (in_array($month, [1, 2])) { // January, February (new year)
            $seasonalMultiplier = 1.2;
        } elseif (in_array($month, [6, 7])) { // June, July (mid-year restocking)
            $seasonalMultiplier = 1.1;
        }
        
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'number' => 'PO-' . $date->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'supplier_id' => $supplier->id,
            'requestor' => fake()->name(),
            'activities' => fake()->sentence(),
            'total' => 0,
            'status' => 'completed',
            'deadline' => $this->safeAddTime($date, 'days', rand(1, 30)),
        ]);

        // Create product lines
        $total = 0;
        $productCount = rand(1, 5);
        $selectedProducts = $products->where('type', '!=', 'service')->random($productCount);
        
        foreach ($selectedProducts as $product) {
            $baseQuantity = rand(1, 10);
            $quantity = max(1, round($baseQuantity * $seasonalMultiplier));
            $cost = $product->cost;
            $lineTotal = $quantity * $cost;
            $total += $lineTotal;
            
            PurchaseOrderProductLine::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }
        
        $purchaseOrder->update(['total' => $total]);
        
        // Create status log
        PurchaseOrderStatusLog::create([
            'purchase_order_id' => $purchaseOrder->id,
            'status' => 'completed',
            'changed_at' => $date,
        ]);
        
        return $purchaseOrder;
    }

    private function createReceipt($company, $date, $warehouses, $suppliers, $purchaseOrder)
    {
        // Use the same warehouse as the purchase order for consistency
        $warehouse = Warehouse::find($purchaseOrder->warehouse_id);
        $supplier = $suppliers->random();
        
        $receipt = Receipt::create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'receive_from' => $supplier->id,
            'scheduled_at' => $this->safeAddTime($date, 'hours', rand(9, 17)),
            'reference' => $purchaseOrder->number,
            'status' => 'completed',
        ]);
        
        // Create product lines based on purchase order
        foreach ($purchaseOrder->productLines as $poLine) {
            ReceiptProductLine::create([
                'receipt_id' => $receipt->id,
                'product_id' => $poLine->product_id,
                'quantity' => $poLine->quantity,
            ]);
        }
        
        // Create status log
        ReceiptStatusLog::create([
            'receipt_id' => $receipt->id,
            'status' => 'completed',
            'changed_at' => $date,
        ]);
        
        return $receipt;
    }

    private function createSalesOrder($company, $date, $warehouses, $customers, $products, $availableStock)
    {
        $warehouse = $warehouses->random();
        $customer = $customers->random();
        
        // Find products with available stock in this warehouse
        $availableProducts = [];
        foreach ($products as $product) {
            if ($product->is_track_inventory && $availableStock[$warehouse->id][$product->id] > 0) {
                $availableProducts[] = $product;
            } elseif (!$product->is_track_inventory) {
                // Services don't need stock tracking
                $availableProducts[] = $product;
            }
        }
        
        // If no products with stock, try another warehouse
        if (empty($availableProducts)) {
            foreach ($warehouses as $w) {
                if ($w->id !== $warehouse->id) {
                    foreach ($products as $product) {
                        if ($product->is_track_inventory && $availableStock[$w->id][$product->id] > 0) {
                            $availableProducts[] = $product;
                            $warehouse = $w;
                            break 2;
                        } elseif (!$product->is_track_inventory) {
                            $availableProducts[] = $product;
                            $warehouse = $w;
                            break 2;
                        }
                    }
                }
            }
        }
        
        // If still no products, create a minimal sales order with services only
        if (empty($availableProducts)) {
            $availableProducts = $products->where('is_track_inventory', false)->take(2);
            if ($availableProducts->isEmpty()) {
                return null; // No products available for sale
            }
        }
        
        $salesOrder = SalesOrder::create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'number' => 'SO-' . $date->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'customer_id' => $customer->id,
            'salesperson' => fake()->name(),
            'activities' => fake()->sentence(),
            'total' => 0,
            'status' => 'completed',
            'deadline' => $this->safeAddTime($date, 'days', rand(1, 30)),
        ]);

        // Create product lines with available stock constraints
        $total = 0;
        $productCount = min(rand(1, 5), count($availableProducts));
        $selectedProducts = collect($availableProducts)->random($productCount);
        
        // Add seasonal variation to sales quantities
        $month = $date->month;
        $seasonalMultiplier = 1.0;
        
        // Higher sales in certain months (e.g., holiday season, year-end)
        if (in_array($month, [11, 12])) { // November, December (holiday season)
            $seasonalMultiplier = 1.4;
        } elseif (in_array($month, [3, 4])) { // March, April (spring season)
            $seasonalMultiplier = 1.2;
        } elseif (in_array($month, [9, 10])) { // September, October (fall season)
            $seasonalMultiplier = 1.1;
        }
        
        foreach ($selectedProducts as $product) {
            if ($product->is_track_inventory) {
                // For inventory products, don't exceed available stock
                $maxQuantity = $availableStock[$warehouse->id][$product->id];
                $baseQuantity = min(rand(1, 10), $maxQuantity);
                $quantity = max(1, round($baseQuantity * $seasonalMultiplier));
                // Ensure we don't exceed available stock
                $quantity = min($quantity, $maxQuantity);
            } else {
                // For services, use random quantity with seasonal variation
                $baseQuantity = rand(1, 10);
                $quantity = max(1, round($baseQuantity * $seasonalMultiplier));
            }
            
            $price = $product->price;
            $lineTotal = $quantity * $price;
            $total += $lineTotal;
            
            SalesOrderProductLine::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }
        
        $salesOrder->update(['total' => $total]);
        
        // Create status log
        SalesOrderStatusLog::create([
            'sales_order_id' => $salesOrder->id,
            'status' => 'completed',
            'changed_at' => $date,
        ]);
        
        return $salesOrder;
    }

    private function createDelivery($company, $date, $warehouses, $salesOrder)
    {
        // Use the same warehouse as the sales order for consistency
        $warehouse = Warehouse::find($salesOrder->warehouse_id);
        
        $delivery = Delivery::create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'delivery_address' => fake()->address(),
            'scheduled_at' => $this->safeAddTime($date, 'hours', rand(9, 17)),
            'reference' => $salesOrder->number,
            'status' => 'completed',
        ]);
        
        // Create product lines based on sales order
        foreach ($salesOrder->productLines as $soLine) {
            DeliveryProductLine::create([
                'delivery_id' => $delivery->id,
                'product_id' => $soLine->product_id,
                'quantity' => $soLine->quantity,
            ]);
        }
        
        // Create status log
        DeliveryStatusLog::create([
            'delivery_id' => $delivery->id,
            'status' => 'completed',
            'changed_at' => $date,
        ]);
        
        return $delivery;
    }

    private function createAdditionalTransactions($company, $date, $accounts, $suppliers, $customers)
    {
        // Randomly create expenses (30% chance)
        if (rand(1, 100) <= 30) {
            $this->createExpense($company, $date, $accounts, $suppliers);
        }
        
        // Randomly create incomes (20% chance)
        if (rand(1, 100) <= 20) {
            $this->createIncome($company, $date, $accounts, $customers);
        }
        
        // Randomly create equity transactions (15% chance)
        if (rand(1, 100) <= 15) {
            $this->createEquityTransaction($company, $date, $accounts);
        }
    }

    private function createExpense($company, $date, $accounts, $suppliers)
    {
        $expenseTypes = [
            'Office Supplies' => [50000, 500000],
            'Utilities' => [200000, 1000000],
            'Marketing' => [100000, 2000000],
            'Travel' => [500000, 3000000],
            'Maintenance' => [100000, 800000],
            'Insurance' => [500000, 2000000],
            'Legal Fees' => [1000000, 5000000],
            'Consulting' => [500000, 3000000],
            'Rent' => [2000000, 10000000],
            'Internet & Phone' => [100000, 800000],
            'Printing & Stationery' => [50000, 300000],
            'Training & Development' => [200000, 1500000],
            'Office Maintenance' => [100000, 1000000],
            'Security Services' => [300000, 2000000],
            'Cleaning Services' => [150000, 1000000],
        ];
        
        $type = array_rand($expenseTypes);
        $amountRange = $expenseTypes[$type];
        $amount = rand($amountRange[0], $amountRange[1]);
        
        // Get appropriate accounts for the expense
        $paymentAccount = $accounts->where('code', '1000')->first(); // Cash account
        $expenseAccount = $accounts->where('code', '5100')->first(); // Operating Expenses
        
        // If specific expense account not found, try to find a suitable one
        if (!$expenseAccount) {
            $expenseAccount = $accounts->where('type', 'expense')->first();
        }
        
        $expense = Expense::create([
            'company_id' => $company->id,
            'number' => 'EXP-' . $date->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'date' => $date,
            'due_date' => $this->safeAddTime($date, 'days', rand(1, 30)),
            'total' => $amount,
            'paid' => true,
            'status' => 'paid',
            'note' => $type,
            'supplier_id' => $suppliers->random()->id,
            'payment_account_id' => $paymentAccount ? $paymentAccount->id : null,
            'description' => "Payment for {$type}",
        ]);
        
        // Create expense detail with account relationship
        if ($expenseAccount) {
            ExpenseDetail::create([
                'expense_id' => $expense->id,
                'account_id' => $expenseAccount->id,
                'value' => $amount,
                'status' => 'approved',
                'description' => $type,
            ]);
        }
        
        // Create GL entry
        $this->createGLEntry($company, $date, 'expense', $amount, $expense->number, $accounts, [
            ['account' => '5100', 'type' => 'debit', 'value' => $amount, 'description' => "Expense: {$type}"],
            ['account' => '1000', 'type' => 'credit', 'value' => $amount, 'description' => "Cash payment for {$type}"],
        ], "Expense transaction for {$type}");
    }

    private function createIncome($company, $date, $accounts, $customers)
    {
        $incomeTypes = [
            'Interest Income' => [100000, 1000000],
            'Rental Income' => [500000, 3000000],
            'Commission Income' => [200000, 2000000],
            'Service Fee' => [100000, 1500000],
            'Royalty Income' => [300000, 2500000],
            'Dividend Income' => [500000, 5000000],
            'Consulting Fee' => [1000000, 8000000],
            'Training Fee' => [500000, 3000000],
            'Software License' => [200000, 2000000],
            'Maintenance Contract' => [300000, 2500000],
        ];
        
        $type = array_rand($incomeTypes);
        $amountRange = $incomeTypes[$type];
        $amount = rand($amountRange[0], $amountRange[1]);
        
        // Get appropriate accounts for the income
        $receiptAccount = $accounts->where('code', '1000')->first(); // Cash account
        $incomeAccount = $accounts->where('code', '4100')->first(); // Other Income
        
        // If specific income account not found, try to find a suitable one
        if (!$incomeAccount) {
            $incomeAccount = $accounts->where('type', 'income')->first();
        }
        
        $income = Income::create([
            'company_id' => $company->id,
            'number' => 'INC-' . $date->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'date' => $date,
            'due_date' => $this->safeAddTime($date, 'days', rand(1, 30)),
            'total' => $amount,
            'paid' => true,
            'status' => 'received',
            'note' => $type,
            'customer_id' => $customers->random()->id,
            'receipt_account_id' => $receiptAccount ? $receiptAccount->id : null,
            'description' => "Receipt from {$type}",
        ]);
        
        // Create income detail with account relationship
        if ($incomeAccount) {
            IncomeDetail::create([
                'income_id' => $income->id,
                'account_id' => $incomeAccount->id,
                'value' => $amount,
                'description' => $type,
            ]);
        }
        
        // Create GL entry
        $this->createGLEntry($company, $date, 'income', $amount, $income->number, $accounts, [
            ['account' => '1000', 'type' => 'debit', 'value' => $amount, 'description' => "Cash received from {$type}"],
            ['account' => '4100', 'type' => 'credit', 'value' => $amount, 'description' => "Income: {$type}"],
        ], "Income transaction for {$type}");
    }

    private function updateStock($company, $date, $warehouses, $products, $purchaseOrder, $receipt, $salesOrder, $delivery, &$availableStock)
    {
        foreach ($warehouses as $warehouse) {
            foreach ($products->where('is_track_inventory', true) as $product) {
                // Get or create stock record
                $stock = Stock::firstOrCreate([
                    'company_id' => $company->id,
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $product->id,
                ], [
                    'quantity_total' => 0,
                    'quantity_reserve' => 0,
                    'quantity_saleable' => 0,
                    'quantity_incoming' => 0,
                ]);
                
                // Calculate stock changes based on transactions
                $incomingQty = 0;
                $outgoingQty = 0;
                
                // Add from receipts
                if ($receipt && $receipt->warehouse_id == $warehouse->id) {
                    $receiptLine = $receipt->productLines()->where('product_id', $product->id)->first();
                    if ($receiptLine) {
                        $incomingQty += $receiptLine->quantity;
                    }
                }
                
                // Subtract from deliveries
                if ($delivery && $delivery->warehouse_id == $warehouse->id) {
                    $deliveryLine = $delivery->productLines()->where('product_id', $product->id)->first();
                    if ($deliveryLine) {
                        $outgoingQty += $deliveryLine->quantity;
                    }
                }

                // Add from internal transfers (if applicable)
                if ($purchaseOrder && $purchaseOrder->warehouse_id == $warehouse->id) {
                    $purchaseOrderLine = $purchaseOrder->productLines()->where('product_id', $product->id)->first();
                    if ($purchaseOrderLine) {
                        $incomingQty += $purchaseOrderLine->quantity;
                    }
                }

                // Subtract from internal transfers (if applicable)
                if ($salesOrder && $salesOrder->warehouse_id == $warehouse->id) {
                    $salesOrderLine = $salesOrder->productLines()->where('product_id', $product->id)->first();
                    if ($salesOrderLine) {
                        $outgoingQty += $salesOrderLine->quantity;
                    }
                }
                
                // Update stock quantities
                $stock->quantity_total += $incomingQty - $outgoingQty;
                $stock->quantity_saleable += $incomingQty - $outgoingQty;
                $stock->save();
                
                // Create stock history
                if ($incomingQty > 0 || $outgoingQty > 0) {
                    $reference = 'ADJUSTMENT';
                    $referenceType = 'adjustment';
                    $referenceId = null;
                    
                    if ($incomingQty > 0 && $receipt && $receipt->number) {
                        $reference = $receipt->number;
                        $referenceType = 'receipt';
                        $referenceId = $receipt->id;
                    } elseif ($outgoingQty > 0 && $delivery && $delivery->number) {
                        $reference = $delivery->number;
                        $referenceType = 'delivery';
                        $referenceId = $delivery->id;
                    } elseif ($incomingQty > 0 && $purchaseOrder && $purchaseOrder->number) {
                        $reference = $purchaseOrder->number;
                        $referenceType = 'purchase_order';
                        $referenceId = $purchaseOrder->id;
                    } elseif ($outgoingQty > 0 && $salesOrder && $salesOrder->number) {
                        $reference = $salesOrder->number;
                        $referenceType = 'sales_order';
                        $referenceId = $salesOrder->id;
                    }
                    
                    // Ensure reference is never empty
                    if (empty($reference)) {
                        $reference = 'ADJUSTMENT-' . $date->format('Ymd');
                    }
                    
                    StockHistory::create([
                        'stock_id' => $stock->id,
                        'company_id' => $company->id,
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $product->id,
                        'quantity_total_before' => $stock->quantity_total - ($incomingQty - $outgoingQty),
                        'quantity_total_after' => $stock->quantity_total,
                        'quantity_reserve_before' => $stock->quantity_reserve,
                        'quantity_reserve_after' => $stock->quantity_reserve,
                        'quantity_saleable_before' => $stock->quantity_saleable - ($incomingQty - $outgoingQty),
                        'quantity_saleable_after' => $stock->quantity_saleable,
                        'quantity_incoming_before' => $stock->quantity_incoming,
                        'quantity_incoming_after' => $stock->quantity_incoming,
                        'type' => $incomingQty > 0 ? 'in' : 'out',
                        'reference' => $reference,
                        'date' => $date,
                        'quantity' => $incomingQty > 0 ? $incomingQty : $outgoingQty,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                        'notes' => $incomingQty > 0 ? 'Goods received' : 'Goods delivered',
                    ]);
                }
            }
        }
    }

    private function createGeneralLedgerEntries($company, $date, $accounts, $purchaseOrder, $receipt, $salesOrder, $delivery)
    {
        // Create GL entry for purchase order (expense)
        if ($purchaseOrder && $purchaseOrder->total > 0) {
            $this->createGLEntry($company, $date, 'expense', $purchaseOrder->total, $purchaseOrder->number, $accounts, [
                ['account' => '5000', 'type' => 'debit', 'value' => $purchaseOrder->total, 'description' => 'Cost of goods purchased'],
                ['account' => '2000', 'type' => 'credit', 'value' => $purchaseOrder->total, 'description' => 'Amount owed to supplier'],
            ], "Purchase order transaction");
        }
        
        // Create GL entry for sales order (revenue)
        if ($salesOrder && $salesOrder->total > 0) {
            $this->createGLEntry($company, $date, 'income', $salesOrder->total, $salesOrder->number, $accounts, [
                ['account' => '1100', 'type' => 'debit', 'value' => $salesOrder->total, 'description' => 'Amount receivable from customer'],
                ['account' => '4000', 'type' => 'credit', 'value' => $salesOrder->total, 'description' => 'Sales revenue earned'],
            ], "Sales order transaction");
        }
    }

    private function createGLEntry($company, $date, $type, $total, $reference, $accounts, $details, $description = null)
    {
        $gl = GeneralLedger::create([
            'company_id' => $company->id,
            'number' => 'GL-' . $date->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'type' => $type,
            'date' => $date,
            'note' => "Transaction: {$reference}",
            'total' => $total,
            'reference' => $reference,
            'description' => $description ?: "Automated entry for {$reference}",
            'status' => 'posted',
        ]);
        
        foreach ($details as $detail) {
            $account = $accounts->where('code', $detail['account'])->first();
            if ($account) {
                GeneralLedgerDetail::create([
                    'general_ledger_id' => $gl->id,
                    'account_id' => $account->id,
                    'type' => $detail['type'],
                    'value' => $detail['value'],
                    'description' => $detail['description'] ?? "{$detail['type']} entry for {$gl->note}",
                ]);
            }
        }
        
        return $gl;
    }
    
    /**
     * Update account balances based on all transactions
     */
    private function updateAccountBalances($company, $accounts, $endDate)
    {
        $this->command->info('Updating account balances...');
        
        foreach ($accounts as $account) {
            // Get the initial balance from the account
            $initialBalance = $account->balance;
            $balance = $initialBalance;
            
            // Calculate balance from general ledger details for this month only
            $glDetails = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('account_id', $account->id)->get();
            
            foreach ($glDetails as $detail) {
                if ($detail->type === 'debit') {
                    $balance += $detail->value;
                } else {
                    $balance -= $detail->value;
                }
            }
            
            // Update account balance (accumulate from previous months)
            $account->update(['balance' => $balance]);
            
            $this->command->info("Updated {$account->name} ({$account->code}) balance: IDR " . number_format($balance, 0, ',', '.'));
        }
        
        // Check if cash account is negative and transfer from accounts receivable if needed
        $this->balanceCashAccount($company, $accounts, $endDate);
        
        $this->command->info('Account balances updated successfully!');
    }

    /**
     * Balance cash account by transferring from accounts receivable if negative
     */
    private function balanceCashAccount($company, $accounts, $endDate = null)
    {
        $cashAccount = $accounts->where('code', '1000')->first(); // Cash account
        $receivableAccount = $accounts->where('code', '1100')->first(); // Accounts Receivable
        
        if (!$cashAccount || !$receivableAccount) {
            $this->command->warn('Cash or Accounts Receivable account not found. Skipping cash balancing.');
            return;
        }
        
        // Use provided date or current date, and add offset for transfer timing
        $transferDate = $endDate ? $this->safeAddTime($endDate, 'hours', rand(1, 4)) : Carbon::create(2024, 12, 31)->subHours(rand(1, 4));
        
        // Refresh account balances from database
        $cashAccount->refresh();
        $receivableAccount->refresh();
        
        $cashBalance = $cashAccount->balance;
        $receivableBalance = $receivableAccount->balance;
        
        $this->command->info("Current cash balance: IDR " . number_format($cashBalance, 0, ',', '.'));
        $this->command->info("Current accounts receivable balance: IDR " . number_format($receivableBalance, 0, ',', '.'));
        
        // If cash is negative and we have receivables to collect
        if ($cashBalance < 0 && $receivableBalance > 0) {
            $transferAmount = abs($cashBalance) + rand(1000000, 5000000); // Transfer negative amount plus buffer
            
            // Don't transfer more than available receivables
            $transferAmount = min($transferAmount, $receivableBalance);
            
            // Ensure we have enough receivables to cover the transfer
            if ($transferAmount > 0) {
                $this->command->info("Cash account is negative. Transferring IDR " . number_format($transferAmount, 0, ',', '.') . " from Accounts Receivable to Cash.");
                
                // Create automatic internal transfer
                $internalTransfer = InternalTransfer::create([
                    'company_id' => $company->id,
                    'number' => 'AUTO-CASH-' . $transferDate->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'date' => $transferDate,
                    'account_in' => $cashAccount->id, // Cash receives the transfer
                    'account_out' => $receivableAccount->id, // Receivables provides the transfer
                    'value' => $transferAmount,
                    'fee' => 0,
                    'fee_charged_to' => 'out',
                    'note' => 'Automatic cash balancing transfer from accounts receivable',
                ]);
                
                // Create GL entry for the automatic transfer
                $this->createGLEntry($company, $transferDate, 'transfer', $transferAmount, $internalTransfer->number, $accounts, [
                    ['account' => '1000', 'type' => 'debit', 'value' => $transferAmount, 'description' => 'Cash received from accounts receivable collection'],
                    ['account' => '1100', 'type' => 'credit', 'value' => $transferAmount, 'description' => 'Accounts receivable collected and transferred to cash'],
                ], "Automatic cash balancing transfer");
                
                // Update account balances after the transfer
                $cashAccount->update(['balance' => $cashBalance + $transferAmount]);
                $receivableAccount->update(['balance' => $receivableBalance - $transferAmount]);
                
                $this->command->info("Successfully transferred IDR " . number_format($transferAmount, 0, ',', '.') . " from Accounts Receivable to Cash.");
                $this->command->info("New cash balance: IDR " . number_format($cashAccount->balance, 0, ',', '.'));
                $this->command->info("New accounts receivable balance: IDR " . number_format($receivableAccount->balance, 0, ',', '.'));
                
            } else {
                $this->command->warn("Not enough accounts receivable to balance cash account.");
            }
        } elseif ($cashBalance < 0 && $receivableBalance <= 0) {
            $this->command->warn("Cash account is negative (IDR " . number_format($cashBalance, 0, ',', '.') . ") but no accounts receivable available for transfer.");
            $this->command->info("Consider creating additional income or equity transactions to balance cash.");
        } else {
            $this->command->info("Cash account is balanced (IDR " . number_format($cashBalance, 0, ',', '.') . "). No transfer needed.");
        }
    }

    /**
     * Check and balance cash account after each transaction to prevent negative balance
     */
    private function checkAndBalanceCash($company, $accounts, $date)
    {
        $cashAccount = $accounts->where('code', '1000')->first(); // Cash account
        $receivableAccount = $accounts->where('code', '1100')->first(); // Accounts Receivable
        
        if (!$cashAccount || !$receivableAccount) {
            return; // Skip if required accounts not found
        }
        
        // Refresh account balances from database
        $cashAccount->refresh();
        $receivableAccount->refresh();
        
        $cashBalance = $cashAccount->balance;
        $receivableBalance = $receivableAccount->balance;
        
        // If cash is negative and we have receivables to collect
        if ($cashBalance < 0 && $receivableBalance > 0) {
            $transferAmount = abs($cashBalance) + rand(500000, 2000000); // Transfer negative amount plus buffer
            
            // Don't transfer more than available receivables
            $transferAmount = min($transferAmount, $receivableBalance);
            
            // Ensure we have enough receivables to cover the transfer
            if ($transferAmount > 0) {
                $this->command->info("Cash went negative after transaction at: " . $date->format('Y-m-d H:i:s'));
                $this->command->info("Auto-transferring IDR " . number_format($transferAmount, 0, ',', '.') . " from Accounts Receivable to Cash.");
                
                // Create transfer date that is after the transaction date (add 1-4 hours)
                $transferDate = $this->safeAddTime($date, 'hours', rand(1, 4));
                
                $this->command->info("Transfer scheduled for: " . $transferDate->format('Y-m-d H:i:s'));
                
                // Create automatic internal transfer
                $internalTransfer = InternalTransfer::create([
                    'company_id' => $company->id,
                    'number' => 'AUTO-TXN-' . $transferDate->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'date' => $transferDate,
                    'account_in' => $cashAccount->id, // Cash receives the transfer
                    'account_out' => $receivableAccount->id, // Receivables provides the transfer
                    'value' => $transferAmount,
                    'fee' => 0,
                    'fee_charged_to' => 'out',
                    'note' => 'Automatic cash balancing transfer from accounts receivable after transaction',
                ]);
                
                // Create GL entry for the automatic transfer
                $this->createGLEntry($company, $transferDate, 'transfer', $transferAmount, $internalTransfer->number, $accounts, [
                    ['account' => '1000', 'type' => 'debit', 'value' => $transferAmount, 'description' => 'Cash received from accounts receivable collection'],
                    ['account' => '1100', 'type' => 'credit', 'value' => $transferAmount, 'description' => 'Accounts receivable collected and transferred to cash'],
                ], "Automatic cash balancing transfer after transaction");
                
                // Update account balances after the transfer
                $cashAccount->update(['balance' => $cashBalance + $transferAmount]);
                $receivableAccount->update(['balance' => $receivableBalance - $transferAmount]);
                
                $this->command->info("Successfully balanced cash account. New cash balance: IDR " . number_format($cashAccount->balance, 0, ',', '.'));
                $this->command->info("Transfer completed at: " . $transferDate->format('Y-m-d H:i:s'));
            }
        } elseif ($cashBalance < 0 && $receivableBalance <= 0) {
            // If both cash and receivables are low, create emergency income
            $this->command->info("Cash went negative at: " . $date->format('Y-m-d H:i:s') . " but no receivables available. Creating emergency income.");
            $this->createEmergencyIncome($company, $date, $accounts, abs($cashBalance));
        }
    }

    /**
     * Create emergency income when both cash and receivables are low
     */
    private function createEmergencyIncome($company, $date, $accounts, $requiredAmount)
    {
        $this->command->info("Both cash and receivables are low. Creating emergency income transaction for IDR " . number_format($requiredAmount, 0, ',', '.'));
        
        // Add buffer to the required amount
        $incomeAmount = $requiredAmount + rand(1000000, 5000000);
        
        // Get appropriate accounts
        $cashAccount = $accounts->where('code', '1000')->first(); // Cash
        $incomeAccount = $accounts->where('code', '4100')->first(); // Other Income
        
        if (!$incomeAccount) {
            $incomeAccount = $accounts->where('type', 'income')->first();
        }
        
        if (!$cashAccount || !$incomeAccount) {
            $this->command->warn("Cannot create emergency income: Required accounts not found");
            return;
        }
        
        // Create emergency income transaction
        $incomeDate = $this->safeAddTime($date, 'hours', rand(1, 4)); // Emergency income happens after the transaction
        
        $income = Income::create([
            'company_id' => $company->id,
            'number' => 'EMG-' . $incomeDate->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'date' => $incomeDate,
            'due_date' => $this->safeAddTime($incomeDate, 'days', rand(1, 30)),
            'total' => $incomeAmount,
            'paid' => true,
            'status' => 'received',
            'note' => 'Emergency income for cash balancing',
            'customer_id' => null, // No specific customer for emergency income
            'receipt_account_id' => $cashAccount->id,
            'description' => 'Emergency income to balance cash account',
        ]);
        
        // Create income detail
        if ($incomeAccount) {
            IncomeDetail::create([
                'income_id' => $income->id,
                'account_id' => $incomeAccount->id,
                'value' => $incomeAmount,
                'description' => 'Emergency income for cash balancing',
            ]);
        }
        
        // Create GL entry
        $this->createGLEntry($company, $incomeDate, 'income', $incomeAmount, $income->number, $accounts, [
            ['account' => '1000', 'type' => 'debit', 'value' => $incomeAmount, 'description' => 'Emergency cash injection'],
            ['account' => '4100', 'type' => 'credit', 'value' => $incomeAmount, 'description' => 'Emergency income received'],
        ], "Emergency income for cash balancing");
        
        // Update account balances
        $cashAccount->update(['balance' => $cashAccount->balance + $incomeAmount]);
        
        $this->command->info("Created emergency income: IDR " . number_format($incomeAmount, 0, ',', '.') . ". New cash balance: IDR " . number_format($cashAccount->balance, 0, ',', '.'));
        $this->command->info("Emergency income received at: " . $incomeDate->format('Y-m-d H:i:s'));
    }

    /**
     * Create internal transfer between accounts
     */
    private function createInternalTransfer($company, $date, $accounts)
    {
        // Only create transfers if we have at least 2 accounts
        if ($accounts->count() < 2) {
            return;
        }

        // Define different types of financial transfers
        $transferTypes = [
            'cash_to_inventory' => [
                'from_account_code' => '1000', // Cash
                'to_account_code' => '1200',   // Inventory
                'description' => 'Cash allocation to inventory',
                'value_range' => [500000, 5000000]
            ],
            'cash_to_fixed_assets' => [
                'from_account_code' => '1000', // Cash
                'to_account_code' => '1300',   // Fixed Assets
                'description' => 'Cash allocation to fixed assets',
                'value_range' => [1000000, 10000000]
            ],
            'inventory_to_cash' => [
                'from_account_code' => '1200', // Inventory
                'to_account_code' => '1000',   // Cash
                'description' => 'Inventory liquidation to cash',
                'value_range' => [300000, 3000000]
            ],
            'cash_to_receivables' => [
                'from_account_code' => '1000', // Cash
                'to_account_code' => '1100',   // Accounts Receivable
                'description' => 'Cash advance to receivables',
                'value_range' => [200000, 2000000]
            ]
        ];
        
        $transferType = array_rand($transferTypes);
        $typeConfig = $transferTypes[$transferType];
        
        // Find the accounts
        $sourceAccount = $accounts->where('code', $typeConfig['from_account_code'])->first();
        $destinationAccount = $accounts->where('code', $typeConfig['to_account_code'])->first();
        
        if (!$sourceAccount || !$destinationAccount) {
            // Fallback to any available accounts
            $sourceAccount = $accounts->where('type', 'asset')->first();
            $destinationAccount = $accounts->where('type', 'asset')->where('id', '!=', $sourceAccount->id)->first();
            
            if (!$sourceAccount || !$destinationAccount) {
                return; // No suitable accounts found
            }
        }
        
        // Calculate total value for the transfer
        $valueRange = $typeConfig['value_range'];
        $totalValue = rand($valueRange[0], $valueRange[1]);
        
        // Create internal transfer
        $internalTransfer = InternalTransfer::create([
            'company_id' => $company->id,
            'number' => 'IT-' . $date->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'date' => $date,
            'account_in' => $destinationAccount->id,
            'account_out' => $sourceAccount->id,
            'value' => $totalValue,
            'fee' => 0, // No fee for internal transfers
            'fee_charged_to' => 'out', // Fee charged to source account (if any)
            'note' => $typeConfig['description'],
        ]);
        
        // Create GL entry for internal transfer
        $this->createGLEntry($company, $date, 'transfer', $totalValue, $internalTransfer->number, $accounts, [
            ['account' => $destinationAccount->code, 'type' => 'debit', 'value' => $totalValue, 'description' => $typeConfig['description']],
            ['account' => $sourceAccount->code, 'type' => 'credit', 'value' => $totalValue, 'description' => $typeConfig['description']],
        ], "Internal financial transfer");
        
        $this->command->info("Created financial transfer: {$typeConfig['description']} - IDR " . number_format($totalValue, 0, ',', '.') . " from {$sourceAccount->name} to {$destinationAccount->name}");
        
        return $internalTransfer;
    }

    /**
     * Create inventory movement between warehouses
     */
    private function createInventoryMovement($company, $date, $warehouses, $products, &$availableStock)
    {
        // Only create movements if we have at least 2 warehouses
        if ($warehouses->count() < 2) {
            return 0; // Return 0 if no movements can be created
        }

        // Select source and destination warehouses
        $sourceWarehouse = $warehouses->random();
        $destinationWarehouse = $warehouses->where('id', '!=', $sourceWarehouse->id)->random();
        
        // Find products with available stock in source warehouse
        $availableProducts = [];
        foreach ($products->where('is_track_inventory', true) as $product) {
            if ($availableStock[$sourceWarehouse->id][$product->id] > 0) {
                $availableProducts[] = $product;
            }
        }
        
        if (empty($availableProducts)) {
            return 0; // No products available for transfer
        }
        
        // Select random products for transfer
        $productCount = min(rand(1, 3), count($availableProducts));
        $selectedProducts = collect($availableProducts)->random($productCount);
        
        $total = 0;
        $transferDetails = [];
        
        foreach ($selectedProducts as $product) {
            $maxQuantity = min(rand(1, 5), $availableStock[$sourceWarehouse->id][$product->id]);
            $transferDetails[] = [
                'product_id' => $product->id,
                'quantity' => $maxQuantity,
                'cost' => $product->cost
            ];
            $total += $maxQuantity * $product->cost;
        }
        
        // Don't create InternalTransfer record - that model is for financial transfers, not inventory movements
        // Instead, just update stock levels and create GL entries for inventory movements
        
        // Update available stock
        foreach ($transferDetails as $detail) {
            $productId = $detail['product_id'];
            $quantity = $detail['quantity'];
            
            // Subtract from source warehouse
            $availableStock[$sourceWarehouse->id][$productId] -= $quantity;
            if ($availableStock[$sourceWarehouse->id][$productId] < 0) {
                $availableStock[$sourceWarehouse->id][$productId] = 0;
            }
            
            // Add to destination warehouse
            $availableStock[$destinationWarehouse->id][$productId] += $quantity;
            
            $this->command->info("Transferred {$quantity} units of product {$productId} from warehouse {$sourceWarehouse->name} to {$destinationWarehouse->name}");
        }
        
        // Create GL entry for inventory movement (no InternalTransfer record needed)
        $this->createGLEntry($company, $date, 'transfer', $total, 'INV-MOV-' . $date->format('Ymd'), $this->getAccounts($company), [
            ['account' => '1200', 'type' => 'debit', 'value' => $total, 'description' => 'Inventory transferred to destination warehouse'],
            ['account' => '1200', 'type' => 'credit', 'value' => $total, 'description' => 'Inventory transferred from source warehouse'],
        ], "Internal inventory transfer");
        
        return $total; // Return the total value for logging
    }

    /**
     * Create asset purchase
     */
    private function createAsset($company, $date, $accounts, $suppliers)
    {
        $assetTypes = [
            'Office Equipment' => [5000000, 50000000],
            'Computer Hardware' => [2000000, 25000000],
            'Furniture' => [1000000, 15000000],
            'Vehicles' => [50000000, 200000000],
            'Machinery' => [10000000, 100000000],
            'Software Licenses' => [500000, 10000000],
            'Buildings' => [100000000, 1000000000],
            'Land' => [50000000, 500000000],
            'Production Equipment' => [15000000, 150000000],
            'Security Systems' => [3000000, 30000000],
            'Communication Equipment' => [1000000, 20000000],
            'HVAC Systems' => [8000000, 80000000],
        ];
        
        $type = array_rand($assetTypes);
        $amountRange = $assetTypes[$type];
        $amount = rand($amountRange[0], $amountRange[1]);
        
        // Get appropriate accounts
        $assetAccount = $accounts->where('code', '1300')->first(); // Fixed Assets
        $paymentAccount = $accounts->where('code', '1000')->first(); // Cash
        
        if (!$assetAccount || !$paymentAccount) {
            // Try to find any suitable accounts as fallback
            $assetAccount = $accounts->where('type', 'asset')->first();
            $paymentAccount = $accounts->where('type', 'asset')->where('id', '!=', $assetAccount?->id)->first();
            
            if (!$assetAccount || !$paymentAccount) {
                $this->command->warn("Skipping asset creation: Required accounts not found for {$type}");
                $this->command->info("Available account types: " . $accounts->pluck('type')->unique()->implode(', '));
                return null; // Required accounts not found
            }
        }
        
        $this->command->info("Using asset account: {$assetAccount->name} ({$assetAccount->code})");
        $this->command->info("Using payment account: {$paymentAccount->name} ({$paymentAccount->code})");
        
        // Calculate useful life based on asset type
        $usefulLife = $this->getAssetUsefulLife($type);
        $depreciationRate = 1 / $usefulLife; // Straight-line depreciation
        
        // Create asset
        try {
            $asset = Asset::create([
                'company_id' => $company->id,
                'name' => $type,
                'number' => 'AST-' . $date->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'purchased_date' => $date,
                'account_asset' => $assetAccount->id,
                'quantity' => 1, // Assets are typically purchased as single units
                'location' => fake()->city(),
                'reference' => 'AUTO-' . $date->format('Ymd'), // Reference for automated creation
            ]);
            
            $this->command->info("Successfully created asset: {$type} with number {$asset->number}");
            
        } catch (\Exception $e) {
            $this->command->error("Failed to create asset {$type}: " . $e->getMessage());
            return null;
        }
        
        // Create GL entry for asset purchase
        try {
            $this->createGLEntry($company, $date, 'asset', $amount, $asset->number, $accounts, [
                ['account' => '1300', 'type' => 'debit', 'value' => $amount, 'description' => "Asset purchase: {$type}"],
                ['account' => '1000', 'type' => 'credit', 'value' => $amount, 'description' => "Cash payment for {$type}"],
            ], "Asset purchase transaction");
            
            $this->command->info("Successfully created GL entry for asset purchase: {$type}");
            
        } catch (\Exception $e) {
            $this->command->error("Failed to create GL entry for asset {$type}: " . $e->getMessage());
            // Don't return null here, as the asset was created successfully
        }
        
        // Randomly create maintenance expenses for the asset (30% chance)
        if (rand(1, 100) <= 30) {
            $maintenanceExpense = $this->createAssetMaintenanceExpense($company, $date, $accounts, $suppliers, $asset, $amount);
            
            if ($maintenanceExpense) {
                $this->command->info("Created maintenance expense for {$asset->name} - IDR " . number_format($maintenanceExpense->total, 0, ',', '.'));
            }
        }
        
        return $asset;
    }

    /**
     * Get useful life for different asset types
     */
    private function getAssetUsefulLife($assetType)
    {
        $usefulLifeMap = [
            'Office Equipment' => 5,
            'Computer Hardware' => 3,
            'Furniture' => 10,
            'Vehicles' => 8,
            'Machinery' => 15,
            'Software Licenses' => 3,
            'Buildings' => 30,
            'Land' => 999, // Land doesn't depreciate
            'Production Equipment' => 12,
            'Security Systems' => 8,
            'Communication Equipment' => 5,
            'HVAC Systems' => 20,
        ];
        
        return $usefulLifeMap[$assetType] ?? 10;
    }

    /**
     * Create maintenance expense for an asset
     */
    private function createAssetMaintenanceExpense($company, $date, $accounts, $suppliers, $asset, $amount)
    {
        $maintenanceTypes = [
            'Preventive Maintenance' => [0.05, 0.15], // 5-15% of asset value
            'Repair Service' => [0.02, 0.08], // 2-8% of asset value
            'Upgrade Service' => [0.10, 0.25], // 10-25% of asset value
            'Emergency Repair' => [0.08, 0.20], // 8-20% of asset value
        ];
        
        $type = array_rand($maintenanceTypes);
        $percentageRange = $maintenanceTypes[$type];
        $percentage = rand($percentageRange[0] * 100, $percentageRange[1] * 100) / 100;
        $maintenanceAmount = round($amount * $percentage);
        
        // Get appropriate accounts
        $paymentAccount = $accounts->where('code', '1000')->first(); // Cash
        $expenseAccount = $accounts->where('code', '5200')->first(); // Maintenance & Repairs
        
        if (!$expenseAccount) {
            $expenseAccount = $accounts->where('type', 'expense')->first();
        }
        
        if (!$paymentAccount || !$expenseAccount) {
            $this->command->warn("Skipping maintenance expense: Required accounts not found for {$asset->name}");
            return null; // Required accounts not found
        }
        
        $expense = Expense::create([
            'company_id' => $company->id,
            'number' => 'MNT-' . $date->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'date' => $date,
            'due_date' => $this->safeAddTime($date, 'days', rand(1, 30)),
            'total' => $maintenanceAmount,
            'paid' => true,
            'status' => 'paid',
            'note' => "{$type} for {$asset->name}",
            'supplier_id' => $suppliers->random()->id,
            'payment_account_id' => $paymentAccount ? $paymentAccount->id : null,
            'description' => "{$type} service for asset {$asset->name}",
        ]);
        
        // Create expense detail
        if ($expenseAccount) {
            ExpenseDetail::create([
                'expense_id' => $expense->id,
                'account_id' => $expenseAccount->id,
                'value' => $maintenanceAmount,
                'status' => 'approved',
                'description' => "{$type} for {$asset->name}",
            ]);
        }
        
        // Create GL entry
        $this->createGLEntry($company, $date, 'expense', $maintenanceAmount, $expense->number, $accounts, [
            ['account' => '5200', 'type' => 'debit', 'value' => $maintenanceAmount, 'description' => "Maintenance: {$type} for {$asset->name}"],
            ['account' => '1000', 'type' => 'credit', 'value' => $maintenanceAmount, 'description' => "Cash payment for maintenance"],
        ], "Asset maintenance expense");
        
        return $expense;
    }

    /**
     * Create equity transaction (e.g., capital injection, dividend payment)
     */
    private function createEquityTransaction($company, $date, $accounts)
    {
        $equityTypes = [
            'Capital Injection' => [10000000, 50000000],
            'Dividend Payment' => [5000000, 20000000],
            'Share Repurchase' => [10000000, 30000000],
            'Share Issuance' => [20000000, 100000000],
            'Retained Earnings Transfer' => [5000000, 50000000],
            'Other Equity Adjustment' => [1000000, 10000000],
        ];
        
        $type = array_rand($equityTypes);
        $amountRange = $equityTypes[$type];
        $amount = rand($amountRange[0], $amountRange[1]);
        
        // Determine if it's a credit (increase equity) or debit (decrease equity)
        $isCredit = in_array($type, ['Capital Injection', 'Share Issuance', 'Retained Earnings Transfer']);
        
        // Get appropriate accounts for equity transactions
        $cashAccount = $accounts->where('code', '1000')->first(); // Cash
        $equityAccount = null;
        
        // Select appropriate equity account based on transaction type
        if (in_array($type, ['Capital Injection', 'Share Issuance'])) {
            $equityAccount = $accounts->where('code', '3000')->first(); // Owner's Equity
        } elseif ($type === 'Dividend Payment') {
            $equityAccount = $accounts->where('code', '3100')->first(); // Retained Earnings
        } elseif ($type === 'Share Repurchase') {
            $equityAccount = $accounts->where('code', '3000')->first(); // Owner's Equity
        } elseif ($type === 'Retained Earnings Transfer') {
            $equityAccount = $accounts->where('code', '3100')->first(); // Retained Earnings
        } else {
            $equityAccount = $accounts->where('code', '3200')->first(); // Current Year Earnings
        }
        
        // Fallback to any equity account if specific one not found
        if (!$equityAccount) {
            $equityAccount = $accounts->where('type', 'Equity')->first();
        }
        
        if (!$cashAccount || !$equityAccount) {
            $this->command->warn("Skipping equity transaction: Required accounts not found for {$type}");
            return null;
        }
        
        try {
            // Create GL entry for equity transaction
            $this->createGLEntry($company, $date, 'equity', $amount, 'EQ-' . $date->format('Ymd'), $accounts, [
                ['account' => $cashAccount->code, 'type' => $isCredit ? 'debit' : 'credit', 'value' => $amount, 'description' => "Cash for {$type}"],
                ['account' => $equityAccount->code, 'type' => $isCredit ? 'credit' : 'debit', 'value' => $amount, 'description' => "Equity adjustment for {$type}"],
            ], "Equity transaction: {$type}");
            
            return true;
            
        } catch (\Exception $e) {
            $this->command->error("Failed to create equity transaction {$type}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get accounts for the company
     */
    private function getAccounts($company)
    {
        return Account::where('company_id', $company->id)->get();
    }

    /**
     * Show final account balances after all seeding is complete
     */
    private function showFinalAccountBalances($company, $accounts)
    {
        $this->command->info('=== FINAL ACCOUNT BALANCES ===');
        
        // Group accounts by type for better organization
        $accountTypes = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        
        foreach ($accountTypes as $type) {
            $typeAccounts = $accounts->where('type', $type);
            if ($typeAccounts->count() > 0) {
                $this->command->info("--- {$type} ACCOUNTS ---");
                foreach ($typeAccounts as $account) {
                    $balance = $account->balance;
                    $status = $balance >= 0 ? '✓' : '⚠';
                    $this->command->info("{$status} {$account->name} ({$account->code}): IDR " . number_format($balance, 0, ',', '.'));
                }
            }
        }
        
        // Highlight cash account status
        $cashAccount = $accounts->where('code', '1000')->first();
        if ($cashAccount) {
            $this->command->info('');
            if ($cashAccount->balance >= 0) {
                $this->command->info("✅ Cash account is balanced: IDR " . number_format($cashAccount->balance, 0, ',', '.'));
            } else {
                $this->command->warn("❌ Cash account is still negative: IDR " . number_format($cashAccount->balance, 0, ',', '.'));
            }
        }
        
        // Validate that all transaction dates are within 2024
        $this->validateAllDatesIn2024($company);
        
        $this->command->info('=== END FINAL BALANCES ===');
    }

    /**
     * Validate that all transaction dates are within 2024
     */
    private function validateAllDatesIn2024($company)
    {
        $this->command->info('=== DATE VALIDATION SUMMARY ===');
        
        // Check purchase orders
        $poCount = PurchaseOrder::where('company_id', $company->id)->count();
        $poInvalidDates = PurchaseOrder::where('company_id', $company->id)
            ->whereYear('created_at', '!=', 2024)
            ->count();
        
        // Check sales orders
        $soCount = SalesOrder::where('company_id', $company->id)->count();
        $soInvalidDates = SalesOrder::where('company_id', $company->id)
            ->whereYear('created_at', '!=', 2024)
            ->count();
        
        // Check internal transfers
        $itCount = InternalTransfer::where('company_id', $company->id)->count();
        $itInvalidDates = InternalTransfer::where('company_id', $company->id)
            ->whereYear('date', '!=', 2024)
            ->count();
        
        // Check general ledgers
        $glCount = GeneralLedger::where('company_id', $company->id)->count();
        $glInvalidDates = GeneralLedger::where('company_id', $company->id)
            ->whereYear('date', '!=', 2024)
            ->count();
        
        $this->command->info("Purchase Orders: {$poCount} total, {$poInvalidDates} outside 2024");
        $this->command->info("Sales Orders: {$soCount} total, {$soInvalidDates} outside 2024");
        $this->command->info("Internal Transfers: {$itCount} total, {$itInvalidDates} outside 2024");
        $this->command->info("General Ledgers: {$glCount} total, {$glInvalidDates} outside 2024");
        
        $totalInvalid = $poInvalidDates + $soInvalidDates + $itInvalidDates + $glInvalidDates;
        if ($totalInvalid === 0) {
            $this->command->info("✅ All transaction dates are within 2024");
        } else {
            $this->command->warn("⚠️  {$totalInvalid} transactions have dates outside 2024");
        }
        
        $this->command->info('=== END DATE VALIDATION ===');
    }
}
