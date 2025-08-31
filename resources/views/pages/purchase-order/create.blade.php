@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Create Purchase Order
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
                        <li class="breadcrumb-item active">Create New</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Purchase Orders
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Purchase order will be created for this company</small>
                </div>
            </div>
        </div>

        <!-- Create Purchase Order Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Purchase Order Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('purchase-orders.store') }}" method="POST" id="purchaseOrderForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Supplier Information -->
                        <div class="col-md-6 mb-3">
                            <label for="supplier_id" class="form-label">
                                <i class="fas fa-truck me-1"></i>
                                Supplier <span class="text-danger">*</span>
                            </label>
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
                            <label for="warehouse_id" class="form-label">
                                <i class="fas fa-warehouse me-1"></i>
                                Warehouse <span class="text-danger">*</span>
                            </label>
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
                            <label for="requestor" class="form-label">
                                <i class="fas fa-user me-1"></i>
                                Requestor <span class="text-danger">*</span>
                            </label>
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

                        <!-- Deadline -->
                        <div class="col-md-6 mb-3">
                            <label for="deadline" class="form-label">
                                <i class="fas fa-calendar me-1"></i>
                                Deadline <span class="text-danger">*</span>
                            </label>
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

                    <div class="row">
                        <!-- Activities -->
                        <div class="col-md-12 mb-3">
                            <label for="activities" class="form-label">
                                <i class="fas fa-clipboard-list me-1"></i>
                                Activities
                            </label>
                            <textarea class="form-control @error('activities') is-invalid @enderror" 
                                      id="activities" 
                                      name="activities" 
                                      rows="3" 
                                      placeholder="Describe the activities or purpose of this purchase order">{{ old('activities') }}</textarea>
                            @error('activities')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Products Section -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="mb-3">
                                <i class="fas fa-boxes me-2"></i>
                                Products
                            </h6>
                        </div>
                    </div>

                    <div id="productsContainer">
                        <div class="product-row row mb-3" data-row="0">
                            <div class="col-md-5">
                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select product-select" name="products[0][product_id]" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-cost="{{ $product->cost ?? $product->price }}"
                                            data-price="{{ $product->price }}">
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
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-product" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addProduct">
                                <i class="fas fa-plus me-2"></i>
                                Add Product
                            </button>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Total Section -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Total Amount:</h6>
                                        <h5 class="mb-0 text-primary" id="totalAmount">Rp 0</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Create Purchase Order
                                </button>
                            </div>
                        </div>
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

// Add product row
document.getElementById('addProduct').addEventListener('click', function() {
    const container = document.getElementById('productsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'product-row row mb-3';
    newRow.setAttribute('data-row', productRowCount);
    
    newRow.innerHTML = `
        <div class="col-md-5">
            <label class="form-label">Product <span class="text-danger">*</span></label>
            <select class="form-select product-select" name="products[${productRowCount}][product_id]" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" 
                        data-cost="{{ $product->cost ?? $product->price }}"
                        data-price="{{ $product->price }}">
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
        <div class="col-md-1">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-outline-danger btn-sm remove-product">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newRow);
    productRowCount++;
    
    // Show remove button for first row if there are multiple rows
    if (productRowCount > 1) {
        document.querySelector('.product-row[data-row="0"] .remove-product').style.display = 'block';
    }
    
    // Add event listeners to new row
    addProductRowEventListeners(newRow);
});

// Remove product row
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-product') || e.target.closest('.remove-product')) {
        const row = e.target.closest('.product-row');
        row.remove();
        
        // Hide remove button for first row if only one row remains
        if (document.querySelectorAll('.product-row').length === 1) {
            document.querySelector('.product-row[data-row="0"] .remove-product').style.display = 'none';
        }
        
        calculateTotal();
    }
});

// Add event listeners to product row
function addProductRowEventListeners(row) {
    const productSelect = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.product-quantity');
    const costInput = row.querySelector('.product-cost');
    
    productSelect.addEventListener('change', function() {
        updateProductCost(this, costInput);
        calculateTotal();
    });
    
    quantityInput.addEventListener('input', function() {
        calculateTotal();
    });
}

// Update product cost when product is selected
function updateProductCost(select, costInput) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        const cost = selectedOption.getAttribute('data-cost');
        costInput.value = cost ? `Rp ${parseFloat(cost).toLocaleString('id-ID')}` : 'Rp 0';
    } else {
        costInput.value = '';
    }
}

// Calculate total amount
function calculateTotal() {
    let total = 0;
    
    document.querySelectorAll('.product-row').forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.product-quantity');
        
        if (productSelect.value && quantityInput.value) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const cost = parseFloat(selectedOption.getAttribute('data-cost')) || 0;
            const quantity = parseFloat(quantityInput.value) || 0;
            total += cost * quantity;
        }
    });
    
    document.getElementById('totalAmount').textContent = `Rp ${total.toLocaleString('id-ID')}`;
}

// Add event listeners to initial row
document.addEventListener('DOMContentLoaded', function() {
    addProductRowEventListeners(document.querySelector('.product-row'));
    
    // Form validation
    document.getElementById('purchaseOrderForm').addEventListener('submit', function(e) {
        const productRows = document.querySelectorAll('.product-row');
        let hasValidProduct = false;
        
        productRows.forEach(row => {
            const productId = row.querySelector('.product-select').value;
            const quantity = row.querySelector('.product-quantity').value;
            
            if (productId && quantity > 0) {
                hasValidProduct = true;
            }
        });
        
        if (!hasValidProduct) {
            e.preventDefault();
            alert('Please add at least one product with valid quantity.');
            return false;
        }
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
