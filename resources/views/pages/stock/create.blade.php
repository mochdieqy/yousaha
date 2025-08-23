@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus text-primary me-2"></i>
            Create New Stock
        </h1>
        <a href="{{ route('stocks.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Stock List
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Create Stock Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Stock Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('stocks.store') }}" id="createStockForm">
                @csrf
                
                <div class="row">
                    <!-- Warehouse Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="warehouse_id" class="form-label">
                            Warehouse <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('warehouse_id') is-invalid @enderror" 
                                id="warehouse_id" name="warehouse_id" required>
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" 
                                        {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->code }} - {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Product Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">
                            Product <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('product_id') is-invalid @enderror" 
                                id="product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->code }} - {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Total Quantity -->
                    <div class="col-md-4 mb-3">
                        <label for="quantity_total" class="form-label">
                            Total Quantity <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control @error('quantity_total') is-invalid @enderror" 
                               id="quantity_total" name="quantity_total" 
                               value="{{ old('quantity_total') }}" min="0" step="0.01" required>
                        @error('quantity_total')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Reserved Quantity -->
                    <div class="col-md-4 mb-3">
                        <label for="quantity_reserve" class="form-label">
                            Reserved Quantity
                        </label>
                        <input type="number" class="form-control @error('quantity_reserve') is-invalid @enderror" 
                               id="quantity_reserve" name="quantity_reserve" 
                               value="{{ old('quantity_reserve', 0) }}" min="0" step="0.01">
                        @error('quantity_reserve')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Incoming Quantity -->
                    <div class="col-md-4 mb-3">
                        <label for="quantity_incoming" class="form-label">
                            Incoming Quantity
                        </label>
                        <input type="number" class="form-control @error('quantity_incoming') is-invalid @enderror" 
                               id="quantity_incoming" name="quantity_incoming" 
                               value="{{ old('quantity_incoming', 0) }}" min="0" step="0.01">
                        @error('quantity_incoming')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Saleable Quantity Display -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Saleable Quantity</label>
                        <div class="form-control-plaintext" id="saleableQuantityDisplay">
                            <span class="text-muted">Calculated automatically</span>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Stock Details Section -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-list me-2"></i>
                            Stock Details (Optional)
                        </h6>
                        <p class="text-muted small">Add detailed information about specific stock batches, including costs, references, and expiration dates.</p>
                    </div>
                </div>

                <div id="stockDetailsContainer">
                    <div class="stock-detail-row row mb-3">
                        <div class="col-md-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control stock-detail-quantity" 
                                   name="details[0][quantity]" min="0" step="0.01" placeholder="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" 
                                   name="details[0][code]" placeholder="Batch code">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cost</label>
                            <input type="number" class="form-control" 
                                   name="details[0][cost]" min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Reference</label>
                            <input type="text" class="form-control" 
                                   name="details[0][reference]" placeholder="PO/Invoice ref">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Expiration Date</label>
                            <input type="date" class="form-control" 
                                   name="details[0][expiration_date]">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-detail" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addDetailRow">
                            <i class="fas fa-plus me-1"></i>
                            Add Stock Detail Row
                        </button>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Create Stock
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
let detailRowIndex = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Calculate saleable quantity
    function calculateSaleableQuantity() {
        const total = parseFloat(document.getElementById('quantity_total').value) || 0;
        const reserved = parseFloat(document.getElementById('quantity_reserve').value) || 0;
        const saleable = total - reserved;
        
        const display = document.getElementById('saleableQuantityDisplay');
        if (saleable < 0) {
            display.innerHTML = '<span class="text-danger">Invalid: Reserved > Total</span>';
        } else {
            display.innerHTML = `<span class="fw-bold text-success">${saleable.toFixed(2)}</span>`;
        }
    }

    // Add event listeners for quantity calculations
    document.getElementById('quantity_total').addEventListener('input', calculateSaleableQuantity);
    document.getElementById('quantity_reserve').addEventListener('input', calculateSaleableQuantity);

    // Add detail row
    document.getElementById('addDetailRow').addEventListener('click', function() {
        const container = document.getElementById('stockDetailsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'stock-detail-row row mb-3';
        newRow.innerHTML = `
            <div class="col-md-2">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control stock-detail-quantity" 
                       name="details[${detailRowIndex}][quantity]" min="0" step="0.01" placeholder="0">
            </div>
            <div class="col-md-2">
                <label class="form-label">Code</label>
                <input type="text" class="form-control" 
                       name="details[${detailRowIndex}][code]" placeholder="Batch code">
            </div>
            <div class="col-md-2">
                <label class="form-label">Cost</label>
                <input type="number" class="form-control" 
                       name="details[${detailRowIndex}][cost]" min="0" step="0.01" placeholder="0.00">
            </div>
            <div class="col-md-2">
                <label class="form-label">Reference</label>
                <input type="text" class="form-control" 
                       name="details[${detailRowIndex}][reference]" placeholder="PO/Invoice ref">
            </div>
            <div class="col-md-2">
                <label class="form-label">Expiration Date</label>
                <input type="date" class="form-control" 
                       name="details[${detailRowIndex}][expiration_date]">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-detail">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(newRow);
        detailRowIndex++;
        
        // Show remove buttons for all rows except the first
        updateRemoveButtons();
    });

    // Remove detail row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-detail') || e.target.closest('.remove-detail')) {
            const row = e.target.closest('.stock-detail-row');
            row.remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.stock-detail-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-detail');
            if (index === 0) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'block';
            }
        });
    }

    // Form validation
    document.getElementById('createStockForm').addEventListener('submit', function(e) {
        const total = parseFloat(document.getElementById('quantity_total').value) || 0;
        const reserved = parseFloat(document.getElementById('quantity_reserve').value) || 0;
        
        if (reserved > total) {
            e.preventDefault();
            showErrorModal('Reserved quantity cannot be greater than total quantity.');
            return false;
        }
    });

    // Initialize
    calculateSaleableQuantity();
    updateRemoveButtons();
});

// Show error modal
function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('errorModal')).show();
}
</script>
@endsection
