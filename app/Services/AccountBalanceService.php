<?php

namespace App\Services;

use App\Models\Account;
use App\Models\GeneralLedgerDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountBalanceService
{
    /**
     * Update account balance based on a transaction
     */
    public static function updateAccountBalance(Account $account, float $amount, string $entryType): void
    {
        try {
            $currentBalance = $account->balance;
            $newBalance = self::calculateNewBalance($account->type, $currentBalance, $amount, $entryType);
            
            $account->update(['balance' => $newBalance]);
            
            Log::info("Updated account {$account->code} ({$account->type}) balance: {$currentBalance} -> {$newBalance} ({$entryType} {$amount})");
            
        } catch (\Exception $e) {
            Log::error("Failed to update account balance for {$account->code}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate new balance based on account type and entry type
     */
    private static function calculateNewBalance(string $accountType, float $currentBalance, float $amount, string $entryType): float
    {
        $isDebit = $entryType === 'debit';
        
        switch (strtolower($accountType)) {
            case 'asset':
            case 'expense':
                // Assets and Expenses increase with debit, decrease with credit
                return $isDebit ? $currentBalance + $amount : $currentBalance - $amount;
                
            case 'liability':
            case 'equity':
            case 'revenue':
                // Liabilities, Equity, and Revenue decrease with debit, increase with credit
                return $isDebit ? $currentBalance - $amount : $currentBalance + $amount;
                
            default:
                Log::warning("Unknown account type: {$accountType}, using default calculation");
                return $isDebit ? $currentBalance + $amount : $currentBalance - $amount;
        }
    }

    /**
     * Recalculate balance for a specific account from general ledger
     */
    public static function recalculateAccountBalance(Account $account): void
    {
        try {
            $balance = self::calculateBalanceFromGeneralLedger($account);
            $account->update(['balance' => $balance]);
            
            Log::info("Recalculated account {$account->code} ({$account->type}) balance: {$balance}");
            
        } catch (\Exception $e) {
            Log::error("Failed to recalculate balance for account {$account->code}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate balance from general ledger for a specific account
     */
    public static function calculateBalanceFromGeneralLedger(Account $account): float
    {
        $glDetails = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account) {
            $query->where('company_id', $account->company_id);
        })->where('account_id', $account->id)->get();

        $balance = 0;

        foreach ($glDetails as $detail) {
            $balance += self::calculateEntryImpact($detail, $account->type);
        }

        return $balance;
    }

    /**
     * Calculate the impact of a general ledger entry on an account balance
     */
    public static function calculateEntryImpact($detail, string $accountType): float
    {
        $value = $detail->value;
        $isDebit = $detail->type === 'debit';
        
        switch (strtolower($accountType)) {
            case 'asset':
                // Assets increase with debits, decrease with credits
                return $isDebit ? $value : -$value;
                
            case 'liability':
                // Liabilities increase with credits, decrease with debits
                return $isDebit ? -$value : $value;
                
            case 'equity':
                // Equity increases with credits, decreases with debits
                return $isDebit ? -$value : $value;
                
            case 'revenue':
                // Revenue increases with credits, decreases with debits
                return $isDebit ? -$value : $value;
                
            case 'expense':
                // Expenses increase with debits, decrease with credits
                return $isDebit ? $value : -$value;
                
            default:
                Log::warning("Unknown account type: {$accountType}, using default calculation");
                return $isDebit ? $value : -$value;
        }
    }

    /**
     * Recalculate all account balances for a company
     */
    public static function recalculateAllAccountBalances(int $companyId): void
    {
        try {
            $accounts = Account::where('company_id', $companyId)->get();
            
            foreach ($accounts as $account) {
                self::recalculateAccountBalance($account);
            }
            
            Log::info("Recalculated all account balances for company {$companyId}");
            
        } catch (\Exception $e) {
            Log::error("Failed to recalculate all account balances for company {$companyId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update account balances for a general ledger transaction
     */
    public static function updateBalancesForTransaction(int $companyId, array $entries): void
    {
        try {
            DB::beginTransaction();
            
            foreach ($entries as $entry) {
                $account = Account::find($entry['account_id']);
                if ($account && $account->company_id === $companyId) {
                    self::updateAccountBalance($account, $entry['value'], $entry['type']);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update balances for transaction: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse account balances for a deleted transaction
     */
    public static function reverseBalancesForTransaction(int $companyId, array $entries): void
    {
        try {
            DB::beginTransaction();
            
            foreach ($entries as $entry) {
                $account = Account::find($entry['account_id']);
                if ($account && $account->company_id === $companyId) {
                    // Reverse the entry type
                    $reversedType = $entry['type'] === 'debit' ? 'credit' : 'debit';
                    self::updateAccountBalance($account, $entry['value'], $reversedType);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to reverse balances for transaction: " . $e->getMessage());
            throw $e;
        }
    }
}
