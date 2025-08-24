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
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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
            </div>
        </div>
    </div>
</div>
@endsection
