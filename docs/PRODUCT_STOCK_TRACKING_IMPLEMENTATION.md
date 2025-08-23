# Product Stock Tracking Implementation

## Overview

This document describes the implementation of the Product Stock Tracking system in the Yousaha ERP application. The system automatically handles stock management based on product type and inventory tracking settings, ensuring that service products don't require stock management while goods and combo products can track inventory properly.

## Key Concepts

### 1. Product Types and Inventory Tracking

#### **Goods Products** (`type = 'goods'`)
- **Inventory Tracking**: Can be enabled/disabled via `is_track_inventory`
- **Stock Management**: Full stock tracking when `is_track_inventory = true`
- **Stock Operations**: Add, reduce, reserve, and track stock levels
- **Sales Impact**: Sales reduce stock levels when inventory tracking is enabled

#### **Service Products** (`type = 'service'`)
- **Inventory Tracking**: **Always disabled** regardless of `is_track_inventory` setting
- **Stock Management**: No stock tracking required
- **Stock Operations**: All stock operations are ignored
- **Sales Impact**: Sales do not affect stock levels

#### **Combo Products** (`type = 'combo'`)
- **Inventory Tracking**: Can be enabled/disabled via `is_track_inventory`
- **Stock Management**: Full stock tracking when `is_track_inventory = true`
- **Stock Operations**: Add, reduce, reserve, and track stock levels
- **Sales Impact**: Sales reduce stock levels when inventory tracking is enabled

### 2. Core Logic

The system uses the `shouldTrackInventory()` method to determine if a product should track inventory:

```php
public function shouldTrackInventory()
{
    return $this->is_track_inventory && !$this->isService();
}
```

**Key Points:**
- Service products **never** track inventory, even if `is_track_inventory` is set to `true`
- Goods and combo products track inventory only when `is_track_inventory = true`
- This ensures business logic consistency and prevents invalid configurations

## Implementation Details

### 1. Product Model Enhancements

#### **New Methods Added:**

```php
// Core inventory tracking logic
public function shouldTrackInventory()
public function requiresStockManagement()

// Stock quantity management
public function getCurrentStockQuantityAttribute()
public function getStockQuantityForWarehouse($warehouseId)
public function hasSufficientStock($quantity, $warehouseId = null)

// Stock operations
public function reserveStock($quantity, $warehouseId = null)
public function reduceStock($quantity, $warehouseId = null, $reference = null)
public function addStock($quantity, $warehouseId, $reference = null)

// Stock history recording
private function recordStockHistory($stock, $type, $quantityChange, $reference, ...)
```

#### **Stock Operations Behavior:**

| Product Type | `is_track_inventory` | Stock Operations | Sales Impact |
|--------------|---------------------|------------------|--------------|
| Goods | `true` | ✅ Full tracking | ✅ Reduces stock |
| Goods | `false` | ❌ No tracking | ❌ No stock impact |
| Service | `true`/`false` | ❌ Always ignored | ❌ No stock impact |
| Combo | `true` | ✅ Full tracking | ✅ Reduces stock |
| Combo | `false` | ❌ No tracking | ❌ No stock impact |

### 2. Stock Management Workflow

#### **Adding Stock (Goods Receipt)**
```php
$product->addStock(100, $warehouseId, 'Initial stock');
```
- Creates stock record if it doesn't exist
- Increases `quantity_total` and `quantity_saleable`
- Records stock history with before/after values

#### **Reserving Stock (Sales Order)**
```php
$product->reserveStock(30, $warehouseId);
```
- Checks if sufficient stock is available
- Increases `quantity_reserve`
- Decreases `quantity_saleable`
- Records reservation in stock history

#### **Reducing Stock (Sales Delivery)**
```php
$product->reduceStock(20, $warehouseId, 'Sales order delivery');
```
- Decreases `quantity_total`
- Decreases `quantity_reserve` (if available)
- Decreases `quantity_saleable`
- Records sale in stock history

### 3. Stock History Tracking

Every stock operation is recorded in the `stock_histories` table with:

- **Before/After Values**: All quantity fields are recorded before and after changes
- **Operation Type**: `receipt`, `reserve`, `sale`, etc.
- **Reference**: Human-readable description of the operation
- **Timestamp**: When the operation occurred

#### **History Record Structure:**
```php
[
    'quantity_total_before' => 0,
    'quantity_total_after' => 100,
    'quantity_reserve_before' => 0,
    'quantity_reserve_after' => 0,
    'quantity_saleable_before' => 0,
    'quantity_saleable_after' => 100,
    'type' => 'receipt',
    'reference' => 'Initial stock',
    'date' => '2025-08-23'
]
```

## User Interface Enhancements

### 1. Form Validation

#### **Service Product Restrictions:**
- Inventory tracking checkbox is automatically disabled for service products
- Warning message displayed: "Service products cannot track inventory"
- Server-side validation prevents invalid configurations

