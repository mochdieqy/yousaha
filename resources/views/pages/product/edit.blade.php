@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Product
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">Edit {{ $product->name }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Products
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Editing product in this company's inventory</small>
                </div>
            </div>
        </div>

        <!-- Product Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Product Information
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">
                            <i class="fas fa-box me-1"></i>
                            {{ ucfirst($product->type) }}
                        </span>
                        <span class="badge bg-info">
                            <i class="fas fa-barcode me-1"></i>
                            {{ $product->sku }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-tag me-1"></i>
                                        Product Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $product->name) }}" 
                                           placeholder="Enter product name"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="sku" class="form-label">
                                        <i class="fas fa-barcode me-1"></i>
                                        SKU <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('sku') is-invalid @enderror" 
                                           id="sku" 
                                           name="sku" 
                                           value="{{ old('sku', $product->sku) }}" 
                                           placeholder="Enter SKU code"
                                           required>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="type" class="form-label">
                                        <i class="fas fa-cube me-1"></i>
                                        Product Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" 
                                            name="type" 
                                            required>
                                        <option value="">Select product type</option>
                                        <option value="goods" {{ old('type', $product->type) == 'goods' ? 'selected' : '' }}>
                                            <i class="fas fa-box"></i> Goods
                                        </option>
                                        <option value="service" {{ old('type', $product->type) == 'service' ? 'selected' : '' }}>
                                            <i class="fas fa-cogs"></i> Service
                                        </option>
                                        <option value="combo" {{ old('type', $product->type) == 'combo' ? 'selected' : '' }}>
                                            <i class="fas fa-layer-group"></i> Combo
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="barcode" class="form-label">
                                        <i class="fas fa-qrcode me-1"></i>
                                        Barcode
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('barcode') is-invalid @enderror" 
                                           id="barcode" 
                                           name="barcode" 
                                           value="{{ old('barcode', $product->barcode) }}" 
                                           placeholder="Enter barcode (optional)">
                                    @error('barcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="price" class="form-label">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        Selling Price <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               id="price" 
                                               name="price" 
                                               value="{{ old('price', $product->price) }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00"
                                               required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="taxes" class="form-label">
                                        <i class="fas fa-percentage me-1"></i>
                                        Taxes
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('taxes') is-invalid @enderror" 
                                               id="taxes" 
                                               name="taxes" 
                                               value="{{ old('taxes', $product->taxes) }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00">
                                    </div>
                                    @error('taxes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="cost" class="form-label">
                                        <i class="fas fa-coins me-1"></i>
                                        Cost Price
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('cost') is-invalid @enderror" 
                                               id="cost" 
                                               name="cost" 
                                               value="{{ old('cost', $product->cost) }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00">
                                    </div>
                                    @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="reference" class="form-label">
                                        <i class="fas fa-file-alt me-1"></i>
                                        Reference
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('reference') is-invalid @enderror" 
                                           id="reference" 
                                           name="reference" 
                                           value="{{ old('reference', $product->reference) }}" 
                                           placeholder="Internal reference (optional)">
                                    @error('reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Options and Settings -->
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-cog me-2"></i>
                                        Product Settings
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_track_inventory" 
                                                   name="is_track_inventory" 
                                                   value="1" 
                                                   {{ old('is_track_inventory', $product->is_track_inventory) ? 'checked' : '' }}
                                                   onchange="toggleInventoryTracking()">
                                            <label class="form-check-label" for="is_track_inventory">
                                                <i class="fas fa-chart-bar me-1"></i>
                                                Track Inventory
                                            </label>
                                        </div>
                                        <small class="text-muted" id="inventory-help-text">Enable stock tracking for this product</small>
                                        <div id="service-inventory-warning" class="alert alert-warning mt-2" style="display: none;">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Service products cannot track inventory. This option will be disabled.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_shrink" 
                                                   name="is_shrink" 
                                                   value="1" 
                                                   {{ old('is_shrink', $product->is_shrink) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_shrink">
                                                <i class="fas fa-arrow-down me-1"></i>
                                                Product Shrink
                                            </label>
                                        </div>
                                        <small class="text-muted">Product may lose quantity over time</small>
                                    </div>

                                    <hr>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-save me-2"></i>
                                            Update Product
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Statistics -->
                            <div class="card bg-light border-0 mt-3">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Product Statistics
                                    </h6>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Total Price (with taxes):</small>
                                        <div class="fw-bold text-success">${{ number_format($product->total_price, 2) }}</div>
                                    </div>
                                    
                                    @if($product->cost)
                                    <div class="mb-2">
                                        <small class="text-muted">Profit Margin:</small>
                                        <div class="fw-bold text-info">${{ number_format($product->profit_margin, 2) }}</div>
                                    </div>
                                    @endif
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Created:</small>
                                        <div class="fw-bold">{{ $product->created_at->format('M d, Y') }}</div>
                                    </div>
                                    
                                    <div class="mb-0">
                                        <small class="text-muted">Last Updated:</small>
                                        <div class="fw-bold">{{ $product->updated_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
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
// Calculate total price including taxes
document.getElementById('price').addEventListener('input', calculateTotal);
document.getElementById('taxes').addEventListener('input', calculateTotal);

function calculateTotal() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const taxes = parseFloat(document.getElementById('taxes').value) || 0;
    const total = price + taxes;
    
    // You can display this somewhere if needed
    console.log('Total Price (including taxes): $' + total.toFixed(2));
}

// Toggle inventory tracking based on product type
function toggleInventoryTracking() {
    const productType = document.getElementById('type').value;
    const inventoryCheckbox = document.getElementById('is_track_inventory');
    const inventoryHelpText = document.getElementById('inventory-help-text');
    const serviceWarning = document.getElementById('service-inventory-warning');
    
    if (productType === 'service') {
        inventoryCheckbox.checked = false;
        inventoryCheckbox.disabled = true;
        inventoryHelpText.textContent = 'Service products cannot track inventory';
        serviceWarning.style.display = 'block';
    } else {
        inventoryCheckbox.disabled = false;
        inventoryHelpText.textContent = 'Enable stock tracking for this product';
        serviceWarning.style.display = 'none';
    }
}

// Initialize inventory tracking state when page loads
document.addEventListener('DOMContentLoaded', function() {
    toggleInventoryTracking();
});

// Update inventory tracking when product type changes
document.getElementById('type').addEventListener('change', toggleInventoryTracking);
</script>
@endsection
