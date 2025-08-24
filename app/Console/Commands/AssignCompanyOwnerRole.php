<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignCompanyOwnerRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-company-owner {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign Company Owner role to a user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found!");
            return 1;
        }

        $role = Role::where('name', 'Company Owner')->first();
        
        if (!$role) {
            $this->error("Company Owner role not found!");
            return 1;
        }

        // Remove all existing roles and assign Company Owner role
        $user->syncRoles([$role]);
        
        $this->info("Successfully assigned Company Owner role to user '{$email}'");
        $this->info("User now has roles: " . $user->getRoleNames()->implode(', '));
        
        return 0;
    }
}
