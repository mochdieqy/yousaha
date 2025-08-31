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
            ['code' => '1000', 'name' => 'Cash', 'type' => 'Asset'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'Asset'],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'Asset'],
            ['code' => '1300', 'name' => 'Prepaid Expenses', 'type' => 'Asset'],
            ['code' => '1400', 'name' => 'Fixed Assets', 'type' => 'Asset'],
            ['code' => '1500', 'name' => 'Accumulated Depreciation', 'type' => 'Asset'],
            
            // Liability Accounts
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability'],
            ['code' => '2100', 'name' => 'Accrued Expenses', 'type' => 'Liability'],
            ['code' => '2200', 'name' => 'Short-term Loans', 'type' => 'Liability'],
            ['code' => '2300', 'name' => 'Long-term Loans', 'type' => 'Liability'],
            
            // Equity Accounts
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'type' => 'Equity'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'Equity'],
            ['code' => '3200', 'name' => 'Current Year Earnings', 'type' => 'Equity'],
            
            // Revenue Accounts
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'Revenue'],
            ['code' => '4100', 'name' => 'Other Income', 'type' => 'Revenue'],
            
            // Expense Accounts
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'Expense'],
            ['code' => '5100', 'name' => 'Operating Expenses', 'type' => 'Expense'],
            ['code' => '5200', 'name' => 'Payroll Expenses', 'type' => 'Expense'],
            ['code' => '5300', 'name' => 'Marketing Expenses', 'type' => 'Expense'],
            ['code' => '5400', 'name' => 'Administrative Expenses', 'type' => 'Expense'],
            ['code' => '5500', 'name' => 'Depreciation Expense', 'type' => 'Expense'],
        ];
        
        foreach ($basicAccounts as $accountData) {
            Account::create([
                'company_id' => $company->id,
                'code' => $accountData['code'],
                'name' => $accountData['name'],
                'type' => $accountData['type'],
            ]);
        }
        
        $this->command->info("Finance accounts seeded for company: {$company->name}");
    }
}
