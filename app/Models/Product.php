<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'sku',
        'type',
        'is_track_inventory',
        'price',
        'taxes',
        'cost',
        'barcode',
        'reference',
        'is_shrink',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_track_inventory' => 'boolean',
        'is_shrink' => 'boolean',
        'price' => 'decimal:2',
        'taxes' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    /**
     * The possible product types.
     */
    const TYPE_GOODS = 'goods';
    const TYPE_SERVICE = 'service';
    const TYPE_COMBO = 'combo';

    /**
     * Get the company that owns the product.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Check if the product is a goods type.
     */
    public function isGoods()
    {
        return $this->type === self::TYPE_GOODS;
    }

    /**
     * Check if the product is a service type.
     */
    public function isService()
    {
        return $this->type === self::TYPE_SERVICE;
    }

    /**
     * Check if the product is a combo type.
     */
    public function isCombo()
    {
        return $this->type === self::TYPE_COMBO;
    }

    /**
     * Get the total price including taxes.
     */
    public function getTotalPriceAttribute()
    {
        return $this->price + ($this->taxes ?? 0);
    }

    /**
     * Get the profit margin.
     */
    public function getProfitMarginAttribute()
    {
        if (!$this->cost) {
            return null;
        }
        
        return $this->price - $this->cost;
    }

    /**
     * Get the product shrink configuration.
     */
    public function productShrink()
    {
        return $this->hasOne(ProductShrink::class);
    }

    /**
     * Get the sales order product lines.
     */
    public function salesOrderLines()
    {
        return $this->hasMany(SalesOrderProductLine::class);
    }

    /**
     * Get the purchase order product lines.
     */
    public function purchaseOrderLines()
    {
        return $this->hasMany(PurchaseOrderProductLine::class);
    }

    /**
     * Get the receipt product lines.
     */
    public function receiptLines()
    {
        return $this->hasMany(ReceiptProductLine::class);
    }

    /**
     * Get the delivery product lines.
     */
    public function deliveryLines()
    {
        return $this->hasMany(DeliveryProductLine::class);
    }

    /**
     * Get the stocks for this product.
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
