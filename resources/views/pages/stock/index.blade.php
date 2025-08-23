@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar text-primary me-2"></i>
            Stock Management
        </h1>
        @can('stocks.create')
        <a href="{{ route('stocks.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>
            Add New Stock
        </a>
        @endcan
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <!-- Error Message -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('stocks.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ $request->search }}" placeholder="Product or warehouse...">
                </div>
                <div class="col-md-2">
                    <label for="warehouse_id" class="form-label">Warehouse</label>
                    <select class="form-select" id="warehouse_id" name="warehouse_id">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $request->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="product_id" class="form-label">Product</label>
                    <select class="form-select" id="product_id" name="product_id">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $request->product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="stock_status" class="form-label">Stock Status</label>
                    <select class="form-select" id="stock_status" name="stock_status">
                        <option value="">All Status</option>
                        <option value="normal" {{ $request->stock_status == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="low" {{ $request->stock_status == 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ $request->stock_status == 'out' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Stocks Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Stock List</h6>
            <span class="badge bg-primary">{{ $stocks->total() }} Total Records</span>
        </div>
        <div class="card-body">
            @if($stocks->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="stocksTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>Product</th>
                            <th>Warehouse</th>
                            <th>Total Qty</th>
                            <th>Saleable Qty</th>
                            <th>Reserved Qty</th>
                            <th>Incoming Qty</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <strong>{{ $stock->product->code }}</strong><br>
                                        <small class="text-muted">{{ $stock->product->name }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $stock->warehouse->code }}</span><br>
                                <small>{{ $stock->warehouse->name }}</small>
                            </td>
                            <td>
                                <span class="fw-bold">{{ number_format($stock->quantity_total) }}</span>
                            </td>
                            <td>
                                <span class="fw-bold {{ $stock->quantity_saleable <= 10 ? 'text-warning' : 'text-success' }}">
                                    {{ number_format($stock->quantity_saleable) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ number_format($stock->quantity_reserve) }}</span>
                            </td>
                            <td>
                                <span class="text-info">{{ number_format($stock->quantity_incoming) }}</span>
                            </td>
                            <td>
                                @if($stock->isOutOfStock())
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($stock->isLowStock())
                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                @else
                                    <span class="badge bg-success">Normal</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $stock->updated_at->format('M d, Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('stocks.view')
                                    <a href="{{ route('stocks.show', $stock) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('stocks.edit')
                                    <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-sm btn-warning" title="Edit Stock">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('stocks.delete')
                                    <button type="button" class="btn btn-sm btn-danger" title="Delete Stock"
                                            onclick="confirmDelete('{{ $stock->id }}', '{{ $stock->product->name }} in {{ $stock->warehouse->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $stocks->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No stocks found</h5>
                <p class="text-muted">Start by adding your first stock record.</p>
                @can('stocks.create')
                <a href="{{ route('stocks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Add First Stock
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the stock for <strong id="deleteStockName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
function confirmDelete(stockId, stockName) {
    document.getElementById('deleteStockName').textContent = stockName;
    document.getElementById('deleteForm').action = `/stocks/${stockId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('select[name="warehouse_id"], select[name="product_id"], select[name="stock_status"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endsection
