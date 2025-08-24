@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-eye text-primary me-2"></i>
                    Sales Order Details: {{ $salesOrder->number }}
                </h5>
                <div class="d-flex gap-2">
                    @can('sales-orders.edit')
                    @if(!in_array($salesOrder->status, ['done', 'cancel']))
                    <a href="{{ route('sales-orders.edit', $salesOrder) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>
                        Edit
                    </a>
                    @endif
                    @endcan
                    
                    @can('sales-orders.generate-quotation')
                    @if($salesOrder->status === 'draft')
                    <form action="{{ route('sales-orders.generate-quotation', $salesOrder) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="fas fa-file-pdf me-1"></i>
                            Generate Quotation
                        </button>
                    </form>
                    @endif
                    @endcan
                    
                    @can('sales-orders.generate-invoice')
                    @if($salesOrder->status !== 'draft')
                    <form action="{{ route('sales-orders.generate-invoice', $salesOrder) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-file-invoice-dollar me-1"></i>
                            Generate Invoice
                        </button>
                    </form>
                    @endif
                    @endcan
                    
                    <a href="{{ route('sales-orders.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Sales Orders
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Order Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Order Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">Order Number:</td>
                                <td>{{ $salesOrder->number }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
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
                                    <span class="badge bg-{{ $statusColor }} fs-6">
                                        {{ ucfirst($salesOrder->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Created:</td>
                                <td>{{ $salesOrder->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Deadline:</td>
                                <td>
                                    @if($salesOrder->isOverdue())
                                        <span class="text-danger fw-bold">
                                            {{ $salesOrder->deadline->format('M d, Y') }}
                                            <i class="fas fa-exclamation-triangle ms-1"></i>
                                            (Overdue)
                                        </span>
                                    @else
                                        {{ $salesOrder->deadline->format('M d, Y') }}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Customer & Warehouse</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">Customer:</td>
                                <td>{{ $salesOrder->customer->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Warehouse:</td>
                                <td>{{ $salesOrder->warehouse->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Salesperson:</td>
                                <td>{{ $salesOrder->salesperson }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total Amount:</td>
                                <td>
                                    <span class="fw-bold text-success fs-5">
                                        {{ number_format($salesOrder->total, 2) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Activities -->
                @if($salesOrder->activities)
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Activities</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            {{ $salesOrder->activities }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Products -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Products</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesOrder->productLines as $productLine)
                                <tr>
                                    <td>
                                        <strong>{{ $productLine->product->name }}</strong>
                                        @if($productLine->product->description)
                                            <br><small class="text-muted">{{ $productLine->product->description }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $productLine->product->sku }}</td>
                                    <td>{{ $productLine->quantity }}</td>
                                    <td>{{ number_format($productLine->product->price, 2) }}</td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            {{ number_format($productLine->line_total, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td>
                                        <span class="fw-bold text-success fs-5">
                                            {{ number_format($salesOrder->total, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Status Update Form -->
                @can('sales-orders.edit')
                @if(!in_array($salesOrder->status, ['done', 'cancel']))
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Update Status</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <form action="{{ route('sales-orders.update-status', $salesOrder) }}" method="POST">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label for="status" class="form-label fw-bold">New Status</label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="">Select Status</option>
                                            <option value="draft" {{ $salesOrder->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="waiting" {{ $salesOrder->status === 'waiting' ? 'selected' : '' }}>Waiting</option>
                                            <option value="accepted" {{ $salesOrder->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                                            <option value="sent" {{ $salesOrder->status === 'sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="done" {{ $salesOrder->status === 'done' ? 'selected' : '' }}>Done</option>
                                            <option value="cancel" {{ $salesOrder->status === 'cancel' ? 'selected' : '' }}>Cancel</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Update Status
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endcan

                <!-- Status History -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Status History</h6>
                    <div class="timeline">
                        @foreach($salesOrder->statusLogs->sortBy('changed_at') as $statusLog)
                        <div class="timeline-item d-flex align-items-center mb-3">
                            <div class="timeline-marker bg-primary rounded-circle me-3" style="width: 12px; height: 12px;"></div>
                            <div class="timeline-content">
                                <div class="fw-bold">{{ ucfirst($statusLog->status) }}</div>
                                <small class="text-muted">{{ $statusLog->changed_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Related Records -->
                @if($salesOrder->status === 'done')
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Related Records</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-truck text-primary me-2"></i>
                                        Delivery Record
                                    </h6>
                                    <p class="card-text text-muted">
                                        A delivery record has been automatically created when this sales order was completed.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-dollar-sign text-success me-2"></i>
                                        Financial Records
                                    </h6>
                                    <p class="card-text text-muted">
                                        Income and general ledger entries have been created for this completed order.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Status Update Information -->
                @if(!in_array($salesOrder->status, ['done', 'cancel']))
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Status Update Information</h6>
                    <div class="card bg-info bg-opacity-10 border-info">
                        <div class="card-body">
                            <h6 class="card-title text-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Important Notes
                            </h6>
                            <ul class="mb-0">
                                <li><strong>Accepted:</strong> Will check stock availability and create delivery if stock is sufficient</li>
                                <li><strong>Done:</strong> Will complete the order, update stock, create delivery, income, and general ledger entries</li>
                                <li><strong>Waiting:</strong> Status will automatically change to waiting if insufficient stock when trying to accept</li>
                                <li><strong>Cancel:</strong> Will cancel the order and release any reserved stock</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    @can('sales-orders.delete')
                    @if($salesOrder->status === 'draft')
                    <form action="{{ route('sales-orders.delete', $salesOrder) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>
                            Delete Sales Order
                        </button>
                    </form>
                    @endif
                    @endcan
                    
                    <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Sales Orders
                    </a>
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
                Are you sure you want to delete this sales order? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
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
    
    // Handle status update form submission
    $('form[action*="update-status"]').on('submit', function(e) {
        const status = $('#status').val();
        if (!status) {
            e.preventDefault();
            alert('Please select a status to update to.');
            return false;
        }
        
        // Show confirmation for certain status changes
        if (status === 'done') {
            if (!confirm('Are you sure you want to mark this sales order as done? This will:\n- Update stock quantities\n- Create delivery record\n- Create income record\n- Create general ledger entries\n\nThis action cannot be easily undone.')) {
                e.preventDefault();
                return false;
            }
        } else if (status === 'cancel') {
            if (!confirm('Are you sure you want to cancel this sales order? This will release any reserved stock.')) {
                e.preventDefault();
                return false;
            }
        }
    });
    

});
</script>

<style>
.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    flex-shrink: 0;
}

.timeline-content {
    flex: 1;
}
</style>
@endsection
