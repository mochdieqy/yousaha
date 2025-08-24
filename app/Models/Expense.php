<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
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
        'supplier_id',
        'payment_account_id',
        'description',
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
     * Get the company that owns the expense.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the expense details.
     */
    public function details()
    {
        return $this->hasMany(ExpenseDetail::class);
    }

    /**
     * Get the supplier for this expense.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the payment account for this expense.
     */
    public function paymentAccount()
    {
        return $this->belongsTo(Account::class, 'payment_account_id');
    }

    /**
     * Check if the expense is overdue.
     */
    public function isOverdue()
    {
        return !$this->paid && $this->due_date->isPast();
    }

    /**
     * Get the remaining amount to be paid.
     */
    public function getRemainingAmountAttribute()
    {
        return $this->paid ? 0 : $this->total;
    }
}
