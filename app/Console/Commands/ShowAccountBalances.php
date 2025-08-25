<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\Account;
use App\Services\AccountBalanceService;

class ShowAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:show-balances {company_id? : The ID of the company to show balances for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show calculated account balances from general ledger transactions';

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
            
            $this->showCompanyBalances($company);
        } else {
            $companies = Company::all();
            
            if ($companies->isEmpty()) {
                $this->error('No companies found.');
                return 1;
            }
            
            foreach ($companies as $company) {
                $this->showCompanyBalances($company);
            }
        }
        
        $this->info('Account balance display completed successfully!');
        return 0;
    }

    /**
     * Show calculated balances for a specific company
     */
    private function showCompanyBalances(Company $company)
    {
        $this->info("Showing calculated account balances for company: {$company->name}");
        
        try {
            // Get accounts to show calculated balances
            $accounts = Account::where('company_id', $company->id)->get();
            
            $this->info("Showing calculated balances for {$accounts->count()} accounts:");
            
            foreach ($accounts as $account) {
                $calculatedBalance = \App\Services\AccountBalanceService::calculateBalanceFromGeneralLedger($account);
                $this->line("  {$account->code} - {$account->name}: IDR " . number_format($calculatedBalance, 0, ',', '.'));
            }
            
        } catch (\Exception $e) {
            $this->error("Failed to calculate balances for company {$company->name}: " . $e->getMessage());
        }
        
        $this->newLine();
    }
}
