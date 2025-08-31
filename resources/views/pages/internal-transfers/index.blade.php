@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-exchange-alt text-primary me-2"></i>
                    Internal Transfer Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Internal Transfers</li>
                    </ol>
                </nav>
            </div>
            @can('internal-transfers.create')
            <a href="{{ route('internal-transfers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                New Transfer
            </a>
            @endcan
        </div>

        <!-- Internal Transfers Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Transfer List
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <span class="badge bg-info text-white">
                                <i class="fas fa-building me-1"></i>
                                {{ Auth::user()->currentCompany->name ?? 'No Company' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Search and Filter Form -->
                <div class="p-3 border-bottom">
                    <form method="GET" action="{{ route('internal-transfers.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search transfers..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="account_type" class="form-select">
                                <option value="">All Account Types</option>
                                <option value="Asset" {{ request('account_type') === 'Asset' ? 'selected' : '' }}>Asset</option>
                                <option value="Liability" {{ request('account_type') === 'Liability' ? 'selected' : '' }}>Liability</option>
                                <option value="Equity" {{ request('account_type') === 'Equity' ? 'selected' : '' }}>Equity</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" 
                                   class="form-control" 
                                   name="date_from" 
                                   placeholder="From Date" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                        </div>
                    </form>
                    @if(request('search') || request('account_type') || request('date_from'))
                    <div class="mt-2">
                        <a href="{{ route('internal-transfers.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    </div>
                    @endif
                </div>

                @if($internalTransfers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Date</th>
                                <th class="border-0">Transfer Number</th>
                                <th class="border-0">From Account</th>
                                <th class="border-0">To Account</th>
                                <th class="border-0">Amount</th>
                                <th class="border-0">Fee</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($internalTransfers as $transfer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-calendar text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $transfer->date->format('Y-m-d') }}</strong>
                                            <br><small class="text-muted">{{ $transfer->date->format('D, M Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $transfer->number }}</span>
                                </td>
                                <td>
                                    @if($transfer->accountOut)
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-arrow-up text-danger"></i>
                                            </div>
                                            <div>
                                                <span class="badge bg-danger">{{ $transfer->accountOut->code }}</span>
                                                <br><small class="text-muted">{{ $transfer->accountOut->name }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transfer->accountIn)
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-arrow-down text-success"></i>
                                            </div>
                                            <div>
                                                <span class="badge bg-success">{{ $transfer->accountIn->code }}</span>
                                                <br><small class="text-muted">{{ $transfer->accountIn->name }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-end">
                                        <strong class="text-primary">Rp {{ number_format($transfer->value, 0, ',', '.') }}</strong>
                                    </div>
                                </td>
                                <td>
                                    @if($transfer->fee > 0)
                                        <div class="text-end">
                                            <span class="badge bg-warning">Rp {{ number_format($transfer->fee, 0, ',', '.') }}</span>
                                            <br><small class="text-muted">
                                                @if($transfer->fee_charged_to == 'in')
                                                    To Account
                                                @else
                                                    From Account
                                                @endif
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted text-center">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('internal-transfers.view')
                                        <a href="{{ route('internal-transfers.show', $transfer) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View Transfer">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('internal-transfers.edit')
                                        <a href="{{ route('internal-transfers.edit', $transfer) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Edit Transfer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('internal-transfers.delete')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Transfer"
                                                onclick="confirmDelete('{{ route('internal-transfers.destroy', $transfer) }}', '{{ $transfer->number }}')">
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
                    <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Internal Transfers Found</h5>
                    <p class="text-muted">Start by creating your first internal transfer between accounts.</p>
                    @can('internal-transfers.create')
                    <a href="{{ route('internal-transfers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Create First Transfer
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            @if($internalTransfers->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $internalTransfers->firstItem() ?? 0 }} to {{ $internalTransfers->lastItem() ?? 0 }} of {{ $internalTransfers->total() }} transfers
                            @if($internalTransfers->total() > 0)
                                (Page {{ $internalTransfers->currentPage() }} of {{ $internalTransfers->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($internalTransfers->total() > 15)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 15</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $internalTransfers->links() }}
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
                <button type="button" class="btn-close" onclick="closeDeleteModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the internal transfer "<strong id="deleteItemName"></strong>"?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Transfer
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

// Initialize modal functionality when page loads
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
