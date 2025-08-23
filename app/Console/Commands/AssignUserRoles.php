<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use Spatie\Permission\Models\Role;

class AssignUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign roles to existing users based on their company relationship';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting role assignment for existing users...');

        $users = User::all();
        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            // Check if user owns a company
            $company = Company::where('owner', $user->id)->first();
            
            if ($company) {
                // User owns a company, assign Company Owner role
                $companyOwnerRole = Role::where('name', 'Company Owner')->first();
                if ($companyOwnerRole && !$user->hasRole('Company Owner')) {
                    $user->assignRole($companyOwnerRole);
                    $this->line("\nAssigned 'Company Owner' role to user: {$user->name} ({$user->email})");
                }
            } else {
                // Check if user is an employee
                $employee = Employee::where('user_id', $user->id)->first();
                
                if ($employee) {
                    // User is an employee, assign Employee role
                    $employeeRole = Role::where('name', 'Employee')->first();
                    if ($employeeRole && !$user->hasRole('Employee')) {
                        $user->assignRole($employeeRole);
                        $this->line("\nAssigned 'Employee' role to user: {$user->name} ({$user->email})");
                    }
                } else {
                    // User has no company association, assign Viewer role
                    $viewerRole = Role::where('name', 'Viewer')->first();
                    if ($viewerRole && !$user->hasRole('Viewer')) {
                        $user->assignRole($viewerRole);
                        $this->line("\nAssigned 'Viewer' role to user: {$user->name} ({$user->email})");
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Role assignment completed successfully!');
    }
}
