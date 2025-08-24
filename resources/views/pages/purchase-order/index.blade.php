@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shopping-cart text-info me-2"></i>
            Purchase Orders
        </h1>
        @can('purchase-orders.create')
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>
            Create Purchase Order
        </a>
        @endcan
    </div>

    <!-- Purchase Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Orders List</h6>
        </div>
        <div class="card-body">
            @if($purchaseOrders->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="purchaseOrdersTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Order Number</th>
                            <th>Supplier</th>
                            <th>Warehouse</th>
                            <th>Requestor</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrders as $purchaseOrder)
                        <tr>
                            <td>
                                <strong>{{ $purchaseOrder->number }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info text-white">
                                    {{ $purchaseOrder->supplier->name }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success text-white">
                                    {{ $purchaseOrder->warehouse->name }}
                                </span>
                            </td>
                            <td>{{ $purchaseOrder->requestor }}</td>
                            <td>
                                <span class="fw-bold text-primary">
                                    {{ number_format($purchaseOrder->total, 2) }}
                                </span>
                            </td>
                            <td>
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
                                <span class="badge {{ $statusColor }} text-white">
                                    {{ ucfirst($purchaseOrder->status) }}
                                </span>
                            </td>
                            <td>
                                @if($purchaseOrder->isOverdue())
                                    <span class="text-danger fw-bold">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $purchaseOrder->deadline->format('M d, Y') }}
                                    </span>
                                @else
                                    {{ $purchaseOrder->deadline->format('M d, Y') }}
                                @endif
                            </td>
                            <td>{{ $purchaseOrder->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('purchase-orders.view')
                                    <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('purchase-orders.edit')
                                    @if(!in_array($purchaseOrder->status, ['done', 'cancel']))
                                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @endcan
                                    
                                    @can('purchase-orders.delete')
                                    @if($purchaseOrder->status === 'draft')
                                    <form action="{{ route('purchase-orders.delete', $purchaseOrder) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                                
                                @can('purchase-orders.edit')
                                @if(!in_array($purchaseOrder->status, ['done', 'cancel']))
                                <!-- Quick Status Update -->
                                <div class="mt-2">
                                    <form action="{{ route('purchase-orders.update-status', $purchaseOrder) }}" 
                                          method="POST" 
                                          class="d-inline status-update-form"
                                          data-current-status="{{ $purchaseOrder->status }}">
                                        @csrf
                                        <select name="status" 
                                                class="form-select form-select-sm d-inline-block w-auto me-2" 
                                                style="width: auto; min-width: 100px;"
                                                onchange="validateStatusChange('{{ $purchaseOrder->status }}', this.value, this)">
                                            @if($purchaseOrder->status === 'draft')
                                                <option value="draft" selected>Draft</option>
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
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-success" 
                                                title="Update Status">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </div>
                                @endif
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $purchaseOrders->links() }}
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Purchase Orders Found</h5>
                <p class="text-muted">Start by creating your first purchase order.</p>
                @can('purchase-orders.create')
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Create Purchase Order
                </a>
                @endcan
            </div>
            @endif
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
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#purchaseOrdersTable').DataTable({
        pageLength: 15,
        order: [[6, 'desc']], // Sort by created date descending
        responsive: true,
        language: {
            search: "Search purchase orders:",
            lengthMenu: "Show _MENU_ purchase orders per page",
            info: "Showing _START_ to _END_ of _TOTAL_ purchase orders",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Handle status change form submission
    $('.status-update-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const currentStatus = form.data('current-status');
        const newStatus = form.find('select[name="status"]').val();
        
        if (currentStatus === newStatus) {
            form[0].submit(); // No change, submit directly
            return;
        }
        
        // Validate status change for draft orders
        if (currentStatus === 'draft' && !['draft', 'accepted', 'cancel'].includes(newStatus)) {
            showStatusChangeModal('Draft purchase orders can only be accepted or cancelled.', false);
            return;
        }
        
        // Show confirmation modal
        showStatusChangeModal(currentStatus, newStatus, form);
    });

    // Handle delete form submission
    $('form[action*="delete"]').on('submit', function(e) {
        e.preventDefault();
        showDeleteModal($(this));
    });
});

// Show status change confirmation modal
function showStatusChangeModal(currentStatus, newStatus, form = null) {
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

// Show delete confirmation modal
function showDeleteModal(form) {
    $('#confirmDelete').off('click').on('click', function() {
        form[0].submit();
        $('#deleteModal').modal('hide');
    });
    
    $('#deleteModal').modal('show');
}

// Validate status change for draft orders (for select change)
function validateStatusChange(currentStatus, newStatus, selectElement) {
    if (currentStatus === 'draft' && !['draft', 'accepted', 'cancel'].includes(newStatus)) {
        showStatusChangeModal('Draft purchase orders can only be accepted or cancelled.', false);
        selectElement.value = 'draft';
        return false;
    }
    return true;
}

// Legacy function for backward compatibility
function confirmDelete() {
    return false; // This will be handled by the modal now
}
</script>
@endsection
