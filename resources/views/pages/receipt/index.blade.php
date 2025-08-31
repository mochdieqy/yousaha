@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-truck text-primary me-2"></i>
                    Receipt Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Receipts</li>
                    </ol>
                </nav>
            </div>
            @can('receipts.create')
            <a href="{{ route('receipts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Receipt
            </a>
            @endcan
        </div>

        <!-- Receipts Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Receipt List
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
                    <form method="GET" action="{{ route('receipts.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search receipts..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                                <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                                <option value="cancel" {{ request('status') === 'cancel' ? 'selected' : '' }}>Cancel</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="warehouse" class="form-select">
                                <option value="">All Warehouses</option>
                                @foreach($warehouses ?? [] as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ request('warehouse') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            @if(request('search') || request('status') || request('warehouse'))
                                <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @if($receipts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Receipt</th>
                                <th class="border-0">Supplier</th>
                                <th class="border-0">Warehouse</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Products</th>
                                <th class="border-0">Scheduled</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $receipt)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-truck text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">Receipt #{{ $receipt->id }}</h6>
                                            @if($receipt->reference)
                                                <small class="text-muted">{{ $receipt->reference }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $receipt->supplier->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $receipt->warehouse->name }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-secondary',
                                            'waiting' => 'bg-warning',
                                            'ready' => 'bg-info',
                                            'done' => 'bg-success',
                                            'cancel' => 'bg-danger'
                                        ];
                                        $statusColor = $statusColors[$receipt->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $statusColor }} text-white">
                                        {{ ucfirst($receipt->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-info">
                                            <i class="fas fa-boxes me-1"></i>
                                            {{ $receipt->productLines->count() }} items
                                        </span>
                                        <small class="text-muted mt-1">
                                            Total: {{ number_format($receipt->productLines->sum('quantity'), 0) }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $receipt->scheduled_at->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $receipt->scheduled_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('receipts.view')
                                        <a href="{{ route('receipts.show', $receipt) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="View Receipt">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('receipts.edit')
                                        @if(!in_array($receipt->status, ['done', 'cancel']))
                                        <a href="{{ route('receipts.edit', $receipt) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Edit Receipt">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                        @endcan
                                        
                                        @can('receipts.delete')
                                        @if($receipt->status === 'draft')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Receipt"
                                                onclick="confirmDelete({{ $receipt->id }}, 'Receipt #{{ $receipt->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                        @endcan
                                        
                                        @can('receipts.edit')
                                        @if(!in_array($receipt->status, ['done', 'cancel']))
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-info" 
                                                title="Update Status"
                                                onclick="showStatusModal({{ $receipt->id }}, '{{ $receipt->status }}')">
                                            <i class="fas fa-sync-alt"></i>
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
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Receipts Found</h5>
                    <p class="text-muted">Start by adding your first receipt to the system.</p>
                    @can('receipts.create')
                    <a href="{{ route('receipts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add First Receipt
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            @if($receipts->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $receipts->firstItem() ?? 0 }} to {{ $receipts->lastItem() ?? 0 }} of {{ $receipts->total() }} receipts
                            @if($receipts->total() > 0)
                                (Page {{ $receipts->currentPage() }} of {{ $receipts->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($receipts->total() > 15)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 15</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $receipts->links() }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Receipt Status</h5>
                <button type="button" class="btn-close" onclick="closeStatusModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="">Select Status</option>
                        </select>
                    </div>
                    <div id="statusNote" class="alert alert-info" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="statusNoteText"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">Cancel</button>
                <button type="submit" form="statusForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Update Status
                </button>
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
                <button type="button" class="btn-close" onclick="closeDeleteModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the receipt "<strong id="receiptName"></strong>"?</p>
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
                        Delete Receipt
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
let statusModalInstance = null;
let deleteModalInstance = null;

function showStatusModal(receiptId, currentStatus) {
    const statusSelect = document.getElementById('status');
    const statusForm = document.getElementById('statusForm');
    const statusNote = document.getElementById('statusNote');
    const statusNoteText = document.getElementById('statusNoteText');
    
    // Clear previous options
    statusSelect.innerHTML = '<option value="">Select Status</option>';
    
    // Define allowed status transitions
    const allowedTransitions = {
        'draft': ['waiting', 'cancel'],
        'waiting': ['ready', 'cancel'],
        'ready': ['done', 'cancel'],
        'done': [],
        'cancel': ['draft']
    };
    
    const availableStatuses = allowedTransitions[currentStatus] || [];
    
    // Add available status options
    availableStatuses.forEach(status => {
        const option = document.createElement('option');
        option.value = status;
        option.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusSelect.appendChild(option);
    });
    
    // Set form action
    statusForm.action = `/receipts/${receiptId}/status`;
    
    // Show status note based on current status
    if (currentStatus === 'waiting' && availableStatuses.includes('ready')) {
        statusNote.style.display = 'block';
        statusNoteText.textContent = 'Changing to Ready status will prepare the receipt for goods receiving.';
    } else if (availableStatuses.includes('done')) {
        statusNote.style.display = 'block';
        statusNoteText.textContent = 'Changing to Done status will complete the receipt process and update stock quantities.';
    } else if (availableStatuses.includes('cancel')) {
        statusNote.style.display = 'block';
        statusNoteText.textContent = 'Cancelling will reverse any stock quantities and mark the receipt as cancelled.';
    } else {
        statusNote.style.display = 'none';
    }
    
    // Show modal
    statusModalInstance = new bootstrap.Modal(document.getElementById('statusModal'));
    statusModalInstance.show();
}

function closeStatusModal() {
    // Method 1: Use stored instance
    if (statusModalInstance) {
        statusModalInstance.hide();
        return;
    }
    
    // Method 2: Try to get existing instance
    const modal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
    if (modal) {
        modal.hide();
        return;
    }
    
    // Method 3: Create new instance and hide immediately
    try {
        const newModal = new bootstrap.Modal(document.getElementById('statusModal'));
        newModal.hide();
    } catch (error) {
        console.error('Error closing status modal:', error);
    }
    
    // Method 4: Manual hide using CSS classes
    const modalElement = document.getElementById('statusModal');
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

function confirmDelete(receiptId, receiptName) {
    document.getElementById('receiptName').textContent = receiptName;
    document.getElementById('deleteForm').action = `/receipts/${receiptId}`;
    
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
        console.error('Error closing delete modal:', error);
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
    const statusModalElement = document.getElementById('statusModal');
    const deleteModalElement = document.getElementById('deleteModal');
    const statusForm = document.getElementById('statusForm');
    const deleteForm = document.getElementById('deleteForm');
    
    // Handle status form submission
    statusForm.addEventListener('submit', function(e) {
        const submitBtn = statusForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        
        // Re-enable after a delay (in case of errors)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 10000);
    });
    
    // Handle delete form submission
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
    
    // Close status modal when clicking outside
    statusModalElement.addEventListener('click', function(event) {
        if (event.target === statusModalElement) {
            closeStatusModal();
        }
    });
    
    // Close modals when pressing ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeStatusModal();
            closeDeleteModal();
        }
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
