@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck me-2"></i>
            Create Delivery
        </h1>
        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>
            Back to List
        </a>
    </div>

    <!-- Create Delivery Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Delivery Information</h6>
        </div>
        <div class="card-body">
            <!-- Stock Availability Notice -->
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Automatic Status Management:</strong> When you create a delivery, the system will automatically:
                <ul class="mb-0 mt-2">
                    <li><strong>Check stock availability</strong> for all products</li>
                    <li><strong>If stock is available:</strong> Status will be set to "Ready" and stock will be automatically reserved</li>
                    <li><strong>If stock is insufficient:</strong> Status will be set to "Waiting" until stock becomes available</li>
                </ul>
            </div>
            <form action="{{ route('deliveries.store') }}" method="POST" id="deliveryForm" onsubmit="return validateAndSubmit(event)">
                @csrf
                
                <div class="row">
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
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="scheduled_at" class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                   id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}" required>
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reference" class="form-label">Reference</label>
                            <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                   id="reference" name="reference" value="{{ old('reference') }}" 
                                   placeholder="Optional reference number">
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label">Delivery Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('delivery_address') is-invalid @enderror" 
                                      id="delivery_address" name="delivery_address" rows="3" 
                                      placeholder="Enter delivery address" required>{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Product Lines -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 font-weight-bold text-primary">Product Lines</h6>
                        <button type="button" class="btn btn-success btn-sm" onclick="addProductLine()">
                            <i class="fas fa-plus me-1"></i>
                            Add Product
                        </button>
                    </div>
                    
                    <div id="productLinesContainer">
                        <!-- Product lines will be added here dynamically -->
                    </div>
                    
                    @error('products')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Create Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Product Line Template (Hidden) -->
<template id="productLineTemplate">
    <div class="product-line border rounded p-3 mb-3" data-index="__INDEX__">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-2">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select class="form-select product-select" name="products[__INDEX__][product_id]" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }} ({{ $product->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-2">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control quantity-input" 
                           name="products[__INDEX__][quantity]" min="1" value="1" required>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-2">
                    <label class="form-label">Unit Price</label>
                    <input type="text" class="form-control price-display" readonly>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="mb-2">
                    <label class="form-label">Line Total</label>
                    <input type="text" class="form-control line-total-display" readonly>
                </div>
            </div>
        </div>
        
        <div class="text-end">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeProductLine(this)">
                <i class="fas fa-trash me-1"></i>
                Remove
            </button>
        </div>
    </div>
</template>
@endsection

@section('script')
<script>
let productLineIndex = 0;

// Initialize with one product line
document.addEventListener('DOMContentLoaded', function() {
    addProductLine();
    
    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(9, 0, 0, 0);
    
    const scheduledAtInput = document.getElementById('scheduled_at');
    scheduledAtInput.min = tomorrow.toISOString().slice(0, 16);
    scheduledAtInput.value = tomorrow.toISOString().slice(0, 16);
});

function addProductLine() {
    const container = document.getElementById('productLinesContainer');
    const template = document.getElementById('productLineTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update index
    const productLine = clone.querySelector('.product-line');
    productLine.dataset.index = productLineIndex;
    
    // Update form field names
    const selects = clone.querySelectorAll('select');
    const inputs = clone.querySelectorAll('input');
    
    selects.forEach(select => {
        select.name = select.name.replace('__INDEX__', productLineIndex);
    });
    
    inputs.forEach(input => {
        input.name = input.name.replace('__INDEX__', productLineIndex);
    });
    
    container.appendChild(clone);
    
    // Add event listeners
    const newProductLine = container.lastElementChild;
    const productSelect = newProductLine.querySelector('.product-select');
    const quantityInput = newProductLine.querySelector('.quantity-input');
    
    productSelect.addEventListener('change', updateLineTotal);
    quantityInput.addEventListener('input', updateLineTotal);
    
    productLineIndex++;
}

function removeProductLine(button) {
    const productLine = button.closest('.product-line');
    productLine.remove();
    
    // Reindex remaining product lines
    const container = document.getElementById('productLinesContainer');
    const productLines = container.querySelectorAll('.product-line');
    
    productLines.forEach((line, index) => {
        line.dataset.index = index;
        
        const selects = line.querySelectorAll('select');
        const inputs = line.querySelectorAll('input');
        
        selects.forEach(select => {
            select.name = select.name.replace(/\[\d+\]/, `[${index}]`);
        });
        
        inputs.forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
        });
    });
    
    productLineIndex = productLines.length;
}

function updateLineTotal() {
    const productLine = this.closest('.product-line');
    const productSelect = productLine.querySelector('.product-select');
    const quantityInput = productLine.querySelector('.quantity-input');
    const priceDisplay = productLine.querySelector('.price-display');
    const lineTotalDisplay = productLine.querySelector('.line-total-display');
    
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const price = selectedOption.dataset.price || 0;
    const quantity = quantityInput.value || 0;
    
    priceDisplay.value = formatCurrency(price);
    lineTotalDisplay.value = formatCurrency(price * quantity);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

// Form validation and submission
function validateAndSubmit(e) {
    const productLines = document.querySelectorAll('.product-line');
    
    if (productLines.length === 0) {
        e.preventDefault();
        alert('Please add at least one product line.');
        return false;
    }
    
    // Validate each product line
    let isValid = true;
    productLines.forEach((line, index) => {
        const productSelect = line.querySelector('.product-select');
        const quantityInput = line.querySelector('.quantity-input');
        
        if (!productSelect.value) {
            isValid = false;
            productSelect.classList.add('is-invalid');
        } else {
            productSelect.classList.remove('is-invalid');
        }
        
        if (!quantityInput.value || quantityInput.value < 1) {
            isValid = false;
            quantityInput.classList.add('is-invalid');
        } else {
            quantityInput.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields for product lines.');
        return false;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating...';
    submitBtn.disabled = true;
    
    // Re-enable button after 5 seconds (fallback)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
    
    return true; // Allow form submission
}


</script>
@endsection
