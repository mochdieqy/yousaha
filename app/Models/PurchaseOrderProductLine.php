<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProductLine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the purchase order that owns this product line.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the product for this line.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the line total (quantity * product cost).
     */
    public function getLineTotalAttribute()
    {
        return $this->quantity * ($this->product->cost ?? $this->product->price);
    }

    /**
     * Get the formatted line total.
     */
    public function getFormattedLineTotalAttribute()
    {
        return number_format($this->line_total, 2);
    }
}
