<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $query = Employee::where('company_id', $company->id)
            ->with(['user', 'department', 'managerUser']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('number', 'like', "%{$search}%")
            ->orWhere('position', 'like', "%{$search}%");
        }

        // Apply department filter
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Apply level filter
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Apply work arrangement filter
        if ($request->filled('work_arrangement')) {
            $query->where('work_arrangement', $request->work_arrangement);
        }

        $employees = $query->orderBy('number')->paginate(15);
        $departments = Department::where('company_id', $company->id)->orderBy('name')->get();

        return view('pages.employees.index', compact('employees', 'departments', 'company'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $departments = Department::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('pages.employees.create', compact('departments', 'company'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'department_id' => 'required|exists:departments,id',
            'number' => 'required|string|max:50|unique:employees,number,NULL,id,company_id,' . $company->id,
            'position' => 'required|string|max:255',
            'level' => 'required|string|max:100',
            'join_date' => 'required|date',
            'work_location' => 'required|string|max:255',
            'work_arrangement' => 'required|in:WFO,WFH,WFA',
        ], [
            'email.required' => 'Please enter the employee email.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'User with this email does not exist. Please ask them to register first.',
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'The selected department is invalid.',
            'number.required' => 'Employee number is required.',
            'number.unique' => 'Employee number already exists.',
            'position.required' => 'Position is required.',
            'level.required' => 'Level is required.',
            'join_date.required' => 'Join date is required.',
            'join_date.date' => 'Join date must be a valid date.',
            'work_location.required' => 'Work location is required.',
            'work_arrangement.required' => 'Work arrangement is required.',
            'work_arrangement.in' => 'Work arrangement must be WFO, WFH, or WFA.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Find the user by email
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return redirect()->back()->with('error', 'User with this email does not exist. Please ask them to register first.')->withInput();
            }

            // Check if user is already an employee in any company
            if (Employee::where('user_id', $user->id)->exists()) {
                return redirect()->back()->with('error', 'This user is already an employee in another company.')->withInput();
            }

            // Check if user is already an employee in this company
            if (Employee::where('company_id', $company->id)
                ->where('user_id', $user->id)
                ->exists()) {
                return redirect()->back()->with('error', 'This user is already an employee in this company.')->withInput();
            }

            // Check if department belongs to the company
            $department = Department::where('id', $request->department_id)
                ->where('company_id', $company->id)
                ->first();
            
            if (!$department) {
                return redirect()->back()->with('error', 'The selected department is invalid.')->withInput();
            }

            Employee::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'number' => $request->number,
                'position' => $request->position,
                'level' => $request->level,
                'join_date' => $request->join_date,
                'manager' => $department->manager_id,
                'work_location' => $request->work_location,
                'work_arrangement' => $request->work_arrangement,
            ]);

            DB::commit();
            return redirect()->route('employees.index')->with('success', 'Employee created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create employee: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $employee->company_id != $company->id) {
            return redirect()->route('employees.index')->with('error', 'Employee not found.');
        }

        $departments = Department::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('pages.employees.edit', compact('employee', 'departments', 'company'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $employee->company_id != $company->id) {
            return redirect()->route('employees.index')->with('error', 'Employee not found.');
        }

        $validator = Validator::make($request->all(), [
            'department_id' => 'required|exists:departments,id',
            'number' => 'required|string|max:50|unique:employees,number,' . $employee->id . ',id,company_id,' . $company->id,
            'position' => 'required|string|max:255',
            'level' => 'required|string|max:100',
            'join_date' => 'required|date',
            'work_location' => 'required|string|max:255',
            'work_arrangement' => 'required|in:WFO,WFH,WFA',
        ], [
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'The selected department is invalid.',
            'number.required' => 'Employee number is required.',
            'number.unique' => 'Employee number already exists.',
            'position.required' => 'Position is required.',
            'level.required' => 'Level is required.',
            'join_date.required' => 'Join date is required.',
            'join_date.date' => 'Join date must be a valid date.',
            'work_location.required' => 'Work location is required.',
            'work_arrangement.required' => 'Work arrangement is required.',
            'work_arrangement.in' => 'Work arrangement must be WFO, WFH, or WFA.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Check if department belongs to the company
            $department = Department::where('id', $request->department_id)
                ->where('company_id', $company->id)
                ->first();
            
            if (!$department) {
                return redirect()->back()->with('error', 'The selected department is invalid.')->withInput();
            }

            $employee->update([
                'department_id' => $request->department_id,
                'number' => $request->number,
                'position' => $request->position,
                'level' => $request->level,
                'join_date' => $request->join_date,
                'manager' => $department->manager_id,
                'work_location' => $request->work_location,
                'work_arrangement' => $request->work_arrangement,
            ]);

            DB::commit();
            return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update employee: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $employee->company_id != $company->id) {
            return redirect()->route('employees.index')->with('error', 'Employee not found.');
        }

        try {
            DB::beginTransaction();
            $employee->delete();
            DB::commit();
            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('employees.index')->with('error', 'Failed to delete employee. Please try again.');
        }
    }
}
