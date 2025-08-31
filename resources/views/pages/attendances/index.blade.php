@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-clock text-primary me-2"></i>
                    Attendance Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Attendance</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <!-- Clock In/Out Buttons for Current User -->
                <form action="{{ route('attendances.clock-in') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Clock In
                    </button>
                </form>
                <form action="{{ route('attendances.clock-out') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Clock Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Attendance Records
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <span class="badge bg-info text-white">
                                <i class="fas fa-building me-1"></i>
                                {{ $company->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Search and Filter Form -->
                <div class="p-3 border-bottom">
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Filter Instructions:</strong> Use "Specific Date" for a single day, or use "Date From" and "Date To" together for a date range. Employee filter can be combined with any date filter.
                        </small>

                    </div>
                    <form method="GET" action="{{ route('attendances.index') }}" class="row g-3">
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
                            <small class="form-text text-muted">View attendance for a single date</small>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            <small class="form-text text-muted">Start of date range</small>
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            <small class="form-text text-muted">End of date range</small>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            @if(request('employee_id') || request('date') || request('date_from') || request('date_to'))
                                <a href="{{ route('attendances.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @if($attendances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Date</th>
                                <th class="border-0">Employee</th>
                                <th class="border-0">Department</th>
                                <th class="border-0">Time In</th>
                                <th class="border-0">Time Out</th>
                                <th class="border-0">Total Hours</th>
                                <th class="border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-calendar text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $attendance->date->format('M d, Y') }}</h6>
                                            <small class="text-muted">{{ $attendance->date->format('l') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-user text-success fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $attendance->employee->user->name }}</h6>
                                            <small class="text-muted">{{ $attendance->employee->number }}</small>
                                        </div>
                                    </div>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-clock fa-3x mb-3"></i>
                    <h5>No attendance records found</h5>
                    <p class="mb-0">Attendance records are created automatically when employees clock in and out.</p>
                </div>
                @endif
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

    // Auto-hide success/error messages after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });
});
</script>
@endsection
