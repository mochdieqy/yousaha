@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-receipt text-primary me-2"></i>
            Receipt Details
        </h1>
        <div>
            <a href="{{ route('receipts.index') }}" class="btn btn-secondary btn-sm me-2">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Receipts
            </a>
            
            @can('receipts.edit')
            @if(in_array($receipt->status, ['draft', 'waiting']))
            <a href="{{ route('receipts.edit', $receipt) }}" class="btn btn-warning btn-sm me-2">
                <i class="fas fa-edit me-1"></i>
                Edit Receipt
            </a>
            @endif
            @endcan
            
            @can('receipts.delete')
            @if($receipt->status === 'draft')
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteReceiptModal">
                <i class="fas fa-trash me-1"></i>
                Delete Receipt
            </button>
            @endif
            @endcan
        </div>
    </div>

    

    <div class="row">
        <!-- Receipt Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Receipt Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Reference:</strong></td>
                                    <td>{{ $receipt->reference ?: 'REC-' . str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Supplier:</strong></td>
                                    <td>{{ $receipt->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Warehouse:</strong></td>
                                    <td>{{ $receipt->warehouse->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Scheduled Date:</strong></td>
                                    <td>{{ $receipt->scheduled_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'waiting' => 'warning',
                                                'ready' => 'info',
                                                'done' => 'success',
                                                'cancel' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$receipt->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }} fs-6">
                                            {{ ucfirst($receipt->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Quantity:</strong></td>
                                    <td>{{ $receipt->total_quantity }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
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
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receipt->productLines as $productLine)
                                <tr>
                                    <td>{{ $productLine->product->name }}</td>
                                    <td>{{ $productLine->quantity }}</td>
                                    <td>Rp {{ number_format($productLine->product->cost ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($productLine->line_total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Management & Actions -->
        <div class="col-lg-4">
            <!-- Status Management -->
            @can('receipts.edit')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Management</h6>
                </div>
                <div class="card-body">
                    @if($receipt->status === 'done')
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Receipt Completed</strong><br>
                            This receipt has been completed through goods receiving. The status cannot be changed.
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="status" class="form-label">Change Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                @php
                                    // Define allowed status transitions based on current status
                                    $allowedTransitions = [
                                        'draft' => ['waiting', 'cancel'],
                                        'waiting' => ['ready', 'cancel'],
                                        'ready' => ['done', 'cancel'],
                                        'done' => [],
                                        'cancel' => ['draft']
                                    ];
                                    $currentStatus = $receipt->status;
                                    $availableStatuses = $allowedTransitions[$currentStatus] ?? [];
                                @endphp
                                
                                @foreach($availableStatuses as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            @if(empty($availableStatuses))
                                <small class="text-muted">No status changes available for completed receipts.</small>
                            @else
                                <small class="text-muted">
                                    Available transitions: {{ implode(', ', array_map('ucfirst', $availableStatuses)) }}
                                </small>
                            @endif
                        </div>
                                                <button type="button" class="btn btn-primary btn-sm w-100" id="updateStatusBtn">
                            <i class="fas fa-sync-alt me-1"></i>
                            Update Status
                        </button>
                    @endif
                </div>
            </div>
            @endcan

            <!-- Goods Receiving -->
            @can('receipts.edit')
            @if($receipt->status === 'ready')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Goods Receiving</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Process goods receiving to update stock quantities and complete the receipt.
                    </p>
                    <button type="button" class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#goodsReceiveModal">
                        <i class="fas fa-check me-1"></i>
                        Process Goods Receiving
                    </button>
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
                        @foreach($receipt->statusLogs->sortBy('changed_at') as $statusLog)
                        <div class="timeline-item mb-2">
                            <div class="d-flex align-items-center">
                                <div class="timeline-marker bg-primary rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                                <div>
                                    <div class="fw-bold">{{ ucfirst($statusLog->status) }}</div>
                                    <small class="text-muted">{{ $statusLog->changed_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Receipt Modal -->
<div class="modal fade" id="deleteReceiptModal" tabindex="-1" aria-labelledby="deleteReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteReceiptModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirm Delete Receipt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to delete this receipt? 
                    <strong>This action cannot be undone.</strong>
                </p>
                <p class="text-muted small mt-2">
                    Receipt Reference: <strong>{{ $receipt->reference ?: 'REC-' . str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <form action="{{ route('receipts.delete', $receipt) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Delete Receipt
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Goods Receive Modal -->
<div class="modal fade" id="goodsReceiveModal" tabindex="-1" aria-labelledby="goodsReceiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="goodsReceiveModalLabel">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Confirm Goods Receiving
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to process goods receiving for this receipt?
                </p>
                <p class="text-muted small mt-2">
                    This action will:
                </p>
                <ul class="text-muted small">
                    <li>Update the receipt status to "Done" (final status)</li>
                    <li>Increase stock quantities for all products</li>
                    <li>Create stock history records</li>
                    <li>Complete the receipt process</li>
                    <li><strong>Note: This action cannot be undone</strong></li>
                </ul>
                <p class="text-muted small mt-2">
                    Receipt Reference: <strong>{{ $receipt->reference ?: 'REC-' . str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <form action="{{ route('receipts.goods-receive', $receipt) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>
                        Process Goods Receiving
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusUpdateModalLabel">
                    <i class="fas fa-sync-alt text-primary me-2"></i>
                    Confirm Status Update
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to change the receipt status from 
                    <strong>{{ ucfirst($receipt->status) }}</strong> to 
                    <strong id="newStatusText"></strong>?
                </p>
                <div id="statusChangeDetails" class="mt-3" style="display: none;">
                    <p class="text-muted small mb-2">This change will:</p>
                    <ul class="text-muted small" id="statusChangeList">
                    </ul>
                </div>
                <p class="text-muted small mt-2">
                    Receipt Reference: <strong>{{ $receipt->reference ?: 'REC-' . str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <form action="{{ route('receipts.update-status', $receipt) }}" method="POST" id="statusUpdateForm">
                    @csrf
                    <input type="hidden" name="status" id="statusInput">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync-alt me-1"></i>
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">
                    <i class="fas fa-exclamation-circle text-danger me-2"></i>
                    Error
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Status change confirmation
    $('#status').change(function() {
        const newStatus = $(this).val();
        const currentStatus = '{{ $receipt->status }}';
        
        if (newStatus && newStatus !== currentStatus) {
            // Reset to current status if no new status selected
            if (!newStatus) {
                $(this).val(currentStatus);
                return;
            }
        }
    });

    // Update Status button click
    $('#updateStatusBtn').click(function() {
        const newStatus = $('#status').val();
        const currentStatus = '{{ $receipt->status }}';
        
        if (!newStatus) {
            showErrorModal('Please select a status first.');
            return;
        }
        
        if (newStatus === currentStatus) {
            showErrorModal('Please select a different status.');
            return;
        }
        
        // Check if receipt is already completed
        if (currentStatus === 'done') {
            showErrorModal('Cannot change status of a completed receipt. Use goods receiving to complete receipts.');
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
            $('#status').val('');
            return;
        }
        
        // Update modal content
        $('#newStatusText').text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
        $('#statusInput').val(newStatus);
        
        // Show/hide status change details based on the new status
        const detailsDiv = $('#statusChangeDetails');
        const detailsList = $('#statusChangeList');
        
        if (newStatus === 'ready' && currentStatus === 'waiting') {
            detailsDiv.show();
            detailsList.html(`
                <li>Update stock quantities for all products</li>
                <li>Prepare receipt for goods receiving</li>
            `);
        } else if (newStatus === 'cancel') {
            detailsDiv.show();
            detailsList.html(`
                <li>Cancel the receipt process</li>
                <li>No stock changes will be made</li>
            `);
        } else if (newStatus === 'done') {
            detailsDiv.show();
            detailsList.html(`
                <li>Complete the receipt process</li>
                <li>Finalize stock updates</li>
                <li>Receipt will be marked as completed</li>
            `);
        } else {
            detailsDiv.hide();
        }
        
        // Show the modal
        $('#statusUpdateModal').modal('show');
    });
});

// Show error modal
function showErrorModal(message) {
    $('#errorMessage').text(message);
    $('#errorModal').modal('show');
}
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
</style>
@endsection
