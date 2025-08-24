<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\Attendance;
use Carbon\Carbon;

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
        
        // Define sample banks for realistic payroll data
        $sampleBanks = [
            'Bank Central Asia (BCA)',
            'Bank Rakyat Indonesia (BRI)',
            'Bank Mandiri',
            'Bank Negara Indonesia (BNI)',
            'Bank Danamon',
            'CIMB Niaga',
            'Bank Permata',
            'Bank Mega',
            'Bank Syariah Indonesia (BSI)',
            'Bank Jago'
        ];
        
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
                
                $employee = Employee::create([
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
                
                // Create payroll information for this employee
                $this->createPayrollInfo($employee, $sampleBanks);
                
                // Create attendance data for weekdays in 2024
                $this->createAttendanceData($employee);
            }
        }

        $this->command->info('Employees, payroll information, and attendance data seeded successfully!');
    }
    
    /**
     * Create payroll information for an employee
     */
    private function createPayrollInfo(Employee $employee, array $sampleBanks): void
    {
        // Check if payroll info already exists
        if (Payroll::where('employee_id', $employee->id)->exists()) {
            return;
        }
        
        // Select a random bank
        $selectedBank = $sampleBanks[array_rand($sampleBanks)];
        
        // Generate realistic account number (Indonesian bank account format)
        $accountNumber = $this->generateAccountNumber($selectedBank);
        
        // Generate tax number (NPWP format: XX.XXX.XXX.X-XXX.XXX)
        $taxNumber = $this->generateTaxNumber();
        
        // Generate insurance numbers (optional - some employees might not have them)
        $employmentInsurance = rand(1, 10) <= 7 ? $this->generateInsuranceNumber('BPJS-K') : null;
        $healthInsurance = rand(1, 10) <= 8 ? $this->generateInsuranceNumber('BPJS-K') : null;
        
        Payroll::create([
            'employee_id' => $employee->id,
            'payment_account_bank' => $selectedBank,
            'payment_account_number' => $accountNumber,
            'tax_number' => $taxNumber,
            'employment_insurance_number' => $employmentInsurance,
            'health_insurance_number' => $healthInsurance,
        ]);
    }
    
    /**
     * Create attendance data for weekdays in 2024
     */
    private function createAttendanceData(Employee $employee): void
    {
        // Check if attendance data already exists for this employee
        if (Attendance::where('employee_id', $employee->id)->exists()) {
            return;
        }
        
        $startDate = Carbon::create(2024, 1, 1);
        $endDate = Carbon::create(2024, 12, 31);
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Only create attendance for weekdays (Monday = 1, Friday = 5)
            if ($currentDate->isWeekday()) {
                // Generate realistic clock-in and clock-out times
                $clockInTime = $this->generateClockInTime();
                $clockOutTime = $this->generateClockOutTime($clockInTime);
                
                // Determine status (most historical data should be approved)
                $status = $this->determineAttendanceStatus();
                
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'clock_in' => $clockInTime,
                    'clock_out' => $clockOutTime,
                    'status' => $status,
                ]);
            }
            
            $currentDate->addDay();
        }
    }
    
    /**
     * Generate realistic clock-in time (between 7:30 AM and 9:30 AM)
     */
    private function generateClockInTime(): string
    {
        $hour = rand(7, 9);
        $minute = $hour === 7 ? rand(30, 59) : ($hour === 9 ? rand(0, 30) : rand(0, 59));
        return sprintf('%02d:%02d', $hour, $minute);
    }
    
    /**
     * Generate realistic clock-out time (between 5:00 PM and 7:00 PM, minimum 8 hours work)
     */
    private function generateClockOutTime(string $clockInTime): string
    {
        $clockInHour = (int) substr($clockInTime, 0, 2);
        $clockInMinute = (int) substr($clockInTime, 3, 2);
        
        // Minimum 8 hours work, maximum 10 hours
        $workHours = rand(8, 10);
        $workMinutes = rand(0, 59);
        
        $totalMinutes = ($clockInHour * 60 + $clockInMinute) + ($workHours * 60 + $workMinutes);
        
        $outHour = intval($totalMinutes / 60);
        $outMinute = $totalMinutes % 60;
        
        // Ensure clock-out is not before 5 PM
        if ($outHour < 17) {
            $outHour = 17;
            $outMinute = rand(0, 59);
        }
        
        // Ensure clock-out is not after 7 PM
        if ($outHour > 19) {
            $outHour = 19;
            $outMinute = rand(0, 59);
        }
        
        return sprintf('%02d:%02d', $outHour, $outMinute);
    }
    
    /**
     * Determine attendance status (mostly approved for historical data)
     */
    private function determineAttendanceStatus(): string
    {
        $random = rand(1, 100);
        
        if ($random <= 85) {
            return 'approved'; // 85% approved
        } elseif ($random <= 95) {
            return 'pending'; // 10% pending
        } else {
            return 'rejected'; // 5% rejected
        }
    }
    
    /**
     * Generate realistic Indonesian bank account number
     */
    private function generateAccountNumber(string $bank): string
    {
        // Different banks have different account number formats
        if (str_contains($bank, 'BCA')) {
            return '123' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } elseif (str_contains($bank, 'BRI')) {
            return '0021' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        } elseif (str_contains($bank, 'Mandiri')) {
            return '144' . str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        } elseif (str_contains($bank, 'BNI')) {
            return '009' . str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        } else {
            // Generic format for other banks
            return str_pad(rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * Generate realistic Indonesian tax number (NPWP)
     */
    private function generateTaxNumber(): string
    {
        $first = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
        $second = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $third = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $fourth = rand(1, 9);
        $fifth = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $sixth = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        return "{$first}.{$second}.{$third}.{$fourth}-{$fifth}.{$sixth}";
    }
    
    /**
     * Generate realistic insurance number
     */
    private function generateInsuranceNumber(string $prefix): string
    {
        $year = date('Y');
        $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        return "{$prefix}-{$year}-{$random}";
    }
}
