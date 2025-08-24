<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Account;

class FinanceAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all companies to seed finance accounts for
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->seedFinanceAccountsForCompany($company);
        }
    }
    
    /**
     * Seed finance accounts for a specific company
     */
    private function seedFinanceAccountsForCompany(Company $company)
    {
        // Check if company already has accounts
        if ($company->accounts()->count() > 0) {
            return; // Skip if company already has accounts
        }
        
        $basicAccounts = [
            // Asset Accounts
            ['code' => '1000', 'name' => 'Cash', 'type' => 'Asset', 'balance' => 50000.00],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'Asset', 'balance' => 25000.00],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'Asset', 'balance' => 75000.00],
            ['code' => '1300', 'name' => 'Prepaid Expenses', 'type' => 'Asset', 'balance' => 5000.00],
            ['code' => '1400', 'name' => 'Fixed Assets', 'type' => 'Asset', 'balance' => 150000.00],
            ['code' => '1500', 'name' => 'Accumulated Depreciation', 'type' => 'Asset', 'balance' => -25000.00],
            
            // Liability Accounts
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability', 'balance' => 30000.00],
            ['code' => '2100', 'name' => 'Accrued Expenses', 'type' => 'Liability', 'balance' => 8000.00],
            ['code' => '2200', 'name' => 'Short-term Loans', 'type' => 'Liability', 'balance' => 50000.00],
            ['code' => '2300', 'name' => 'Long-term Loans', 'type' => 'Liability', 'balance' => 100000.00],
            
            // Equity Accounts
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'type' => 'Equity', 'balance' => 100000.00],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'Equity', 'balance' => 45000.00],
            ['code' => '3200', 'name' => 'Current Year Earnings', 'type' => 'Equity', 'balance' => 22000.00],
            
            // Revenue Accounts
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'Revenue', 'balance' => 0.00],
            ['code' => '4100', 'name' => 'Other Income', 'type' => 'Revenue', 'balance' => 0.00],
            
            // Expense Accounts
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5100', 'name' => 'Operating Expenses', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5200', 'name' => 'Payroll Expenses', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5300', 'name' => 'Marketing Expenses', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5400', 'name' => 'Administrative Expenses', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5500', 'name' => 'Depreciation Expense', 'type' => 'Expense', 'balance' => 0.00],
        ];
        
        foreach ($basicAccounts as $accountData) {
            Account::create([
                'company_id' => $company->id,
                'code' => $accountData['code'],
                'name' => $accountData['name'],
                'type' => $accountData['type'],
                'balance' => $accountData['balance'],
            ]);
        }
        
        $this->command->info("Finance accounts seeded for company: {$company->name}");
    }
}
