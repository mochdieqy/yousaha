@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                    @if($isCompanyOwner)
                        Time Off Management
                    @else
                        My Time Off Requests
                    @endif
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Time Offs</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                @can('time-offs.approve')
                <a href="{{ route('time-offs.approval') }}" class="btn btn-warning">
                    <i class="fas fa-check-circle me-2"></i>
                    Approval Queue
                </a>
                @endcan
                @if(!$isCompanyOwner)
                    @can('time-offs.create')
                    <a href="{{ route('time-offs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Request Time Off
                    </a>
                    @endcan
                @endif
            </div>
        </div>

        <!-- Time Offs Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            @if($isCompanyOwner)
                                Company Time Off List
                            @else
                                My Time Off Requests
                            @endif
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
                    <form method="GET" action="{{ route('time-offs.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="search"
                                       name="search" 
                                       placeholder="Search employee or reason..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_from"
                                   name="date_from" 
                                   placeholder="From Date"
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_to"
                                   name="date_to" 
                                   placeholder="To Date"
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                                @if(request('search') || request('status') || request('date_from') || request('date_to'))
                                    <a href="{{ route('time-offs.index') }}" class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-times me-2"></i>Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                @if($timeOffs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                @if($isCompanyOwner)
                                    <th class="border-0">Employee</th>
                                @endif
                                <th class="border-0">Date</th>
                                <th class="border-0">Reason</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeOffs as $timeOff)
                            <tr>
                                @if($isCompanyOwner)
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas fa-user text-primary fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $timeOff->employee->user->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $timeOff->employee->number }} - {{ $timeOff->employee->department->name ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td>
                                    <div>
                                        <strong class="text-primary">{{ $timeOff->date->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $timeOff->date->format('l') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-wrap" style="max-width: 300px;">
                                        {{ Str::limit($timeOff->reason, 100) }}
                                        @if(strlen($timeOff->reason) > 100)
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
                                    @if($timeOff->status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>
                                            Pending
                                        </span>
                                    @elseif($timeOff->status === 'approved')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>
                                            Approved
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($timeOff->status === 'pending')
                                            @if($isCompanyOwner)
                                                <a href="{{ route('time-offs.approval-form', $timeOff) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Review & Approve">
                                                    <i class="fas fa-gavel"></i>
                                                </a>
                                            @else
                                                @can('time-offs.edit')
                                                <a href="{{ route('time-offs.edit', $timeOff) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('time-offs.delete')
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Delete"
                                                        onclick="confirmDelete({{ $timeOff->id }}, '{{ addslashes($timeOff->date->format('M d, Y')) }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endcan
                                            @endif
                                        @else
                                            <span class="text-muted small">No actions</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">
                        @if($isCompanyOwner)
                            No Time Off Requests Found
                        @else
                            No Time Off Requests Yet
                        @endif
                    </h5>
                    <p class="text-muted">
                        @if($isCompanyOwner)
                            Employees can request time off, and you'll be able to approve them here.
                        @else
                            Start by requesting your first time off.
                        @endif
                    </p>
                    @if(!$isCompanyOwner)
                        @can('time-offs.create')
                        <a href="{{ route('time-offs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Request Time Off
                        </a>
                        @endcan
                    @endif
                </div>
                @endif
            </div>
            @if($timeOffs->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $timeOffs->firstItem() ?? 0 }} to {{ $timeOffs->lastItem() ?? 0 }} of {{ $timeOffs->total() }} time off requests
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeDeleteModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the time off request "<strong id="timeOffName"></strong>"?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeDeleteModal()">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let deleteModalInstance = null;

function confirmDelete(timeOffId, timeOffName) {
    document.getElementById('timeOffName').textContent = timeOffName;
    document.getElementById('deleteForm').action = `/time-offs/${timeOffId}`;
    
    // Create modal instance and store it globally
    deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModalInstance.show();
}

function closeDeleteModal() {
    // Method 1: Use stored instance
    if (deleteModalInstance) {
        deleteModalInstance.hide();
        return;
    }
    
    // Method 2: Try to get existing instance
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    if (modal) {
        modal.hide();
        return;
    }
    
    // Method 3: Create new instance and hide immediately
    try {
        const newModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        newModal.hide();
    } catch (error) {
        console.error('Error closing modal:', error);
    }
    
    // Method 4: Manual hide using CSS classes
    const modalElement = document.getElementById('deleteModal');
    if (modalElement) {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
}

// Close modal when clicking outside or pressing ESC
document.addEventListener('DOMContentLoaded', function() {
    const deleteModalElement = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    
    // Close modal when clicking outside
    deleteModalElement.addEventListener('click', function(event) {
        if (event.target === deleteModalElement) {
            closeDeleteModal();
        }
    });
    
    // Close modal when pressing ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
        }
    });
    
    // Handle form submission with loading state
    deleteForm.addEventListener('submit', function() {
        const submitBtn = deleteForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
        
        // Re-enable after a delay (in case of errors)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 10000);
    });
    
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
