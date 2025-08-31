@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-eye text-primary me-2"></i>
                    Delivery Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('deliveries.index') }}">Deliveries</a></li>
                        <li class="breadcrumb-item active">#{{ $delivery->id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('deliveries.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Deliveries
                </a>
                @if(in_array($delivery->status, ['draft', 'waiting']))
                    @can('deliveries.edit')
                    <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>
                        Edit Delivery
                    </a>
                    @endcan
                @endif
            </div>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ Auth::user()->currentCompany->name }}
                    <br>
                    <small class="text-muted">Delivery details for this company</small>
                </div>
            </div>
        </div>

        <!-- Status and Actions -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Delivery Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Reference</label>
                                    <p class="mb-0">{{ $delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <div>
                                        @switch($delivery->status)
                                            @case('draft')
                                                <span class="badge bg-secondary fs-6">
                                                    <i class="fas fa-edit me-1"></i>Draft
                                                </span>
                                                @break
                                            @case('waiting')
                                                <span class="badge bg-warning text-dark fs-6">
                                                    <i class="fas fa-clock me-1"></i>Waiting
                                                </span>
                                                @break
                                            @case('ready')
                                                <span class="badge bg-info fs-6">
                                                    <i class="fas fa-check-circle me-1"></i>Ready
                                                </span>
                                                @break
                                            @case('done')
                                                <span class="badge bg-success fs-6">
                                                    <i class="fas fa-shipping-fast me-1"></i>Delivered
                                                </span>
                                                @break
                                            @case('cancel')
                                                <span class="badge bg-danger fs-6">
                                                    <i class="fas fa-times me-1"></i>Cancelled
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary fs-6">{{ ucfirst($delivery->status) }}</span>
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Warehouse</label>
                                    <p class="mb-0">
                                        <i class="fas fa-warehouse me-2 text-primary"></i>
                                        {{ $delivery->warehouse->name }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Scheduled At</label>
                                    <p class="mb-0">
                                        <i class="fas fa-calendar me-2 text-primary"></i>
                                        {{ $delivery->scheduled_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Delivery Address</label>
                            <p class="mb-0">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                {{ $delivery->delivery_address }}
                            </p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created At</label>
                                    <p class="mb-0">
                                        <i class="fas fa-clock me-2 text-muted"></i>
                                        {{ $delivery->created_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Last Updated</label>
                                    <p class="mb-0">
                                        <i class="fas fa-edit me-2 text-muted"></i>
                                        {{ $delivery->updated_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($delivery->status === 'ready')
                            @can('deliveries.edit')
                            <button type="button" class="btn btn-success w-100 mb-2" onclick="showGoodsIssueModal()">
                                <i class="fas fa-shipping-fast me-2"></i>
                                Process Goods Issue
                            </button>
                            <button type="button" class="btn btn-warning w-100 mb-2" onclick="showCancelModal()">
                                <i class="fas fa-times me-2"></i>
                                Cancel Delivery
                            </button>
                            @endcan
                        @elseif($delivery->status === 'waiting')
                            @can('deliveries.edit')
                            <button type="button" class="btn btn-info w-100 mb-2" onclick="checkStockAvailability()">
                                <i class="fas fa-search me-2"></i>
                                Check Stock Availability
                            </button>
                            @endcan
                        @endif
                        
                        @if(in_array($delivery->status, ['draft', 'waiting']))
                            @can('deliveries.edit')
                            <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-edit me-2"></i>
                                Edit Delivery
                            </a>
                            @endcan
                        @endif
                        
                        @if($delivery->status === 'draft')
                            @can('deliveries.delete')
                            <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                                <i class="fas fa-trash me-2"></i>
                                Delete Delivery
                            </button>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Lines -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    Product Lines ({{ $delivery->productLines->count() }} items)
                </h5>
            </div>
            <div class="card-body p-0">
                @if($delivery->productLines->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Product</th>
                                <th class="border-0">SKU</th>
                                <th class="border-0">Quantity</th>
                                <th class="border-0">Stock Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($delivery->productLines as $productLine)
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
                                <td>
                                    <span class="badge bg-primary fs-6">{{ number_format($productLine->quantity) }}</span>
                                </td>
                                <td>
                                    @php
                                        $stock = App\Models\Stock::where('company_id', $delivery->company_id)
                                            ->where('warehouse_id', $delivery->warehouse_id)
                                            ->where('product_id', $productLine->product_id)
                                            ->first();
                                        
                                        $stockQuantity = $stock ? $stock->quantity_saleable : 0;
                                        $isAvailable = $stockQuantity >= $productLine->quantity;
                                    @endphp
                                    
                                    @if($isAvailable)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Available
                                        </span>
                                        <br><small class="text-muted">Stock: {{ number_format($stockQuantity) }}</small>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Insufficient
                                        </span>
                                        <br><small class="text-muted">Stock: {{ number_format($stockQuantity) }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-boxes fa-2x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No products in this delivery.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Status History -->
        @if($delivery->statusLogs->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Status History
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Status</th>
                                <th class="border-0">Changed At</th>
                                <th class="border-0">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($delivery->statusLogs->sortByDesc('changed_at') as $statusLog)
                            <tr>
                                <td>
                                    @switch($statusLog->status)
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
                                            <span class="badge bg-secondary">{{ ucfirst($statusLog->status) }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $statusLog->changed_at->format('M d, Y H:i:s') }}</td>
                                <td>
                                    @if($loop->index < $delivery->statusLogs->count() - 1)
                                        @php
                                            $nextLog = $delivery->statusLogs->sortByDesc('changed_at')->values()[$loop->index + 1];
                                            $duration = $statusLog->changed_at->diffForHumans($nextLog->changed_at, true);
                                        @endphp
                                        {{ $duration }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
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
                    <button type="button" class="btn-close" onclick="closeGoodsIssueModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to process goods issue for delivery <strong id="goodsIssueDeliveryReference"></strong>?</p>
                    <p class="text-muted">This will update the stock quantities and mark the delivery as completed.</p>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="validate" name="validate" value="1" required>
                        <label class="form-check-label" for="validate">
                            I confirm that all items have been properly validated
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeGoodsIssueModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-shipping-fast me-2"></i>
                        Process Goods Issue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Delivery Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cancelForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Delivery</h5>
                    <button type="button" class="btn-close" onclick="closeCancelModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel the delivery "<strong id="cancelDeliveryReference"></strong>"?</p>
                    <p class="text-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> This will release all reserved stock back to saleable inventory.
                    </p>
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-info-circle me-2"></i>What happens when you cancel:</h6>
                        <ul class="mb-0">
                            <li>Delivery status will change to "Cancelled"</li>
                            <li>Reserved stock quantities will be released back to saleable inventory</li>
                            <li>Stock history will be updated to reflect the cancellation</li>
                            <li>This action cannot be undone</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCancelModal()">Keep Delivery</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>
                        Cancel Delivery
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
                <button type="button" class="btn-close" onclick="closeDeleteModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the delivery "<strong id="deleteDeliveryReference"></strong>"?</p>
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
                        Delete Delivery
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let goodsIssueModalInstance = null;
let cancelModalInstance = null;
let deleteModalInstance = null;

// Global function to close any modal by ID
function closeModalById(modalId) {
    const modalElement = document.getElementById(modalId);
    if (!modalElement) return;
    
    // Method 1: Try to get existing Bootstrap instance
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
        return;
    }
    
    // Method 2: Create new instance and hide immediately
    try {
        const newModal = new bootstrap.Modal(modalElement);
        newModal.hide();
    } catch (error) {
        console.error('Error closing modal:', error);
    }
    
    // Method 3: Manual hide using CSS classes
    modalElement.classList.remove('show');
    modalElement.style.display = 'none';
    document.body.classList.remove('modal-open');
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
    
    // Method 4: Force remove all modal-related classes and styles
    modalElement.classList.remove('fade', 'show');
    modalElement.style.display = '';
    modalElement.style.paddingRight = '';
    document.body.classList.remove('modal-open');
    document.body.style.paddingRight = '';
    document.body.style.overflow = '';
    
    // Remove any remaining backdrop
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => backdrop.remove());
}

function showGoodsIssueModal() {
    const form = document.getElementById('goodsIssueForm');
    form.action = `/deliveries/{{ $delivery->id }}/goods-issue`;
    
    document.getElementById('goodsIssueDeliveryReference').textContent = `{{ $delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT) }}`;

    goodsIssueModalInstance = new bootstrap.Modal(document.getElementById('goodsIssueModal'));
    goodsIssueModalInstance.show();
}

function closeGoodsIssueModal() {
    if (goodsIssueModalInstance) {
        goodsIssueModalInstance.hide();
        return;
    }
    closeModalById('goodsIssueModal');
}

function showCancelModal() {
    const form = document.getElementById('cancelForm');
    form.action = `/deliveries/{{ $delivery->id }}/cancel`;
    
    document.getElementById('cancelDeliveryReference').textContent = `{{ $delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT) }}`;

    cancelModalInstance = new bootstrap.Modal(document.getElementById('cancelModal'));
    cancelModalInstance.show();
}

function closeCancelModal() {
    if (cancelModalInstance) {
        cancelModalInstance.hide();
        return;
    }
    closeModalById('cancelModal');
}

function confirmDelete() {
    const form = document.getElementById('deleteForm');
    form.action = `/deliveries/{{ $delivery->id }}`;
    
    document.getElementById('deleteDeliveryReference').textContent = `{{ $delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT) }}`;

    deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModalInstance.show();
}

function closeDeleteModal() {
    if (deleteModalInstance) {
        deleteModalInstance.hide();
        return;
    }
    closeModalById('deleteModal');
}

function checkStockAvailability() {
    if (confirm('Check stock availability for this delivery? This will automatically update the status if stock is available.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/deliveries/{{ $delivery->id }}/check-stock`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside or pressing ESC
document.addEventListener('DOMContentLoaded', function() {
    const goodsIssueModalElement = document.getElementById('goodsIssueModal');
    const cancelModalElement = document.getElementById('cancelModal');
    const deleteModalElement = document.getElementById('deleteModal');
    
    // Debug function to test modal functionality
    window.testModalClose = function() {
        console.log('Testing modal close functionality...');
        console.log('Goods Issue Modal Instance:', goodsIssueModalInstance);
        console.log('Cancel Modal Instance:', cancelModalInstance);
        console.log('Delete Modal Instance:', deleteModalInstance);
        
        // Test closing goods issue modal
        if (goodsIssueModalInstance) {
            console.log('Closing goods issue modal via instance...');
            goodsIssueModalInstance.hide();
        } else {
            console.log('No goods issue modal instance, using fallback...');
            closeModalById('goodsIssueModal');
        }
    };
    
    // Close modals when clicking outside
    goodsIssueModalElement.addEventListener('click', function(event) {
        if (event.target === goodsIssueModalElement) {
            console.log('Clicking outside goods issue modal, closing...');
            closeGoodsIssueModal();
        }
    });
    
    cancelModalElement.addEventListener('click', function(event) {
        if (event.target === cancelModalElement) {
            console.log('Clicking outside cancel modal, closing...');
            closeCancelModal();
        }
    });

    deleteModalElement.addEventListener('click', function(event) {
        if (event.target === deleteModalElement) {
            console.log('Clicking outside delete modal, closing...');
            closeDeleteModal();
        }
    });
    
    // Close modals when pressing ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            console.log('ESC key pressed, closing modals...');
            closeGoodsIssueModal();
            closeCancelModal();
            closeDeleteModal();
        }
    });
    
    // Additional fallback: Add event listeners to all close buttons
    const closeButtons = document.querySelectorAll('[onclick*="close"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Close button clicked:', this.getAttribute('onclick'));
            e.preventDefault();
            const onclick = this.getAttribute('onclick');
            if (onclick.includes('closeGoodsIssueModal')) {
                closeGoodsIssueModal();
            } else if (onclick.includes('closeCancelModal')) {
                closeCancelModal();
            } else if (onclick.includes('closeDeleteModal')) {
                closeDeleteModal();
            }
        });
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
