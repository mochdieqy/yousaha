<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No casts needed since we're using calculated balances
    ];

    /**
     * Get the company that owns the account.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the general ledger details for this account.
     */
    public function generalLedgerDetails()
    {
        return $this->hasMany(GeneralLedgerDetail::class);
    }

    /**
     * Get the expense details for this account.
     */
    public function expenseDetails()
    {
        return $this->hasMany(ExpenseDetail::class);
    }

    /**
     * Get the income details for this account.
     */
    public function incomeDetails()
    {
        return $this->hasMany(IncomeDetail::class);
    }

    /**
     * Get the internal transfers where this account is the receiving account.
     */
    public function internalTransfersIn()
    {
        return $this->hasMany(InternalTransfer::class, 'account_in');
    }

    /**
     * Get the internal transfers where this account is the sending account.
     */
    public function internalTransfersOut()
    {
        return $this->hasMany(InternalTransfer::class, 'account_out');
    }

    /**
     * Get the assets associated with this account.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class, 'account_asset');
    }
    
    /**
     * Check if this account is a critical system account that cannot be deleted.
     * Critical accounts are used in sales orders and purchase orders.
     * Only account codes are used for identification as names may change.
     */
    public function isCriticalAccount(): bool
    {
        $criticalAccountCodes = [
            '1100', // Accounts Receivable - used in sales orders
            '2000', // Accounts Payable - used in purchase orders
            '4000', // Sales Revenue - used in sales orders
            '5000', // Cost of Goods Sold - used in purchase orders
        ];
        
        return in_array($this->code, $criticalAccountCodes);
    }
    
    /**
     * Check if this account can be deleted.
     * An account cannot be deleted if:
     * 1. It's a critical system account
     * 2. It has associated general ledger details
     * 3. It has associated expense/income details
     * 4. It has associated internal transfers
     */
    public function canBeDeleted(): bool
    {
        // Check if it's a critical account
        if ($this->isCriticalAccount()) {
            return false;
        }
        
        // Check if it has any associated transactions
        return !($this->generalLedgerDetails()->exists() ||
                $this->expenseDetails()->exists() ||
                $this->incomeDetails()->exists() ||
                $this->internalTransfersIn()->exists() ||
                $this->internalTransfersOut()->exists());
    }
    
    /**
     * Get the reason why this account cannot be deleted.
     */
    public function getDeletionBlockReason(): ?string
    {
        if ($this->isCriticalAccount()) {
            return 'This is a critical system account used in sales/purchase orders and cannot be deleted.';
        }
        
        if ($this->generalLedgerDetails()->exists()) {
            return 'Cannot delete account. It has associated general ledger entries.';
        }
        
        if ($this->expenseDetails()->exists()) {
            return 'Cannot delete account. It has associated expense records.';
        }
        
        if ($this->incomeDetails()->exists()) {
            return 'Cannot delete account. It has associated income records.';
        }
        
        if ($this->internalTransfersIn()->exists() || $this->internalTransfersOut()->exists()) {
            return 'Cannot delete account. It has associated internal transfer records.';
        }
        
        return null;
    }

    /**
     * Get the calculated balance from general ledger transactions.
     * This provides real-time balance calculation instead of using the stored balance field.
     */
    public function getCalculatedBalanceAttribute(): float
    {
        return \App\Services\AccountBalanceService::calculateBalanceFromGeneralLedger($this);
    }
}
