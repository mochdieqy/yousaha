<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'warehouse_id',
        'product_id',
        'quantity_total',
        'quantity_reserve',
        'quantity_saleable',
        'quantity_incoming',
        'quantity',
    ];

    /**
     * Get the company that owns the stock.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the warehouse for this stock.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the product for this stock.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the stock histories for this stock.
     */
    public function histories()
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Get the stock details for this stock.
     */
    public function details()
    {
        return $this->hasMany(StockDetail::class);
    }

    /**
     * Check if the stock is low (saleable quantity is less than or equal to 10).
     */
    public function isLowStock($threshold = 10)
    {
        return $this->quantity_saleable <= $threshold;
    }

    /**
     * Check if the stock is out of stock.
     */
    public function isOutOfStock()
    {
        return $this->quantity_saleable <= 0;
    }

    /**
     * Get the available quantity (saleable - reserved).
     */
    public function getAvailableQuantityAttribute()
    {
        return $this->quantity_saleable - $this->quantity_reserve;
    }
}
