<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\GeneralLedgerDetail;
use Illuminate\Support\Facades\DB;

class RecalculateAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:recalculate-balances {company_id? : The company ID to recalculate balances for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all account balances from general ledger entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->argument('company_id');
        
        if ($companyId) {
            $companies = collect([\App\Models\Company::find($companyId)]);
            if (!$companies->first()) {
                $this->error("Company with ID {$companyId} not found.");
                return 1;
            }
        } else {
            $companies = \App\Models\Company::all();
        }

        foreach ($companies as $company) {
            $this->info("Recalculating balances for company: {$company->name}");
            
            $accounts = Account::where('company_id', $company->id)->get();
            $this->info("Found " . $accounts->count() . " accounts to update.");
            
            $bar = $this->output->createProgressBar($accounts->count());
            $bar->start();
            
            foreach ($accounts as $account) {
                $this->recalculateAccountBalance($account);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("Completed balance recalculation for {$company->name}");
        }

        $this->info('All account balances have been recalculated successfully!');
        return 0;
    }

    /**
     * Recalculate balance for a specific account
     */
    private function recalculateAccountBalance(Account $account)
    {
        try {
            // Get all general ledger details for this account
            $glDetails = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account) {
                $query->where('company_id', $account->company_id);
            })->where('account_id', $account->id)->get();

            $balance = 0;

            foreach ($glDetails as $detail) {
                if ($detail->type === 'debit') {
                    $balance += $detail->value;
                } else {
                    $balance -= $detail->value;
                }
            }

            // Update the account balance
            $account->update(['balance' => $balance]);

        } catch (\Exception $e) {
            $this->error("Failed to recalculate balance for account {$account->code}: " . $e->getMessage());
        }
    }
}
