@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-users text-primary me-2"></i>
                    Customer Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Customers</li>
                    </ol>
                </nav>
            </div>
            @can('customers.create')
            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Customer
            </a>
            @endcan
        </div>

        <!-- Customers Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Customer List
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
                    <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
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
                                       placeholder="Search customers..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="individual" {{ request('type') === 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="company" {{ request('type') === 'company' ? 'selected' : '' }}>Company</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                                @if(request('search') || request('type'))
                                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-times me-2"></i>Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                @if($customers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Customer</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Address</th>
                                <th class="border-0">Phone</th>
                                <th class="border-0">Email</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            @if($customer->isIndividual())
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @else
                                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-building text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $customer->name }}</h6>
                                            <small class="text-muted">ID: {{ $customer->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($customer->isIndividual())
                                        <span class="badge bg-primary">
                                            <i class="fas fa-user me-1"></i>Individual
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="fas fa-building me-1"></i>Company
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->address)
                                        <span class="text-muted">{{ Str::limit($customer->address, 30) }}</span>
                                    @else
                                        <span class="text-muted fst-italic">No address</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->phone)
                                        <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                            <i class="fas fa-phone me-1 text-primary"></i>
                                            {{ $customer->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted fst-italic">No phone</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->email)
                                        <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                            <i class="fas fa-envelope me-1 text-primary"></i>
                                            {{ Str::limit($customer->email, 25) }}
                                        </a>
                                    @else
                                        <span class="text-muted fst-italic">No email</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('customers.edit')
                                        <a href="{{ route('customers.edit', $customer) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit Customer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('customers.delete')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Customer"
                                                onclick="confirmDelete({{ $customer->id }}, '{{ $customer->name }}')">
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
                    <h5 class="text-muted">No Customers Found</h5>
                    <p class="text-muted">Start by adding your first customer to manage your customer relationships.</p>
                    @can('customers.create')
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add First Customer
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            @if($customers->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
                            @if($customers->total() > 0)
                                (Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($customers->total() > 15)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 15</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $customers->links() }}
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
                <p>Are you sure you want to delete the customer "<strong id="customerName"></strong>"?</p>
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
                        <i class="fas fa-trash me-2"></i>Delete Customer
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

function confirmDelete(customerId, customerName) {
    document.getElementById('customerName').textContent = customerName;
    document.getElementById('deleteForm').action = `/customers/${customerId}`;
    
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
