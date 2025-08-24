<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Only set up permissions if the permissions table exists
        try {
            // Clear permission cache
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            
            // Check if permissions table exists
            if (Schema::hasTable('permissions')) {
                // Create all necessary permissions for testing
                $this->createTestPermissions();
            }
        } catch (\Exception $e) {
            // If permissions table doesn't exist, skip permission setup
            // This can happen during initial test runs
        }
    }

    protected function createTestPermissions(): void
    {
        // Master Data permissions
        $this->createPermissionIfNotExists('products.view');
        $this->createPermissionIfNotExists('products.create');
        $this->createPermissionIfNotExists('products.edit');
        $this->createPermissionIfNotExists('products.delete');
        
        $this->createPermissionIfNotExists('customers.view');
        $this->createPermissionIfNotExists('customers.create');
        $this->createPermissionIfNotExists('customers.edit');
        $this->createPermissionIfNotExists('customers.delete');
        
        $this->createPermissionIfNotExists('suppliers.view');
        $this->createPermissionIfNotExists('suppliers.create');
        $this->createPermissionIfNotExists('suppliers.edit');
        $this->createPermissionIfNotExists('suppliers.delete');
        
        $this->createPermissionIfNotExists('accounts.view');
        $this->createPermissionIfNotExists('accounts.create');
        $this->createPermissionIfNotExists('accounts.edit');
        $this->createPermissionIfNotExists('accounts.delete');

        // Inventory permissions
        $this->createPermissionIfNotExists('warehouses.view');
        $this->createPermissionIfNotExists('warehouses.create');
        $this->createPermissionIfNotExists('warehouses.edit');
        $this->createPermissionIfNotExists('warehouses.delete');
        
        $this->createPermissionIfNotExists('stocks.view');
        $this->createPermissionIfNotExists('stocks.create');
        $this->createPermissionIfNotExists('stocks.edit');
        $this->createPermissionIfNotExists('stocks.delete');
        
        $this->createPermissionIfNotExists('receipts.view');
        $this->createPermissionIfNotExists('receipts.create');
        $this->createPermissionIfNotExists('receipts.edit');
        $this->createPermissionIfNotExists('receipts.delete');
        
        $this->createPermissionIfNotExists('deliveries.view');
        $this->createPermissionIfNotExists('deliveries.create');
        $this->createPermissionIfNotExists('deliveries.edit');
        $this->createPermissionIfNotExists('deliveries.delete');

        // Sales permissions
        $this->createPermissionIfNotExists('sales-orders.view');
        $this->createPermissionIfNotExists('sales-orders.create');
        $this->createPermissionIfNotExists('sales-orders.edit');
        $this->createPermissionIfNotExists('sales-orders.delete');
        $this->createPermissionIfNotExists('sales-orders.approve');
        $this->createPermissionIfNotExists('sales-orders.generate-quotation');
        $this->createPermissionIfNotExists('sales-orders.generate-invoice');

        // Purchase permissions
        $this->createPermissionIfNotExists('purchase-orders.view');
        $this->createPermissionIfNotExists('purchase-orders.create');
        $this->createPermissionIfNotExists('purchase-orders.edit');
        $this->createPermissionIfNotExists('purchase-orders.delete');
        $this->createPermissionIfNotExists('purchase-orders.approve');

        // Finance permissions
        $this->createPermissionIfNotExists('general-ledger.view');
        $this->createPermissionIfNotExists('general-ledger.create');
        $this->createPermissionIfNotExists('general-ledger.edit');
        $this->createPermissionIfNotExists('general-ledger.delete');
        
        $this->createPermissionIfNotExists('expenses.view');
        $this->createPermissionIfNotExists('expenses.create');
        $this->createPermissionIfNotExists('expenses.edit');
        $this->createPermissionIfNotExists('expenses.delete');
        
        $this->createPermissionIfNotExists('incomes.view');
        $this->createPermissionIfNotExists('incomes.create');
        $this->createPermissionIfNotExists('incomes.edit');
        $this->createPermissionIfNotExists('incomes.delete');
        
        $this->createPermissionIfNotExists('internal-transfers.view');
        $this->createPermissionIfNotExists('internal-transfers.create');
        $this->createPermissionIfNotExists('internal-transfers.edit');
        $this->createPermissionIfNotExists('internal-transfers.delete');
        
        $this->createPermissionIfNotExists('assets.view');
        $this->createPermissionIfNotExists('assets.create');
        $this->createPermissionIfNotExists('assets.edit');
        $this->createPermissionIfNotExists('assets.delete');

        // HR permissions
        $this->createPermissionIfNotExists('departments.view');
        $this->createPermissionIfNotExists('departments.create');
        $this->createPermissionIfNotExists('departments.edit');
        $this->createPermissionIfNotExists('departments.delete');
        
        $this->createPermissionIfNotExists('employees.view');
        $this->createPermissionIfNotExists('employees.create');
        $this->createPermissionIfNotExists('employees.edit');
        $this->createPermissionIfNotExists('employees.delete');
        
        $this->createPermissionIfNotExists('attendances.view');
        $this->createPermissionIfNotExists('attendances.create');
        $this->createPermissionIfNotExists('attendances.edit');
        $this->createPermissionIfNotExists('attendances.delete');
        $this->createPermissionIfNotExists('attendances.approve');
        
        $this->createPermissionIfNotExists('time-offs.view');
        $this->createPermissionIfNotExists('time-offs.create');
        $this->createPermissionIfNotExists('time-offs.edit');
        $this->createPermissionIfNotExists('time-offs.delete');
        $this->createPermissionIfNotExists('time-offs.approve');
        
        $this->createPermissionIfNotExists('payrolls.view');
        $this->createPermissionIfNotExists('payrolls.create');
        $this->createPermissionIfNotExists('payrolls.edit');
        $this->createPermissionIfNotExists('payrolls.delete');

        // Company permissions
        $this->createPermissionIfNotExists('company.view');
        $this->createPermissionIfNotExists('company.edit');
        $this->createPermissionIfNotExists('company.manage-employees');
        $this->createPermissionIfNotExists('company.invite-employees');

        // Create Company Owner role with all permissions (only if it doesn't exist)
        try {
            $this->createTestRole('Company Owner', Permission::all()->pluck('name')->toArray());
        } catch (\Exception $e) {
            // Role might already exist, ignore the error
        }
    }

    protected function createPermissionIfNotExists(string $name): ?Permission
    {
        try {
            return Permission::firstOrCreate(['name' => $name]);
        } catch (\Exception $e) {
            // If permission creation fails, try to find existing permission
            return Permission::where('name', $name)->first();
        }
    }

    protected function createTestRole(string $name, array $permissions = []): Role
    {
        try {
            $role = Role::firstOrCreate(['name' => $name]);
            
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }
            
            return $role;
        } catch (\Exception $e) {
            // If role creation fails, try to find existing role
            $role = Role::where('name', $name)->first();
            if ($role && !empty($permissions)) {
                $role->syncPermissions($permissions);
            }
            return $role;
        }
    }

    protected function createTestUserWithRole(string $roleName, array $permissions = []): \App\Models\User
    {
        $role = $this->createTestRole($roleName, $permissions);
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);
        
        return $user;
    }
}
