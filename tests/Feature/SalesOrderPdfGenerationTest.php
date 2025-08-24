<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\SalesOrder;
use App\Models\SalesOrderProductLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SalesOrderPdfGenerationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;
    protected $customer;
    protected $product;
    protected $warehouse;
    protected $salesOrder;

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

        // Create a sales order
        $this->salesOrder = SalesOrder::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'number' => 'SO-' . $this->company->id . '-000001',
            'customer_id' => $this->customer->id,
            'salesperson' => 'John Doe',
            'activities' => 'Test sales order',
            'total' => 200.00,
            'status' => 'draft',
            'deadline' => now()->addDays(7),
        ]);

        // Create product line
        SalesOrderProductLine::create([
            'sales_order_id' => $this->salesOrder->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function user_can_generate_quotation_pdf()
    {
        $response = $this->actingAs($this->user)
            ->post(route('sales-orders.generate-quotation', $this->salesOrder));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=quotation-' . $this->salesOrder->number . '.pdf');
    }

    /** @test */
    public function user_can_generate_invoice_pdf()
    {
        // Change status to allow invoice generation
        $this->salesOrder->update(['status' => 'waiting']);

        $response = $this->actingAs($this->user)
            ->post(route('sales-orders.generate-invoice', $this->salesOrder));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=invoice-' . $this->salesOrder->number . '.pdf');
    }

    /** @test */
    public function quotation_cannot_be_generated_for_non_draft_orders()
    {
        // Change status to non-draft
        $this->salesOrder->update(['status' => 'waiting']);

        $response = $this->actingAs($this->user)
            ->post(route('sales-orders.generate-quotation', $this->salesOrder));

        $response->assertRedirect(route('sales-orders.index'));
        $response->assertSessionHas('error', 'Quotation can only be generated for draft sales orders.');
    }

    /** @test */
    public function invoice_cannot_be_generated_for_draft_orders()
    {
        $response = $this->actingAs($this->user)
            ->post(route('sales-orders.generate-invoice', $this->salesOrder));

        $response->assertRedirect(route('sales-orders.index'));
        $response->assertSessionHas('error', 'Invoice cannot be generated for draft sales orders.');
    }
}
