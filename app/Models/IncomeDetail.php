<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'income_id',
        'account_id',
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
     * Get the income that owns this detail.
     */
    public function income()
    {
        return $this->belongsTo(Income::class);
    }

    /**
     * Get the account for this detail.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
