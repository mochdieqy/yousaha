<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\Account;
use App\Services\AccountBalanceService;

class RecalculateAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:recalculate-balances {company_id? : The ID of the company to recalculate balances for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all account balances from general ledger transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->argument('company_id');
        
        if ($companyId) {
            $company = Company::find($companyId);
            if (!$company) {
                $this->error("Company with ID {$companyId} not found.");
                return 1;
            }
            
            $this->recalculateCompanyBalances($company);
        } else {
            $companies = Company::all();
            
            if ($companies->isEmpty()) {
                $this->error('No companies found.');
                return 1;
            }
            
            foreach ($companies as $company) {
                $this->recalculateCompanyBalances($company);
            }
        }
        
        $this->info('Account balance recalculation completed successfully!');
        return 0;
    }

    /**
     * Recalculate balances for a specific company
     */
    private function recalculateCompanyBalances(Company $company)
    {
        $this->info("Recalculating account balances for company: {$company->name}");
        
        try {
            // Use the AccountBalanceService to recalculate all balances
            AccountBalanceService::recalculateAllAccountBalances($company->id);
            
            // Get updated accounts to show results
            $accounts = Account::where('company_id', $company->id)->get();
            
            $this->info("Successfully recalculated balances for {$accounts->count()} accounts:");
            
            foreach ($accounts as $account) {
                $this->line("  {$account->code} - {$account->name}: IDR " . number_format($account->balance, 0, ',', '.'));
            }
            
        } catch (\Exception $e) {
            $this->error("Failed to recalculate balances for company {$company->name}: " . $e->getMessage());
        }
        
        $this->newLine();
    }
}
