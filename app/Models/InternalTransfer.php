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
        'value',
        'fee',
        'fee_charged_to',
        'note',
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
}
