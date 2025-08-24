<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();
        
        if (!$company) {
            $this->command->error('No company found. Please run CompanySeeder first.');
            return;
        }

        // Create a default department if none exists
        $department = Department::firstOrCreate([
            'name' => 'General',
            'company_id' => $company->id,
        ], [
            'description' => 'General department for all employees',
            'location' => 'Main Office',
            'manager_id' => $company->owner, // Set company owner as department manager
        ]);
        
        // If department exists but has no manager, set the company owner as manager
        if (!$department->manager_id) {
            $department->update(['manager_id' => $company->owner]);
        }

        // Get existing users and create employees for them
        $users = User::all();
        $firstUserId = $users->first()->id;
        
        // Define valid levels from the dropdown
        $validLevels = ['Junior', 'Middle', 'Senior', 'Lead', 'Manager', 'Director', 'VP', 'C-Level'];
        
        foreach ($users as $user) {
            // Check if employee already exists
            if (!Employee::where('user_id', $user->id)->exists()) {
                // Determine position and level based on user ID
                $isFirstUser = $user->id === $firstUserId;
                $position = $isFirstUser ? 'Manager' : 'Employee';
                
                // Assign levels based on user ID to create a realistic hierarchy
                if ($isFirstUser) {
                    $level = 'Manager'; // First user is a manager
                } elseif ($user->id === $firstUserId + 1) {
                    $level = 'Senior'; // Second user is senior
                } elseif ($user->id === $firstUserId + 2) {
                    $level = 'Lead'; // Third user is lead
                } else {
                    $level = $validLevels[array_rand($validLevels)]; // Random level for others
                }
                
                // For the first user, they will be their own manager (self-managed)
                // For others, the first user will be their manager
                $managerId = $firstUserId;
                
                Employee::create([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'department_id' => $department->id,
                    'number' => 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'position' => $position,
                    'level' => $level,
                    'join_date' => now()->subMonths(rand(1, 12)),
                    'manager' => $managerId, // First user manages everyone (including themselves)
                    'work_arrangement' => 'WFO',
                    'work_location' => 'Main Office',
                ]);
            }
        }

        $this->command->info('Employees seeded successfully!');
    }
}
