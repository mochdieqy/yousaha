<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EmployeeRoleController extends Controller
{
    /**
     * Display a listing of employees with their roles.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Get employees from the current company only
        $query = User::whereHas('employee', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->orWhereHas('companies', function($query) use ($company) {
            $query->where('id', $company->id);
        })->with('roles');

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Handle role filter
        if ($request->filled('role_filter')) {
            $roleId = $request->role_filter;
            $query->whereHas('roles', function($q) use ($roleId) {
                $q->where('id', $roleId);
            });
        }

        $employees = $query->get();
        
        return view('pages.employee-roles.index', compact('employees', 'company'));
    }

    /**
     * Show the form for creating a new role assignment.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Get employees from the current company only
        $employees = User::whereHas('employee', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->orWhereHas('companies', function($query) use ($company) {
            $query->where('id', $company->id);
        })->get();
        
        $roles = Role::all();
        
        return view('pages.employee-roles.create', compact('employees', 'roles', 'company'));
    }

    /**
     * Store a newly assigned role to an employee.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verify the employee belongs to the current company
        $employee = User::whereHas('employee', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->orWhereHas('companies', function($query) use ($company) {
            $query->where('id', $company->id);
        })->findOrFail($request->employee_id);

        $role = Role::findOrFail($request->role_id);

        // Check if employee already has this role
        if ($employee->hasRole($role)) {
            return redirect()->back()->with('error', 'Employee already has this role')->withInput();
        }

        // Assign the role to the employee
        $employee->assignRole($role);

        return redirect()->route('employee-roles.index')->with('success', 'Role assigned successfully');
    }

    /**
     * Remove all roles from an employee.
     */
    public function destroy($employeeId)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Verify the employee belongs to the current company
        $employee = User::whereHas('employee', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->orWhereHas('companies', function($query) use ($company) {
            $query->where('id', $company->id);
        })->findOrFail($employeeId);
        
        // Remove all roles from the employee
        $employee->syncRoles([]);

        return redirect()->route('employee-roles.index')->with('success', 'All roles removed successfully');
    }
}
