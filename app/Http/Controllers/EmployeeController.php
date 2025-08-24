<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $employees = Employee::where('company_id', $company->id)
            ->with(['user', 'department', 'managerUser'])
            ->orderBy('number')
            ->get();

        return view('pages.employees.index', compact('employees'));
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

        return view('pages.employees.create', compact('departments'));
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

        try {
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

            return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create employee: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $employee->company_id !== $company->id) {
            return redirect()->route('employees.index')->with('error', 'Employee not found.');
        }

        $departments = Department::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('pages.employees.edit', compact('employee', 'departments'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $employee->company_id !== $company->id) {
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

        // Check if department belongs to the company
        $department = Department::where('id', $request->department_id)
            ->where('company_id', $company->id)
            ->first();
        
        if (!$department) {
            return redirect()->back()->with('error', 'The selected department is invalid.')->withInput();
        }

        try {
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

            return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update employee: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $employee->company_id !== $company->id) {
            return redirect()->route('employees.index')->with('error', 'Employee not found.');
        }

        try {
            $employee->delete();
            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Failed to delete employee. Please try again.');
        }
    }
}
