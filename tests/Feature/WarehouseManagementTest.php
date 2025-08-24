<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WarehouseManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;
    protected $role;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create role with warehouse permissions
        $this->role = $this->createTestRole('warehouse-manager', [
            'warehouses.view',
            'warehouses.create',
            'warehouses.edit',
            'warehouses.delete',
            'products.view' // Required for layout
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
    public function user_can_view_warehouse_list()
    {
        // Create some warehouses
        Warehouse::factory()->count(3)->create(['company_id' => $this->company->id]);
        
        $response = $this->actingAs($this->user)
            ->get(route('warehouses.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.warehouse.index');
        $response->assertViewHas('warehouses');
        $response->assertSee('Warehouse Management');
    }

    /** @test */
    public function user_can_view_create_warehouse_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('warehouses.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.warehouse.create');
        $response->assertSee('Create New Warehouse');
    }

    /** @test */
    public function user_can_create_warehouse()
    {
        $warehouseData = [
            'code' => 'TEST001',
            'name' => 'Test Warehouse',
            'address' => '123 Test Street, Test City'
        ];
        
        $response = $this->actingAs($this->user)
            ->post(route('warehouses.store'), $warehouseData);
        
        $response->assertRedirect(route('warehouses.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('warehouses', [
            'company_id' => $this->company->id,
            'code' => 'TEST001',
            'name' => 'Test Warehouse'
        ]);
    }

    /** @test */
    public function user_can_view_edit_warehouse_form()
    {
        $warehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id
        ]);
        
        $response = $this->actingAs($this->user)
            ->get(route('warehouses.edit', $warehouse));
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.warehouse.edit');
        $response->assertSee('Edit Warehouse');
        $response->assertViewHas('warehouse', $warehouse);
    }

    /** @test */
    public function user_can_update_warehouse()
    {
        $warehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id
        ]);
        
        $updateData = [
            'code' => 'UPDATED001',
            'name' => 'Updated Warehouse',
            'address' => '456 Updated Street, Updated City'
        ];
        
        $response = $this->actingAs($this->user)
            ->put(route('warehouses.update', $warehouse), $updateData);
        
        $response->assertRedirect(route('warehouses.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouse->id,
            'code' => 'UPDATED001',
            'name' => 'Updated Warehouse'
        ]);
    }

    /** @test */
    public function user_can_delete_warehouse()
    {
        $warehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id
        ]);
        
        $response = $this->actingAs($this->user)
            ->delete(route('warehouses.delete', $warehouse));
        
        $response->assertRedirect(route('warehouses.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('warehouses', [
            'id' => $warehouse->id
        ]);
    }

    /** @test */
    public function warehouse_validation_works()
    {
        $response = $this->actingAs($this->user)
            ->post(route('warehouses.store'), []);
        
        $response->assertSessionHasErrors(['code', 'name']);
    }

    /** @test */
    public function warehouse_code_must_be_unique()
    {
        // Create first warehouse
        Warehouse::factory()->create([
            'company_id' => $this->company->id,
            'code' => 'DUPLICATE'
        ]);
        
        // Try to create second warehouse with same code
        $response = $this->actingAs($this->user)
            ->post(route('warehouses.store'), [
                'code' => 'DUPLICATE',
                'name' => 'Second Warehouse',
                'address' => 'Test Address'
            ]);
        
        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function warehouse_search_functionality_works()
    {
        // Create warehouses with specific names
        Warehouse::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Main Warehouse'
        ]);
        
        Warehouse::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Storage Facility'
        ]);
        
        $response = $this->actingAs($this->user)
            ->get(route('warehouses.index', ['search' => 'Main']));
        
        $response->assertStatus(200);
        $response->assertSee('Main Warehouse');
        $response->assertDontSee('Storage Facility');
    }

    /** @test */
    public function warehouse_pagination_works()
    {
        // Create more warehouses than the pagination limit
        Warehouse::factory()->count(25)->create(['company_id' => $this->company->id]);
        
        $response = $this->actingAs($this->user)
            ->get(route('warehouses.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.warehouse.index');
        $response->assertViewHas('warehouses');
        
        // Check if pagination is working
        $warehouses = $response->viewData('warehouses');
        $this->assertLessThanOrEqual(15, $warehouses->count()); // Assuming 15 per page
    }

    /** @test */
    public function user_cannot_access_warehouse_without_permission()
    {
        // Create user without warehouse permissions
        $unauthorizedUser = User::factory()->create();
        $unauthorizedUser->assignRole($this->createTestRole('viewer', ['products.view']));
        
        $response = $this->actingAs($unauthorizedUser)
            ->get(route('warehouses.index'));
        
        $response->assertStatus(403);
    }

    /** @test */
    public function warehouse_belongs_to_company()
    {
        $otherCompany = Company::factory()->create();
        
        $warehouse = Warehouse::factory()->create([
            'company_id' => $otherCompany->id
        ]);
        
        $response = $this->actingAs($this->user)
            ->get(route('warehouses.edit', $warehouse));
        
        // The warehouse controller redirects to index with error message for unauthorized access
        $response->assertStatus(302);
        $response->assertRedirect(route('warehouses.index'));
    }
}
