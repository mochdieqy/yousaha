<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderProductLine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sales_order_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the sales order that owns this product line.
     */
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /**
     * Get the product for this line.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the line total (quantity * product price).
     */
    public function getLineTotalAttribute()
    {
        return $this->quantity * $this->product->price;
    }

    /**
     * Get the line total with taxes.
     */
    public function getLineTotalWithTaxesAttribute()
    {
        return $this->quantity * $this->product->total_price;
    }
}
