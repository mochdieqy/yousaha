<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptProductLine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receipt_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the receipt that owns this product line.
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
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
}
