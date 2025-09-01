@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-tie text-primary me-2"></i>
                    Employee Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Employees</li>
                    </ol>
                </nav>
            </div>
            @can('employees.create')
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Employee
            </a>
            @endcan
        </div>

        <!-- Employees Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Employee List
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
                    <form method="GET" action="{{ route('employees.index') }}" class="row g-3">
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
                                       placeholder="Search employees..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="department_id" class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="level" class="form-label">Level</label>
                            <select name="level" id="level" class="form-select">
                                <option value="">All Levels</option>
                                <option value="Junior" {{ request('level') === 'Junior' ? 'selected' : '' }}>Junior</option>
                                <option value="Middle" {{ request('level') === 'Middle' ? 'selected' : '' }}>Middle</option>
                                <option value="Senior" {{ request('level') === 'Senior' ? 'selected' : '' }}>Senior</option>
                                <option value="Lead" {{ request('level') === 'Lead' ? 'selected' : '' }}>Lead</option>
                                <option value="Manager" {{ request('level') === 'Manager' ? 'selected' : '' }}>Manager</option>
                                <option value="Director" {{ request('level') === 'Director' ? 'selected' : '' }}>Director</option>
                                <option value="VP" {{ request('level') === 'VP' ? 'selected' : '' }}>VP</option>
                                <option value="C-Level" {{ request('level') === 'C-Level' ? 'selected' : '' }}>C-Level</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="work_arrangement" class="form-label">Work Arrangement</label>
                            <select name="work_arrangement" id="work_arrangement" class="form-select">
                                <option value="">All Arrangements</option>
                                <option value="WFO" {{ request('work_arrangement') === 'WFO' ? 'selected' : '' }}>WFO</option>
                                <option value="WFH" {{ request('work_arrangement') === 'WFH' ? 'selected' : '' }}>WFH</option>
                                <option value="WFA" {{ request('work_arrangement') === 'WFA' ? 'selected' : '' }}>WFA</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                                @if(request('search') || request('department_id') || request('level') || request('work_arrangement'))
                                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-times me-2"></i>Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Employee</th>
                                <th class="border-0">Department</th>
                                <th class="border-0">Position</th>
                                <th class="border-0">Level</th>
                                <th class="border-0">Join Date</th>
                                <th class="border-0">Manager</th>
                                <th class="border-0">Work Arrangement</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-user text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $employee->user->name }}</h6>
                                            <small class="text-muted">{{ $employee->user->email }}</small>
                                            <br><span class="badge bg-primary">{{ $employee->number }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($employee->department)
                                        <span class="badge bg-info">{{ $employee->department->name }}</span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $employee->position }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $employee->level }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $employee->join_date->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $employee->years_of_service }} years</small>
                                    </div>
                                </td>
                                <td>
                                    @if($employee->managerUser)
                                        <span class="fw-medium">{{ $employee->managerUser->name }}</span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if($employee->work_arrangement === 'WFO')
                                            <span class="badge bg-success">WFO</span>
                                        @elseif($employee->work_arrangement === 'WFH')
                                            <span class="badge bg-warning">WFH</span>
                                        @else
                                            <span class="badge bg-info">WFA</span>
                                        @endif
                                        <small class="text-muted mt-1">{{ $employee->work_location }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('employees.edit')
                                        <a href="{{ route('employees.edit', $employee) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit Employee">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('employees.delete')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Employee"
                                                onclick="confirmDelete({{ $employee->id }}, '{{ addslashes($employee->user->name) }}')">
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
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Employees Found</h5>
                    <p class="text-muted">Start by adding your first employee to the system.</p>
                    @can('employees.create')
                    <a href="{{ route('employees.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add First Employee
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            @if($employees->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} employees
                            @if($employees->total() > 0)
                                (Page {{ $employees->currentPage() }} of {{ $employees->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($employees->total() > 15)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 15</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $employees->links() }}
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
                <p>Are you sure you want to delete the employee "<strong id="employeeName"></strong>"?</p>
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
                        Delete Employee
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

function confirmDelete(employeeId, employeeName) {
    document.getElementById('employeeName').textContent = employeeName;
    document.getElementById('deleteForm').action = `/employees/${employeeId}`;
    
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
