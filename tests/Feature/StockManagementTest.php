<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\StockHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StockManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'stocks.view']);
        Permission::create(['name' => 'stocks.create']);
        Permission::create(['name' => 'stocks.edit']);
        Permission::create(['name' => 'stocks.delete']);
        
        // Create Company Owner role
        $companyOwnerRole = Role::create(['name' => 'Company Owner']);
        $companyOwnerRole->givePermissionTo(Permission::all());
    }

    /** @test */
    public function user_can_view_stock_list()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        // Set current company in session
        session(['current_company_id' => $company->id]);

        $response = $this->actingAs($user)->get('/stocks');

        $response->assertStatus(200);
        $response->assertViewIs('pages.stock.index');
    }

    /** @test */
    public function user_can_create_new_stock()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        // Set current company in session
        session(['current_company_id' => $company->id]);

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        $response = $this->actingAs($user)->get('/stocks/create');

        $response->assertStatus(200);
        $response->assertViewIs('pages.stock.create');
        $response->assertSee($warehouse->name);
        $response->assertSee($product->name);
    }

    /** @test */
    public function user_can_store_new_stock()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        // Set current company in session
        session(['current_company_id' => $company->id]);

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        $stockData = [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => 100,
            'quantity_reserve' => 10,
            'quantity_incoming' => 5,
        ];

        $response = $this->actingAs($user)->post('/stocks', $stockData);

        $response->assertRedirect('/stocks');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stocks', [
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => 100,
            'quantity_reserve' => 10,
            'quantity_saleable' => 90, // 100 - 10
            'quantity_incoming' => 5,
        ]);

        // Check if stock history was created
        $stock = Stock::where('product_id', $product->id)->first();
        $this->assertNotNull($stock);
        $this->assertEquals(1, $stock->histories()->count());
    }

    /** @test */
    public function user_can_edit_stock()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        // Set current company in session
        session(['current_company_id' => $company->id]);

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        $stock = Stock::factory()->create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => 100,
            'quantity_reserve' => 10,
            'quantity_saleable' => 90,
            'quantity_incoming' => 5,
        ]);

        $response = $this->actingAs($user)->get("/stocks/{$stock->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('pages.stock.edit');
        $response->assertSee($stock->quantity_total);
    }

    /** @test */
    public function user_can_update_stock()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        // Set current company in session
        session(['current_company_id' => $company->id]);

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        $stock = Stock::factory()->create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => 100,
            'quantity_reserve' => 10,
            'quantity_saleable' => 90,
            'quantity_incoming' => 5,
        ]);

        $updateData = [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => 150,
            'quantity_reserve' => 15,
            'quantity_incoming' => 10,
        ];

        $response = $this->actingAs($user)->put("/stocks/{$stock->id}", $updateData);

        $response->assertRedirect('/stocks');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stocks', [
            'id' => $stock->id,
            'quantity_total' => 150,
            'quantity_reserve' => 15,
            'quantity_saleable' => 135, // 150 - 15
            'quantity_incoming' => 10,
        ]);

        // Check if stock history was created
        $this->assertEquals(1, $stock->fresh()->histories()->count());
    }

    /** @test */
    public function user_can_view_stock_details()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        // Set current company in session
        session(['current_company_id' => $company->id]);

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        $stock = Stock::factory()->create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => 100,
            'quantity_reserve' => 10,
            'quantity_saleable' => 90,
            'quantity_incoming' => 5,
        ]);

        $response = $this->actingAs($user)->get("/stocks/{$stock->id}");

        $response->assertStatus(200);
        $response->assertViewIs('pages.stock.show');
        $response->assertSee($product->name);
        $response->assertSee($warehouse->name);
    }

    /** @test */
    public function user_can_delete_stock()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        // Set current company in session
        session(['current_company_id' => $company->id]);

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        $stock = Stock::factory()->create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => 100,
            'quantity_reserve' => 10,
            'quantity_saleable' => 90,
            'quantity_incoming' => 5,
        ]);

        $response = $this->actingAs($user)->delete("/stocks/{$stock->id}");

        $response->assertRedirect('/stocks');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }

    /** @test */
    public function stock_prevents_duplicate_product_warehouse_combination()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        // Set current company in session
        session(['current_company_id' => $company->id]);

        $warehouse = Warehouse::factory()->create([
            'company_id' => $company->id,
        ]);

        $product = Product::factory()->create([
            'company_id' => $company->id,
            'type' => 'goods',
            'is_track_inventory' => true,
        ]);

        // Create first stock
        Stock::create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => 100,
            'quantity_reserve' => 0,
            'quantity_saleable' => 100,
            'quantity_incoming' => 0,
        ]);

        // Try to create duplicate stock
        $stockData = [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity_total' => 50,
        ];

        $response = $this->actingAs($user)->post('/stocks', $stockData);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertEquals(1, Stock::where('product_id', $product->id)->count());
    }
}
