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
        'balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
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
}
