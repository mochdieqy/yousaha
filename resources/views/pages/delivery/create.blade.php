@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Add New Delivery
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('deliveries.index') }}">Deliveries</a></li>
                        <li class="breadcrumb-item active">Add New</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('deliveries.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Deliveries
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ Auth::user()->currentCompany->name }}
                    <br>
                    <small class="text-muted">Delivery will be created for this company</small>
                </div>
            </div>
        </div>

        <!-- Stock Availability Notice -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-lg"></i>
                <div>
                    <strong>Automatic Status Management:</strong> When you create a delivery, the system will automatically:
                    <ul class="mb-0 mt-2">
                        <li><strong>Check stock availability</strong> for all products</li>
                        <li><strong>If stock is available:</strong> Status will be set to "Ready" and stock will be automatically reserved</li>
                        <li><strong>If stock is insufficient:</strong> Status will be set to "Waiting" until stock becomes available</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Create Delivery Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Delivery Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('deliveries.store') }}" method="POST" id="deliveryForm" onsubmit="return validateAndSubmit(event)">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="warehouse_id" class="form-label">
                                    <i class="fas fa-warehouse me-1"></i>
                                    Warehouse <span class="text-danger">*</span>
                                </label>
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
                                <label for="scheduled_at" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>
                                    Scheduled Date & Time <span class="text-danger">*</span>
                                </label>
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
                                <label for="reference" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>
                                    Reference
                                </label>
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
                                <label for="delivery_address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Delivery Address <span class="text-danger">*</span>
                                </label>
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
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-boxes me-2"></i>
                                Product Lines
                            </h6>
                            <button type="button" class="btn btn-success btn-sm" onclick="addProductLine()">
                                <i class="fas fa-plus me-1"></i>
                                Add Product
                            </button>
                        </div>
                        
                        <div id="productLinesContainer">
                            <!-- Product lines will be added here dynamically -->
                        </div>
                        
                        <div class="text-center mt-3" id="noProductsMessage">
                            <p class="text-muted">No products added yet. Click "Add Product" to start.</p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('deliveries.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>
                            Create Delivery
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
let productLineCount = 0;

function addProductLine() {
    productLineCount++;
    
    const container = document.getElementById('productLinesContainer');
    const noProductsMessage = document.getElementById('noProductsMessage');
    
    // Hide the "no products" message
    noProductsMessage.style.display = 'none';
    
    const productLineHtml = `
        <div class="card border mb-3" id="productLine_${productLineCount}">
            <div class="card-header bg-light py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Product Line ${productLineCount}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeProductLine(${productLineCount})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="product_id_${productLineCount}" class="form-label">Product <span class="text-danger">*</span></label>
                            <select class="form-select" id="product_id_${productLineCount}" name="products[${productLineCount}][product_id]" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-sku="{{ $product->sku }}">
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="quantity_${productLineCount}" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity_${productLineCount}" 
                                   name="products[${productLineCount}][quantity]" min="1" value="1" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', productLineHtml);
}

function removeProductLine(lineNumber) {
    const productLine = document.getElementById(`productLine_${lineNumber}`);
    productLine.remove();
    
    // Check if there are any product lines left
    const container = document.getElementById('productLinesContainer');
    const noProductsMessage = document.getElementById('noProductsMessage');
    
    if (container.children.length === 0) {
        noProductsMessage.style.display = 'block';
    }
}

function validateAndSubmit(event) {
    const container = document.getElementById('productLinesContainer');
    
    if (container.children.length === 0) {
        alert('Please add at least one product line before submitting.');
        event.preventDefault();
        return false;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
    
    // Re-enable after a delay (in case of errors)
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 10000);
    
    return true;
}

// Add first product line when page loads
document.addEventListener('DOMContentLoaded', function() {
    addProductLine();
    
    // Set default scheduled time to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(9, 0, 0, 0); // 9:00 AM tomorrow
    
    const scheduledAtInput = document.getElementById('scheduled_at');
    if (scheduledAtInput && !scheduledAtInput.value) {
        scheduledAtInput.value = tomorrow.toISOString().slice(0, 16);
    }
});
</script>
@endsection
