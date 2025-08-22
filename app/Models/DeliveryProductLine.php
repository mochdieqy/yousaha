<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryProductLine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'delivery_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the delivery that owns this product line.
     */
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
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
