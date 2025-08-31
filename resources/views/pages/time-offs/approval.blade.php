@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-check-circle text-warning me-2"></i>
                    @if($isCompanyOwner)
                        Time Off Approval Queue
                    @else
                        Time Off Approval Queue
                    @endif
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('time-offs.index') }}">Time Offs</a></li>
                        <li class="breadcrumb-item active">Approval Queue</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-info text-white me-3">
                    <i class="fas fa-building me-1"></i>
                    {{ $company->name }}
                </span>
                <a href="{{ route('time-offs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Time Offs
                </a>
            </div>
        </div>

        <!-- Approval Queue Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            @if($isCompanyOwner)
                                Company Time Off Approval Queue
                                <small class="text-muted d-block">(Company Owner - All Employees)</small>
                            @else
                                Time Off Approval Queue
                                <small class="text-muted d-block">(Manager - Managed Employees)</small>
                            @endif
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-clock me-1"></i>
                                {{ $timeOffs->total() }} Pending Requests
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Search and Filter Form -->
                <div class="p-3 border-bottom">
                    <form method="GET" action="{{ route('time-offs.approval') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search employee or reason..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="department" class="form-select">
                                <option value="">All Departments</option>
                                @php
                                    $departments = \App\Models\Department::where('company_id', $company->id)->get();
                                @endphp
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            @if(request('search') || request('department'))
                                <a href="{{ route('time-offs.approval') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @if($timeOffs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Employee</th>
                                <th class="border-0">Date</th>
                                <th class="border-0">Reason</th>
                                <th class="border-0">Department</th>
                                @if($isCompanyOwner)
                                    <th class="border-0">Manager</th>
                                @endif
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeOffs as $timeOff)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-user text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $timeOff->employee->user->name }}</h6>
                                            <small class="text-muted">{{ $timeOff->employee->number }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-primary">{{ $timeOff->date->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $timeOff->date->format('l') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-wrap" style="max-width: 250px;">
                                        {{ Str::limit($timeOff->reason, 80) }}
                                        @if(strlen($timeOff->reason) > 80)
                                            <button type="button" 
                                                    class="btn btn-link btn-sm p-0 ms-1" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top" 
                                                    title="{{ $timeOff->reason }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $timeOff->employee->department->name ?? 'N/A' }}
                                    </span>
                                </td>
                                @if($isCompanyOwner)
                                    <td>
                                        @if($timeOff->employee->managerUser)
                                            <small class="text-muted">{{ $timeOff->employee->managerUser->name }}</small>
                                        @else
                                            <span class="text-muted">No Manager</span>
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    <a href="{{ route('time-offs.approval-form', $timeOff) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>
                                        Review & Approve
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">
                        @if($isCompanyOwner)
                            No Pending Time Off Requests
                        @else
                            No Pending Requests from Managed Employees
                        @endif
                    </h5>
                    <p class="text-muted">
                        @if($isCompanyOwner)
                            All time off requests in the company have been processed.
                        @else
                            All time off requests from your managed employees have been processed.
                        @endif
                    </p>
                    <a href="{{ route('time-offs.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Time Offs
                    </a>
                </div>
                @endif
            </div>
            @if($timeOffs->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $timeOffs->firstItem() ?? 0 }} to {{ $timeOffs->lastItem() ?? 0 }} of {{ $timeOffs->total() }} pending requests
                            @if($timeOffs->total() > 0)
                                (Page {{ $timeOffs->currentPage() }} of {{ $timeOffs->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($timeOffs->total() > 15)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 15</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $timeOffs->links() }}
                        </div>
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
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
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
