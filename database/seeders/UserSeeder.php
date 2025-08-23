<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users for each role first
        $this->createCompanyOwner();
        $this->createFinanceManager();
        $this->createSalesManager();
        $this->createPurchaseManager();
        $this->createInventoryManager();
        $this->createHRManager();
        $this->createEmployee();
        $this->createViewer();

        // Create a default company for testing with the company owner
        $companyOwner = User::where('email', 'company.owner@yousaha.com')->first();
        if ($companyOwner) {
            $company = Company::firstOrCreate([
                'name' => 'Yousaha Demo Company',
            ], [
                'owner' => $companyOwner->id,
                'address' => 'Jl. Demo Street No. 123, Jakarta',
                'phone' => '+62-21-1234-5678',
                'website' => 'https://demo.yousaha.com',
            ]);
            
            $this->command->info("Created company: {$company->name} with owner: {$companyOwner->name}");
        }
    }

    private function createCompanyOwner()
    {
        $user = User::firstOrCreate([
            'email' => 'company.owner@yousaha.com',
        ], [
            'name' => 'Company Owner',
            'password' => Hash::make('satu23empat'),
            'phone' => '+62-812-3456-7890',
            'birthday' => '1990-01-01',
            'gender' => 'male',
            'marital_status' => 'married',
            'identity_number' => '1234567890',
            'address' => 'Jl. Owner Street No. 1, Jakarta',
        ]);

        $role = Role::where('name', 'Company Owner')->first();
        if ($role) {
            $user->assignRole($role);
        }

        $this->command->info("Created Company Owner user: {$user->email}");
    }

    private function createFinanceManager()
    {
        $user = User::firstOrCreate([
            'email' => 'finance.manager@yousaha.com',
        ], [
            'name' => 'Finance Manager',
            'password' => Hash::make('satu23empat'),
            'phone' => '+62-812-3456-7891',
            'birthday' => '1985-02-15',
            'gender' => 'female',
            'marital_status' => 'married',
            'identity_number' => '1234567891',
            'address' => 'Jl. Finance Street No. 2, Jakarta',
        ]);

        $role = Role::where('name', 'Finance Manager')->first();
        if ($role) {
            $user->assignRole($role);
        }

        $this->command->info("Created Finance Manager user: {$user->email}");
    }

    private function createSalesManager()
    {
        $user = User::firstOrCreate([
            'email' => 'sales.manager@yousaha.com',
        ], [
            'name' => 'Sales Manager',
            'password' => Hash::make('satu23empat'),
            'phone' => '+62-812-3456-7892',
            'birthday' => '1988-03-20',
            'gender' => 'male',
            'marital_status' => 'single',
            'identity_number' => '1234567892',
            'address' => 'Jl. Sales Street No. 3, Jakarta',
        ]);

        $role = Role::where('name', 'Sales Manager')->first();
        if ($role) {
            $user->assignRole($role);
        }

        $this->command->info("Created Sales Manager user: {$user->email}");
    }

    private function createPurchaseManager()
    {
        $user = User::firstOrCreate([
            'email' => 'purchase.manager@yousaha.com',
        ], [
            'name' => 'Purchase Manager',
            'password' => Hash::make('satu23empat'),
            'phone' => '+62-812-3456-7893',
            'birthday' => '1987-04-25',
            'gender' => 'female',
            'marital_status' => 'married',
            'identity_number' => '1234567893',
            'address' => 'Jl. Purchase Street No. 4, Jakarta',
        ]);

        $role = Role::where('name', 'Purchase Manager')->first();
        if ($role) {
            $user->assignRole($role);
        }

        $this->command->info("Created Purchase Manager user: {$user->email}");
    }

    private function createInventoryManager()
    {
        $user = User::firstOrCreate([
            'email' => 'inventory.manager@yousaha.com',
        ], [
            'name' => 'Inventory Manager',
            'password' => Hash::make('satu23empat'),
            'phone' => '+62-812-3456-7894',
            'birthday' => '1986-05-30',
            'gender' => 'male',
            'marital_status' => 'single',
            'identity_number' => '1234567894',
            'address' => 'Jl. Inventory Street No. 5, Jakarta',
        ]);

        $role = Role::where('name', 'Inventory Manager')->first();
        if ($role) {
            $user->assignRole($role);
        }

        $this->command->info("Created Inventory Manager user: {$user->email}");
    }

    private function createHRManager()
    {
        $user = User::firstOrCreate([
            'email' => 'hr.manager@yousaha.com',
        ], [
            'name' => 'HR Manager',
            'password' => Hash::make('satu23empat'),
            'phone' => '+62-812-3456-7895',
            'birthday' => '1989-06-10',
            'gender' => 'female',
            'marital_status' => 'married',
            'identity_number' => '1234567895',
            'address' => 'Jl. HR Street No. 6, Jakarta',
        ]);

        $role = Role::where('name', 'HR Manager')->first();
        if ($role) {
            $user->assignRole($role);
        }

        $this->command->info("Created HR Manager user: {$user->email}");
    }

    private function createEmployee()
    {
        $user = User::firstOrCreate([
            'email' => 'employee@yousaha.com',
        ], [
            'name' => 'Regular Employee',
            'password' => Hash::make('satu23empat'),
            'phone' => '+62-812-3456-7896',
            'birthday' => '1992-07-15',
            'gender' => 'male',
            'marital_status' => 'single',
            'identity_number' => '1234567896',
            'address' => 'Jl. Employee Street No. 7, Jakarta',
        ]);

        $role = Role::where('name', 'Employee')->first();
        if ($role) {
            $user->assignRole($role);
        }

        $this->command->info("Created Employee user: {$user->email}");
    }

    private function createViewer()
    {
        $user = User::firstOrCreate([
            'email' => 'viewer@yousaha.com',
        ], [
            'name' => 'System Viewer',
            'password' => Hash::make('satu23empat'),
            'phone' => '+62-812-3456-7897',
            'birthday' => '1991-08-20',
            'gender' => 'female',
            'marital_status' => 'single',
            'identity_number' => '1234567897',
            'address' => 'Jl. Viewer Street No. 8, Jakarta',
        ]);

        $role = Role::where('name', 'Viewer')->first();
        if ($role) {
            $user->assignRole($role);
        }

        $this->command->info("Created Viewer user: {$user->email}");
    }
}
