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
     * Check if the product should track inventory.
     * Services typically don't track inventory.
     */
    public function shouldTrackInventory()
    {
        return $this->is_track_inventory && !$this->isService();
    }

    /**
     * Check if the product requires stock management.
     */
    public function requiresStockManagement()
    {
        return $this->shouldTrackInventory();
    }

    /**
     * Get the current stock quantity across all warehouses.
     */
    public function getCurrentStockQuantityAttribute()
    {
        if (!$this->shouldTrackInventory()) {
            return null;
        }

        return $this->stocks()->sum('quantity_saleable');
    }

    /**
     * Get the current stock quantity for a specific warehouse.
     */
    public function getStockQuantityForWarehouse($warehouseId)
    {
        if (!$this->shouldTrackInventory()) {
            return null;
        }

        $stock = $this->stocks()->where('warehouse_id', $warehouseId)->first();
        return $stock ? $stock->quantity_saleable : 0;
    }

    /**
     * Check if the product has sufficient stock for a given quantity.
     */
    public function hasSufficientStock($quantity, $warehouseId = null)
    {
        if (!$this->shouldTrackInventory()) {
            return true; // Services don't need stock
        }

        if ($warehouseId) {
            $availableStock = $this->getStockQuantityForWarehouse($warehouseId);
        } else {
            $availableStock = $this->current_stock_quantity;
        }

        return $availableStock >= $quantity;
    }

    /**
     * Reserve stock for a sales order.
     */
    public function reserveStock($quantity, $warehouseId = null)
    {
        if (!$this->shouldTrackInventory()) {
            return true; // Services don't need stock reservation
        }

        if (!$this->hasSufficientStock($quantity, $warehouseId)) {
            return false;
        }

        if ($warehouseId) {
            $stock = $this->stocks()->where('warehouse_id', $warehouseId)->first();
        } else {
            // Use first available stock if no specific warehouse
            $stock = $this->stocks()->where('quantity_saleable', '>', 0)->first();
        }

        if ($stock) {
            $stock->quantity_reserve += $quantity;
            $stock->quantity_saleable -= $quantity;
            $stock->save();

            // Record stock history
            $this->recordStockHistory($stock, 'reserve', $quantity, 'Sales order reservation');
            return true;
        }

        return false;
    }

    /**
     * Reduce stock when a sale is confirmed/delivered.
     */
    public function reduceStock($quantity, $warehouseId = null, $reference = null)
    {
        if (!$this->shouldTrackInventory()) {
            return true; // Services don't reduce stock
        }

        if ($warehouseId) {
            $stock = $this->stocks()->where('warehouse_id', $warehouseId)->first();
        } else {
            // Use first available stock if no specific warehouse
            $stock = $this->stocks()->where('quantity_saleable', '>', 0)->first();
        }

        if ($stock) {
            $stock->quantity_total -= $quantity;
            $stock->quantity_reserve = max(0, $stock->quantity_reserve - $quantity);
            $stock->quantity_saleable = max(0, $stock->quantity_saleable - $quantity);
            $stock->save();

            // Record stock history
            $this->recordStockHistory($stock, 'sale', -$quantity, $reference ?? 'Sales order delivery');
            return true;
        }

        return false;
    }

    /**
     * Add stock when goods are received.
     */
    public function addStock($quantity, $warehouseId, $reference = null)
    {
        if (!$this->shouldTrackInventory()) {
            return true; // Services don't add stock
        }

        $stock = $this->stocks()->firstOrCreate([
            'warehouse_id' => $warehouseId,
            'company_id' => $this->company_id,
        ], [
            'quantity_total' => 0,
            'quantity_reserve' => 0,
            'quantity_saleable' => 0,
            'quantity_incoming' => 0,
        ]);

        // Capture values before changes for history
        $quantityTotalBefore = $stock->quantity_total;
        $quantityReserveBefore = $stock->quantity_reserve;
        $quantitySaleableBefore = $stock->quantity_saleable;
        $quantityIncomingBefore = $stock->quantity_incoming;

        $stock->quantity_total += $quantity;
        $stock->quantity_saleable += $quantity;
        $stock->save();

        // Record stock history with captured values
        $this->recordStockHistory(
            $stock, 
            'receipt', 
            $quantity, 
            $reference ?? 'Goods receipt',
            $quantityTotalBefore,
            $quantityReserveBefore,
            $quantitySaleableBefore,
            $quantityIncomingBefore
        );
        return true;
    }

    /**
     * Record stock history for tracking changes.
     */
    private function recordStockHistory($stock, $type, $quantityChange, $reference, $totalBefore = null, $reserveBefore = null, $saleableBefore = null, $incomingBefore = null)
    {
        // Use provided values or fall back to getOriginal for updates
        $totalBefore = $totalBefore ?? $stock->getOriginal('quantity_total');
        $reserveBefore = $reserveBefore ?? $stock->getOriginal('quantity_reserve');
        $saleableBefore = $saleableBefore ?? $stock->getOriginal('quantity_saleable');
        $incomingBefore = $incomingBefore ?? $stock->getOriginal('quantity_incoming');

        $stock->histories()->create([
            'quantity_total_before' => $totalBefore,
            'quantity_total_after' => $stock->quantity_total,
            'quantity_reserve_before' => $reserveBefore,
            'quantity_reserve_after' => $stock->quantity_reserve,
            'quantity_saleable_before' => $saleableBefore,
            'quantity_saleable_after' => $stock->quantity_saleable,
            'quantity_incoming_before' => $incomingBefore,
            'quantity_incoming_after' => $stock->quantity_incoming,
            'type' => $type,
            'reference' => $reference,
            'date' => now(),
        ]);
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
