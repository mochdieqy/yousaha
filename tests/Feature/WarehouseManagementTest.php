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
        
        // Create permissions
        Permission::create(['name' => 'warehouses.view']);
        Permission::create(['name' => 'warehouses.create']);
        Permission::create(['name' => 'warehouses.edit']);
        Permission::create(['name' => 'warehouses.delete']);
        
        // Create role and assign permissions
        $this->role = Role::create(['name' => 'warehouse-manager']);
        $this->role->givePermissionTo([
            'warehouses.view',
            'warehouses.create',
            'warehouses.edit',
            'warehouses.delete'
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
    public function warehouse_code_must_be_unique_per_company()
    {
        // Create existing warehouse
        Warehouse::factory()->create([
            'company_id' => $this->company->id,
            'code' => 'TEST001'
        ]);
        
        $warehouseData = [
            'code' => 'TEST001', // Same code
            'name' => 'Another Warehouse',
            'address' => '456 Another Street'
        ];
        
        $response = $this->actingAs($this->user)
            ->post(route('warehouses.store'), $warehouseData);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors('code');
    }

    /** @test */
    public function user_can_view_edit_warehouse_form()
    {
        $warehouse = Warehouse::factory()->create(['company_id' => $this->company->id]);
        
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
        $warehouse = Warehouse::factory()->create(['company_id' => $this->company->id]);
        
        $updateData = [
            'code' => 'UPDATED001',
            'name' => 'Updated Warehouse Name',
            'address' => 'Updated Address'
        ];
        
        $response = $this->actingAs($this->user)
            ->put(route('warehouses.update', $warehouse), $updateData);
        
        $response->assertRedirect(route('warehouses.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouse->id,
            'code' => 'UPDATED001',
            'name' => 'Updated Warehouse Name'
        ]);
    }

    /** @test */
    public function user_can_delete_warehouse()
    {
        $warehouse = Warehouse::factory()->create(['company_id' => $this->company->id]);
        
        $response = $this->actingAs($this->user)
            ->delete(route('warehouses.delete', $warehouse));
        
        $response->assertRedirect(route('warehouses.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('warehouses', ['id' => $warehouse->id]);
    }

    /** @test */
    public function user_cannot_access_warehouse_from_different_company()
    {
        $otherCompany = Company::factory()->create();
        $warehouse = Warehouse::factory()->create(['company_id' => $otherCompany->id]);
        
        $response = $this->actingAs($this->user)
            ->get(route('warehouses.edit', $warehouse));
        
        $response->assertRedirect(route('warehouses.index'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function warehouse_search_functionality_works()
    {
        // Create warehouses with different names
        Warehouse::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Main Warehouse',
            'code' => 'MAIN'
        ]);
        
        Warehouse::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Storage Facility',
            'code' => 'STOR'
        ]);
        
        $response = $this->actingAs($this->user)
            ->get(route('warehouses.index', ['search' => 'Main']));
        
        $response->assertStatus(200);
        $response->assertSee('Main Warehouse');
        $response->assertDontSee('Storage Facility');
    }

    /** @test */
    public function warehouse_validation_works()
    {
        $invalidData = [
            'code' => '', // Required
            'name' => '', // Required
            'address' => str_repeat('a', 1001) // Too long
        ];
        
        $response = $this->actingAs($this->user)
            ->post(route('warehouses.store'), $invalidData);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['code', 'name', 'address']);
    }

    /** @test */
    public function warehouse_code_format_validation_works()
    {
        $invalidData = [
            'code' => 'TEST@001', // Contains invalid character @
            'name' => 'Test Warehouse',
            'address' => 'Test Address'
        ];
        
        $response = $this->actingAs($this->user)
            ->post(route('warehouses.store'), $invalidData);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors('code');
    }

    /** @test */
    public function warehouse_pagination_works()
    {
        // Create more than 15 warehouses (default pagination)
        Warehouse::factory()->count(20)->create(['company_id' => $this->company->id]);
        
        $response = $this->actingAs($this->user)
            ->get(route('warehouses.index'));
        
        $response->assertStatus(200);
        $response->assertViewHas('warehouses');
        
        // Check if pagination is present
        $warehouses = $response->viewData('warehouses');
        $this->assertTrue($warehouses->hasPages());
    }
}
