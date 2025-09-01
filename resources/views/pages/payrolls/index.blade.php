@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-tie text-primary me-2"></i>
                    Employee Payroll Information Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Payroll Information</li>
                    </ol>
                </nav>
            </div>
            @can('payrolls.create')
            <a href="{{ route('payrolls.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add Employee Payroll Info
            </a>
            @endcan
        </div>

        <!-- Payroll Information Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Employee Payroll Information List
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
                    <form method="GET" action="{{ route('payrolls.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="search"
                                       name="search" 
                                       placeholder="Search employees, bank, account..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="department" class="form-label">Department</label>
                            <select name="department" id="department" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                                @if(request('search') || request('department'))
                                    <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-times me-2"></i>Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                @if($payrolls->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Employee</th>
                                <th class="border-0">Department</th>
                                <th class="border-0">Bank</th>
                                <th class="border-0">Account Number</th>
                                <th class="border-0">Tax Number</th>
                                <th class="border-0">Insurance Numbers</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrolls as $payroll)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-user-tie text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $payroll->employee->user->name }}</h6>
                                            <small class="text-muted">{{ $payroll->employee->number }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($payroll->employee->department)
                                        <span class="badge bg-info">{{ $payroll->employee->department->name }}</span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">
                                        {{ $payroll->payment_account_bank }}
                                    </span>
                                </td>
                                <td>
                                    <code class="text-dark">{{ $payroll->payment_account_number }}</code>
                                </td>
                                <td>
                                    @if($payroll->tax_number)
                                        <span class="badge bg-success">{{ $payroll->tax_number }}</span>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if($payroll->employment_insurance_number)
                                            <span class="badge bg-info mb-1">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                Employment: {{ $payroll->employment_insurance_number }}
                                            </span>
                                        @endif
                                        @if($payroll->health_insurance_number)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-heartbeat me-1"></i>
                                                Health: {{ $payroll->health_insurance_number }}
                                            </span>
                                        @endif
                                        @if(!$payroll->employment_insurance_number && !$payroll->health_insurance_number)
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('payrolls.view')
                                        <a href="{{ route('payrolls.show', $payroll) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('payrolls.edit')
                                        <a href="{{ route('payrolls.edit', $payroll) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('payrolls.delete')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete"
                                                onclick="confirmDelete({{ $payroll->id }}, '{{ addslashes($payroll->employee->user->name) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Payroll Information Found</h5>
                    <p class="text-muted">Start by adding payroll information for your employees.</p>
                    @can('payrolls.create')
                    <a href="{{ route('payrolls.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add First Employee Payroll Info
                    </a>
                    @endcan
                </div>
                @endif
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info border-0 shadow-sm mt-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-lg"></i>
                <div>
                    <strong>Note:</strong> This system manages employee payroll information (bank details, tax numbers, insurance) for payroll setup purposes. 
                    It does NOT handle salary calculations or automatic payments.
                </div>
            </div>
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
                <p>Are you sure you want to delete the payroll information for "<strong id="payrollName"></strong>"?</p>
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
                        Delete Payroll Information
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

function confirmDelete(payrollId, payrollName) {
    document.getElementById('payrollName').textContent = payrollName;
    document.getElementById('deleteForm').action = `/payrolls/${payrollId}`;
    
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
