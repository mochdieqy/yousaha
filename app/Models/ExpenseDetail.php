<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expense_id',
        'account_id',
        'value',
        'status',
        'description',
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
     * Get the expense that owns this detail.
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Get the account for this detail.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the amount (alias for value).
     */
    public function getAmountAttribute()
    {
        return $this->value;
    }

    /**
     * Set the amount (alias for value).
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['value'] = $value;
    }
}
