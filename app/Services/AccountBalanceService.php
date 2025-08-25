<?php

namespace App\Services;

use App\Models\Account;
use App\Models\GeneralLedgerDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountBalanceService
{
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
     * Calculate balance from general ledger for a specific account within a date range
     */
    public static function calculateBalanceFromGeneralLedgerInRange(Account $account, $startDate, $endDate): float
    {
        $glDetails = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account, $startDate, $endDate) {
            $query->where('company_id', $account->company_id)
                  ->whereBetween('date', [$startDate, $endDate]);
        })->where('account_id', $account->id)->get();

        $balance = 0;

        foreach ($glDetails as $detail) {
            $balance += self::calculateEntryImpact($detail, $account->type);
        }

        return $balance;
    }

    /**
     * Calculate opening balance (balance before a specific date)
     */
    public static function calculateOpeningBalance(Account $account, $date): float
    {
        $glDetails = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account, $date) {
            $query->where('company_id', $account->company_id)
                  ->where('date', '<', $date);
        })->where('account_id', $account->id)->get();

        $balance = 0;

        foreach ($glDetails as $detail) {
            $balance += self::calculateEntryImpact($detail, $account->type);
        }

        return $balance;
    }
}
