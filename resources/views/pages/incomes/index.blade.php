@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-money-bill-wave text-primary me-2"></i>
                    Income Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Incomes</li>
                    </ol>
                </nav>
            </div>
            @can('incomes.create')
            <a href="{{ route('incomes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Income
            </a>
            @endcan
        </div>

        <!-- Incomes Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Income List
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
                    <form method="GET" action="{{ route('incomes.index') }}" class="row g-3">
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
                                       placeholder="Search incomes..." 
                                       value="{{ request('search') }}">
                            </div>
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
                        <div class="col-md-2">
                            <label for="amount_min" class="form-label">Min Amount</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="amount_min"
                                   name="amount_min" 
                                   placeholder="Min Amount"
                                   value="{{ request('amount_min') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="amount_max" class="form-label">Max Amount</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="amount_max"
                                   name="amount_max" 
                                   placeholder="Max Amount"
                                   value="{{ request('amount_max') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </form>
                    @if(request('search') || request('date_from') || request('date_to') || request('amount_min') || request('amount_max'))
                        <div class="mt-2">
                            <a href="{{ route('incomes.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                        </div>
                    @endif
                </div>

                @if($incomes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Income</th>
                                <th class="border-0">Date</th>
                                <th class="border-0">Customer</th>
                                <th class="border-0">Amount</th>
                                <th class="border-0">Receipt Account</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incomes as $income)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-money-bill-wave text-success fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $income->number }}</h6>
                                            @if($income->note)
                                                <small class="text-muted">{{ Str::limit($income->note, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $income->date->format('M d, Y') }}</span>
                                </td>
                                <td>
                                    @if($income->customer)
                                        <span class="badge bg-primary">{{ $income->customer->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-success">Rp {{ number_format($income->total, 0, ',', '.') }}</strong>
                                    </div>
                                </td>
                                <td>
                                    @if($income->receiptAccount)
                                        <span class="badge bg-info">
                                            {{ $income->receiptAccount->code }} - {{ $income->receiptAccount->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('incomes.view')
                                        <a href="{{ route('incomes.show', $income) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View Income">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('incomes.edit')
                                        <a href="{{ route('incomes.edit', $income) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Edit Income">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('incomes.delete')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Income"
                                                onclick="confirmDelete({{ $income->id }}, '{{ addslashes($income->number) }}')">
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
                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Incomes Found</h5>
                    <p class="text-muted">Start by adding your first income to the system.</p>
                    @can('incomes.create')
                    <a href="{{ route('incomes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add First Income
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            @if($incomes->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $incomes->firstItem() ?? 0 }} to {{ $incomes->lastItem() ?? 0 }} of {{ $incomes->total() }} incomes
                            @if($incomes->total() > 0)
                                (Page {{ $incomes->currentPage() }} of {{ $incomes->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($incomes->total() > 15)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 15</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $incomes->links() }}
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
                <p>Are you sure you want to delete the income "<strong id="incomeName"></strong>"?</p>
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
                        Delete Income
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

function confirmDelete(incomeId, incomeName) {
    document.getElementById('incomeName').textContent = incomeName;
    document.getElementById('deleteForm').action = `/incomes/${incomeId}`;
    
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
