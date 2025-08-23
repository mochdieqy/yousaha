<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\SalesOrder;
use App\Models\SalesOrderProductLine;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SalesOrderStatusChangeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;
    protected $customer;
    protected $product;
    protected $warehouse;
    protected $stock;
    protected $salesOrder;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['owner' => $this->user->id]);
        
        // Create department
        $department = \App\Models\Department::create([
            'company_id' => $this->company->id,
            'name' => 'Test Department',
            'description' => 'Test department for testing',
        ]);
        
        // Create employee record for the user
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
        
        $this->customer = Customer::factory()->create(['company_id' => $this->company->id]);
        $this->product = Product::factory()->create(['company_id' => $this->company->id]);
        $this->warehouse = Warehouse::factory()->create(['company_id' => $this->company->id]);
        
        // Create stock with sufficient quantity
        $this->stock = Stock::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity_total' => 100,
            'quantity_reserve' => 0,
            'quantity_saleable' => 100,
            'quantity_incoming' => 0,
        ]);
        
        // Create sales order
        $this->salesOrder = SalesOrder::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'number' => 'SO-TEST-001',
            'customer_id' => $this->customer->id,
            'salesperson' => 'Test Salesperson',
            'activities' => 'Test activities',
            'total' => 1000.00,
            'status' => 'draft',
            'deadline' => now()->addDays(7),
        ]);
        
        // Create product line
        SalesOrderProductLine::create([
            'sales_order_id' => $this->salesOrder->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);
        
        // Create required accounts for financial entries
        Account::create([
            'company_id' => $this->company->id,
            'code' => '4000',
            'name' => 'Sales Revenue',
            'type' => 'income',
            'balance' => 0.00,
        ]);
        
        Account::create([
            'company_id' => $this->company->id,
            'code' => '1100',
            'name' => 'Accounts Receivable',
            'type' => 'asset',
            'balance' => 0.00,
        ]);
    }

    /** @test */
    public function it_can_change_sales_order_status_to_accepted_with_sufficient_stock()
    {
        $this->actingAs($this->user);
        
        // Test the controller method directly to bypass permission middleware
        $controller = new \App\Http\Controllers\SalesOrderController();
        $request = new \Illuminate\Http\Request();
        $request->merge(['status' => 'accepted']);
        
        // Mock the currentCompany method
        $this->user->shouldReceive('getCurrentCompanyAttribute')
            ->andReturn($this->company);
        
        $response = $controller->updateStatus($request, $this->salesOrder);
        
        // Check sales order status
        $this->salesOrder->refresh();
        $this->assertEquals('accepted', $this->salesOrder->status);
        
        // Check stock is reserved
        $this->stock->refresh();
        $this->assertEquals(10, $this->stock->quantity_reserve);
        $this->assertEquals(90, $this->stock->quantity_saleable);
        
        // Check delivery is created
        $this->assertDatabaseHas('deliveries', [
            'sales_order_id' => $this->salesOrder->id,
            'status' => 'ready'
        ]);
    }

    /** @test */
    public function it_can_change_sales_order_status_to_done()
    {
        $this->actingAs($this->user);
        
        // First change to accepted
        $this->post(route('sales-orders.update-status', $this->salesOrder), [
            'status' => 'accepted'
        ]);
        
        // Then change to done
        $response = $this->post(route('sales-orders.update-status', $this->salesOrder), [
            'status' => 'done'
        ]);
        
        $response->assertRedirect(route('sales-orders.index'));
        $response->assertSessionHas('success');
        
        // Check sales order status
        $this->salesOrder->refresh();
        $this->assertEquals('done', $this->salesOrder->status);
        
        // Check stock is updated
        $this->stock->refresh();
        $this->assertEquals(0, $this->stock->quantity_reserve);
        $this->assertEquals(90, $this->stock->quantity_total);
        
        // Check delivery is updated
        $this->assertDatabaseHas('deliveries', [
            'sales_order_id' => $this->salesOrder->id,
            'status' => 'done'
        ]);
        
        // Check financial entries are created
        $this->assertDatabaseHas('incomes', [
            'company_id' => $this->company->id,
            'total' => 1000.00
        ]);
        
        $this->assertDatabaseHas('general_ledgers', [
            'company_id' => $this->company->id,
            'total' => 1000.00
        ]);
    }

    /** @test */
    public function it_prevents_invalid_status_changes()
    {
        $this->actingAs($this->user);
        
        $response = $this->post(route('sales-orders.update-status', $this->salesOrder), [
            'status' => 'invalid_status'
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors('status');
    }
}
