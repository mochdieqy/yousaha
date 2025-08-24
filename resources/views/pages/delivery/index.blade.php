@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck me-2"></i>
            Delivery Management
        </h1>
        @can('deliveries.create')
        <a href="{{ route('deliveries.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>
            Create Delivery
        </a>
        @endcan
    </div>

    <!-- Delivery List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Delivery List</h6>
        </div>
        <div class="card-body">
            @if($deliveries->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Warehouse</th>
                            <th>Delivery Address</th>
                            <th>Scheduled At</th>
                            <th>Status</th>
                            <th>Total Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>
                            <td>
                                <strong>{{ $delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td>{{ $delivery->warehouse->name }}</td>
                            <td>{{ Str::limit($delivery->delivery_address, 50) }}</td>
                            <td>{{ $delivery->scheduled_at->format('M d, Y H:i') }}</td>
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
                            <td>
                                <span class="badge bg-primary">{{ $delivery->productLines->count() }} items</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('deliveries.view')
                                    <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    
                                    @if(in_array($delivery->status, ['draft', 'waiting']))
                                        @can('deliveries.edit')
                                        <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                    @endif
                                    
                                    @if($delivery->status === 'ready')
                                        @can('deliveries.edit')
                                        <button type="button" class="btn btn-success btn-sm" title="Goods Issue" 
                                                onclick="showGoodsIssueModal({{ $delivery->id }})">
                                            <i class="fas fa-shipping-fast"></i>
                                        </button>
                                        @endcan
                                    @elseif($delivery->status === 'waiting')
                                        @can('deliveries.edit')
                                        <button type="button" class="btn btn-info btn-sm" title="Check Stock Availability" 
                                                onclick="checkStockAvailability({{ $delivery->id }})">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @endcan
                                    @endif
                                    
                                    @if($delivery->status === 'draft')
                                        @can('deliveries.delete')
                                        <button type="button" class="btn btn-danger btn-sm" title="Delete" 
                                                onclick="confirmDelete({{ $delivery->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $deliveries->links() }}
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No deliveries found</h5>
                <p class="text-muted">Start by creating your first delivery order.</p>
                @can('deliveries.create')
                <a href="{{ route('deliveries.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Create Delivery
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Goods Issue Modal -->
<div class="modal fade" id="goodsIssueModal" tabindex="-1" aria-labelledby="goodsIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="goodsIssueForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="goodsIssueModalLabel">Process Goods Issue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to process this goods issue?</p>
                    <p class="text-muted">This will update the stock quantities and mark the delivery as completed.</p>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="validate" name="validate" value="1" required>
                        <label class="form-check-label" for="validate">
                            I confirm that all items have been properly validated
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-shipping-fast me-1"></i>
                        Process Goods Issue
                    </button>
                </div>
            </form>
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
                <p>Are you sure you want to delete this delivery?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function showGoodsIssueModal(deliveryId) {
    const form = document.getElementById('goodsIssueForm');
    form.action = `/deliveries/${deliveryId}/goods-issue`;
    
    const modal = new bootstrap.Modal(document.getElementById('goodsIssueModal'));
    modal.show();
}

function confirmDelete(deliveryId) {
    const form = document.getElementById('deleteForm');
    form.action = `/deliveries/${deliveryId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function checkStockAvailability(deliveryId) {
    if (confirm('Check stock availability for this delivery? This will automatically update the status if stock is available.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/deliveries/${deliveryId}/check-stock`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

// Initialize DataTable
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]]
    });
});
</script>
@endsection
