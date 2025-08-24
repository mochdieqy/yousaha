@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-edit text-warning me-2"></i>
                    Edit Sales Order: {{ $salesOrder->number }}
                </h5>
                <a href="{{ route('sales-orders.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to Sales Orders
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('sales-orders.update', $salesOrder) }}" method="POST" id="salesOrderForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Customer Information -->
                        <div class="col-md-6 mb-3">
                            <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" 
                                    id="customer_id" 
                                    name="customer_id" 
                                    required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                            {{ old('customer_id', $salesOrder->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
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
                                            {{ old('warehouse_id', $salesOrder->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
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
                        <!-- Salesperson -->
                        <div class="col-md-6 mb-3">
                            <label for="salesperson" class="form-label">Salesperson <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('salesperson') is-invalid @enderror" 
                                   id="salesperson" 
                                   name="salesperson" 
                                   value="{{ old('salesperson', $salesOrder->salesperson) }}" 
                                   placeholder="Enter salesperson name" 
                                   required>
                            @error('salesperson')
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
                                   value="{{ old('deadline', $salesOrder->deadline->format('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                   required>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Activities -->
                    <div class="mb-3">
                        <label for="activities" class="form-label">Activities</label>
                        <textarea class="form-control @error('activities') is-invalid @enderror" 
                                  id="activities" 
                                  name="activities" 
                                  rows="3" 
                                  placeholder="Enter activities description">{{ old('activities', $salesOrder->activities) }}</textarea>
                        @error('activities')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status Display (Read-only) -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Current Status</label>
                        <input type="text" 
                               class="form-control" 
                               id="status" 
                               value="{{ ucfirst($salesOrder->status) }}" 
                               readonly>
                        <div class="form-text">
                            <strong>Note:</strong> Use the "Quick Status Change" form below to modify the status. Status changes to "Accepted" or "Done" will automatically create deliveries and update stock.
                        </div>
                    </div>

                    <!-- Products Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">
                                <i class="fas fa-boxes text-primary me-2"></i>
                                Products
                                @if(!in_array($salesOrder->status, ['draft', 'waiting']))
                                    <span class="badge bg-warning text-dark ms-2">Read-only for this status</span>
                                @endif
                            </h6>
                            @if(in_array($salesOrder->status, ['draft', 'waiting']))
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addProductRow">
                                <i class="fas fa-plus me-1"></i>
                                Add Product
                            </button>
                            @endif
                        </div>

                        <div id="productsContainer">
                            <!-- Product rows will be populated here -->
                        </div>

                        @error('products')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Total Section -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Total:</h6>
                                        <h5 class="mb-0 text-success" id="totalAmount">{{ number_format($salesOrder->total, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>
                            Update Sales Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Form -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Change Sales Order Status
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('sales-orders.update-status', $salesOrder) }}" method="POST" id="statusChangeForm">
                    @csrf
                    
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="new_status" class="form-label fw-bold">New Status <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="new_status" name="status" required>
                                <option value="">Select New Status</option>
                                <option value="draft" {{ $salesOrder->status == 'draft' ? 'disabled' : '' }}>Draft</option>
                                <option value="waiting" {{ $salesOrder->status == 'waiting' ? 'disabled' : '' }}>Waiting</option>
                                <option value="accepted" {{ $salesOrder->status == 'accepted' ? 'disabled' : '' }}>Accepted</option>
                                <option value="sent" {{ $salesOrder->status == 'sent' ? 'disabled' : '' }}>Sent</option>
                                <option value="done" {{ $salesOrder->status == 'done' ? 'disabled' : '' }}>Done</option>
                                <option value="cancel" {{ $salesOrder->status == 'cancel' ? 'disabled' : '' }}>Cancel</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="changeStatusBtn">
                                <i class="fas fa-sync-alt me-1"></i>
                                Update Status
                            </button>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-primary mb-0">
                                <small>
                                    <strong>Status Change Rules:</strong><br>
                                    • <strong>Accepted:</strong> Checks stock availability. If sufficient, creates delivery and reserves stock.<br>
                                    • <strong>Waiting:</strong> Set automatically if stock is insufficient.<br>
                                    • <strong>Done:</strong> Creates delivery, updates stock, and creates financial entries.
                                </small>
                            </div>
                        </div>
                    </div>
                </form>
                

                

                

            </div>
        </div>
    </div>
</div>

<!-- Product Row Template -->
<template id="productRowTemplate">
    <div class="product-row border rounded p-3 mb-3">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label">Product <span class="text-danger">*</span></label>
                <select class="form-select product-select" name="products[INDEX][product_id]" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                                data-price="{{ $product->price }}">
                            {{ $product->name }} - {{ $product->sku }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" 
                       class="form-control product-quantity" 
                       name="products[INDEX][quantity]" 
                       min="1" 
                       value="1" 
                       required>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Unit Price</label>
                <input type="text" 
                       class="form-control product-price" 
                       readonly>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label">Line Total</label>
                <input type="text" 
                       class="form-control product-line-total" 
                       readonly>
            </div>
        </div>
        <div class="text-end">
            @if(in_array($salesOrder->status, ['draft', 'waiting']))
            <button type="button" class="btn btn-outline-danger btn-sm remove-product">
                <i class="fas fa-trash me-1"></i>
                Remove
            </button>
            @endif
        </div>
    </div>
</template>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let productIndex = 0;
    const isReadOnly = {{ in_array($salesOrder->status, ['draft', 'waiting']) ? 'false' : 'true' }};
    
    // Populate existing products
    @foreach($salesOrder->productLines as $index => $productLine)
        addProductRow({{ $productLine->product_id }}, {{ $productLine->quantity }}, {{ $productLine->product->price }});
    @endforeach
    
    // Add product row button
    $('#addProductRow').click(function() {
        addProductRow();
    });
    
    // Remove product row
    $(document).on('click', '.remove-product', function() {
        if ($('.product-row').length > 1) {
            $(this).closest('.product-row').remove();
            calculateTotal();
        }
    });
    
    // Product selection change
    $(document).on('change', '.product-select', function() {
        const row = $(this).closest('.product-row');
        const productId = $(this).val();
        const priceField = row.find('.product-price');
        const quantityField = row.find('.product-quantity');
        
        if (productId) {
            const selectedOption = $(this).find('option:selected');
            const price = selectedOption.data('price');
            priceField.val(formatCurrency(price));
            quantityField.trigger('input');
        } else {
            priceField.val('');
            row.find('.product-line-total').val('');
        }
    });
    
    // Quantity change
    $(document).on('input', '.product-quantity', function() {
        const row = $(this).closest('.product-row');
        const price = parseFloat(row.find('.product-price').val().replace(/[^\d.-]/g, '')) || 0;
        const quantity = parseInt($(this).val()) || 0;
        const lineTotal = price * quantity;
        
        row.find('.product-line-total').val(formatCurrency(lineTotal));
        calculateTotal();
    });
    
    // Form validation
    $('#salesOrderForm').submit(function(e) {
        if ($('.product-row').length === 0) {
            e.preventDefault();
            alert('Please add at least one product to the sales order.');
            return false;
        }
        
        // Validate that all products have values
        let isValid = true;
        $('.product-row').each(function() {
            const productId = $(this).find('.product-select').val();
            const quantity = $(this).find('.product-quantity').val();
            
            if (!productId || !quantity) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all product details.');
            return false;
        }
        
        // No status change validation needed in main form
    });
    
    // Status change form validation - Simple and direct
    $('#statusChangeForm').submit(function(e) {
        const newStatus = $('#new_status').val();
        const currentStatus = '{{ $salesOrder->status }}';
        
        // Basic validation
        if (!newStatus) {
            e.preventDefault();
            alert('Please select a new status.');
            return false;
        }
        
        if (newStatus === currentStatus) {
            e.preventDefault();
            alert('Please select a different status.');
            return false;
        }
        
        // Disable button to prevent double submission
        $('#changeStatusBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Processing...');
        
        // Allow form to submit
        return true;
    });
    
    function addProductRow(productId = null, quantity = 1, price = 0) {
        const template = document.getElementById('productRowTemplate');
        const clone = template.content.cloneNode(true);
        
        // Update index
        $(clone).find('select, input').each(function() {
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace('INDEX', productIndex));
            }
        });
        
        // Set values if provided
        if (productId) {
            $(clone).find('.product-select').val(productId).trigger('change');
            $(clone).find('.product-quantity').val(quantity);
            $(clone).find('.product-price').val(formatCurrency(price));
            $(clone).find('.product-line-total').val(formatCurrency(price * quantity));
        }
        
        // Make fields read-only if not editable
        if (isReadOnly) {
            $(clone).find('select, input').prop('readonly', true).prop('disabled', true);
        }
        
        $('#productsContainer').append(clone);
        productIndex++;
        
        if (productId) {
            calculateTotal();
        }
    }
    
    function calculateTotal() {
        let total = 0;
        $('.product-line-total').each(function() {
            const value = parseFloat($(this).val().replace(/[^\d.-]/g, '')) || 0;
            total += value;
        });
        
        $('#totalAmount').text(formatCurrency(total));
    }
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }
    

});
</script>
@endsection
