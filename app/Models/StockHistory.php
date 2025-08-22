<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stock_id',
        'quantity_total_before',
        'quantity_total_after',
        'quantity_reserve_before',
        'quantity_reserve_after',
        'quantity_saleable_before',
        'quantity_saleable_after',
        'quantity_incoming_before',
        'quantity_incoming_after',
        'type',
        'reference',
        'date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the stock that owns this history.
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Get the total quantity change.
     */
    public function getTotalQuantityChangeAttribute()
    {
        return $this->quantity_total_after - $this->quantity_total_before;
    }

    /**
     * Get the saleable quantity change.
     */
    public function getSaleableQuantityChangeAttribute()
    {
        return $this->quantity_saleable_after - $this->quantity_saleable_before;
    }

    /**
     * Check if this is an increase in stock.
     */
    public function isIncrease()
    {
        return $this->total_quantity_change > 0;
    }

    /**
     * Check if this is a decrease in stock.
     */
    public function isDecrease()
    {
        return $this->total_quantity_change < 0;
    }
}
