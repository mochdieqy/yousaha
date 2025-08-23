@extends('layouts.home')

@section('content')
<!-- Success Message -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Error Message -->
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice text-success me-2"></i>
                    Sales Orders
                </h5>
                @can('sales-orders.create')
                <a href="{{ route('sales-orders.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-1"></i>
                    Create Sales Order
                </a>
                @endcan
            </div>
            <div class="card-body">
                @if($salesOrders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Warehouse</th>
                                <th>Salesperson</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesOrders as $salesOrder)
                            <tr>
                                <td>
                                    <strong>{{ $salesOrder->number }}</strong>
                                </td>
                                <td>{{ $salesOrder->customer->name }}</td>
                                <td>{{ $salesOrder->warehouse->name }}</td>
                                <td>{{ $salesOrder->salesperson }}</td>
                                <td>
                                    <span class="fw-bold text-success">
                                        {{ number_format($salesOrder->total, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'waiting' => 'warning',
                                            'accepted' => 'info',
                                            'sent' => 'primary',
                                            'done' => 'success',
                                            'cancel' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$salesOrder->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ ucfirst($salesOrder->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($salesOrder->isOverdue())
                                        <span class="text-danger fw-bold">
                                            {{ $salesOrder->deadline->format('M d, Y') }}
                                            <i class="fas fa-exclamation-triangle ms-1"></i>
                                        </span>
                                    @else
                                        {{ $salesOrder->deadline->format('M d, Y') }}
                                    @endif
                                </td>
                                <td>{{ $salesOrder->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('sales-orders.view')
                                        <a href="{{ route('sales-orders.show', $salesOrder) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('sales-orders.edit')
                                        @if(!in_array($salesOrder->status, ['done', 'cancel']))
                                        <a href="{{ route('sales-orders.edit', $salesOrder) }}" 
                                           class="btn btn-outline-warning btn-sm" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                        @endcan
                                        
                                        @if(!in_array($salesOrder->status, ['done', 'cancel']))
                                        <button type="button" 
                                                class="btn btn-outline-info btn-sm" 
                                                title="Quick Status Change"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#statusChangeModal" 
                                                data-sales-order-id="{{ $salesOrder->id }}"
                                                data-current-status="{{ $salesOrder->status }}"
                                                data-sales-order-number="{{ $salesOrder->number }}">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        @endif
                                        
                                        @can('sales-orders.generate-quotation')
                                        @if($salesOrder->status === 'draft')
                                        <form action="{{ route('sales-orders.generate-quotation', $salesOrder) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-outline-info btn-sm" 
                                                    title="Generate Quotation">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @endcan
                                        
                                        @can('sales-orders.generate-invoice')
                                        @if($salesOrder->status !== 'draft')
                                        <form action="{{ route('sales-orders.generate-invoice', $salesOrder) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-outline-success btn-sm" 
                                                    title="Generate Invoice">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @endcan
                                        
                                        @can('sales-orders.delete')
                                        @if($salesOrder->status === 'draft')
                                        <form action="{{ route('sales-orders.delete', $salesOrder) }}" 
                                              method="POST" 
                                              class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this sales order?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $salesOrders->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Sales Orders Found</h5>
                    <p class="text-muted">Start by creating your first sales order.</p>
                    @can('sales-orders.create')
                    <a href="{{ route('sales-orders.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>
                        Create Sales Order
                    </a>
                    @endcan
                </div>
                @endif
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
                Are you sure you want to delete this sales order? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
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
                            <option value="sent">Sent</option>
                            <option value="done">Done</option>
                            <option value="cancel">Cancel</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <strong>Status Change Rules:</strong><br>
                            • <strong>Accepted:</strong> Checks stock availability. If sufficient, creates delivery and reserves stock.<br>
                            • <strong>Waiting:</strong> Set automatically if stock is insufficient.<br>
                            • <strong>Done:</strong> Creates delivery, updates stock, and creates financial entries.
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
<script>
$(document).ready(function() {
    // Handle delete confirmation
    $('.delete-form').on('submit', function(e) {
        if (!confirm('Are you sure you want to delete this sales order? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
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
        
        // Disable current status option
        modal.find('#modal_new_status option').prop('disabled', false);
        modal.find('#modal_new_status option[value="' + currentStatus + '"]').prop('disabled', true);
        
        // Store sales order ID for form submission
        modal.data('sales-order-id', salesOrderId);
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
