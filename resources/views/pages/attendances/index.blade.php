@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Attendance Management
                    </h5>
                    <div class="d-flex gap-2">
                        <!-- Clock In/Out Buttons for Current User -->
                        <form action="{{ route('attendances.clock-in') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Clock In
                            </button>
                        </form>
                        <form action="{{ route('attendances.clock-out') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Clock Out
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Filters -->
                    <div class="p-3 border-bottom">
                        <form method="GET" action="{{ route('attendances.index') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="employee_id" class="form-label">Employee</label>
                                    <select name="employee_id" id="employee_id" class="form-select">
                                        <option value="">All Employees</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->user->name }} ({{ $employee->number }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date" class="form-label">Specific Date</label>
                                    <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label">Date From</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label">Date To</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>
                                            Filter
                                        </button>
                                        <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table Section -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Date</th>
                                    <th class="border-0">Employee</th>
                                    <th class="border-0">Department</th>
                                    <th class="border-0">Time In</th>
                                    <th class="border-0">Time Out</th>
                                    <th class="border-0">Total Hours</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                <tr>
                                    <td>
                                        <strong>{{ $attendance->date->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $attendance->date->format('l') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $attendance->employee->user->name }}</strong>
                                        <br><small class="text-muted">{{ $attendance->employee->number }}</small>
                                    </td>
                                    <td>
                                        @if($attendance->employee->department)
                                            <span class="badge bg-info">{{ $attendance->employee->department->name }}</span>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->clock_in)
                                            <span class="badge bg-success">{{ $attendance->clock_in }}</span>
                                        @else
                                            <span class="text-muted">Not recorded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->clock_out)
                                            <span class="badge bg-warning">{{ $attendance->clock_out }}</span>
                                        @else
                                            <span class="text-muted">Not recorded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->clock_in && $attendance->clock_out)
                                            @php
                                                $timeIn = \Carbon\Carbon::parse($attendance->clock_in);
                                                $timeOut = \Carbon\Carbon::parse($attendance->clock_out);
                                                $hours = $timeIn->diffInHours($timeOut);
                                                $minutes = $timeIn->diffInMinutes($timeOut) % 60;
                                            @endphp
                                            <span class="badge bg-primary">{{ $hours }}h {{ $minutes }}m</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->clock_in && $attendance->clock_out)
                                            <span class="badge bg-success">Complete</span>
                                        @elseif($attendance->clock_in)
                                            <span class="badge bg-warning">Clocked In</span>
                                        @else
                                            <span class="badge bg-secondary">No Record</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <span class="text-muted">No actions available</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-clock fa-2x mb-3"></i>
                                        <p>No attendance records found.</p>
                                        <small class="text-muted">Attendance records are created automatically when employees clock in and out.</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                @if($attendances->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing {{ $attendances->firstItem() ?? 0 }} to {{ $attendances->lastItem() ?? 0 }} of {{ $attendances->total() }} entries
                            </div>
                            <div>
                                {{ $attendances->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');

    // Clear specific date when date range is used
    function clearSpecificDate() {
        if (dateFromInput.value || dateToInput.value) {
            dateInput.value = '';
        }
    }

    // Clear date range when specific date is used
    function clearDateRange() {
        if (dateInput.value) {
            dateFromInput.value = '';
            dateToInput.value = '';
        }
    }

    // Add event listeners
    dateFromInput.addEventListener('change', clearSpecificDate);
    dateToInput.addEventListener('change', clearSpecificDate);
    dateInput.addEventListener('change', clearDateRange);
});
</script>
@endsection
