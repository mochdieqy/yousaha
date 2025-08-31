<?php

namespace App\Services;

use App\Models\Account;
use App\Models\GeneralLedgerDetail;
use Illuminate\Support\Facades\DB;

class AccountBalanceService
{
    /**
     * Update account balances for a transaction
     */
    public static function updateBalancesForTransaction($companyId, $entries)
    {
        foreach ($entries as $entry) {
            $account = Account::find($entry['account_id']);
            
            if ($account && $account->company_id == $companyId) {
                if ($entry['type'] === 'debit') {
                    $account->increment('balance', $entry['value']);
                } else {
                    $account->decrement('balance', $entry['value']);
                }
            }
        }
    }

    /**
     * Reverse account balances for a transaction
     */
    public static function reverseBalancesForTransaction($companyId, $entries)
    {
        foreach ($entries as $entry) {
            $account = Account::find($entry['account_id']);
            
            if ($account && $account->company_id == $companyId) {
                if ($entry['type'] === 'debit') {
                    $account->decrement('balance', $entry['value']);
                } else {
                    $account->increment('balance', $entry['value']);
                }
            }
        }
    }

    /**
     * Calculate balance from general ledger for an account
     */
    public static function calculateBalanceFromGeneralLedger($account)
    {
        $debits = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account) {
            $query->where('company_id', $account->company_id)
                  ->where('status', 'posted');
        })->where('account_id', $account->id)
          ->where('type', 'debit')
          ->sum('value');

        $credits = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account) {
            $query->where('company_id', $account->company_id)
                  ->where('status', 'posted');
        })->where('account_id', $account->id)
          ->where('type', 'credit')
          ->sum('value');

        return $debits - $credits;
    }

    /**
     * Calculate balance from general ledger in a date range
     */
    public static function calculateBalanceFromGeneralLedgerInRange($account, $startDate, $endDate)
    {
        $debits = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account, $startDate, $endDate) {
            $query->where('company_id', $account->company_id)
                  ->where('status', 'posted')
                  ->whereBetween('date', [$startDate, $endDate]);
        })->where('account_id', $account->id)
          ->where('type', 'debit')
          ->sum('value');

        $credits = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account, $startDate, $endDate) {
            $query->where('company_id', $account->company_id)
                  ->where('status', 'posted')
                  ->whereBetween('date', [$startDate, $endDate]);
        })->where('account_id', $account->id)
          ->where('type', 'credit')
          ->sum('value');

        return $debits - $credits;
    }

    /**
     * Calculate opening balance for an account before a specific date
     */
    public static function calculateOpeningBalance($account, $date)
    {
        $debits = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account, $date) {
            $query->where('company_id', $account->company_id)
                  ->where('status', 'posted')
                  ->where('date', '<', $date);
        })->where('account_id', $account->id)
          ->where('type', 'debit')
          ->sum('value');

        $credits = GeneralLedgerDetail::whereHas('generalLedger', function($query) use ($account, $date) {
            $query->where('company_id', $account->company_id)
                  ->where('status', 'posted')
                  ->where('date', '<', $date);
        })->where('account_id', $account->id)
          ->where('type', 'credit')
          ->sum('value');

        return $debits - $credits;
    }
}
