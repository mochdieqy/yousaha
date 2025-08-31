<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * PayrollController
 * 
 * IMPORTANT: This controller ONLY manages employee payroll information records.
 * It does NOT handle:
 * - Salary calculations
 * - Automatic payment processing
 * - Payroll period management
 * - Actual salary disbursements
 * 
 * What it DOES manage:
 * - Employee bank account details for payroll
 * - Tax identification numbers
 * - Employment insurance numbers
 * - Health insurance numbers
 * 
 * This is essentially a "payroll setup" system for storing employee
 * payment and insurance information, not a payroll processing system.
 */
class PayrollController extends Controller
{
    /**
     * Display a listing of payrolls.
     * 
     * Note: This system only manages employee payroll information
     * (bank details, tax numbers, insurance) and does NOT handle
     * salary calculations or automatic payments.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $query = Payroll::whereHas('employee', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with(['employee.user', 'employee.department']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('employee', function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%");
            })->orWhere('payment_account_bank', 'like', "%{$search}%")
              ->orWhere('payment_account_number', 'like', "%{$search}%");
        }

        // Apply department filter
        if ($request->filled('department')) {
            $query->whereHas('employee.department', function($q) use ($request) {
                $q->where('id', $request->department);
            });
        }

        $payrolls = $query->orderBy('created_at', 'desc')->get();

        // Get departments for filter dropdown
        $departments = $company->departments()->orderBy('name')->get();

        return view('pages.payrolls.index', compact('payrolls', 'company', 'departments'));
    }

    /**
     * Show the form for creating a new payroll information record.
     * 
     * Note: This creates payroll information records, not salary payments.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $employees = Employee::where('company_id', $company->id)
            ->whereDoesntHave('payroll') // Only show employees without payroll info
            ->with('user')
            ->orderBy('number')
            ->get();

        return view('pages.payrolls.create', compact('employees', 'company'));
    }

    /**
     * Store a newly created payroll information record.
     * 
     * Note: This stores employee payroll information (bank details, tax numbers, insurance)
     * and does NOT process salary payments or calculations.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'payment_account_bank' => 'required|string|max:255',
            'payment_account_number' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'employment_insurance_number' => 'nullable|string|max:255',
            'health_insurance_number' => 'nullable|string|max:255',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'The selected employee is invalid.',
            'payment_account_bank.required' => 'Bank name is required.',
            'payment_account_bank.max' => 'Bank name cannot exceed 255 characters.',
            'payment_account_number.required' => 'Account number is required.',
            'payment_account_number.max' => 'Account number cannot exceed 255 characters.',
            'tax_number.max' => 'Tax number cannot exceed 255 characters.',
            'employment_insurance_number.max' => 'Employment insurance number cannot exceed 255 characters.',
            'health_insurance_number.max' => 'Health insurance number cannot exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if employee belongs to the company and doesn't have payroll info
        $employee = Employee::where('id', $request->employee_id)
            ->where('company_id', $company->id)
            ->whereDoesntHave('payroll')
            ->first();
        
        if (!$employee) {
            return redirect()->back()->with('error', 'The selected employee is invalid or already has payroll information.')->withInput();
        }

        try {
            Payroll::create([
                'employee_id' => $request->employee_id,
                'payment_account_bank' => $request->payment_account_bank,
                'payment_account_number' => $request->payment_account_number,
                'tax_number' => $request->tax_number,
                'employment_insurance_number' => $request->employment_insurance_number,
                'health_insurance_number' => $request->health_insurance_number,
            ]);

            return redirect()->route('payrolls.index')->with('success', 'Payroll information created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create payroll information. Please try again.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified payroll information.
     */
    public function edit(Payroll $payroll)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $payroll->employee->company_id != $company->id) {
            return redirect()->route('payrolls.index')->with('error', 'Payroll information not found.');
        }

        $employees = Employee::where('company_id', $company->id)
            ->where(function($query) use ($payroll) {
                $query->whereDoesntHave('payroll')
                      ->orWhere('id', $payroll->employee_id);
            })
            ->with('user')
            ->orderBy('number')
            ->get();

        return view('pages.payrolls.edit', compact('payroll', 'employees', 'company'));
    }

    /**
     * Update the specified payroll information.
     */
    public function update(Request $request, Payroll $payroll)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $payroll->employee->company_id != $company->id) {
            return redirect()->route('payrolls.index')->with('error', 'Payroll information not found.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'payment_account_bank' => 'required|string|max:255',
            'payment_account_number' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'employment_insurance_number' => 'nullable|string|max:255',
            'health_insurance_number' => 'nullable|string|max:255',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'The selected employee is invalid.',
            'payment_account_bank.required' => 'Bank name is required.',
            'payment_account_bank.max' => 'Bank name cannot exceed 255 characters.',
            'payment_account_number.required' => 'Account number is required.',
            'payment_account_number.max' => 'Account number cannot exceed 255 characters.',
            'tax_number.max' => 'Tax number cannot exceed 255 characters.',
            'employment_insurance_number.max' => 'Employment insurance number cannot exceed 255 characters.',
            'health_insurance_number.max' => 'Health insurance number cannot exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if employee belongs to the company
        $employee = Employee::where('id', $request->employee_id)
            ->where('company_id', $company->id)
            ->first();
        
        if (!$employee) {
            return redirect()->back()->with('error', 'The selected employee is invalid.')->withInput();
        }

        // Check if payroll information already exists for this employee (excluding current payroll)
        $existingPayroll = Payroll::where('employee_id', $request->employee_id)
            ->where('id', '!=', $payroll->id)
            ->first();

        if ($existingPayroll) {
            return redirect()->back()->with('error', 'Payroll information already exists for this employee.')->withInput();
        }

        try {
            $payroll->update([
                'employee_id' => $request->employee_id,
                'payment_account_bank' => $request->payment_account_bank,
                'payment_account_number' => $request->payment_account_number,
                'tax_number' => $request->tax_number,
                'employment_insurance_number' => $request->employment_insurance_number,
                'health_insurance_number' => $request->health_insurance_number,
            ]);

            return redirect()->route('payrolls.index')->with('success', 'Payroll information updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update payroll information. Please try again.')->withInput();
        }
    }

    /**
     * Remove the specified payroll information.
     */
    public function destroy(Payroll $payroll)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $payroll->employee->company_id != $company->id) {
            return redirect()->route('payrolls.index')->with('error', 'Payroll information not found.');
        }

        try {
            $payroll->delete();
            return redirect()->route('payrolls.index')->with('success', 'Payroll information deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payrolls.index')->with('error', 'Failed to delete payroll information. Please try again.');
        }
    }

    /**
     * Show payroll information details.
     */
    public function show(Payroll $payroll)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $payroll->employee->company_id != $company->id) {
            return redirect()->route('payrolls.index')->with('error', 'Payroll information not found.');
        }

        return view('pages.payrolls.show', compact('payroll', 'company'));
    }
}