#### **JavaScript Integration:**
```javascript
function toggleInventoryTracking() {
    const productType = document.getElementById('type').value;
    const inventoryCheckbox = document.getElementById('is_track_inventory');
    
    if (productType === 'service') {
        inventoryCheckbox.checked = false;
        inventoryCheckbox.disabled = true;
        // Show warning message
    } else {
        inventoryCheckbox.disabled = false;
        // Hide warning message
    }
}
```

### 2. Product List Display

#### **Inventory Status Indicators:**
- **Tracked Products**: Show current stock quantity
- **Non-Tracked Products**: Display appropriate status (e.g., "Service Product")
- **Visual Icons**: Different icons for different product types

#### **Stock Information:**
```php
@if($product->shouldTrackInventory())
    <span class="badge bg-info">
        <i class="fas fa-chart-bar me-1"></i>
        Tracked
    </span>
    @if($product->current_stock_quantity !== null)
        <small class="text-muted mt-1">
            Stock: {{ $product->current_stock_quantity }}
        </small>
    @endif
@else
    <span class="badge bg-secondary">
        <i class="fas fa-times me-1"></i>
        Not Tracked
    </span>
    @if($product->isService())
        <small class="text-muted mt-1">Service Product</small>
    @endif
@endif
```

## Business Logic Examples

### 1. Service Product Sales

```php
// Service product - no stock impact
$serviceProduct = Product::where('type', 'service')->first();
$serviceProduct->reduceStock(100, $warehouseId, 'Service delivery');
// Returns true, no stock changes made
// $serviceProduct->current_stock_quantity remains null
```

### 2. Goods Product Sales

```php
// Goods product with inventory tracking
$goodsProduct = Product::where('type', 'goods')
    ->where('is_track_inventory', true)
    ->first();

// Check stock availability
if ($goodsProduct->hasSufficientStock(50, $warehouseId)) {
    // Reserve stock for sales order
    $goodsProduct->reserveStock(50, $warehouseId);
    
    // Later, reduce stock when delivered
    $goodsProduct->reduceStock(50, $warehouseId, 'Sales order #123');
}
```

### 3. Stock Level Monitoring

```php
// Get current stock across all warehouses
$totalStock = $product->current_stock_quantity;

// Get stock for specific warehouse
$warehouseStock = $product->getStockQuantityForWarehouse($warehouseId);

// Check if stock is sufficient for order
$canFulfill = $product->hasSufficientStock($orderQuantity, $warehouseId);
```

## Testing

### 1. Test Coverage

The implementation includes comprehensive tests covering:

- **Product Type Validation**: Service products cannot track inventory
- **Stock Operations**: Add, reserve, reduce stock for goods products
- **Service Product Behavior**: Stock operations are ignored for services
- **Stock History**: Proper recording of all stock changes
- **Multi-Warehouse Support**: Stock management across multiple locations

### 2. Test Scenarios

```bash
# Run stock tracking tests
php artisan test tests/Feature/ProductStockTrackingTest.php

# Run all product management tests
php artisan test tests/Feature/ProductManagementTest.php
```

## Integration Points

### 1. Sales Order Processing

When a sales order is created:
1. **Check Stock**: Verify sufficient stock for tracked products
2. **Reserve Stock**: Reserve quantities for tracked products
3. **Ignore Stock**: Skip stock operations for service products

### 2. Delivery Processing

When goods are delivered:
1. **Reduce Stock**: Decrease stock levels for tracked products
2. **Update History**: Record stock reduction in history
3. **No Impact**: Service products remain unaffected

### 3. Purchase Receipts

When goods are received:
1. **Add Stock**: Increase stock levels for tracked products
2. **Update History**: Record stock addition in history
3. **No Impact**: Service products remain unaffected

## Future Enhancements

### 1. Advanced Stock Management
- **Stock Transfers**: Move stock between warehouses
- **Stock Adjustments**: Manual stock corrections
- **Stock Valuations**: Cost-based stock valuation
- **Stock Aging**: Track stock age and expiration

### 2. Inventory Optimization
- **Reorder Points**: Automatic reorder notifications
- **Safety Stock**: Maintain minimum stock levels
- **ABC Analysis**: Categorize products by importance
- **Demand Forecasting**: Predict future stock needs

### 3. Reporting and Analytics
- **Stock Reports**: Current stock levels by warehouse
- **Movement Reports**: Stock in/out over time periods
- **Turnover Analysis**: Stock rotation rates
- **Dead Stock**: Identify slow-moving inventory

## Conclusion

The Product Stock Tracking system provides a robust, business-logic-aware approach to inventory management. By automatically handling the distinction between service and physical products, it ensures data integrity while providing comprehensive stock tracking for products that require it.

**Key Benefits:**
- **Automatic Logic**: Service products never track inventory
- **Flexible Configuration**: Goods and combo products can be configured as needed
- **Comprehensive Tracking**: Full stock history and warehouse support
- **Business Rules**: Enforces logical product configurations
- **Scalable Architecture**: Supports future enhancements and integrations

The system is production-ready and provides a solid foundation for comprehensive ERP inventory management.
