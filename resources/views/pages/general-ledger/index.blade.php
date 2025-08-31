@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-book text-primary me-2"></i>
                    General Ledger Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">General Ledger</li>
                    </ol>
                </nav>
            </div>
            @can('general-ledger.create')
            <a href="{{ route('general-ledger.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                New Entry
            </a>
            @endcan
        </div>

        <!-- General Ledger Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            General Ledger Entries
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
                    <form method="GET" action="{{ route('general-ledger.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Search</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search entries..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                                <option value="asset" {{ request('type') === 'asset' ? 'selected' : '' }}>Asset</option>
                                <option value="equity" {{ request('type') === 'equity' ? 'selected' : '' }}>Equity</option>
                                <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="posted" {{ request('status') === 'posted' ? 'selected' : '' }}>Posted</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">From Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">To Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small text-muted mb-1">Filter</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i>
                            </button>
                        </div>
                    </form>
                    
                    @if(request('search') || request('type') || request('status') || request('start_date') || request('end_date'))
                    <div class="mt-2">
                        <a href="{{ route('general-ledger.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>Clear Filters
                        </a>
                    </div>
                    @endif
                </div>

                @if($generalLedgers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Date</th>
                                <th class="border-0">Number</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Total Amount</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Reference</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($generalLedgers as $ledger)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-calendar text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $ledger->date->format('M j, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $ledger->date->format('g:i A') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $ledger->number }}</span>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'adjustment' => 'bg-warning',
                                            'transfer' => 'bg-info',
                                            'expense' => 'bg-danger',
                                            'income' => 'bg-success',
                                            'asset' => 'bg-primary',
                                            'equity' => 'bg-secondary',
                                            'other' => 'bg-dark'
                                        ];
                                        $typeColor = $typeColors[$ledger->type] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $typeColor }}">{{ ucfirst($ledger->type) }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-primary">Rp {{ number_format($ledger->total, 0, ',', '.') }}</strong>
                                        @if($ledger->description)
                                            <br><small class="text-muted">{{ Str::limit($ledger->description, 30) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($ledger->status === 'posted')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Posted
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Draft
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($ledger->reference)
                                        <span class="text-muted">{{ $ledger->reference }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('general-ledger.view')
                                        <a href="{{ route('general-ledger.show', $ledger) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('general-ledger.edit')
                                        <a href="{{ route('general-ledger.edit', $ledger) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Edit Entry">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('general-ledger.delete')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Entry"
                                                onclick="confirmDelete('{{ route('general-ledger.delete', $ledger) }}', '{{ addslashes($ledger->number) }}')">
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
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No General Ledger Entries Found</h5>
                    <p class="text-muted">Start by creating your first general ledger entry.</p>
                    @can('general-ledger.create')
                    <a href="{{ route('general-ledger.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Create First Entry
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            @if($generalLedgers->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $generalLedgers->firstItem() ?? 0 }} to {{ $generalLedgers->lastItem() ?? 0 }} of {{ $generalLedgers->total() }} entries
                            @if($generalLedgers->total() > 0)
                                (Page {{ $generalLedgers->currentPage() }} of {{ $generalLedgers->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($generalLedgers->total() > 15)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 15</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $generalLedgers->links() }}
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
                <p>Are you sure you want to delete the general ledger entry "<strong id="deleteItemName"></strong>"?</p>
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
                        Delete Entry
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

function confirmDelete(deleteUrl, itemName) {
    document.getElementById('deleteItemName').textContent = itemName;
    document.getElementById('deleteForm').action = deleteUrl;
    
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
