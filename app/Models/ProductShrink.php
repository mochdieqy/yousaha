<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShrink extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'percentage',
        'period',
        'limit',
    ];

    /**
     * Get the product that owns this shrink configuration.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate the shrink amount for a given quantity.
     */
    public function calculateShrinkAmount($quantity)
    {
        $shrinkAmount = ($quantity * $this->percentage) / 100;
        
        return min($shrinkAmount, $this->limit);
    }

    /**
     * Check if shrinkage applies to the given quantity.
     */
    public function appliesTo($quantity)
    {
        return $quantity > 0 && $this->percentage > 0;
    }

    /**
     * Get the effective shrink percentage (considering the limit).
     */
    public function getEffectiveShrinkPercentage($quantity)
    {
        if ($quantity <= 0) {
            return 0;
        }

        $theoreticalShrink = ($quantity * $this->percentage) / 100;
        $actualShrink = min($theoreticalShrink, $this->limit);
        
        return ($actualShrink / $quantity) * 100;
    }
}
