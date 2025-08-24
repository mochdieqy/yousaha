<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollController extends Controller
{
    /**
     * Display a listing of payrolls.
     */
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $payrolls = Payroll::whereHas('employee', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with(['employee.user', 'employee.department'])
            ->orderBy('period', 'desc')
            ->get();

        return view('pages.payrolls.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new payroll.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $employees = Employee::where('company_id', $company->id)
            ->with('user')
            ->orderBy('number')
            ->get();

        return view('pages.payrolls.create', compact('employees'));
    }

    /**
     * Store a newly created payroll in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'period' => 'required|date_format:Y-m',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'The selected employee is invalid.',
            'period.required' => 'Payroll period is required.',
            'period.date_format' => 'Period must be in YYYY-MM format.',
            'basic_salary.required' => 'Basic salary is required.',
            'basic_salary.numeric' => 'Basic salary must be a number.',
            'basic_salary.min' => 'Basic salary cannot be negative.',
            'allowances.numeric' => 'Allowances must be a number.',
            'allowances.min' => 'Allowances cannot be negative.',
            'deductions.numeric' => 'Deductions must be a number.',
            'deductions.min' => 'Deductions cannot be negative.',
            'overtime_pay.numeric' => 'Overtime pay must be a number.',
            'overtime_pay.min' => 'Overtime pay cannot be negative.',
            'bonus.numeric' => 'Bonus must be a number.',
            'bonus.min' => 'Bonus cannot be negative.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
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

        // Check if payroll already exists for this employee in this period
        $existingPayroll = Payroll::where('employee_id', $request->employee_id)
            ->where('period', $request->period)
            ->first();

        if ($existingPayroll) {
            return redirect()->back()->with('error', 'Payroll already exists for this employee in this period.')->withInput();
        }

        try {
            $netSalary = $request->basic_salary + 
                        ($request->allowances ?? 0) + 
                        ($request->overtime_pay ?? 0) + 
                        ($request->bonus ?? 0) - 
                        ($request->deductions ?? 0);

            Payroll::create([
                'employee_id' => $request->employee_id,
                'period' => $request->period,
                'basic_salary' => $request->basic_salary,
                'allowances' => $request->allowances ?? 0,
                'deductions' => $request->deductions ?? 0,
                'overtime_pay' => $request->overtime_pay ?? 0,
                'bonus' => $request->bonus ?? 0,
                'net_salary' => $netSalary,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            return redirect()->route('payrolls.index')->with('success', 'Payroll created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create payroll. Please try again.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified payroll.
     */
    public function edit(Payroll $payroll)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $payroll->employee->company_id !== $company->id) {
            return redirect()->route('payrolls.index')->with('error', 'Payroll not found.');
        }

        // Only allow editing if status is pending
        if ($payroll->status !== 'pending') {
            return redirect()->route('payrolls.index')->with('error', 'Cannot edit processed payroll.');
        }

        $employees = Employee::where('company_id', $company->id)
            ->with('user')
            ->orderBy('number')
            ->get();

        return view('pages.payrolls.edit', compact('payroll', 'employees'));
    }

    /**
     * Update the specified payroll in storage.
     */
    public function update(Request $request, Payroll $payroll)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $payroll->employee->company_id !== $company->id) {
            return redirect()->route('payrolls.index')->with('error', 'Payroll not found.');
        }

        // Only allow editing if status is pending
        if ($payroll->status !== 'pending') {
            return redirect()->route('payrolls.index')->with('error', 'Cannot edit processed payroll.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'period' => 'required|date_format:Y-m',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'The selected employee is invalid.',
            'period.required' => 'Payroll period is required.',
            'period.date_format' => 'Period must be in YYYY-MM format.',
            'basic_salary.required' => 'Basic salary is required.',
            'basic_salary.numeric' => 'Basic salary must be a number.',
            'basic_salary.min' => 'Basic salary cannot be negative.',
            'allowances.numeric' => 'Allowances must be a number.',
            'allowances.min' => 'Allowances cannot be negative.',
            'deductions.numeric' => 'Deductions must be a number.',
            'deductions.min' => 'Deductions cannot be negative.',
            'overtime_pay.numeric' => 'Overtime pay must be a number.',
            'overtime_pay.min' => 'Overtime pay cannot be negative.',
            'bonus.numeric' => 'Bonus must be a number.',
            'bonus.min' => 'Bonus cannot be negative.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
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

        // Check if payroll already exists for this employee in this period (excluding current payroll)
        $existingPayroll = Payroll::where('employee_id', $request->employee_id)
            ->where('period', $request->period)
            ->where('id', '!=', $payroll->id)
            ->first();

        if ($existingPayroll) {
            return redirect()->back()->with('error', 'Payroll already exists for this employee in this period.')->withInput();
        }

        try {
            $netSalary = $request->basic_salary + 
                        ($request->allowances ?? 0) + 
                        ($request->overtime_pay ?? 0) + 
                        ($request->bonus ?? 0) - 
                        ($request->deductions ?? 0);

            $payroll->update([
                'employee_id' => $request->employee_id,
                'period' => $request->period,
                'basic_salary' => $request->basic_salary,
                'allowances' => $request->allowances ?? 0,
                'deductions' => $request->deductions ?? 0,
                'overtime_pay' => $request->overtime_pay ?? 0,
                'bonus' => $request->bonus ?? 0,
                'net_salary' => $netSalary,
                'notes' => $request->notes,
            ]);

            return redirect()->route('payrolls.index')->with('success', 'Payroll updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update payroll. Please try again.')->withInput();
        }
    }

    /**
     * Remove the specified payroll from storage.
     */
    public function destroy(Payroll $payroll)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $payroll->employee->company_id !== $company->id) {
            return redirect()->route('payrolls.index')->with('error', 'Payroll not found.');
        }

        // Only allow deletion if status is pending
        if ($payroll->status !== 'pending') {
            return redirect()->route('payrolls.index')->with('error', 'Cannot delete processed payroll.');
        }

        try {
            $payroll->delete();
            return redirect()->route('payrolls.index')->with('success', 'Payroll deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payrolls.index')->with('error', 'Failed to delete payroll. Please try again.');
        }
    }

    /**
     * Process payroll (change status to processed).
     */
    public function process(Payroll $payroll)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $payroll->employee->company_id !== $company->id) {
            return redirect()->route('payrolls.index')->with('error', 'Payroll not found.');
        }

        // Only allow processing if status is pending
        if ($payroll->status !== 'pending') {
            return redirect()->route('payrolls.index')->with('error', 'This payroll has already been processed.');
        }

        try {
            $payroll->update([
                'status' => 'processed',
                'processed_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            return redirect()->route('payrolls.index')->with('success', 'Payroll processed successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payrolls.index')->with('error', 'Failed to process payroll. Please try again.');
        }
    }
}
