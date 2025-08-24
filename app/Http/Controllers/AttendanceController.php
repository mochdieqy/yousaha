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

        // Get employees for filter dropdown
        $employees = Employee::where('company_id', $company->id)
            ->with('user')
            ->orderBy('number')
            ->get();

        // Build query with filters
        $query = Attendance::whereHas('employee', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with(['employee.user', 'employee.department']);

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

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('pages.attendances.index', compact('attendances', 'employees'));
    }

    /**
     * Show the form for creating a new attendance.
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

        return view('pages.attendances.create', compact('employees'));
    }

    /**
     * Store a newly created attendance in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'notes' => 'nullable|string|max:500',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'The selected employee is invalid.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
            'clock_in.date_format' => 'Clock in must be in HH:MM format.',
            'clock_out.date_format' => 'Clock out must be in HH:MM format.',
            'clock_out.after' => 'Clock out must be after clock in.',
            'notes.max' => 'Notes cannot exceed 500 characters.',
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

        // Check if attendance already exists for this employee on this date
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->where('date', $request->date)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'Attendance record already exists for this employee on this date.')->withInput();
        }

        try {
            Attendance::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'clock_in' => $request->clock_in,
                'clock_out' => $request->clock_out,
                'status' => 'pending',
            ]);

            return redirect()->route('attendances.index')->with('success', 'Attendance created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create attendance. Please try again.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified attendance.
     */
    public function edit(Attendance $attendance)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $attendance->employee->company_id !== $company->id) {
            return redirect()->route('attendances.index')->with('error', 'Attendance not found.');
        }

        $employees = Employee::where('company_id', $company->id)
            ->with('user')
            ->orderBy('number')
            ->get();

        return view('pages.attendances.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified attendance in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $attendance->employee->company_id !== $company->id) {
            return redirect()->route('attendances.index')->with('error', 'Attendance not found.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'notes' => 'nullable|string|max:500',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'The selected employee is invalid.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
            'clock_in.date_format' => 'Clock in must be in HH:MM format.',
            'clock_out.date_format' => 'Clock out must be in HH:MM format.',
            'clock_out.after' => 'Clock out must be after clock in.',
            'notes.max' => 'Notes cannot exceed 500 characters.',
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

        // Check if attendance already exists for this employee on this date (excluding current attendance)
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->where('date', $request->date)
            ->where('id', '!=', $attendance->id)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'Attendance record already exists for this employee on this date.')->withInput();
        }

        try {
            $attendance->update([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'clock_in' => $request->clock_in,
                'clock_out' => $request->clock_out,
                'status' => 'pending',
            ]);

            return redirect()->route('attendances.index')->with('success', 'Attendance updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update attendance. Please try again.')->withInput();
        }
    }

    /**
     * Remove the specified attendance from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $attendance->employee->company_id !== $company->id) {
            return redirect()->route('attendances.index')->with('error', 'Attendance not found.');
        }

        try {
            $attendance->delete();
            return redirect()->route('attendances.index')->with('success', 'Attendance deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('attendances.index')->with('error', 'Failed to delete attendance. Please try again.');
        }
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
}
