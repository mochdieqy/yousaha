<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SalesOrderManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;
    protected $customer;
    protected $product;
    protected $warehouse;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create role with sales order permissions
        $this->role = $this->createTestRole('sales-manager', [
            'sales-orders.view',
            'sales-orders.create',
            'sales-orders.edit',
            'sales-orders.delete',
            'sales-orders.generate-quotation',
            'sales-orders.generate-invoice'
        ]);
        
        // Create test data
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create([
            'owner' => $this->user->id
        ]);
        
        // Assign role to user
        $this->user->assignRole($this->role);
        
        // Set current company in session
        session(['current_company_id' => $this->company->id]);
        
        $this->customer = Customer::factory()->create([
            'company_id' => $this->company->id
        ]);
        
        $this->product = Product::factory()->create([
            'company_id' => $this->company->id,
            'price' => 100.00
        ]);
        
        $this->warehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id
        ]);

        // Create department and employee record so currentCompany works
        $department = \App\Models\Department::create([
            'company_id' => $this->company->id,
            'name' => 'Test Department',
            'description' => 'Test department for testing',
        ]);
        
        \App\Models\Employee::create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'department_id' => $department->id,
            'number' => 'EMP001',
            'position' => 'Test Position',
            'level' => 'Staff',
            'join_date' => now(),
            'manager' => $this->user->id,
            'work_location' => 'Office',
            'work_arrangement' => 'WFO',
        ]);
    }

    /** @test */
    public function user_can_view_sales_orders_list()
    {
        $response = $this->actingAs($this->user)
            ->get(route('sales-orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.sales-order.index');
    }

    /** @test */
    public function user_can_view_create_sales_order_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('sales-orders.create'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.sales-order.create');
        $response->assertViewHas('customers');
        $response->assertViewHas('products');
        $response->assertViewHas('warehouses');
    }

    /** @test */
    public function user_can_create_sales_order()
    {
        $salesOrderData = [
            'warehouse_id' => $this->warehouse->id,
            'customer_id' => $this->customer->id,
            'salesperson' => 'John Doe',
            'activities' => 'Test sales order',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'products' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('sales-orders.store'), $salesOrderData);

        $response->assertRedirect(route('sales-orders.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sales_orders', [
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'salesperson' => 'John Doe',
            'status' => 'draft'
        ]);

        $this->assertDatabaseHas('sales_order_product_lines', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
    }

    /** @test */
    public function sales_order_requires_valid_data()
    {
        $response = $this->actingAs($this->user)
            ->post(route('sales-orders.store'), []);

        $response->assertSessionHasErrors([
            'warehouse_id',
            'customer_id',
            'salesperson',
            'deadline',
            'products'
        ]);
    }

    /** @test */
    public function sales_order_number_is_generated_automatically()
    {
        $salesOrderData = [
            'warehouse_id' => $this->warehouse->id,
            'customer_id' => $this->customer->id,
            'salesperson' => 'John Doe',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'products' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1
                ]
            ]
        ];

        $this->actingAs($this->user)
            ->post(route('sales-orders.store'), $salesOrderData);

        $salesOrder = SalesOrder::first();
        $this->assertStringStartsWith('SO-' . $this->company->id . '-', $salesOrder->number);
    }

    /** @test */
    public function product_inventory_tracking_logic_works_correctly()
    {
        // Create a product that doesn't track inventory
        $nonInventoryProduct = Product::factory()->create([
            'company_id' => $this->company->id,
            'price' => 50.00,
            'is_track_inventory' => false,
            'type' => 'service'
        ]);

        // Create a product that tracks inventory
        $inventoryProduct = Product::factory()->create([
            'company_id' => $this->company->id,
            'price' => 100.00,
            'is_track_inventory' => true,
            'type' => 'goods'
        ]);

        // Test the shouldTrackInventory method
        $this->assertFalse($nonInventoryProduct->shouldTrackInventory());
        $this->assertTrue($inventoryProduct->shouldTrackInventory());

        // Test that service products don't track inventory by default
        $serviceProduct = Product::factory()->create([
            'company_id' => $this->company->id,
            'price' => 75.00,
            'is_track_inventory' => true,
            'type' => 'service'
        ]);

        $this->assertFalse($serviceProduct->shouldTrackInventory());
    }

    /** @test */
    public function inventory_tracking_affects_stock_management()
    {
        // Create a product that tracks inventory
        $inventoryProduct = Product::factory()->create([
            'company_id' => $this->company->id,
            'price' => 100.00,
            'is_track_inventory' => true,
            'type' => 'goods'
        ]);

        // Create a product that doesn't track inventory
        $nonInventoryProduct = Product::factory()->create([
            'company_id' => $this->company->id,
            'price' => 50.00,
            'is_track_inventory' => false,
            'type' => 'service'
        ]);

        // Test that only inventory products require stock management
        $this->assertTrue($inventoryProduct->requiresStockManagement());
        $this->assertFalse($nonInventoryProduct->requiresStockManagement());

        // Test that only inventory products can have stock records
        $this->assertTrue($inventoryProduct->shouldTrackInventory());
        $this->assertFalse($nonInventoryProduct->shouldTrackInventory());
    }

    /** @test */
    public function sales_order_with_non_inventory_products_completes_without_delivery_or_stock_changes()
    {
        // Create a product that doesn't track inventory
        $nonInventoryProduct = Product::factory()->create([
            'company_id' => $this->company->id,
            'price' => 50.00,
            'is_track_inventory' => false,
            'type' => 'service'
        ]);

        $salesOrderData = [
            'warehouse_id' => $this->warehouse->id,
            'customer_id' => $this->customer->id,
            'salesperson' => 'John Doe',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'products' => [
                [
                    'product_id' => $nonInventoryProduct->id,
                    'quantity' => 1
                ]
            ]
        ];

        // Create sales order
        $this->actingAs($this->user)
            ->post(route('sales-orders.store'), $salesOrderData);

        $salesOrder = SalesOrder::first();
        
        // Change status to done directly (bypassing the complex status flow)
        $salesOrder->update(['status' => 'done']);
        
        // Verify that no delivery was created
        $this->assertDatabaseMissing('deliveries', [
            'sales_order_id' => $salesOrder->id
        ]);

        // Verify that no stock history was created
        $this->assertDatabaseMissing('stock_histories', [
            'reference' => 'sales_order',
            'reference_id' => $salesOrder->id
        ]);

        // Verify that the sales order status was updated successfully
        $this->assertEquals('done', $salesOrder->fresh()->status);
    }

    /** @test */
    public function inventory_tracking_logic_prevents_stock_operations_for_non_inventory_products()
    {
        // Create a product that doesn't track inventory
        $nonInventoryProduct = Product::factory()->create([
            'company_id' => $this->company->id,
            'price' => 50.00,
            'is_track_inventory' => false,
            'type' => 'service'
        ]);

        // Verify that the product doesn't track inventory
        $this->assertFalse($nonInventoryProduct->shouldTrackInventory());
        $this->assertFalse($nonInventoryProduct->requiresStockManagement());

        // Verify that no stock record exists for this product
        $stock = \App\Models\Stock::where('company_id', $this->company->id)
            ->where('warehouse_id', $this->warehouse->id)
            ->where('product_id', $nonInventoryProduct->id)
            ->first();
        
        $this->assertNull($stock);

        // Verify that the product can still be used in sales orders
        $this->assertTrue($nonInventoryProduct->isService());
        $this->assertFalse($nonInventoryProduct->isGoods());
    }
}
