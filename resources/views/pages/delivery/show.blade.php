@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck me-2"></i>
            Delivery Details
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('deliveries.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Back to List
            </a>
            @if(in_array($delivery->status, ['draft', 'waiting']))
                @can('deliveries.edit')
                <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i>
                    Edit
                </a>
                @endcan
            @endif
            @if($delivery->status === 'waiting')
                @can('deliveries.edit')
                <form action="{{ route('deliveries.check-stock', $delivery) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('Check stock availability for this delivery?')">
                        <i class="fas fa-search me-1"></i>
                        Check Stock
                    </button>
                </form>
                @endcan
            @endif
        </div>
    </div>

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
        <!-- Delivery Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Delivery Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Reference:</td>
                                    <td>{{ $delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @switch($delivery->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">Draft</span>
                                                @break
                                            @case('waiting')
                                                <span class="badge bg-warning text-dark">Waiting</span>
                                                @break
                                            @case('ready')
                                                <span class="badge bg-info">Ready</span>
                                                @break
                                            @case('done')
                                                <span class="badge bg-success">Delivered</span>
                                                @break
                                            @case('cancel')
                                                <span class="badge bg-danger">Cancelled</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($delivery->status) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Warehouse:</td>
                                    <td>{{ $delivery->warehouse->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Scheduled At:</td>
                                    <td>{{ $delivery->scheduled_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>{{ $delivery->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Last Updated:</td>
                                    <td>{{ $delivery->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Items:</td>
                                    <td>{{ $delivery->productLines->count() }} items</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Quantity:</td>
                                    <td>{{ $delivery->total_quantity }} units</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="fw-bold">Delivery Address:</h6>
                            <p class="text-muted">{{ $delivery->delivery_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Lines -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Lines</h6>
                </div>
                <div class="card-body">
                    @if($delivery->productLines->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($delivery->productLines as $productLine)
                                <tr>
                                    <td>
                                        <strong>{{ $productLine->product->name }}</strong>
                                        @if($productLine->product->description)
                                            <br><small class="text-muted">{{ $productLine->product->description }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $productLine->product->sku }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $productLine->quantity }}</span>
                                    </td>
                                    <td>{{ number_format($productLine->product->price, 0, ',', '.') }}</td>
                                    <td>{{ number_format($productLine->line_total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">No product lines found.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Management & History -->
        <div class="col-lg-4">
            <!-- Status Management -->
            @if(in_array($delivery->status, ['draft', 'waiting', 'ready']))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Management</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('deliveries.update-status', $delivery) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Update Status</label>
                            <select class="form-select" id="status" name="status" required>
                                @if($delivery->status === 'draft')
                                    <option value="waiting">Waiting</option>
                                    <option value="ready">Ready</option>
                                    <option value="cancel">Cancel</option>
                                @elseif($delivery->status === 'waiting')
                                    <option value="ready">Ready</option>
                                    <option value="cancel">Cancel</option>
                                @elseif($delivery->status === 'ready')
                                    <option value="waiting">Waiting</option>
                                    <option value="done">Done</option>
                                    <option value="cancel">Cancel</option>
                                @endif
                            </select>
                        </div>
                        
                        <!-- Status Help Information -->
                        <div class="alert alert-info small mb-3">
                            <strong>Status Meanings:</strong>
                            <ul class="mb-0 mt-1">
                                @if($delivery->status === 'draft')
                                    <li><strong>Waiting:</strong> Check stock availability</li>
                                    <li><strong>Ready:</strong> Stock reserved, ready for delivery</li>
                                    <li><strong>Cancel:</strong> Cancel this delivery</li>
                                @elseif($delivery->status === 'waiting')
                                    <li><strong>Ready:</strong> Stock reserved, ready for delivery</li>
                                    <li><strong>Cancel:</strong> Cancel this delivery</li>
                                @elseif($delivery->status === 'ready')
                                    <li><strong>Waiting:</strong> Return to waiting status</li>
                                    <li><strong>Done:</strong> Complete delivery (process goods issue)</li>
                                    <li><strong>Cancel:</strong> Cancel this delivery</li>
                                @endif
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-sync-alt me-1"></i>
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Goods Issue -->
            @can('deliveries.edit')
            @if($delivery->status === 'ready')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Goods Issue</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Process goods issue to update stock quantities and complete the delivery.
                    </p>
                    <button type="button" class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#goodsIssueModal">
                        <i class="fas fa-shipping-fast me-1"></i>
                        Process Goods Issue
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
                    @if($delivery->statusLogs->count() > 0)
                    <div class="timeline">
                        @foreach($delivery->statusLogs->sortByDesc('changed_at') as $statusLog)
                        <div class="timeline-item mb-3">
                            <div class="d-flex align-items-center">
                                <div class="timeline-marker me-3">
                                    @switch($statusLog->status)
                                        @case('draft')
                                            <i class="fas fa-edit text-secondary"></i>
                                            @break
                                        @case('waiting')
                                            <i class="fas fa-clock text-warning"></i>
                                            @break
                                        @case('ready')
                                            <i class="fas fa-check text-info"></i>
                                            @break
                                        @case('done')
                                            <i class="fas fa-check-circle text-success"></i>
                                            @break
                                        @case('cancel')
                                            <i class="fas fa-times-circle text-danger"></i>
                                            @break
                                        @default
                                            <i class="fas fa-circle text-secondary"></i>
                                    @endswitch
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-capitalize">{{ $statusLog->status }}</div>
                                    <small class="text-muted">{{ $statusLog->changed_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted mb-0">No status history available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Goods Issue Modal -->
<div class="modal fade" id="goodsIssueModal" tabindex="-1" aria-labelledby="goodsIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="goodsIssueModalLabel">
                    <i class="fas fa-shipping-fast text-success me-2"></i>
                    Confirm Goods Issue
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to process goods issue for this delivery?
                </p>
                <p class="text-muted small mt-2">
                    This action will:
                </p>
                <ul class="text-muted small">
                    <li>Update the delivery status to "Done" (final status)</li>
                    <li>Decrease stock quantities for all products</li>
                    <li>Process reserved stock for delivery</li>
                    <li>Create stock history records</li>
                    <li>Complete the delivery process</li>
                    <li><strong>Note: This action cannot be undone</strong></li>
                </ul>
                <p class="text-muted small mt-2">
                    Delivery Reference: <strong>{{ $delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT) }}</strong>
                </p>
                
                <form action="{{ route('deliveries.goods-issue', $delivery) }}" method="POST" id="goodsIssueForm">
                    @csrf
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="validate" name="validate" value="1" required>
                        <label class="form-check-label" for="validate">
                            I confirm that all items have been properly validated and are ready for delivery
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <button type="submit" form="goodsIssueForm" class="btn btn-success">
                    <i class="fas fa-shipping-fast me-1"></i>
                    Process Goods Issue
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Goods Issue modal is now handled by Bootstrap data attributes
</script>

<style>
.timeline-marker {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
}

.timeline-item:not(:last-child) {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 15px;
    top: 30px;
    bottom: -15px;
    width: 2px;
    background-color: #e9ecef;
}
</style>
@endsection
