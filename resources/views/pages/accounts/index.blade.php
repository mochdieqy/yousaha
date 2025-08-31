@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-chart-area text-primary me-2"></i>
                    Chart of Accounts
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Accounts</li>
                    </ol>
                </nav>
            </div>
            @can('accounts.create')
            <a href="{{ route('accounts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Account
            </a>
            @endcan
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Accounts Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Account List
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <span class="badge bg-info text-white">
                                <i class="fas fa-building me-1"></i>
                                {{ Auth::user()->currentCompany->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Search and Filter Form -->
                <div class="p-3 border-bottom">
                    <form method="GET" action="{{ route('accounts.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search accounts..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="Asset" {{ request('type') === 'Asset' ? 'selected' : '' }}>Asset</option>
                                <option value="Liability" {{ request('type') === 'Liability' ? 'selected' : '' }}>Liability</option>
                                <option value="Equity" {{ request('type') === 'Equity' ? 'selected' : '' }}>Equity</option>
                                <option value="Revenue" {{ request('type') === 'Revenue' ? 'selected' : '' }}>Revenue</option>
                                <option value="Expense" {{ request('type') === 'Expense' ? 'selected' : '' }}>Expense</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            @if(request('search') || request('type'))
                                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @if($accounts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Account</th>
                                <th class="border-0">Code</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Balance</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @switch($account->type)
                                                @case('Asset')
                                                    <i class="fas fa-university text-primary fa-lg"></i>
                                                    @break
                                                @case('Liability')
                                                    <i class="fas fa-hand-holding-usd text-danger fa-lg"></i>
                                                    @break
                                                @case('Equity')
                                                    <i class="fas fa-chart-pie text-info fa-lg"></i>
                                                    @break
                                                @case('Revenue')
                                                    <i class="fas fa-arrow-up text-success fa-lg"></i>
                                                    @break
                                                @case('Expense')
                                                    <i class="fas fa-arrow-down text-warning fa-lg"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-chart-area text-secondary fa-lg"></i>
                                            @endswitch
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $account->name }}</h6>
                                            @if($account->isCriticalAccount())
                                                <small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Critical System Account
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $account->code }}</span>
                                </td>
                                <td>
                                    @switch($account->type)
                                        @case('Asset')
                                            <span class="badge bg-primary">{{ $account->type }}</span>
                                            @break
                                        @case('Liability')
                                            <span class="badge bg-danger">{{ $account->type }}</span>
                                            @break
                                        @case('Equity')
                                            <span class="badge bg-info">{{ $account->type }}</span>
                                            @break
                                        @case('Revenue')
                                            <span class="badge bg-success">{{ $account->type }}</span>
                                            @break
                                        @case('Expense')
                                            <span class="badge bg-warning">{{ $account->type }}</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $account->type }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="@if($account->type === 'Asset' || $account->type === 'Expense') text-danger @else text-success @endif">
                                            Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}
                                        </strong>
                                        <small class="text-muted">
                                            @if($account->type === 'Asset' || $account->type === 'Expense')
                                                Debit Balance
                                            @else
                                                Credit Balance
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('accounts.view')
                                        <a href="{{ route('accounts.show', $account) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="View Account">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('accounts.edit')
                                        <a href="{{ route('accounts.edit', $account) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Edit Account">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('accounts.delete')
                                        @if(!$account->isCriticalAccount())
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Account"
                                                onclick="confirmDelete('{{ route('accounts.delete', $account) }}', '{{ $account->code }} - {{ $account->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @else
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Critical account cannot be deleted"
                                                disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
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
                    <i class="fas fa-chart-area fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Accounts Found</h5>
                    <p class="text-muted">Start by adding your first account to the chart of accounts.</p>
                    @can('accounts.create')
                    <a href="{{ route('accounts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add First Account
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            @if($accounts->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $accounts->firstItem() ?? 0 }} to {{ $accounts->lastItem() ?? 0 }} of {{ $accounts->total() }} accounts
                            @if($accounts->total() > 0)
                                (Page {{ $accounts->currentPage() }} of {{ $accounts->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($accounts->total() > 15)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 15</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $accounts->links() }}
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
                <p>Are you sure you want to delete the account "<strong id="deleteItemName"></strong>"?</p>
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
                        Delete Account
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
