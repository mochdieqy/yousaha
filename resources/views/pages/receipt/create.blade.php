@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus text-primary me-2"></i>
            Create Receipt
        </h1>
        <a href="{{ route('receipts.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Receipts
        </a>
    </div>

    <!-- Create Receipt Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Receipt Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('receipts.store') }}" method="POST" id="receiptForm">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="receive_from" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select @error('receive_from') is-invalid @enderror" id="receive_from" name="receive_from" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('receive_from') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('receive_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">Warehouse <span class="text-danger">*</span></label>
                            <select class="form-select @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="scheduled_at" class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                   id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}" required>
                            @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="reference" class="form-label">Reference</label>
                    <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                           id="reference" name="reference" value="{{ old('reference') }}" placeholder="Optional reference number">
                    @error('reference')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr>
                
                <!-- Products Section -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Products</h6>
                        <button type="button" class="btn btn-success btn-sm" id="addProductRow">
                            <i class="fas fa-plus me-1"></i>
                            Add Product
                        </button>
                    </div>
                    
                    <div id="productsContainer">
                        <div class="product-row row mb-2">
                            <div class="col-md-5">
                                <select class="form-select product-select" name="products[0][product_id]" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="products[0][quantity]" 
                                       placeholder="Quantity" step="0.01" min="0.01" required>
                            </div>
                            <div class="col-md-3">
                                <span class="form-control-plaintext product-info"></span>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-product" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    @error('products')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Create Receipt
                    </button>
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
    $('#addProductRow').click(function() {
        const newRow = `
            <div class="product-row row mb-2">
                <div class="col-md-5">
                    <select class="form-select product-select" name="products[${productRowCount}][product_id]" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="products[${productRowCount}][quantity]" 
                           placeholder="Quantity" step="0.01" min="0.01" required>
                </div>
                <div class="col-md-3">
                    <span class="form-control-plaintext product-info"></span>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-product">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#productsContainer').append(newRow);
        productRowCount++;
        updateRemoveButtons();
    });
    
    // Remove product row
    $(document).on('click', '.remove-product', function() {
        $(this).closest('.product-row').remove();
        updateRemoveButtons();
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const rows = $('.product-row');
        rows.each(function(index) {
            const removeBtn = $(this).find('.remove-product');
            if (rows.length === 1) {
                removeBtn.hide();
            } else {
                removeBtn.show();
            }
        });
    }
    
    // Handle product selection change
    $(document).on('change', '.product-select', function() {
        const row = $(this).closest('.product-row');
        const productId = $(this).val();
        const infoSpan = row.find('.product-info');
        
        if (productId) {
            // You can add AJAX call here to get product details
            infoSpan.text('Product selected');
        } else {
            infoSpan.text('');
        }
    });
    
    // Form validation
    $('#receiptForm').submit(function(e) {
        const products = $('.product-row');
        let isValid = true;
        
        products.each(function() {
            const productId = $(this).find('.product-select').val();
            const quantity = $(this).find('input[name*="[quantity]"]').val();
            
            if (!productId || !quantity) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showErrorModal('Please fill in all product fields.');
        }
    });
    
    // Initialize
    updateRemoveButtons();
});

// Show error modal
function showErrorModal(message) {
    $('#errorMessage').text(message);
    $('#errorModal').modal('show');
}
</script>
@endsection
