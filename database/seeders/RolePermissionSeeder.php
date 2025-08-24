<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for each module
        $this->createMasterDataPermissions();
        $this->createInventoryPermissions();
        $this->createSalesPermissions();
        $this->createPurchasePermissions();
        $this->createFinancePermissions();
        $this->createHRPermissions();
        $this->createCompanyPermissions();

        // Create roles
        $this->createRoles();
    }

    private function createMasterDataPermissions()
    {
        // Product permissions
        Permission::create(['name' => 'products.view']);
        Permission::create(['name' => 'products.create']);
        Permission::create(['name' => 'products.edit']);
        Permission::create(['name' => 'products.delete']);

        // Customer permissions
        Permission::create(['name' => 'customers.view']);
        Permission::create(['name' => 'customers.create']);
        Permission::create(['name' => 'customers.edit']);
        Permission::create(['name' => 'customers.delete']);

        // Supplier permissions
        Permission::create(['name' => 'suppliers.view']);
        Permission::create(['name' => 'suppliers.create']);
        Permission::create(['name' => 'suppliers.edit']);
        Permission::create(['name' => 'suppliers.delete']);

        // Account permissions
        Permission::create(['name' => 'accounts.view']);
        Permission::create(['name' => 'accounts.create']);
        Permission::create(['name' => 'accounts.edit']);
        Permission::create(['name' => 'accounts.delete']);
    }

    private function createInventoryPermissions()
    {
        // Warehouse permissions
        Permission::create(['name' => 'warehouses.view']);
        Permission::create(['name' => 'warehouses.create']);
        Permission::create(['name' => 'warehouses.edit']);
        Permission::create(['name' => 'warehouses.delete']);

        // Stock permissions
        Permission::create(['name' => 'stocks.view']);
        Permission::create(['name' => 'stocks.create']);
        Permission::create(['name' => 'stocks.edit']);
        Permission::create(['name' => 'stocks.delete']);

        // Receipt permissions
        Permission::create(['name' => 'receipts.view']);
        Permission::create(['name' => 'receipts.create']);
        Permission::create(['name' => 'receipts.edit']);
        Permission::create(['name' => 'receipts.delete']);

        // Delivery permissions
        Permission::create(['name' => 'deliveries.view']);
        Permission::create(['name' => 'deliveries.create']);
        Permission::create(['name' => 'deliveries.edit']);
        Permission::create(['name' => 'deliveries.delete']);
    }

    private function createSalesPermissions()
    {
        // Sales Order permissions
        Permission::create(['name' => 'sales-orders.view']);
        Permission::create(['name' => 'sales-orders.create']);
        Permission::create(['name' => 'sales-orders.edit']);
        Permission::create(['name' => 'sales-orders.delete']);
        Permission::create(['name' => 'sales-orders.approve']);
        Permission::create(['name' => 'sales-orders.generate-quotation']);
        Permission::create(['name' => 'sales-orders.generate-invoice']);
    }

    private function createPurchasePermissions()
    {
        // Purchase Order permissions
        Permission::create(['name' => 'purchase-orders.view']);
        Permission::create(['name' => 'purchase-orders.create']);
        Permission::create(['name' => 'purchase-orders.edit']);
        Permission::create(['name' => 'purchase-orders.delete']);
        Permission::create(['name' => 'purchase-orders.approve']);
    }

    private function createFinancePermissions()
    {
        // General Ledger permissions
        Permission::create(['name' => 'general-ledger.view']);
        Permission::create(['name' => 'general-ledger.create']);
        Permission::create(['name' => 'general-ledger.edit']);
        Permission::create(['name' => 'general-ledger.delete']);

        // Expense permissions
        Permission::create(['name' => 'expenses.view']);
        Permission::create(['name' => 'expenses.create']);
        Permission::create(['name' => 'expenses.edit']);
        Permission::create(['name' => 'expenses.delete']);

        // Income permissions
        Permission::create(['name' => 'incomes.view']);
        Permission::create(['name' => 'incomes.create']);
        Permission::create(['name' => 'incomes.edit']);
        Permission::create(['name' => 'incomes.delete']);

        // Internal Transfer permissions
        Permission::create(['name' => 'internal-transfers.view']);
        Permission::create(['name' => 'internal-transfers.create']);
        Permission::create(['name' => 'internal-transfers.edit']);
        Permission::create(['name' => 'internal-transfers.delete']);

        // Asset permissions
        Permission::create(['name' => 'assets.view']);
        Permission::create(['name' => 'assets.create']);
        Permission::create(['name' => 'assets.edit']);
        Permission::create(['name' => 'assets.delete']);
    }

    private function createHRPermissions()
    {
        // Department permissions
        Permission::create(['name' => 'departments.view']);
        Permission::create(['name' => 'departments.create']);
        Permission::create(['name' => 'departments.edit']);
        Permission::create(['name' => 'departments.delete']);

        // Employee permissions
        Permission::create(['name' => 'employees.view']);
        Permission::create(['name' => 'employees.create']);
        Permission::create(['name' => 'employees.edit']);
        Permission::create(['name' => 'employees.delete']);

        // Attendance permissions
        Permission::create(['name' => 'attendances.view']);
        Permission::create(['name' => 'attendances.create']);
        Permission::create(['name' => 'attendances.edit']);
        Permission::create(['name' => 'attendances.delete']);
        Permission::create(['name' => 'attendances.approve']);

        // Time Off permissions
        Permission::create(['name' => 'time-offs.view']);
        Permission::create(['name' => 'time-offs.create']);
        Permission::create(['name' => 'time-offs.edit']);
        Permission::create(['name' => 'time-offs.delete']);
        Permission::create(['name' => 'time-offs.approve']);

        // Payroll permissions
        Permission::create(['name' => 'payrolls.view']);
        Permission::create(['name' => 'payrolls.create']);
        Permission::create(['name' => 'payrolls.edit']);
        Permission::create(['name' => 'payrolls.delete']);
    }

    private function createCompanyPermissions()
    {
        // Company management permissions
        Permission::create(['name' => 'company.view']);
        Permission::create(['name' => 'company.edit']);
        Permission::create(['name' => 'company.manage-employees']);
        Permission::create(['name' => 'company.invite-employees']);
        Permission::create(['name' => 'company.manage-employee-roles']);
    }

    private function createRoles()
    {
        // Company Owner - Has all permissions
        $companyOwner = Role::create(['name' => 'Company Owner']);
        $companyOwner->givePermissionTo(Permission::all());

        // Finance Manager - Has finance and reporting permissions
        $financeManager = Role::create(['name' => 'Finance Manager']);
        $financeManager->givePermissionTo([
            'accounts.view', 'accounts.create', 'accounts.edit',
            'general-ledger.view', 'general-ledger.create', 'general-ledger.edit',
            'expenses.view', 'expenses.create', 'expenses.edit',
            'incomes.view', 'incomes.create', 'incomes.edit',
            'internal-transfers.view', 'internal-transfers.create', 'internal-transfers.edit',
            'assets.view', 'assets.create', 'assets.edit',
            'company.view'
        ]);

        // Sales Manager - Has sales and customer permissions
        $salesManager = Role::create(['name' => 'Sales Manager']);
        $salesManager->givePermissionTo([
            'products.view', 'products.create', 'products.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'sales-orders.view', 'sales-orders.create', 'sales-orders.edit', 'sales-orders.approve',
            'sales-orders.generate-quotation', 'sales-orders.generate-invoice',
            'deliveries.view', 'deliveries.create', 'deliveries.edit',
            'company.view'
        ]);

        // Purchase Manager - Has purchase and supplier permissions
        $purchaseManager = Role::create(['name' => 'Purchase Manager']);
        $purchaseManager->givePermissionTo([
            'products.view', 'products.create', 'products.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit',
            'purchase-orders.view', 'purchase-orders.create', 'purchase-orders.edit', 'purchase-orders.approve',
            'receipts.view', 'receipts.create', 'receipts.edit',
            'company.view'
        ]);

        // Inventory Manager - Has inventory and warehouse permissions
        $inventoryManager = Role::create(['name' => 'Inventory Manager']);
        $inventoryManager->givePermissionTo([
            'products.view', 'products.create', 'products.edit',
            'warehouses.view', 'warehouses.create', 'warehouses.edit',
            'stocks.view', 'stocks.create', 'stocks.edit',
            'receipts.view', 'receipts.create', 'receipts.edit',
            'deliveries.view', 'deliveries.create', 'deliveries.edit',
            'company.view'
        ]);

        // HR Manager - Has HR and employee permissions
        $hrManager = Role::create(['name' => 'HR Manager']);
        $hrManager->givePermissionTo([
            'departments.view', 'departments.create', 'departments.edit',
            'employees.view', 'employees.create', 'employees.edit',
            'attendances.view', 'attendances.create', 'attendances.edit', 'attendances.approve',
            'time-offs.view', 'time-offs.create', 'time-offs.edit', 'time-offs.approve',
            'payrolls.view', 'payrolls.create', 'payrolls.edit',
            'company.view', 'company.manage-employees', 'company.invite-employees'
        ]);

        // Employee - Basic permissions for daily work
        $employee = Role::create(['name' => 'Employee']);
        $employee->givePermissionTo([
            'attendances.view', 'attendances.create',
            'time-offs.view', 'time-offs.create',
            'company.view'
        ]);

        // Viewer - Read-only access to most modules
        $viewer = Role::create(['name' => 'Viewer']);
        $viewer->givePermissionTo([
            'products.view',
            'customers.view',
            'suppliers.view',
            'warehouses.view',
            'stocks.view',
            'sales-orders.view',
            'purchase-orders.view',
            'receipts.view',
            'deliveries.view',
            'accounts.view',
            'general-ledger.view',
            'expenses.view',
            'incomes.view',
            'departments.view',
            'employees.view',
            'attendances.view',
            'time-offs.view',
            'company.view'
        ]);
    }
}
