@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-eye text-primary me-2"></i>
                    Purchase Order Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
                        <li class="breadcrumb-item active">{{ $purchaseOrder->number }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                @can('purchase-orders.edit')
                @if(!in_array($purchaseOrder->status, ['done', 'cancel']))
                <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
                @endif
                @endcan
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $purchaseOrder->company->name }}
                    <br>
                    <small class="text-muted">Purchase order details for this company</small>
                </div>
            </div>
        </div>

        <!-- Purchase Order Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Basic Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Order Number</label>
                        <p class="mb-0">{{ $purchaseOrder->number }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <div>
                            @php
                                $statusColors = [
                                    'draft' => 'bg-secondary',
                                    'accepted' => 'bg-info',
                                    'done' => 'bg-success',
                                    'cancel' => 'bg-danger'
                                ];
                                $statusColor = $statusColors[$purchaseOrder->status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $statusColor }} text-white fs-6">
                                {{ ucfirst($purchaseOrder->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Supplier</label>
                        <p class="mb-0">
                            <i class="fas fa-truck me-2 text-info"></i>
                            {{ $purchaseOrder->supplier->name }}
                        </p>
                        @if($purchaseOrder->supplier->email)
                            <small class="text-muted">{{ $purchaseOrder->supplier->email }}</small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Warehouse</label>
                        <p class="mb-0">
                            <i class="fas fa-warehouse me-2 text-success"></i>
                            {{ $purchaseOrder->warehouse->name }}
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Requestor</label>
                        <p class="mb-0">
                            <i class="fas fa-user me-2 text-primary"></i>
                            {{ $purchaseOrder->requestor }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Deadline</label>
                        <p class="mb-0">
                            <i class="fas fa-calendar me-2 text-warning"></i>
                            @if($purchaseOrder->isOverdue())
                                <span class="text-danger fw-bold">
                                    {{ $purchaseOrder->deadline->format('M d, Y') }}
                                    <i class="fas fa-exclamation-triangle ms-1"></i>
                                </span>
                            @else
                                {{ $purchaseOrder->deadline->format('M d, Y') }}
                            @endif
                        </p>
                    </div>
                </div>
                @if($purchaseOrder->activities)
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Activities</label>
                        <p class="mb-0">
                            <i class="fas fa-clipboard-list me-2 text-secondary"></i>
                            {{ $purchaseOrder->activities }}
                        </p>
                    </div>
                </div>
                @endif
                
                <!-- Status Change Button -->
                <div class="d-grid gap-2 mt-3">
                    @if(!in_array($purchaseOrder->status, ['done', 'cancel']))
                    <button type="button" 
                            class="btn btn-outline-info" 
                            data-bs-toggle="modal" 
                            data-bs-target="#statusChangeModal" 
                            data-purchase-order-id="{{ $purchaseOrder->id }}"
                            data-current-status="{{ $purchaseOrder->status }}"
                            data-purchase-order-number="{{ $purchaseOrder->number }}">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Change Status
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Products Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    Products
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Product</th>
                                <th class="border-0">SKU</th>
                                <th class="border-0">Quantity</th>
                                <th class="border-0">Unit Cost</th>
                                <th class="border-0">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->productLines as $line)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-box text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $line->product->name }}</h6>
                                            @if($line->product->reference)
                                                <small class="text-muted">{{ $line->product->reference }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $line->product->sku }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ number_format($line->quantity, 0) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        Rp {{ number_format($line->product->cost ?? $line->product->price, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-primary">
                                        Rp {{ number_format(($line->product->cost ?? $line->product->price) * $line->quantity, 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total Amount:</td>
                                <td>
                                    <h5 class="mb-0 text-primary">
                                        Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}
                                    </h5>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status History -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Status History
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($purchaseOrder->statusLogs->sortBy('changed_at') as $log)
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-marker me-3">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-secondary',
                                    'accepted' => 'bg-info',
                                    'done' => 'bg-success',
                                    'cancel' => 'bg-danger'
                                ];
                                $statusColor = $statusColors[$log->status] ?? 'bg-secondary';
                            @endphp
                            <div class="rounded-circle {{ $statusColor }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-circle"></i>
                            </div>
                        </div>
                        <div class="timeline-content flex-grow-1">
                            <h6 class="mb-1">
                                Status changed to <span class="badge {{ $statusColor }} text-white">{{ ucfirst($log->status) }}</span>
                            </h6>
                            <p class="text-muted mb-0">
                                <i class="fas fa-clock me-1"></i>
                                {{ $log->changed_at->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2"></i>
                    Additional Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Created At</label>
                        <p class="mb-0">
                            <i class="fas fa-calendar-plus me-2 text-info"></i>
                            {{ $purchaseOrder->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Last Updated</label>
                        <p class="mb-0">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            {{ $purchaseOrder->updated_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
                @if($purchaseOrder->status === 'done')
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success border-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 fa-lg"></i>
                                <div>
                                    <strong>Purchase Order Completed!</strong>
                                    <br>
                                    <small>This purchase order has been completed and all related receipts have been processed. Stock quantities have been updated accordingly.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeModalLabel">Change Purchase Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickStatusChangeForm">
                    @csrf
                    <div class="mb-3">
                        <label for="modal_purchase_order_number" class="form-label">Purchase Order</label>
                        <input type="text" class="form-control" id="modal_purchase_order_number" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal_current_status" class="form-label">Current Status</label>
                        <input type="text" class="form-control" id="modal_current_status" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal_new_status" class="form-label">New Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="modal_new_status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="draft">Draft</option>
                            <option value="accepted">Accepted</option>
                            <option value="done">Done</option>
                            <option value="cancel">Cancel</option>
                        </select>
                        <div class="form-text">
                            <small class="text-muted">Only valid status transitions are enabled based on current status.</small>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <strong>Status Change Rules:</strong><br>
                            • <strong>Draft:</strong> Can change to Accepted or Cancel<br>
                            • <strong>Accepted:</strong> Can change to Done or Cancel (checks stock availability)<br>
                            • <strong>Done/Cancel:</strong> Cannot change status<br>
                            • <strong>Accepted:</strong> Creates receipt and reserves stock if stock is sufficient<br>
                            • <strong>Done:</strong> Creates receipt, updates stock, and creates financial entries
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" id="modalChangeStatusBtn">
                    <i class="fas fa-sync-alt me-1"></i>
                    Change Status
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<style>
/* Style for disabled select options */
#modal_new_status option:disabled {
    color: #6c757d;
    font-style: italic;
    background-color: #f8f9fa;
}

/* Style for enabled select options */
#modal_new_status option:not(:disabled) {
    color: #212529;
    font-weight: 500;
}

/* Status transition rules styling */
.alert-info small strong {
    color: #0c5460;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Handle status change modal
    $('#statusChangeModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const purchaseOrderId = button.data('purchase-order-id');
        const currentStatus = button.data('current-status');
        const purchaseOrderNumber = button.data('purchase-order-number');
        
        const modal = $(this);
        modal.find('#modal_purchase_order_number').val(purchaseOrderNumber);
        modal.find('#modal_current_status').val(currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1));
        modal.find('#modal_new_status').val('').prop('disabled', false);
        
        // Reset all options to enabled first
        modal.find('#modal_new_status option').prop('disabled', false);
        
        // Disable current status option
        modal.find('#modal_new_status option[value="' + currentStatus + '"]').prop('disabled', true);
        
        // Apply status transition rules
        const statusSelect = modal.find('#modal_new_status');
        const currentStatusValue = currentStatus.toLowerCase();
        
        // Disable all options first
        statusSelect.find('option').prop('disabled', true);
        
        // Enable only valid transitions based on current status
        switch (currentStatusValue) {
            case 'draft':
                // Draft can only change to: accepted or cancel
                statusSelect.find('option[value="accepted"]').prop('disabled', false);
                statusSelect.find('option[value="cancel"]').prop('disabled', false);
                break;
                
            case 'accepted':
                // Accepted can only change to: done or cancel
                statusSelect.find('option[value="done"]').prop('disabled', false);
                statusSelect.find('option[value="cancel"]').prop('disabled', false);
                break;
                
            case 'done':
            case 'cancel':
                // Done and cancel cannot change status
                statusSelect.find('option').prop('disabled', true);
                // Show message that no status changes are allowed
                modal.find('.alert-info').html('<small><strong>Status Change Rules:</strong><br>• <strong>' + currentStatusValue.charAt(0).toUpperCase() + currentStatusValue.slice(1) + ':</strong> Cannot change status - this purchase order is already ' + currentStatusValue + '.</small>');
                break;
                
            default:
                // For any other status, allow all transitions
                statusSelect.find('option').prop('disabled', false);
                break;
        }
        
        // Store purchase order ID for form submission
        modal.data('purchase-order-id', purchaseOrderId);
        
        // Update modal button state based on available transitions
        const hasValidTransitions = statusSelect.find('option:not(:disabled)').length > 1; // > 1 because empty option is always enabled
        const changeStatusBtn = modal.find('#modalChangeStatusBtn');
        
        if (!hasValidTransitions) {
            changeStatusBtn.prop('disabled', true).html('<i class="fas fa-ban me-1"></i>No Status Changes Allowed');
            changeStatusBtn.removeClass('btn-info').addClass('btn-secondary');
        } else {
            changeStatusBtn.prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i>Change Status');
            changeStatusBtn.removeClass('btn-secondary').addClass('btn-info');
        }
    });
    
    // Handle modal status change form submission
    $('#modalChangeStatusBtn').click(function() {
        const modal = $('#statusChangeModal');
        const purchaseOrderId = modal.data('purchase-order-id');
        const newStatus = $('#modal_new_status').val();
        const currentStatus = $('#modal_current_status').val().toLowerCase();
        
        if (!newStatus) {
            alert('Please select a new status.');
            return;
        }
        
        if (newStatus === currentStatus) {
            alert('Please select a different status.');
            return;
        }
        
        // Disable button and show loading
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Processing...');
        
        // Create form and submit
        const form = $('<form>', {
            'method': 'POST',
            'action': '/purchase-orders/' + purchaseOrderId + '/status'
        }).append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': $('meta[name="csrf-token"]').attr('content')
        })).append($('<input>', {
            'type': 'hidden',
            'name': 'status',
            'value': newStatus
        }));
        
        $('body').append(form);
        form.submit();
    });
});
</script>
@endsection
