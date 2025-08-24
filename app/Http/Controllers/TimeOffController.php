<?php

namespace App\Http\Controllers;

use App\Models\TimeOff;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimeOffController extends Controller
{
    /**
     * Display a listing of time offs.
     */
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if current user is the company owner
        $isCompanyOwner = $company->owner === Auth::id();

        if ($isCompanyOwner) {
            // Company owner can see all time off requests in the company
            $timeOffs = TimeOff::whereHas('employee', function($query) use ($company) {
                    $query->where('company_id', $company->id);
                })
                ->with(['employee.user', 'employee.department'])
                ->orderBy('date', 'desc')
                ->get();
        } else {
            // Regular employees can only see their own time off requests
            $currentEmployee = Employee::where('company_id', $company->id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$currentEmployee) {
                return redirect()->route('home')->with('error', 'You must be an employee to view time off requests.');
            }

            $timeOffs = TimeOff::where('employee_id', $currentEmployee->id)
                ->with(['employee.user', 'employee.department'])
                ->orderBy('date', 'desc')
                ->get();
        }

        return view('pages.time-offs.index', compact('timeOffs', 'isCompanyOwner'));
    }

    /**
     * Show the form for creating a new time off.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Get current user's employee record
        $currentEmployee = Employee::where('company_id', $company->id)
            ->where('user_id', Auth::id())
            ->with('user')
            ->first();

        if (!$currentEmployee) {
            return redirect()->route('time-offs.index')->with('error', 'You must be an employee to request time off.');
        }

        return view('pages.time-offs.create', compact('currentEmployee'));
    }

    /**
     * Store a newly created time off in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|max:500',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'The selected employee is invalid.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
            'date.after_or_equal' => 'Date must be today or in the future.',
            'reason.required' => 'Reason is required.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if employee belongs to the company and is the current user
        $employee = Employee::where('id', $request->employee_id)
            ->where('company_id', $company->id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$employee) {
            return redirect()->back()->with('error', 'You can only request time off for yourself.')->withInput();
        }

        // Check for overlapping time off requests
        $overlappingTimeOff = TimeOff::where('employee_id', $request->employee_id)
            ->where('status', '!=', 'rejected')
            ->where('date', $request->date)
            ->first();

        if ($overlappingTimeOff) {
            return redirect()->back()->with('error', 'There is already a time off request for this period.')->withInput();
        }

        try {
            TimeOff::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            return redirect()->route('time-offs.index')->with('success', 'Time off request created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create time off request. Please try again.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified time off.
     */
    public function edit(TimeOff $timeOff)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $timeOff->employee->company_id !== $company->id) {
            return redirect()->route('time-offs.index')->with('error', 'Time off request not found.');
        }

        // Only allow editing if status is pending
        if ($timeOff->status !== 'pending') {
            return redirect()->route('time-offs.index')->with('error', 'Cannot edit approved or rejected time off requests.');
        }

        // Only allow users to edit their own time off requests
        if ($timeOff->employee->user_id !== Auth::id()) {
            return redirect()->route('time-offs.index')->with('error', 'You can only edit your own time off requests.');
        }

        return view('pages.time-offs.edit', compact('timeOff'));
    }

    /**
     * Update the specified time off in storage.
     */
    public function update(Request $request, TimeOff $timeOff)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $timeOff->employee->company_id !== $company->id) {
            return redirect()->route('time-offs.index')->with('error', 'Time off request not found.');
        }

        // Only allow editing if status is pending
        if ($timeOff->status !== 'pending') {
            return redirect()->route('time-offs.index')->with('error', 'Cannot edit approved or rejected time off requests.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|max:500',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'The selected employee is invalid.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
            'date.after_or_equal' => 'Date must be today or in the future.',
            'reason.required' => 'Reason is required.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if employee belongs to the company and is the current user
        $employee = Employee::where('id', $request->employee_id)
            ->where('company_id', $company->id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$employee) {
            return redirect()->back()->with('error', 'You can only request time off for yourself.')->withInput();
        }

        // Check for overlapping time off requests (excluding current one)
        $overlappingTimeOff = TimeOff::where('employee_id', $request->employee_id)
            ->where('id', '!=', $timeOff->id)
            ->where('status', '!=', 'rejected')
            ->where('date', $request->date)
            ->first();

        if ($overlappingTimeOff) {
            return redirect()->back()->with('error', 'There is already a time off request for this period.')->withInput();
        }

        try {
            $timeOff->update([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'reason' => $request->reason,
            ]);

            return redirect()->route('time-offs.index')->with('success', 'Time off request updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update time off request. Please try again.')->withInput();
        }
    }

    /**
     * Remove the specified time off from storage.
     */
    public function destroy(TimeOff $timeOff)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $timeOff->employee->company_id !== $company->id) {
            return redirect()->route('time-offs.index')->with('error', 'Time off request not found.');
        }

        // Only allow deletion if status is pending
        if ($timeOff->status !== 'pending') {
            return redirect()->route('time-offs.index')->with('error', 'Cannot delete approved or rejected time off requests.');
        }

        // Only allow users to delete their own time off requests
        if ($timeOff->employee->user_id !== Auth::id()) {
            return redirect()->route('time-offs.index')->with('error', 'You can only delete your own time off requests.');
        }

        try {
            $timeOff->delete();
            return redirect()->route('time-offs.index')->with('success', 'Time off request deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('time-offs.index')->with('error', 'Failed to delete time off request. Please try again.');
        }
    }

    /**
     * Show time off requests for approval (for managers and company owners).
     */
    public function approvalIndex()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        // Check if current user is the company owner
        $isCompanyOwner = $company->owner === Auth::id();

        if ($isCompanyOwner) {
            // Company owner can see all pending time off requests
            $timeOffs = TimeOff::whereHas('employee', function($query) use ($company) {
                    $query->where('company_id', $company->id);
                })
                ->where('status', 'pending')
                ->with(['employee.user', 'employee.department'])
                ->orderBy('date', 'asc')
                ->get();
        } else {
            // Regular managers can only see employees they manage
            $managedEmployees = Employee::where('company_id', $company->id)
                ->where('manager', Auth::id())
                ->pluck('id');

            $timeOffs = TimeOff::whereIn('employee_id', $managedEmployees)
                ->where('status', 'pending')
                ->with(['employee.user', 'employee.department'])
                ->orderBy('date', 'asc')
                ->get();
        }

        return view('pages.time-offs.approval', compact('timeOffs', 'isCompanyOwner'));
    }

    /**
     * Show the form for approving/rejecting a time off request.
     */
    public function approvalForm(TimeOff $timeOff)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $timeOff->employee->company_id !== $company->id) {
            return redirect()->route('time-offs.approval')->with('error', 'Time off request not found.');
        }

        // Check if current user is the company owner or the manager of this employee
        $isCompanyOwner = $company->owner === Auth::id();
        $isManager = $timeOff->employee->manager === Auth::id();

        if (!$isCompanyOwner && !$isManager) {
            return redirect()->route('time-offs.approval')->with('error', 'You are not authorized to approve this request.');
        }

        // Check if status is still pending
        if ($timeOff->status !== 'pending') {
            return redirect()->route('time-offs.approval')->with('error', 'This request has already been processed.');
        }

        return view('pages.time-offs.approval-form', compact('timeOff', 'isCompanyOwner'));
    }

    /**
     * Process time off approval/rejection.
     */
    public function processApproval(Request $request, TimeOff $timeOff)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $timeOff->employee->company_id !== $company->id) {
            return redirect()->route('time-offs.approval')->with('error', 'Time off request not found.');
        }

        // Check if current user is the company owner or the manager of this employee
        $isCompanyOwner = $company->owner === Auth::id();
        $isManager = $timeOff->employee->manager === Auth::id();

        if (!$isCompanyOwner && !$isManager) {
            return redirect()->route('time-offs.approval')->with('error', 'You are not authorized to approve this request.');
        }

        // Check if status is still pending
        if ($timeOff->status !== 'pending') {
            return redirect()->route('time-offs.approval')->with('error', 'This request has already been processed.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
        ], [
            'status.required' => 'Please select a status.',
            'status.in' => 'Status must be approved or rejected.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $timeOff->update([
                'status' => $request->status,
            ]);

            $statusMessage = $request->status === 'approved' ? 'approved' : 'rejected';
            return redirect()->route('time-offs.approval')->with('success', "Time off request {$statusMessage} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process approval. Please try again.')->withInput();
        }
    }
}
