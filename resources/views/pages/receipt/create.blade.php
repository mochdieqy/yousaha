@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Create Receipt
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('receipts.index') }}">Receipts</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('receipts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Receipts
            </a>
        </div>

        <!-- Create Receipt Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Receipt Information
                </h5>
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
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reference" class="form-label">Reference</label>
                                <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                       id="reference" name="reference" value="{{ old('reference') }}" placeholder="Optional reference number">
                                @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Products Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">
                                <i class="fas fa-boxes me-2"></i>
                                Products
                            </h6>
                            <button type="button" class="btn btn-success btn-sm" id="addProductRow">
                                <i class="fas fa-plus me-2"></i>
                                Add Product
                            </button>
                        </div>
                        
                        <div id="productsContainer">
                            <div class="product-row row mb-3">
                                <div class="col-md-5">
                                    <select class="form-select product-select" name="products[0][product_id]" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" name="products[0][quantity]" 
                                           placeholder="Quantity" step="0.01" min="0.01" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-product" style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Create Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let productRowCount = 1;

document.getElementById('addProductRow').addEventListener('click', function() {
    const container = document.getElementById('productsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'product-row row mb-3';
    
    // Build options dynamically
    let optionsHtml = '<option value="">Select Product</option>';
    @foreach($products as $product)
    optionsHtml += '<option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>';
    @endforeach
    
    newRow.innerHTML = `
        <div class="col-md-5">
            <select class="form-select product-select" name="products[${productRowCount}][product_id]" required>
                ${optionsHtml}
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" class="form-control" name="products[${productRowCount}][quantity]" 
                   placeholder="Quantity" step="0.01" min="0.01" required>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-outline-danger btn-sm remove-product">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newRow);
    productRowCount++;
    
    // Show remove button for all rows
    updateRemoveButtons();
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-product')) {
        e.target.closest('.product-row').remove();
        updateRemoveButtons();
    }
});

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.product-row');
    const removeButtons = document.querySelectorAll('.remove-product');
    
    if (rows.length === 1) {
        removeButtons[0].style.display = 'none';
    } else {
        removeButtons.forEach(btn => btn.style.display = 'block');
    }
}

// Form validation
document.getElementById('receiptForm').addEventListener('submit', function(e) {
    const productRows = document.querySelectorAll('.product-row');
    let hasValidProducts = false;
    
    productRows.forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('input[name*="[quantity]"]');
        
        if (productSelect.value && quantityInput.value) {
            hasValidProducts = true;
        }
    });
    
    if (!hasValidProducts) {
        e.preventDefault();
        alert('Please add at least one product with quantity.');
        return false;
    }
});

// Initialize remove buttons
updateRemoveButtons();
</script>
@endsection
