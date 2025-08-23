<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'products.view']);
        Permission::create(['name' => 'products.create']);
        Permission::create(['name' => 'products.edit']);
        Permission::create(['name' => 'products.delete']);
        
        // Create Company Owner role
        $companyOwnerRole = Role::create(['name' => 'Company Owner']);
        $companyOwnerRole->givePermissionTo(Permission::all());
    }

    /** @test */
    public function user_can_view_products_list_with_permission()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);
        $response->assertViewIs('pages.product.index');
    }

    /** @test */
    public function user_cannot_view_products_without_permission()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_create_product_with_permission()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $response = $this->actingAs($user)->get('/products/create');

        $response->assertStatus(200);
        $response->assertViewIs('pages.product.create');
    }

    /** @test */
    public function user_can_store_product_with_valid_data()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');

        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST001',
            'type' => 'goods',
            'price' => 99.99,
            'taxes' => 5.00,
            'cost' => 50.00,
            'is_track_inventory' => true,
            'is_shrink' => false,
        ];

        $response = $this->actingAs($user)->post('/products', $productData);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST001',
            'company_id' => $company->id,
        ]);
    }

    /** @test */
    public function user_can_edit_product_with_permission()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        $product = Product::factory()->create([
            'company_id' => $company->id,
            'name' => 'Test Product',
            'sku' => 'TEST001',
        ]);

        $response = $this->actingAs($user)->get("/products/{$product->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('pages.product.edit');
    }

    /** @test */
    public function user_can_update_product_with_valid_data()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        $product = Product::factory()->create([
            'company_id' => $company->id,
            'name' => 'Test Product',
            'sku' => 'TEST001',
        ]);

        $updateData = [
            'name' => 'Updated Product',
            'sku' => 'TEST001',
            'type' => 'goods',
            'price' => 149.99,
            'taxes' => 10.00,
            'cost' => 75.00,
            'is_track_inventory' => true,
            'is_shrink' => false,
        ];

        $response = $this->actingAs($user)->put("/products/{$product->id}", $updateData);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 149.99,
        ]);
    }

    /** @test */
    public function user_can_delete_product_with_permission()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        $product = Product::factory()->create([
            'company_id' => $company->id,
            'name' => 'Test Product',
            'sku' => 'TEST001',
        ]);

        $response = $this->actingAs($user)->delete("/products/{$product->id}");

        $response->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function product_sku_must_be_unique_within_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner' => $user->id]);
        $user->assignRole('Company Owner');
        
        // Create first product
        Product::factory()->create([
            'company_id' => $company->id,
            'sku' => 'TEST001',
        ]);

        // Try to create second product with same SKU
        $productData = [
            'name' => 'Duplicate Product',
            'sku' => 'TEST001',
            'type' => 'goods',
            'price' => 99.99,
        ];

        $response = $this->actingAs($user)->post('/products', $productData);

        $response->assertSessionHasErrors('sku');
        $this->assertDatabaseCount('products', 1);
    }
}
