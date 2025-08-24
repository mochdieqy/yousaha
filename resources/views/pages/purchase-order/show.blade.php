@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shopping-cart text-info me-2"></i>
            Purchase Order Details
        </h1>
        <div>
            @can('purchase-orders.edit')
            @if(!in_array($purchaseOrder->status, ['done', 'cancel']))
            <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning btn-sm me-2">
                <i class="fas fa-edit me-1"></i>
                Edit
            </a>
            @endif
            @endcan
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Purchase Order Information -->
    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Purchase Order Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Order Number:</label>
                            <p class="form-control-plaintext">{{ $purchaseOrder->number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <p class="form-control-plaintext">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-secondary',
                                        'accepted' => 'bg-info',
                                        'sent' => 'bg-warning',
                                        'done' => 'bg-success',
                                        'cancel' => 'bg-danger'
                                    ];
                                    $statusColor = $statusColors[$purchaseOrder->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $statusColor }} text-white fs-6">
                                    {{ ucfirst($purchaseOrder->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Supplier:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info text-white">
                                    {{ $purchaseOrder->supplier->name }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $purchaseOrder->supplier->email }}</small>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Warehouse:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-success text-white">
                                    {{ $purchaseOrder->warehouse->name }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Requestor:</label>
                            <p class="form-control-plaintext">{{ $purchaseOrder->requestor }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Deadline:</label>
                            <p class="form-control-plaintext">
                                @if($purchaseOrder->isOverdue())
                                    <span class="text-danger fw-bold">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $purchaseOrder->deadline->format('M d, Y') }}
                                        <br><small>OVERDUE</small>
                                    </span>
                                @else
                                    {{ $purchaseOrder->deadline->format('M d, Y') }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Total Amount:</label>
                            <p class="form-control-plaintext">
                                <span class="h4 text-primary fw-bold">
                                    Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    @if($purchaseOrder->activities)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Activities:</label>
                            <p class="form-control-plaintext">{{ $purchaseOrder->activities }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Created:</label>
                            <p class="form-control-plaintext">{{ $purchaseOrder->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Updated:</label>
                            <p class="form-control-plaintext">{{ $purchaseOrder->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Products</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->productLines as $line)
                                <tr>
                                    <td>
                                        <strong>{{ $line->product->name }}</strong>
                                    </td>
                                    <td>{{ $line->product->sku }}</td>
                                    <td>{{ $line->quantity }}</td>
                                    <td>Rp {{ number_format($line->product->cost ?? $line->product->price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            Rp {{ number_format($line->line_total, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td>
                                        <span class="h5 text-primary fw-bold">
                                            Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Update -->
            @can('purchase-orders.edit')
            @if(!in_array($purchaseOrder->status, ['done', 'cancel']))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">New Status:</label>
                            <select class="form-select" name="status" required>
                                <option value="">Select Status</option>
                                @if($purchaseOrder->status === 'draft')
                                    <option value="draft" {{ $purchaseOrder->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="cancel">Cancel</option>
                                @else
                                    <option value="draft" {{ $purchaseOrder->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="accepted" {{ $purchaseOrder->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="sent" {{ $purchaseOrder->status == 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="done" {{ $purchaseOrder->status == 'done' ? 'selected' : '' }}>Done</option>
                                    <option value="cancel" {{ $purchaseOrder->status == 'cancel' ? 'selected' : '' }}>Cancel</option>
                                @endif
                            </select>
                            @if($purchaseOrder->status === 'draft')
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Draft orders can only be accepted or cancelled.
                            </small>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-sync-alt me-1"></i>
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
            @endif
            @endcan

            <!-- Status History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status History</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($purchaseOrder->statusLogs->sortBy('changed_at') as $log)
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="fw-bold">{{ ucfirst($log->status) }}</div>
                                <small class="text-muted">{{ $log->changed_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('purchase-orders.edit')
                        @if(!in_array($purchaseOrder->status, ['done', 'cancel']))
                        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>
                            Edit Purchase Order
                        </a>
                        @endif
                        @endcan
                        
                        @can('purchase-orders.delete')
                        @if($purchaseOrder->status === 'draft')
                        <form action="{{ route('purchase-orders.delete', $purchaseOrder) }}" 
                              method="POST" 
                              class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash me-1"></i>
                                Delete Purchase Order
                            </button>
                        </form>
                        @endif
                        @endcan
                        
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list me-1"></i>
                            View All Orders
                        </a>
                    </div>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this purchase order?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
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

<!-- Error Modal -->
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
                <p id="errorMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-left: 20px;
}

.timeline-marker {
    position: absolute;
    left: -10px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 10px;
}
</style>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Add any additional JavaScript functionality here
    console.log('Purchase Order Details Page Loaded');

    // Handle delete form submission
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        showDeleteModal($(this));
    });

    // Handle status update form validation
    $('form[action*="update-status"]').submit(function(e) {
        const currentStatus = '{{ $purchaseOrder->status }}';
        const selectedStatus = $(this).find('select[name="status"]').val();
        
        if (!selectedStatus) {
            e.preventDefault();
            showErrorModal('Please select a status first.');
            return false;
        }
        
        // If current status is draft, only allow draft, accepted, or cancel
        if (currentStatus === 'draft' && !['draft', 'accepted', 'cancel'].includes(selectedStatus)) {
            e.preventDefault();
            showErrorModal('Draft purchase orders can only be accepted or cancelled.');
            return false;
        }
        
        // Confirm status change
        if (currentStatus !== selectedStatus) {
            const confirmMessage = `Are you sure you want to change the status from <strong>"${currentStatus}"</strong> to <strong>"${selectedStatus}"</strong>?`;
            if (selectedStatus === 'accepted' && currentStatus === 'draft') {
                showStatusChangeModal(currentStatus, selectedStatus, $(this));
                e.preventDefault();
                return false;
            } else {
                showStatusChangeModal(currentStatus, selectedStatus, $(this));
                e.preventDefault();
                return false;
            }
        }
    });
});

// Show delete confirmation modal
function showDeleteModal(form) {
    $('#confirmDelete').off('click').on('click', function() {
        form[0].submit();
        $('#deleteModal').modal('hide');
    });
    
    $('#deleteModal').modal('show');
}

// Show status change confirmation modal
function showStatusChangeModal(currentStatus, newStatus, form) {
    let message = `Are you sure you want to change the status from <strong>"${currentStatus}"</strong> to <strong>"${newStatus}"</strong>?`;
    
    // Special message for draft to accepted transition
    if (newStatus === 'accepted' && currentStatus === 'draft') {
        message += '<br><br><strong>Note:</strong> This will automatically create a receipt for the purchase order.';
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

// Show error modal
function showErrorModal(message) {
    $('#errorMessage').text(message);
    $('#errorModal').modal('show');
}
</script>
@endsection
