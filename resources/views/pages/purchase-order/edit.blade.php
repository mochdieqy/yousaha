@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-warning me-2"></i>
            Edit Purchase Order
        </h1>
        <div>
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-info btn-sm me-2">
                <i class="fas fa-eye me-1"></i>
                View Details
            </a>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Error Message -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Edit Purchase Order Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Order Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST" id="purchaseOrderForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Order Number (Read-only) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order Number</label>
                        <input type="text" class="form-control" value="{{ $purchaseOrder->number }}" readonly>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            @if($purchaseOrder->status === 'draft')
                                <option value="draft" {{ $purchaseOrder->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="accepted" {{ $purchaseOrder->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="cancel" {{ $purchaseOrder->status == 'cancel' ? 'selected' : '' }}>Cancel</option>
                            @else
                                <option value="draft" {{ $purchaseOrder->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="accepted" {{ $purchaseOrder->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="sent" {{ $purchaseOrder->status == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="done" {{ $purchaseOrder->status == 'done' ? 'selected' : '' }}>Done</option>
                                <option value="cancel" {{ $purchaseOrder->status == 'cancel' ? 'selected' : '' }}>Cancel</option>
                            @endif
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Supplier Information -->
                    <div class="col-md-6 mb-3">
                        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                id="supplier_id" 
                                name="supplier_id" 
                                required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                    {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }} - {{ $supplier->email }}
                            </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Warehouse -->
                    <div class="col-md-6 mb-3">
                        <label for="warehouse_id" class="form-label">Warehouse <span class="text-danger">*</span></label>
                        <select class="form-select @error('warehouse_id') is-invalid @enderror" 
                                id="warehouse_id" 
                                name="warehouse_id" 
                                required>
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" 
                                    {{ old('warehouse_id', $purchaseOrder->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Requestor -->
                    <div class="col-md-6 mb-3">
                        <label for="requestor" class="form-label">Requestor <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('requestor') is-invalid @enderror" 
                               id="requestor" 
                               name="requestor" 
                               value="{{ old('requestor', $purchaseOrder->requestor) }}" 
                               required>
                        @error('requestor')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Activities -->
                    <div class="col-md-6 mb-3">
                        <label for="activities" class="form-label">Activities</label>
                        <textarea class="form-control @error('activities') is-invalid @enderror" 
                                  id="activities" 
                                  name="activities" 
                                  rows="3" 
                                  placeholder="Describe the activities or purpose of this purchase order">{{ old('activities', $purchaseOrder->activities) }}</textarea>
                        @error('activities')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deadline -->
                    <div class="col-md-6 mb-3">
                        <label for="deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('deadline') is-invalid @enderror" 
                               id="deadline" 
                               name="deadline" 
                               value="{{ old('deadline', $purchaseOrder->deadline->format('Y-m-d')) }}" 
                               required>
                        @error('deadline')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Products Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-boxes me-2"></i>
                            Products
                            <small class="text-muted ms-2">
                                @if($purchaseOrder->status !== 'draft')
                                    (Products can only be modified when status is Draft)
                                @endif
                            </small>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="productsContainer">
                            @foreach($purchaseOrder->productLines as $index => $line)
                            <div class="product-row row mb-3" data-row="{{ $index }}">
                                <div class="col-md-4">
                                    <label class="form-label">Product <span class="text-danger">*</span></label>
                                    <select class="form-select product-select" 
                                            name="products[{{ $index }}][product_id]" 
                                            {{ $purchaseOrder->status !== 'draft' ? 'disabled' : '' }}
                                            required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-cost="{{ $product->cost ?? $product->price }}"
                                                {{ $line->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - {{ $product->sku }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control product-quantity" 
                                           name="products[{{ $index }}][quantity]" 
                                           min="1" 
                                           value="{{ $line->quantity }}" 
                                           {{ $purchaseOrder->status !== 'draft' ? 'readonly' : '' }}
                                           required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit Cost</label>
                                    <input type="text" 
                                           class="form-control product-cost" 
                                           readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Line Total</label>
                                    <input type="text" 
                                           class="form-control product-total" 
                                           readonly>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($purchaseOrder->status === 'draft')
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addProduct">
                                    <i class="fas fa-plus me-1"></i>
                                    Add Product
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="removeProduct" 
                                        style="display: {{ count($purchaseOrder->productLines) > 1 ? 'inline-block' : 'none' }};">
                                    <i class="fas fa-minus me-1"></i>
                                    Remove Product
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Total Section -->
                        <div class="row mt-4">
                            <div class="col-md-6 offset-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-0">Total Amount:</h5>
                                            <h4 class="mb-0 text-primary" id="totalAmount">${{ number_format($purchaseOrder->total, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Update Purchase Order
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="errorModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Validation Error
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let productRowCount = {{ count($purchaseOrder->productLines) }};

    // Add product row
    $('#addProduct').click(function() {
        const newRow = `
            <div class="product-row row mb-3" data-row="${productRowCount}">
                <div class="col-md-4">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select class="form-select product-select" 
                            name="products[${productRowCount}][product_id]" 
                            required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                                data-cost="{{ $product->cost ?? $product->price }}">
                            {{ $product->name }} - {{ $product->sku }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control product-quantity" 
                           name="products[${productRowCount}][quantity]" 
                           min="1" 
                           value="1" 
                           required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unit Cost</label>
                    <input type="text" 
                           class="form-control product-cost" 
                           readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Line Total</label>
                    <input type="text" 
                           class="form-control product-total" 
                           readonly>
                </div>
            </div>
        `;
        
        $('#productsContainer').append(newRow);
        productRowCount++;
        
        if (productRowCount > 1) {
            $('#removeProduct').show();
        }
        
        updateTotal();
    });

    // Remove product row
    $('#removeProduct').click(function() {
        if (productRowCount > 1) {
            $('.product-row:last').remove();
            productRowCount--;
            
            if (productRowCount === 1) {
                $('#removeProduct').hide();
            }
            
            updateTotal();
        }
    });

    // Handle product selection and quantity changes
    $(document).on('change', '.product-select', function() {
        const row = $(this).closest('.product-row');
        const cost = $(this).find(':selected').data('cost') || 0;
        const quantity = row.find('.product-quantity').val() || 0;
        
        row.find('.product-cost').val('$' + parseFloat(cost).toFixed(2));
        row.find('.product-total').val('$' + (cost * quantity).toFixed(2));
        
        updateTotal();
    });

    $(document).on('input', '.product-quantity', function() {
        const row = $(this).closest('.product-row');
        const cost = parseFloat(row.find('.product-select option:selected').data('cost') || 0);
        const quantity = parseInt($(this).val()) || 0;
        
        row.find('.product-total').val('$' + (cost * quantity).toFixed(2));
        
        updateTotal();
    });

    // Calculate total
    function updateTotal() {
        let total = 0;
        $('.product-total').each(function() {
            const value = parseFloat($(this).val().replace('$', '')) || 0;
            total += value;
        });
        
        $('#totalAmount').text('$' + total.toFixed(2));
    }

    // Initialize existing rows
    $('.product-select').each(function() {
        $(this).trigger('change');
    });
    $('.product-quantity').each(function() {
        $(this).trigger('input');
    });

    // Form validation
    $('#purchaseOrderForm').submit(function(e) {
        let isValid = true;
        
        // Check if at least one product is selected
        let hasProducts = false;
        $('.product-select').each(function() {
            if ($(this).val()) {
                hasProducts = true;
                return false;
            }
        });
        
        if (!hasProducts) {
            showErrorModal('Please select at least one product.');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});

// Show error modal
function showErrorModal(message) {
    $('#errorMessage').text(message);
    $('#errorModal').modal('show');
}
</script>
@endsection
