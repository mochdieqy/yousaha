<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CheckPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and create missing permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking permissions...');

        // Check if the permission exists
        $permission = Permission::where('name', 'company.manage-employee-roles')->first();
        
        if (!$permission) {
            $this->warn('Permission "company.manage-employee-roles" not found. Creating...');
            $permission = Permission::create(['name' => 'company.manage-employee-roles']);
            $this->info('Permission created successfully!');
        } else {
            $this->info('Permission "company.manage-employee-roles" already exists.');
        }

        // Check Company Owner role
        $companyOwnerRole = Role::where('name', 'Company Owner')->first();
        
        if ($companyOwnerRole) {
            if (!$companyOwnerRole->hasPermissionTo('company.manage-employee-roles')) {
                $this->warn('Company Owner role does not have the permission. Assigning...');
                $companyOwnerRole->givePermissionTo('company.manage-employee-roles');
                $this->info('Permission assigned to Company Owner role!');
            } else {
                $this->info('Company Owner role already has the permission.');
            }
        } else {
            $this->error('Company Owner role not found!');
        }

        // List all permissions
        $this->info('All permissions:');
        Permission::all()->each(function($p) {
            $this->line("- {$p->name}");
        });

        $this->info('Done!');
    }
}
