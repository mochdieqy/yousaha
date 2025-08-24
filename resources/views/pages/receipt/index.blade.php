@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck text-info me-2"></i>
            Receipts Management
        </h1>
        @can('receipts.create')
        <a href="{{ route('receipts.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>
            Create Receipt
        </a>
        @endcan
    </div>

    

    <!-- Receipts Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Receipts List</h6>
        </div>
        <div class="card-body">
            @forelse($receipts as $receipt)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h6 class="card-title text-primary">Receipt #{{ $receipt->id }}</h6>
                            <p class="card-text">
                                <strong>Reference:</strong> {{ $receipt->reference ?: 'N/A' }}<br>
                                <strong>Status:</strong> 
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
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="card-text">
                                <strong>Supplier:</strong> {{ $receipt->supplier->name }}<br>
                                <strong>Warehouse:</strong> {{ $receipt->warehouse->name }}<br>
                                <strong>Scheduled:</strong> {{ $receipt->scheduled_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="card-text">
                                <strong>Products:</strong> {{ $receipt->productLines->count() }} items<br>
                                <strong>Total Quantity:</strong> {{ $receipt->productLines->sum('quantity') }}<br>
                                <strong>Created:</strong> {{ $receipt->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group-vertical w-100" role="group">
                                @can('receipts.view')
                                <a href="{{ route('receipts.show', $receipt) }}" 
                                   class="btn btn-sm btn-outline-primary mb-1">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                @endcan
                                
                                @can('receipts.edit')
                                @if(!in_array($receipt->status, ['done', 'cancel']))
                                <a href="{{ route('receipts.edit', $receipt) }}" 
                                   class="btn btn-sm btn-outline-warning mb-1">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                @endif
                                @endcan
                                
                                @can('receipts.delete')
                                @if($receipt->status === 'draft')
                                <form action="{{ route('receipts.delete', $receipt) }}" 
                                      method="POST" 
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger mb-1 w-100">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                                @endif
                                @endcan
                                
                                @can('receipts.edit')
                                @if(!in_array($receipt->status, ['done', 'cancel']))
                                <!-- Quick Status Update -->
                                <form action="{{ route('receipts.update-status', $receipt) }}" 
                                      method="POST" 
                                      class="status-update-form"
                                      data-current-status="{{ $receipt->status }}">
                                    @csrf
                                    <div class="input-group input-group-sm mb-1">
                                        <select name="status" 
                                                class="form-select form-select-sm">
                                            <option value="">Select Status</option>
                                            @foreach(['draft', 'waiting', 'ready', 'done', 'cancel'] as $status)
                                                @if($status !== $receipt->status)
                                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button type="submit" 
                                                class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Receipts Found</h5>
                <p class="text-muted">Start by creating your first receipt.</p>
                @can('receipts.create')
                <a href="{{ route('receipts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Create Receipt
                </a>
                @endcan
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Status Change Confirmation Modal -->
<div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="statusChangeMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Confirm Change</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this receipt?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Alert Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="errorModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#receiptsTable').DataTable({
        "pageLength": 25,
        "order": [[5, "desc"]], // Sort by created_at desc
        "language": {
            "search": "Search receipts:",
            "lengthMenu": "Show _MENU_ receipts per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ receipts",
            "infoEmpty": "Showing 0 to 0 of 0 receipts",
            "infoFiltered": "(filtered from _MAX_ total receipts)"
        }
    });

    // Handle status change form submission
    $('.status-update-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const currentStatus = form.data('current-status');
        const newStatus = form.find('select[name="status"]').val();
        
        if (!newStatus) {
            showErrorModal('Please select a status first.');
            return;
        }
        
        if (currentStatus === newStatus) {
            showErrorModal('Please select a different status.');
            return;
        }
        
        // Validate status transition
        const allowedTransitions = {
            'draft': ['waiting', 'cancel'],
            'waiting': ['ready', 'cancel'],
            'ready': ['done', 'cancel'],
            'done': [],
            'cancel': ['draft']
        };
        
        const availableStatuses = allowedTransitions[currentStatus] || [];
        if (!availableStatuses.includes(newStatus)) {
            showErrorModal(`Invalid status transition. Current status '${currentStatus}' can only change to: ${availableStatuses.join(', ')}`);
            form.find('select[name="status"]').val('');
            return;
        }
        
        // Show confirmation modal
        showStatusChangeModal(currentStatus, newStatus, form);
    });

    // Handle delete form submission
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        showDeleteModal($(this));
    });
});

// Show status change confirmation modal
function showStatusChangeModal(currentStatus, newStatus, form) {
    let message = `Are you sure you want to change the status from <strong>"${currentStatus}"</strong> to <strong>"${newStatus}"</strong>?`;
    
    // Special messages for specific transitions
    if (newStatus === 'ready' && currentStatus === 'waiting') {
        message += '<br><br><strong>Note:</strong> This will update stock quantities for all products.';
    } else if (newStatus === 'done') {
        message += '<br><br><strong>Note:</strong> This will complete the receipt process.';
    } else if (newStatus === 'cancel') {
        message += '<br><br><strong>Note:</strong> This will cancel the receipt process.';
    }
    
    $('#statusChangeMessage').html(message);
    
    // Store form reference for confirmation
    $('#confirmStatusChange').off('click').on('click', function() {
        if (form) {
            form[0].submit();
        }
        $('#statusChangeModal').modal('hide');
    });
    
    $('#statusChangeModal').modal('show');
}

// Show delete confirmation modal
function showDeleteModal(form) {
    $('#confirmDelete').off('click').on('click', function() {
        form[0].submit();
        $('#deleteModal').modal('hide');
    });
    
    $('#deleteModal').modal('show');
}

// Show error modal
function showErrorModal(message) {
    $('#errorMessage').text(message);
    $('#errorModal').modal('show');
}

// Legacy functions for backward compatibility
function validateReceiptStatusChange(currentStatus, newStatus, selectElement) {
    if (!newStatus) {
        showErrorModal('Please select a status first.');
        return false;
    }
    
    const allowedTransitions = {
        'draft': ['waiting', 'cancel'],
        'waiting': ['ready', 'cancel'],
        'ready': ['done', 'cancel'],
        'done': [],
        'cancel': ['draft']
    };
    
    const availableStatuses = allowedTransitions[currentStatus] || [];
    if (!availableStatuses.includes(newStatus)) {
        showErrorModal(`Invalid status transition. Current status '${currentStatus}' can only change to: ${availableStatuses.join(', ')}`);
        selectElement.value = '';
        return false;
    }
    return true;
}

function confirmReceiptStatusChange(currentStatus, newStatus) {
    if (!newStatus) {
        showErrorModal('Please select a status first.');
        return false;
    }
    
    if (currentStatus === newStatus) {
        showErrorModal('Please select a different status.');
        return false;
    }
    
    // This will be handled by the modal now
    return true;
}
</script>
@endsection
