<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stock_id',
        'quantity',
        'code',
        'cost',
        'reference',
        'expiration_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cost' => 'decimal:2',
        'expiration_date' => 'date',
    ];

    /**
     * Get the stock that owns this detail.
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Check if this stock detail is expired.
     */
    public function isExpired()
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    /**
     * Check if this stock detail is expiring soon (within 30 days).
     */
    public function isExpiringSoon($days = 30)
    {
        return $this->expiration_date && $this->expiration_date->isAfter(now()) && $this->expiration_date->isBefore(now()->addDays($days));
    }

    /**
     * Get the total value of this stock detail.
     */
    public function getTotalValueAttribute()
    {
        return $this->quantity * ($this->cost ?? 0);
    }
}
