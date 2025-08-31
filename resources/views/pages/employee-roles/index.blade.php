@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-shield text-primary me-2"></i>
                    Employee Access Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Employee Roles</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-info me-2">
                    <i class="fas fa-eye me-1"></i>
                    View Available Roles
                </a>
                <a href="{{ route('employee-roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Assign New Role
                </a>
            </div>
        </div>

        <!-- Session Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Employee Roles Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Employee Roles Overview
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
                    <form method="GET" action="{{ route('employee-roles.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search employees..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="role_filter" class="form-select">
                                <option value="">All Roles</option>
                                @foreach($employees->flatMap->roles->unique('id') as $role)
                                    <option value="{{ $role->id }}" {{ request('role_filter') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            @if(request('search') || request('role_filter'))
                                <a href="{{ route('employee-roles.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Employee</th>
                                <th class="border-0">Email</th>
                                <th class="border-0">Current Roles</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas fa-user-circle text-primary fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $employee->name }}</h6>
                                                @if($employee->employee && $employee->employee->department)
                                                    <small class="text-muted">{{ $employee->employee->department->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $employee->email }}</span>
                                    </td>
                                    <td>
                                        @if($employee->roles->count() > 0)
                                            @foreach($employee->roles as $role)
                                                <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No roles assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('employee-roles.create') }}?employee_id={{ $employee->id }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Add Role">
                                                <i class="fas fa-plus me-1"></i>
                                                Add Role
                                            </a>
                                            @if($employee->roles->count() > 0)
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Remove All Roles"
                                                        onclick="confirmRemoveRoles({{ $employee->id }}, '{{ addslashes($employee->name) }}')">
                                                    <i class="fas fa-trash me-1"></i>
                                                    Remove All
                                                </button>
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
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Employees Found</h5>
                    <p class="text-muted">No employees are currently assigned to this company.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Remove Roles Confirmation Modal -->
<div class="modal fade" id="removeRolesModal" tabindex="-1" aria-labelledby="removeRolesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeRolesModalLabel">Confirm Remove All Roles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeRemoveRolesModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove all roles from "<strong id="employeeName"></strong>"?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeRemoveRolesModal()">Cancel</button>
                <form id="removeRolesForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Remove All Roles
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let removeRolesModalInstance = null;

function confirmRemoveRoles(employeeId, employeeName) {
    document.getElementById('employeeName').textContent = employeeName;
    document.getElementById('removeRolesForm').action = `/employee-roles/${employeeId}`;
    
    // Create modal instance and store it globally
    removeRolesModalInstance = new bootstrap.Modal(document.getElementById('removeRolesModal'));
    removeRolesModalInstance.show();
}

function closeRemoveRolesModal() {
    // Method 1: Use stored instance
    if (removeRolesModalInstance) {
        removeRolesModalInstance.hide();
        return;
    }
    
    // Method 2: Try to get existing instance
    const modal = bootstrap.Modal.getInstance(document.getElementById('removeRolesModal'));
    if (modal) {
        modal.hide();
        return;
    }
    
    // Method 3: Create new instance and hide immediately
    try {
        const newModal = new bootstrap.Modal(document.getElementById('removeRolesModal'));
        newModal.hide();
    } catch (error) {
        console.error('Error closing modal:', error);
    }
    
    // Method 4: Manual hide using CSS classes
    const modalElement = document.getElementById('removeRolesModal');
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
    const removeRolesModalElement = document.getElementById('removeRolesModal');
    const removeRolesForm = document.getElementById('removeRolesForm');
    
    // Close modal when clicking outside
    removeRolesModalElement.addEventListener('click', function(event) {
        if (event.target === removeRolesModalElement) {
            closeRemoveRolesModal();
        }
    });
    
    // Close modal when pressing ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeRemoveRolesModal();
        }
    });
    
    // Handle form submission with loading state
    removeRolesForm.addEventListener('submit', function() {
        const submitBtn = removeRolesForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Removing...';
        
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
