<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $departments = Department::where('company_id', $company->id)
            ->with(['manager', 'parent', 'children'])
            ->orderBy('name')
            ->get();

        return view('pages.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
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

        $managers = $company->usersQuery()->orderBy('name')->get();

        return view('pages.departments.create', compact('departments', 'managers'));
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string|max:50|unique:departments,code,NULL,id,company_id,' . $company->id,
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($company) {
                    if ($value) {
                        $parentDepartment = Department::where('id', $value)
                            ->where('company_id', $company->id)
                            ->first();
                        
                        if (!$parentDepartment) {
                            $fail('The selected parent department is invalid.');
                        }
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Department::create([
                'company_id' => $company->id,
                'code' => $request->code,
                'name' => $request->name,
                'manager_id' => $request->manager_id,
                'description' => $request->description,
                'location' => $request->location,
                'parent_id' => $request->parent_id,
            ]);

            return redirect()->route('departments.index')->with('success', 'Department created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create department. Please try again.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $department->company_id !== $company->id) {
            return redirect()->route('departments.index')->with('error', 'Department not found.');
        }

        $departments = Department::where('company_id', $company->id)
            ->where('id', '!=', $department->id)
            ->orderBy('name')
            ->get();

        $managers = $company->usersQuery()->orderBy('name')->get();

        return view('pages.departments.edit', compact('department', 'departments', 'managers'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $department->company_id !== $company->id) {
            return redirect()->route('departments.index')->with('error', 'Department not found.');
        }

        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string|max:50|unique:departments,code,' . $department->id . ',id,company_id,' . $company->id,
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($company, $department) {
                    if ($value) {
                        if ($value == $department->id) {
                            $fail('A department cannot be its own parent.');
                        }
                        
                        $parentDepartment = Department::where('id', $value)
                            ->where('company_id', $company->id)
                            ->first();
                        
                        if (!$parentDepartment) {
                            $fail('The selected parent department is invalid.');
                        }
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $department->update([
                'code' => $request->code,
                'name' => $request->name,
                'manager_id' => $request->manager_id,
                'description' => $request->description,
                'location' => $request->location,
                'parent_id' => $request->parent_id,
            ]);

            return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update department. Please try again.')->withInput();
        }
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $department->company_id !== $company->id) {
            return redirect()->route('departments.index')->with('error', 'Department not found.');
        }

        // Check if department has employees
        if ($department->employees()->exists()) {
            return redirect()->route('departments.index')->with('error', 'Cannot delete department. It has employees assigned to it.');
        }

        // Check if department has child departments
        if ($department->children()->exists()) {
            return redirect()->route('departments.index')->with('error', 'Cannot delete department. It has child departments.');
        }

        try {
            $department->delete();
            return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('departments.index')->with('error', 'Failed to delete department. Please try again.');
        }
    }
}
