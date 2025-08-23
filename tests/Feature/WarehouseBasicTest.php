<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WarehouseBasicTest extends TestCase
{
    use RefreshDatabase;

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
    public function warehouse_model_can_be_created()
    {
        $warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'code' => 'TEST001',
            'name' => 'Test Warehouse',
            'address' => 'Test Address'
        ]);

        $this->assertInstanceOf(Warehouse::class, $warehouse);
        $this->assertEquals('TEST001', $warehouse->code);
        $this->assertEquals('Test Warehouse', $warehouse->name);
        $this->assertEquals($this->company->id, $warehouse->company_id);
    }

    /** @test */
    public function warehouse_controller_store_method_works()
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
    public function warehouse_controller_update_method_works()
    {
        $warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'code' => 'TEST001',
            'name' => 'Test Warehouse',
            'address' => 'Test Address'
        ]);
        
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
    public function warehouse_controller_delete_method_works()
    {
        $warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'code' => 'TEST001',
            'name' => 'Test Warehouse',
            'address' => 'Test Address'
        ]);
        
        $response = $this->actingAs($this->user)
            ->delete(route('warehouses.delete', $warehouse));
        
        $response->assertRedirect(route('warehouses.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('warehouses', ['id' => $warehouse->id]);
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
}
