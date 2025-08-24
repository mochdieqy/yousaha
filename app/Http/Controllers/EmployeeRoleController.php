<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class EmployeeRoleController extends Controller
{
    /**
     * Display a listing of employees with their roles.
     */
    public function index()
    {
        $employees = User::with('roles')->get();
        
        return view('pages.employee-roles.index', compact('employees'));
    }

    /**
     * Show the form for creating a new role assignment.
     */
    public function create()
    {
        $employees = User::all();
        $roles = Role::all();
        
        return view('pages.employee-roles.create', compact('employees', 'roles'));
    }

    /**
     * Store a newly assigned role to an employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = User::findOrFail($request->employee_id);
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
        $employee = User::findOrFail($employeeId);
        
        // Remove all roles from the employee
        $employee->syncRoles([]);

        return redirect()->route('employee-roles.index')->with('success', 'All roles removed successfully');
    }
}
