@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus text-success me-2"></i>
            Create Purchase Order
        </h1>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>
            Back to List
        </a>
    </div>

    <!-- Create Purchase Order Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Order Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('purchase-orders.store') }}" method="POST" id="purchaseOrderForm">
                @csrf
                
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
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                    {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
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
                               value="{{ old('requestor', Auth::user()->name) }}" 
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
                                  placeholder="Describe the activities or purpose of this purchase order">{{ old('activities') }}</textarea>
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
                               value="{{ old('deadline') }}" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
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
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="productsContainer">
                            <div class="product-row row mb-3" data-row="0">
                                <div class="col-md-4">
                                    <label class="form-label">Product <span class="text-danger">*</span></label>
                                    <select class="form-select product-select" 
                                            name="products[0][product_id]" 
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
                                           name="products[0][quantity]" 
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
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addProduct">
                                    <i class="fas fa-plus me-1"></i>
                                    Add Product
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="removeProduct" style="display: none;">
                                    <i class="fas fa-minus me-1"></i>
                                    Remove Product
                                </button>
                            </div>
                        </div>

                        <!-- Total Section -->
                        <div class="row mt-4">
                            <div class="col-md-6 offset-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-0">Total Amount:</h5>
                                            <h4 class="mb-0 text-primary" id="totalAmount">$0.00</h4>
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
                                Create Purchase Order
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
    let productRowCount = 1;

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

    // Initialize first row
    $('.product-select:first').trigger('change');
    $('.product-quantity:first').trigger('input');

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
