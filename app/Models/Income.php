<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
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
        'due_date',
        'total',
        'paid',
        'status',
        'note',
        'description',
        'customer_id',
        'receipt_account_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'total' => 'decimal:2',
        'paid' => 'boolean',
    ];

    /**
     * Get the company that owns the income.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the income details.
     */
    public function details()
    {
        return $this->hasMany(IncomeDetail::class);
    }

    /**
     * Get the customer for this income.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the receipt account for this income.
     */
    public function receiptAccount()
    {
        return $this->belongsTo(Account::class, 'receipt_account_id');
    }
}
