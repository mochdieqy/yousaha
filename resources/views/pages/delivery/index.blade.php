@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-truck text-primary me-2"></i>
                    Delivery Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Deliveries</li>
                    </ol>
                </nav>
            </div>
            @can('deliveries.create')
            <a href="{{ route('deliveries.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Delivery
            </a>
            @endcan
        </div>

        <!-- Deliveries Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Delivery List
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <span class="badge bg-info text-white">
                                <i class="fas fa-building me-1"></i>
                                {{ Auth::user()->currentCompany->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Search and Filter Form -->
                <div class="p-3 border-bottom">
                    <form method="GET" action="{{ route('deliveries.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search deliveries..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                                <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancel" {{ request('status') === 'cancel' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="warehouse_id" class="form-select">
                                <option value="">All Warehouses</option>
                                @foreach($warehouses ?? [] as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            @if(request('search') || request('status') || request('warehouse_id'))
                                <a href="{{ route('deliveries.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @if($deliveries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Reference</th>
                                <th class="border-0">Warehouse</th>
                                <th class="border-0">Delivery Address</th>
                                <th class="border-0">Scheduled At</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Total Items</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deliveries as $delivery)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-truck text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT) }}</h6>
                                            @if($delivery->reference)
                                                <small class="text-muted">ID: {{ $delivery->id }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $delivery->warehouse->name }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ Str::limit($delivery->delivery_address, 40) }}</strong>
                                        @if(strlen($delivery->delivery_address) > 40)
                                            <br><small class="text-muted" title="{{ $delivery->delivery_address }}">{{ Str::limit($delivery->delivery_address, 40) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $delivery->scheduled_at->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $delivery->scheduled_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @switch($delivery->status)
                                        @case('draft')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-edit me-1"></i>Draft
                                            </span>
                                            @break
                                        @case('waiting')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>Waiting
                                            </span>
                                            @break
                                        @case('ready')
                                            <span class="badge bg-info">
                                                <i class="fas fa-check-circle me-1"></i>Ready
                                            </span>
                                            @break
                                        @case('done')
                                            <span class="badge bg-success">
                                                <i class="fas fa-shipping-fast me-1"></i>Delivered
                                            </span>
                                            @break
                                        @case('cancel')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Cancelled
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($delivery->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-boxes me-1"></i>{{ $delivery->productLines->count() }} items
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('deliveries.view')
                                        <a href="{{ route('deliveries.show', $delivery) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View Delivery">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @if(in_array($delivery->status, ['draft', 'waiting']))
                                            @can('deliveries.edit')
                                            <a href="{{ route('deliveries.edit', $delivery) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Edit Delivery">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        @endif
                                        
                                        @if($delivery->status === 'ready')
                                            @can('deliveries.edit')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success" 
                                                    title="Process Goods Issue" 
                                                    onclick="showGoodsIssueModal({{ $delivery->id }}, '{{ addslashes($delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT)) }}')">
                                                <i class="fas fa-shipping-fast"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-warning" 
                                                    title="Cancel Delivery" 
                                                    onclick="showCancelModal({{ $delivery->id }}, '{{ addslashes($delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT)) }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endcan
                                        @elseif($delivery->status === 'waiting')
                                            @can('deliveries.edit')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info" 
                                                    title="Check Stock Availability" 
                                                    onclick="checkStockAvailability({{ $delivery->id }})">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            @endcan
                                        @endif
                                        
                                        @if($delivery->status === 'draft')
                                            @can('deliveries.delete')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Delete Delivery" 
                                                    onclick="confirmDelete({{ $delivery->id }}, '{{ addslashes($delivery->reference ?: 'DEL-' . str_pad($delivery->id, 6, '0', STR_PAD_LEFT)) }}')">
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
                @else
                <div class="text-center py-5">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Deliveries Found</h5>
                    <p class="text-muted">Start by creating your first delivery order.</p>
                    @can('deliveries.create')
                    <a href="{{ route('deliveries.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add First Delivery
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            @if($deliveries->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $deliveries->firstItem() ?? 0 }} to {{ $deliveries->lastItem() ?? 0 }} of {{ $deliveries->total() }} deliveries
                            @if($deliveries->total() > 0)
                                (Page {{ $deliveries->currentPage() }} of {{ $deliveries->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($deliveries->total() > 10)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 10</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $deliveries->links() }}
                        </div>
                    </div>
                </div>
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
                    <button type="button" class="btn-close" onclick="closeGoodsIssueModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to process goods issue for delivery <strong id="deliveryReference"></strong>?</p>
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
<div class="modal fade" id="cancelDeliveryModal" tabindex="-1" aria-labelledby="cancelDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cancelDeliveryForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelDeliveryModalLabel">Confirm Cancel Delivery</h5>
                    <button type="button" class="btn-close" onclick="closeCancelDeliveryModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel the delivery "<strong id="cancelDeliveryName"></strong>"?</p>
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCancelDeliveryModal()">Cancel</button>
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
                <p>Are you sure you want to delete the delivery "<strong id="deleteDeliveryName"></strong>"?</p>
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
let cancelDeliveryModalInstance = null;
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

function showGoodsIssueModal(deliveryId, deliveryReference) {
    document.getElementById('deliveryReference').textContent = deliveryReference;
    document.getElementById('goodsIssueForm').action = `/deliveries/${deliveryId}/goods-issue`;
    
    goodsIssueModalInstance = new bootstrap.Modal(document.getElementById('goodsIssueModal'));
    goodsIssueModalInstance.show();
}

function showCancelModal(deliveryId, deliveryReference) {
    document.getElementById('cancelDeliveryName').textContent = deliveryReference;
    document.getElementById('cancelDeliveryForm').action = `/deliveries/${deliveryId}/cancel`;
    
    cancelDeliveryModalInstance = new bootstrap.Modal(document.getElementById('cancelDeliveryModal'));
    cancelDeliveryModalInstance.show();
}

function confirmDelete(deliveryId, deliveryName) {
    document.getElementById('deleteDeliveryName').textContent = deliveryName;
    document.getElementById('deleteForm').action = `/deliveries/${deliveryId}`;
    
    deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModalInstance.show();
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

// Close modal functions
function closeGoodsIssueModal() {
    if (goodsIssueModalInstance) {
        goodsIssueModalInstance.hide();
        return;
    }
    closeModalById('goodsIssueModal');
}

function closeCancelDeliveryModal() {
    if (cancelDeliveryModalInstance) {
        cancelDeliveryModalInstance.hide();
        return;
    }
    closeModalById('cancelDeliveryModal');
}

function closeDeleteModal() {
    if (deleteModalInstance) {
        deleteModalInstance.hide();
        return;
    }
    closeModalById('deleteModal');
}

// Close modal when clicking outside or pressing ESC
document.addEventListener('DOMContentLoaded', function() {
    const goodsIssueModalElement = document.getElementById('goodsIssueModal');
    const cancelDeliveryModalElement = document.getElementById('cancelDeliveryModal');
    const deleteModalElement = document.getElementById('deleteModal');
    const goodsIssueForm = document.getElementById('goodsIssueForm');
    const cancelDeliveryForm = document.getElementById('cancelDeliveryForm');
    const deleteForm = document.getElementById('deleteForm');
    
    // Debug function to test modal functionality
    window.testModalClose = function() {
        console.log('Testing modal close functionality...');
        console.log('Goods Issue Modal Instance:', goodsIssueModalInstance);
        console.log('Cancel Delivery Modal Instance:', cancelDeliveryModalInstance);
        console.log('Delete Modal Instance:', deleteModalInstance);
        
        // Test closing all modals
        closeGoodsIssueModal();
        closeCancelDeliveryModal();
        closeDeleteModal();
    };
    
    // Close modals when clicking outside
    goodsIssueModalElement.addEventListener('click', function(event) {
        if (event.target === goodsIssueModalElement) {
            console.log('Clicking outside goods issue modal, closing...');
            closeGoodsIssueModal();
        }
    });
    
    cancelDeliveryModalElement.addEventListener('click', function(event) {
        if (event.target === cancelDeliveryModalElement) {
            console.log('Clicking outside cancel delivery modal, closing...');
            closeCancelDeliveryModal();
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
            closeCancelDeliveryModal();
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
            } else if (onclick.includes('closeCancelDeliveryModal')) {
                closeCancelDeliveryModal();
            } else if (onclick.includes('closeDeleteModal')) {
                closeDeleteModal();
            }
        });
    });
    
    // Handle form submissions with loading state
    goodsIssueForm.addEventListener('submit', function() {
        const submitBtn = goodsIssueForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 10000);
    });
    
    cancelDeliveryForm.addEventListener('submit', function() {
        const submitBtn = cancelDeliveryForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cancelling...';
        
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 10000);
    });
    
    deleteForm.addEventListener('submit', function() {
        const submitBtn = deleteForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
        
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
});
</script>
@endsection
