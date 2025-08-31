@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-eye text-primary me-2"></i>
                    Sales Order Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sales-orders.index') }}">Sales Orders</a></li>
                        <li class="breadcrumb-item active">{{ $salesOrder->number }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                @can('sales-orders.edit')
                @if(!in_array($salesOrder->status, ['done', 'cancel']))
                <a href="{{ route('sales-orders.edit', $salesOrder) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
                @endif
                @endcan
                <a href="{{ route('sales-orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Sales Orders
                </a>
            </div>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Sales order details for this company</small>
                </div>
            </div>
        </div>

        <!-- Sales Order Information -->
        <div class="row">
            <!-- Main Information -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Order Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Order Number</label>
                                <p class="mb-0 fs-5 fw-bold text-primary">{{ $salesOrder->number }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Status</label>
                                <div>
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'waiting' => 'warning',
                                            'accepted' => 'info',
                                            'done' => 'success',
                                            'cancel' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$salesOrder->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} fs-6">
                                        {{ ucfirst($salesOrder->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Customer</label>
                                <p class="mb-0 fs-6">{{ $salesOrder->customer->name }}</p>
                                @if($salesOrder->customer->email)
                                    <small class="text-muted">{{ $salesOrder->customer->email }}</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Warehouse</label>
                                <p class="mb-0 fs-6">{{ $salesOrder->warehouse->name }}</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Salesperson</label>
                                <p class="mb-0 fs-6">{{ $salesOrder->salesperson }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Deadline</label>
                                <p class="mb-0 fs-6">
                                    @if($salesOrder->isOverdue())
                                        <span class="text-danger fw-bold">
                                            {{ $salesOrder->deadline->format('M d, Y') }}
                                            <i class="fas fa-exclamation-triangle ms-1"></i>
                                        </span>
                                    @else
                                        {{ $salesOrder->deadline->format('M d, Y') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        @if($salesOrder->activities)
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Activities</label>
                            <p class="mb-0 fs-6">{{ $salesOrder->activities }}</p>
                        </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Created</label>
                                <p class="mb-0 fs-6">{{ $salesOrder->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Last Updated</label>
                                <p class="mb-0 fs-6">{{ $salesOrder->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products -->
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
                                        <th class="border-0 text-center">Quantity</th>
                                        <th class="border-0 text-end">Unit Price</th>
                                        <th class="border-0 text-end">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesOrder->productLines as $productLine)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-box text-primary fa-lg"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $productLine->product->name }}</h6>
                                                    @if($productLine->product->reference)
                                                        <small class="text-muted">{{ $productLine->product->reference }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $productLine->product->sku }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold">{{ $productLine->quantity }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-muted">Rp {{ number_format($productLine->product->price, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-success">Rp {{ number_format($productLine->product->price * $productLine->quantity, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-end">
                            <h4 class="mb-0">
                                <span class="text-muted me-3">Total:</span>
                                <span class="text-success">Rp {{ number_format($salesOrder->total, 0, ',', '.') }}</span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Status History -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Status History
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($salesOrder->statusLogs->sortBy('changed_at') as $statusLog)
                            <div class="list-group-item border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary">{{ ucfirst($statusLog->status) }}</span>
                                    </div>
                                    <small class="text-muted">{{ $statusLog->changed_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if(!in_array($salesOrder->status, ['done', 'cancel']))
                            <button type="button" 
                                    class="btn btn-outline-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#statusChangeModal" 
                                    data-sales-order-id="{{ $salesOrder->id }}"
                                    data-current-status="{{ $salesOrder->status }}"
                                    data-sales-order-number="{{ $salesOrder->number }}">
                                <i class="fas fa-exchange-alt me-2"></i>
                                Change Status
                            </button>
                            @endif
                            
                            @can('sales-orders.generate-quotation')
                            @if($salesOrder->status === 'draft')
                            <form action="{{ route('sales-orders.generate-quotation', $salesOrder) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-info w-100">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    Generate Quotation
                                </button>
                            </form>
                            @endif
                            @endcan
                            
                            @can('sales-orders.generate-invoice')
                            @if($salesOrder->status !== 'draft')
                            <form action="{{ route('sales-orders.generate-invoice', $salesOrder) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-success w-100">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>
                                    Generate Invoice
                                </button>
                            </form>
                            @endif
                            @endcan
                            
                            @can('sales-orders.delete')
                            @if($salesOrder->status === 'draft')
                            <button type="button" 
                                    class="btn btn-outline-danger w-100"
                                    onclick="confirmDelete({{ $salesOrder->id }}, '{{ addslashes($salesOrder->number) }}')">
                                <i class="fas fa-trash me-2"></i>
                                Delete Order
                            </button>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Order Summary
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="mb-0 text-primary">{{ $salesOrder->productLines->count() }}</h4>
                                    <small class="text-muted">Products</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-0 text-success">{{ $salesOrder->productLines->sum('quantity') }}</h4>
                                <small class="text-muted">Total Items</small>
                            </div>
                        </div>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeDeleteModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the sales order "<strong id="salesOrderName"></strong>"?</p>
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
                        Delete Sales Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeModalLabel">Change Sales Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickStatusChangeForm">
                    @csrf
                    <div class="mb-3">
                        <label for="modal_sales_order_number" class="form-label">Sales Order</label>
                        <input type="text" class="form-control" id="modal_sales_order_number" readonly>
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
                            <option value="waiting">Waiting</option>
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
                            • <strong>Draft:</strong> Can change to Accepted, Waiting, or Cancel<br>
                            • <strong>Waiting:</strong> Can change to Accepted or Cancel<br>
                            • <strong>Accepted:</strong> Can change to Done or Cancel (checks stock availability)<br>
                            • <strong>Done/Cancel:</strong> Cannot change status<br>
                            • <strong>Accepted:</strong> Creates delivery and reserves stock if stock is sufficient<br>
                            • <strong>Done:</strong> Creates delivery, updates stock, and creates financial entries
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
let deleteModalInstance = null;

function confirmDelete(salesOrderId, salesOrderName) {
    document.getElementById('salesOrderName').textContent = salesOrderName;
    document.getElementById('deleteForm').action = `/sales-orders/${salesOrderId}`;
    
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
    
    // Handle status change modal
    $('#statusChangeModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const salesOrderId = button.data('sales-order-id');
        const currentStatus = button.data('current-status');
        const salesOrderNumber = button.data('sales-order-number');
        
        const modal = $(this);
        modal.find('#modal_sales_order_number').val(salesOrderNumber);
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
                // Draft can only change to: accepted, waiting, or cancel
                statusSelect.find('option[value="accepted"]').prop('disabled', false);
                statusSelect.find('option[value="waiting"]').prop('disabled', false);
                statusSelect.find('option[value="cancel"]').prop('disabled', false);
                break;
                
            case 'waiting':
                // Waiting can only change to: accepted or cancel
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
                modal.find('.alert-info').html('<small><strong>Status Change Rules:</strong><br>• <strong>' + currentStatusValue.charAt(0).toUpperCase() + currentStatusValue.slice(1) + ':</strong> Cannot change status - this sales order is already ' + currentStatusValue + '.</small>');
                break;
                
            default:
                // For any other status, allow all transitions
                statusSelect.find('option').prop('disabled', false);
                break;
        }
        
        // Store sales order ID for form submission
        modal.data('sales-order-id', salesOrderId);
        
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
        const salesOrderId = modal.data('sales-order-id');
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
            'action': '/sales-orders/' + salesOrderId + '/status'
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
