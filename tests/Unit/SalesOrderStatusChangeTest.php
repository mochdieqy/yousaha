<?php

namespace Tests\Unit;

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
use App\Models\Department;
use App\Models\Employee;
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
        $department = Department::create([
            'company_id' => $this->company->id,
            'name' => 'Test Department',
            'description' => 'Test department for testing',
        ]);
        
        // Create employee record for the user
        Employee::create([
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
        $this->product = Product::factory()->create([
            'company_id' => $this->company->id,
            'type' => 'goods',
            'is_track_inventory' => true
        ]);
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
    public function it_can_check_stock_availability_for_sales_order()
    {
        $controller = new \App\Http\Controllers\SalesOrderController();
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('checkStockAvailability');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, $this->salesOrder, $this->company);
        
        $this->assertTrue($result['available']);
        $this->assertEmpty($result['insufficient_products']);
        $this->assertArrayHasKey($this->product->id, $result['stock_data']);
    }

    /** @test */
    public function it_can_process_accepted_sales_order()
    {
        $controller = new \App\Http\Controllers\SalesOrderController();
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('processAcceptedSalesOrder');
        $method->setAccessible(true);
        
        $stockData = [
            $this->product->id => [
                'stock' => $this->stock,
                'required_quantity' => 10
            ]
        ];
        
        $method->invoke($controller, $this->salesOrder, $this->company, $stockData);
        
        // Check delivery is created
        $this->assertDatabaseHas('deliveries', [
            'sales_order_id' => $this->salesOrder->id,
            'status' => 'ready'
        ]);
        
        // Check stock is updated
        $this->stock->refresh();
        $this->assertEquals(10, $this->stock->quantity_reserve);
        $this->assertEquals(90, $this->stock->quantity_saleable);
        
        // Check stock history is created
        $this->assertDatabaseHas('stock_histories', [
            'product_id' => $this->product->id,
            'type' => 'reserve',
            'reference' => 'sales_order',
        ]);
    }

    /** @test */
    public function it_can_process_done_sales_order()
    {
        $controller = new \App\Http\Controllers\SalesOrderController();
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('handleDoneStatus');
        $method->setAccessible(true);
        
        $method->invoke($controller, $this->salesOrder, $this->company);
        
        // Check delivery is created
        $this->assertDatabaseHas('deliveries', [
            'sales_order_id' => $this->salesOrder->id,
            'status' => 'done'
        ]);
        
        // Check stock is updated
        $this->stock->refresh();
        $this->assertEquals(0, $this->stock->quantity_reserve);
        $this->assertEquals(90, $this->stock->quantity_total);
        
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
    public function it_handles_insufficient_stock_correctly()
    {
        // Update stock to have insufficient quantity
        $this->stock->update([
            'quantity_saleable' => 5 // Less than required 10
        ]);
        
        // Refresh the sales order to ensure relationships are loaded
        $this->salesOrder->load(['productLines.product']);
        
        $controller = new \App\Http\Controllers\SalesOrderController();
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('handleAcceptedStatus');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, $this->salesOrder, $this->company);
        
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Insufficient stock', $result['message']);
        
        // Check sales order status is changed to waiting
        $this->salesOrder->refresh();
        $this->assertEquals('waiting', $this->salesOrder->status);
    }
}
