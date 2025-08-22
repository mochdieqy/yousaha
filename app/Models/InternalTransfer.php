<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalTransfer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'number',
        'date',
        'account_in',
        'account_out',
        'note',
        'value',
        'fee',
        'fee_charged_to',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'value' => 'decimal:2',
        'fee' => 'decimal:2',
    ];

    /**
     * The possible fee charge options.
     */
    const FEE_CHARGED_TO_IN = 'in';
    const FEE_CHARGED_TO_OUT = 'out';

    /**
     * Get the company that owns the transfer.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the account receiving the transfer.
     */
    public function accountIn()
    {
        return $this->belongsTo(Account::class, 'account_in');
    }

    /**
     * Get the account sending the transfer.
     */
    public function accountOut()
    {
        return $this->belongsTo(Account::class, 'account_out');
    }

    /**
     * Get the net amount for the receiving account.
     */
    public function getNetAmountInAttribute()
    {
        return $this->fee_charged_to === self::FEE_CHARGED_TO_IN 
            ? $this->value - $this->fee 
            : $this->value;
    }

    /**
     * Get the net amount for the sending account.
     */
    public function getNetAmountOutAttribute()
    {
        return $this->fee_charged_to === self::FEE_CHARGED_TO_OUT 
            ? $this->value + $this->fee 
            : $this->value;
    }
}
