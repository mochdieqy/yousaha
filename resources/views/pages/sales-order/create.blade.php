@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Add New Sales Order
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sales-orders.index') }}">Sales Orders</a></li>
                        <li class="breadcrumb-item active">Add New</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('sales-orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Sales Orders
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Sales order will be created for this company</small>
                </div>
            </div>
        </div>

        <!-- Sales Order Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Sales Order Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sales-orders.store') }}" method="POST" id="salesOrderForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Customer Information -->
                        <div class="col-md-6 mb-3">
                            <label for="customer_id" class="form-label">
                                <i class="fas fa-user me-1"></i>
                                Customer <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" 
                                    id="customer_id" 
                                    name="customer_id" 
                                    required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                        <!-- Salesperson -->
                        <div class="col-md-6 mb-3">
                            <label for="salesperson" class="form-label">
                                <i class="fas fa-user-tie me-1"></i>
                                Salesperson <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('salesperson') is-invalid @enderror" 
                                   id="salesperson" 
                                   name="salesperson" 
                                   value="{{ old('salesperson') }}" 
                                   placeholder="Enter salesperson name" 
                                   required>
                            @error('salesperson')
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

                    <!-- Activities -->
                    <div class="mb-4">
                        <label for="activities" class="form-label">
                            <i class="fas fa-tasks me-1"></i>
                            Activities
                        </label>
                        <textarea class="form-control @error('activities') is-invalid @enderror" 
                                  id="activities" 
                                  name="activities" 
                                  rows="3" 
                                  placeholder="Enter activities or notes for this sales order">{{ old('activities') }}</textarea>
                        @error('activities')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Products Section -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">
                                <i class="fas fa-boxes me-2"></i>
                                Products
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="productsContainer">
                                <div class="product-row row mb-3" data-row="0">
                                    <div class="col-md-5">
                                        <label class="form-label">Product <span class="text-danger">*</span></label>
                                        <select class="form-select product-select" name="products[0][product_id]" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                        data-price="{{ $product->price }}"
                                                        data-sku="{{ $product->sku }}">
                                                    {{ $product->name }} ({{ $product->sku }})
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
                                        <label class="form-label">Unit Price</label>
                                        <input type="text" 
                                               class="form-control product-price" 
                                               readonly 
                                               value="Rp 0">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm remove-product" 
                                                style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="button" class="btn btn-outline-primary" id="addProduct">
                                    <i class="fas fa-plus me-2"></i>
                                    Add Another Product
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Total Section -->
                    <div class="card border-0 bg-success text-white mb-4">
                        <div class="card-body text-center">
                            <h4 class="mb-0">
                                <i class="fas fa-calculator me-2"></i>
                                Total: <span id="totalAmount">Rp 0</span>
                            </h4>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('sales-orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Create Sales Order
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
$(document).ready(function() {
    let productRowCount = 1;
    
    // Add new product row
    $('#addProduct').click(function() {
        const newRow = $('.product-row:first').clone();
        newRow.attr('data-row', productRowCount);
        newRow.find('select, input').val('');
        newRow.find('select').attr('name', `products[${productRowCount}][product_id]`);
        newRow.find('input[name*="quantity"]').attr('name', `products[${productRowCount}][quantity]`);
        newRow.find('.remove-product').show();
        newRow.find('.product-price').val('Rp 0');
        
        $('#productsContainer').append(newRow);
        productRowCount++;
        
        // Show remove button for first row if there are multiple rows
        if (productRowCount > 1) {
            $('.product-row:first .remove-product').show();
        }
    });
    
    // Remove product row
    $(document).on('click', '.remove-product', function() {
        $(this).closest('.product-row').remove();
        calculateTotal();
        
        // Hide remove button for first row if only one row remains
        if ($('.product-row').length === 1) {
            $('.product-row:first .remove-product').hide();
        }
    });
    
    // Handle product selection
    $(document).on('change', '.product-select', function() {
        const row = $(this).closest('.product-row');
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price') || 0;
        const quantity = row.find('.product-quantity').val() || 1;
        
        row.find('.product-price').val(`Rp ${formatNumber(price)}`);
        calculateTotal();
    });
    
    // Handle quantity change
    $(document).on('input', '.product-quantity', function() {
        calculateTotal();
    });
    
    // Calculate total
    function calculateTotal() {
        let total = 0;
        
        $('.product-row').each(function() {
            const row = $(this);
            const selectedOption = row.find('.product-select option:selected');
            const price = selectedOption.data('price') || 0;
            const quantity = parseInt(row.find('.product-quantity').val()) || 0;
            
            total += price * quantity;
        });
        
        $('#totalAmount').text(`Rp ${formatNumber(total)}`);
    }
    
    // Format number with commas
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Form validation
    $('#salesOrderForm').on('submit', function(e) {
        const productRows = $('.product-row');
        let hasValidProducts = false;
        
        productRows.each(function() {
            const productId = $(this).find('.product-select').val();
            const quantity = $(this).find('.product-quantity').val();
            
            if (productId && quantity > 0) {
                hasValidProducts = true;
                return false; // break loop
            }
        });
        
        if (!hasValidProducts) {
            e.preventDefault();
            alert('Please add at least one product with valid quantity.');
            return false;
        }
    });
    
    // Initialize first row
    $('.product-row:first .remove-product').hide();
});
</script>
@endsection
