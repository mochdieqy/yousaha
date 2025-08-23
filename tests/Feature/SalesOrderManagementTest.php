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
        
        // Run permission seeder
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        
        // Create test data
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create([
            'owner' => $this->user->id
        ]);
        
        // Set current company in session
        session(['current_company_id' => $this->company->id]);
        
        // Assign sales order permissions to user
        $this->user->givePermissionTo([
            'sales-orders.view',
            'sales-orders.create',
            'sales-orders.edit',
            'sales-orders.delete',
            'sales-orders.generate-quotation',
            'sales-orders.generate-invoice'
        ]);
        
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
}
