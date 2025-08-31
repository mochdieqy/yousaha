<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendances.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $user = Auth::user();
        $employee = Employee::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$employee) {
            return redirect()->route('attendances.index')->with('error', 'You are not registered as an employee in this company.');
        }

        // Get employees for filter dropdown based on user role
        $employees = $this->getAccessibleEmployees($company, $employee, $user);

        // Build query with filters and access control
        $query = Attendance::whereHas('employee', function($query) use ($company, $employee, $user) {
                $query->where('company_id', $company->id);
                
                // Apply access control based on user role
                if (!$this->canViewAllEmployees($user)) {
                    if ($this->isManager($user, $employee)) {
                        // Manager can see employees in their department
                        $query->where('department_id', $employee->department_id);
                    } else {
                        // Regular employee can only see their own attendance
                        $query->where('user_id', $user->id);
                    }
                }
            })
            ->with(['employee.user', 'employee.department']);

        // Apply filters
        $this->applyFilters($query, $request);

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('pages.attendances.index', compact('attendances', 'employees', 'company'));
    }

    /**
     * Clock in for the current user.
     */
    public function clockIn()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $employee = Employee::where('company_id', $company->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$employee) {
            return redirect()->route('attendances.index')->with('error', 'You are not registered as an employee in this company.');
        }

        $today = Carbon::today();
        
        // Check if already clocked in today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->clock_in) {
            return redirect()->route('attendances.index')->with('error', 'You have already clocked in today.');
        }

        try {
            if ($existingAttendance) {
                // Update existing attendance with clock in
                $existingAttendance->update([
                    'clock_in' => Carbon::now()->format('H:i'),
                ]);
            } else {
                // Create new attendance record
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'clock_in' => Carbon::now()->format('H:i'),
                    'status' => 'pending',
                ]);
            }

            return redirect()->route('attendances.index')->with('success', 'Clock in successful!');
        } catch (\Exception $e) {
            return redirect()->route('attendances.index')->with('error', 'Failed to clock in. Please try again.');
        }
    }

    /**
     * Clock out for the current user.
     */
    public function clockOut()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $employee = Employee::where('company_id', $company->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$employee) {
            return redirect()->route('attendances.index')->with('error', 'You are not registered as an employee in this company.');
        }

        $today = Carbon::today();
        
        // Check if attendance exists and has clock in
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return redirect()->route('attendances.index')->with('error', 'You must clock in before clocking out.');
        }

        if ($attendance->clock_out) {
            return redirect()->route('attendances.index')->with('error', 'You have already clocked out today.');
        }

        try {
            $attendance->update([
                'clock_out' => Carbon::now()->format('H:i'),
            ]);

            return redirect()->route('attendances.index')->with('success', 'Clock out successful!');
        } catch (\Exception $e) {
            return redirect()->route('attendances.index')->with('error', 'Failed to clock out. Please try again.');
        }
    }

    /**
     * Get accessible employees based on user role
     */
    private function getAccessibleEmployees($company, $employee, $user)
    {
        if ($this->canViewAllEmployees($user)) {
            // Company owner can see all employees
            return Employee::where('company_id', $company->id)
                ->with('user')
                ->orderBy('number')
                ->get();
        } elseif ($this->isManager($user, $employee)) {
            // Manager can see employees in their department
            return Employee::where('company_id', $company->id)
                ->where('department_id', $employee->department_id)
                ->with('user')
                ->orderBy('number')
                ->get();
        } else {
            // Regular employee can only see themselves
            return Employee::where('company_id', $company->id)
                ->where('user_id', $user->id)
                ->with('user')
                ->orderBy('number')
                ->get();
        }
    }

    /**
     * Check if user can view all employees (company owner)
     */
    private function canViewAllEmployees($user)
    {
        return $user->hasPermissionTo('company.manage-employee-roles') || 
               $user->hasRole('company_owner') ||
               $user->hasRole('admin');
    }

    /**
     * Check if user is a manager
     */
    private function isManager($user, $employee)
    {
        // Check if user has manager permission or is a department manager
        return $user->hasPermissionTo('employees.view') || 
               $user->hasRole('manager') ||
               ($employee->department && $employee->department->manager_id === $user->id);
    }

    /**
     * Apply filters to the attendance query
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Filter by specific date
        if ($request->filled('date') && !$request->filled('date_from') && !$request->filled('date_to')) {
            $query->where('date', $request->date);
        }
    }
}
