<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedgerDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'general_ledger_id',
        'account_id',
        'type',
        'value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
    ];

    /**
     * The possible entry types.
     */
    const TYPE_DEBIT = 'debit';
    const TYPE_CREDIT = 'credit';

    /**
     * Get the general ledger that owns this detail.
     */
    public function generalLedger()
    {
        return $this->belongsTo(GeneralLedger::class);
    }

    /**
     * Get the account for this detail.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Check if this is a debit entry.
     */
    public function isDebit()
    {
        return $this->type === self::TYPE_DEBIT;
    }

    /**
     * Check if this is a credit entry.
     */
    public function isCredit()
    {
        return $this->type === self::TYPE_CREDIT;
    }
}
