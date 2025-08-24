<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Delivery;
use App\Models\DeliveryProductLine;
use App\Models\DeliveryStatusLog;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeliveryManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;
    protected $warehouse;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create role with delivery permissions
        $this->role = $this->createTestRole('delivery-manager', [
            'deliveries.view',
            'deliveries.create',
            'deliveries.edit',
            'deliveries.delete'
        ]);
        
        // Create a user with delivery permissions
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['owner' => $this->user->id]);
        $this->warehouse = Warehouse::factory()->create(['company_id' => $this->company->id]);
        $this->product = Product::factory()->create(['company_id' => $this->company->id]);
        
        // Assign role to user
        $this->user->assignRole($this->role);
        
        // Set current company in session
        session(['current_company_id' => $this->company->id]);
        
        // Create stock for the product
        Stock::factory()->create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity_total' => 100,
            'quantity_reserve' => 0,
            'quantity_saleable' => 100,
            'quantity_incoming' => 0
        ]);
    }

    /** @test */
    public function user_can_view_delivery_list()
    {
        // Create a delivery
        $delivery = Delivery::factory()->create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('deliveries.index'));

        $response->assertStatus(200);
        $response->assertSee($delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT));
    }

    /** @test */
    public function user_can_create_delivery()
    {
        $deliveryData = [
            'warehouse_id' => $this->warehouse->id,
            'delivery_address' => $this->faker->address,
            'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i'),
            'reference' => 'TEST-DEL-001',
            'products' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('deliveries.store'), $deliveryData);

        $response->assertRedirect(route('deliveries.index'));
        $this->assertDatabaseHas('deliveries', [
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'ready' // Status is automatically updated to 'ready' if stock is available
        ]);
        
        $this->assertDatabaseHas('delivery_product_lines', [
            'product_id' => $this->product->id,
            'quantity' => 10
        ]);
    }

    /** @test */
    public function user_can_update_delivery_status()
    {
        $delivery = Delivery::factory()->create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('deliveries.update-status', $delivery), [
                'status' => 'waiting'
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'waiting'
        ]);
        
        $this->assertDatabaseHas('delivery_status_logs', [
            'delivery_id' => $delivery->id,
            'status' => 'waiting'
        ]);
    }

    /** @test */
    public function user_can_process_goods_issue()
    {
        $delivery = Delivery::factory()->create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'ready'
        ]);

        // Create product line
        DeliveryProductLine::create([
            'delivery_id' => $delivery->id,
            'product_id' => $this->product->id,
            'quantity' => 10
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('deliveries.goods-issue', $delivery), [
                'validate' => true
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'done'
        ]);
        
        // Check if stock was updated
        $this->assertDatabaseHas('stocks', [
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity_total' => 90 // 100 - 10
        ]);
    }

    /** @test */
    public function delivery_cannot_be_processed_if_not_ready()
    {
        $delivery = Delivery::factory()->create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('deliveries.goods-issue', $delivery), [
                'validate' => true
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function delivery_cannot_be_edited_if_not_draft_or_waiting()
    {
        $delivery = Delivery::factory()->create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'done'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('deliveries.edit', $delivery));

        $response->assertRedirect();
    }

    /** @test */
    public function delivery_cannot_be_deleted_if_not_draft()
    {
        $delivery = Delivery::factory()->create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'waiting'
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('deliveries.delete', $delivery));

        $response->assertRedirect();
        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id
        ]);
    }
}
