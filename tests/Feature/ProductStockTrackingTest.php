<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProductStockTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create role with product permissions
        $this->role = $this->createTestRole('product-manager', [
            'products.view',
            'products.create',
            'products.edit',
            'products.delete'
        ]);
        
        // Create user and company
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        
        // Assign role to user
        $this->user->assignRole($this->role);
        
        // Set current company for user by making user own the company
        $this->company->owner = $this->user->id;
        $this->company->save();
    }

    /** @test */
    public function goods_product_can_track_inventory()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        $this->assertTrue($product->shouldTrackInventory());
        $this->assertTrue($product->requiresStockManagement());
        $this->assertFalse($product->isService());
    }

    /** @test */
    public function service_product_cannot_track_inventory()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'service',
            'is_track_inventory' => false, // Even if set to true, should be ignored
        ]);

        $this->assertFalse($product->shouldTrackInventory());
        $this->assertFalse($product->requiresStockManagement());
        $this->assertTrue($product->isService());
    }

    /** @test */
    public function combo_product_can_track_inventory()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'combo',
            'is_track_inventory' => true,
        ]);

        $this->assertTrue($product->shouldTrackInventory());
        $this->assertTrue($product->requiresStockManagement());
        $this->assertFalse($product->isService());
    }

    /** @test */
    public function service_product_always_has_sufficient_stock()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'service',
            'is_track_inventory' => false,
        ]);

        // Service products should always have sufficient stock regardless of quantity
        $this->assertTrue($product->hasSufficientStock(1000));
        $this->assertTrue($product->hasSufficientStock(0));
        $this->assertTrue($product->hasSufficientStock(-100));
    }

    /** @test */
    public function goods_product_stock_management_works_correctly()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
            'name' => 'Main Warehouse',
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        // Initially no stock
        $this->assertEquals(0, $product->current_stock_quantity);

        // Add stock
        $product->addStock(100, $warehouse->id, 'Initial stock');
        $this->assertEquals(100, $product->current_stock_quantity);

        // Check warehouse-specific stock
        $this->assertEquals(100, $product->getStockQuantityForWarehouse($warehouse->id));

        // Check sufficient stock
        $this->assertTrue($product->hasSufficientStock(50));
        $this->assertFalse($product->hasSufficientStock(150));

        // Reserve stock
        $this->assertTrue($product->reserveStock(30, $warehouse->id));
        $this->assertEquals(70, $product->current_stock_quantity);

        // Reduce stock
        $this->assertTrue($product->reduceStock(20, $warehouse->id, 'Sales order delivery'));
        $this->assertEquals(50, $product->current_stock_quantity);
    }

    /** @test */
    public function service_product_stock_operations_are_ignored()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
            'name' => 'Main Warehouse',
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'service',
            'is_track_inventory' => false,
        ]);

        // Service products should ignore all stock operations
        $this->assertTrue($product->addStock(100, $warehouse->id, 'Service stock'));
        $this->assertTrue($product->reserveStock(50, $warehouse->id));
        $this->assertTrue($product->reduceStock(30, $warehouse->id, 'Service delivery'));

        // Stock quantity should remain null for services
        $this->assertNull($product->current_stock_quantity);
    }

    /** @test */
    public function product_stock_history_is_recorded_correctly()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
            'name' => 'Main Warehouse',
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        // Add stock and check history
        $product->addStock(100, $warehouse->id, 'Initial stock');
        $stock = $product->stocks()->where('warehouse_id', $warehouse->id)->first();
        
        $this->assertNotNull($stock);
        $this->assertEquals(1, $stock->histories()->count());
        
        $history = $stock->histories()->first();
        $this->assertEquals('receipt', $history->type);
        $this->assertEquals(100, $history->total_quantity_change);
        $this->assertEquals('Initial stock', $history->reference);
    }

    /** @test */
    public function product_type_validation_prevents_service_with_inventory_tracking()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $response = $this->actingAs($user)->post('/products', [
            'name' => 'Test Service',
            'sku' => 'SERVICE001',
            'type' => 'service',
            'is_track_inventory' => true, // This should cause validation error
            'price' => 100.00,
            'taxes' => 0,
            'cost' => 50.00,
        ]);

        $response->assertSessionHasErrors('is_track_inventory');
        $this->assertDatabaseMissing('products', ['sku' => 'SERVICE001']);
    }

    /** @test */
    public function product_type_validation_allows_goods_with_inventory_tracking()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $response = $this->actingAs($user)->post('/products', [
            'name' => 'Test Goods',
            'sku' => 'GOODS001',
            'type' => 'goods',
            'is_track_inventory' => true,
            'price' => 100.00,
            'taxes' => 0,
            'cost' => 50.00,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['sku' => 'GOODS001']);
    }

    /** @test */
    public function product_stock_operations_handle_multiple_warehouses()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $warehouse1 = Warehouse::factory()->create([
            'company_id' => $company->id,
            'name' => 'Warehouse A',
        ]);

        $warehouse2 = Warehouse::factory()->create([
            'company_id' => $company->id,
            'name' => 'Warehouse B',
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        // Add stock to both warehouses
        $product->addStock(100, $warehouse1->id, 'Stock A');
        $product->addStock(50, $warehouse2->id, 'Stock B');

        // Check total stock across all warehouses
        $this->assertEquals(150, $product->current_stock_quantity);

        // Check warehouse-specific stock
        $this->assertEquals(100, $product->getStockQuantityForWarehouse($warehouse1->id));
        $this->assertEquals(50, $product->getStockQuantityForWarehouse($warehouse2->id));

        // Reduce stock from specific warehouse
        $product->reduceStock(30, $warehouse1->id, 'Sales from A');
        $this->assertEquals(70, $product->getStockQuantityForWarehouse($warehouse1->id));
        $this->assertEquals(50, $product->getStockQuantityForWarehouse($warehouse2->id));
        $this->assertEquals(120, $product->current_stock_quantity);
    }
}
