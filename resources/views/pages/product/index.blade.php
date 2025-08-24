@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-boxes text-primary me-2"></i>
                    Product Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </nav>
            </div>
            @can('products.create')
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Product
            </a>
            @endcan
        </div>



        <!-- Products Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Product List
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <span class="badge bg-info text-white">
                                <i class="fas fa-building me-1"></i>
                                {{ $company->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Search and Filter Form -->
                <div class="p-3 border-bottom">
                    <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search products..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="goods" {{ request('type') === 'goods' ? 'selected' : '' }}>Goods</option>
                                <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Services</option>
                                <option value="combo" {{ request('type') === 'combo' ? 'selected' : '' }}>Combos</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            @if(request('search') || request('type'))
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Product</th>
                                <th class="border-0">SKU</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Price</th>
                                <th class="border-0">Cost</th>
                                <th class="border-0">Inventory</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($product->type === 'goods')
                                                <i class="fas fa-box text-primary fa-lg"></i>
                                            @elseif($product->type === 'service')
                                                <i class="fas fa-cogs text-success fa-lg"></i>
                                            @else
                                                <i class="fas fa-layer-group text-warning fa-lg"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $product->name }}</h6>
                                            @if($product->reference)
                                                <small class="text-muted">{{ $product->reference }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $product->sku }}</span>
                                </td>
                                <td>
                                    @if($product->type === 'goods')
                                        <span class="badge bg-primary">Goods</span>
                                    @elseif($product->type === 'service')
                                        <span class="badge bg-success">Service</span>
                                    @else
                                        <span class="badge bg-warning">Combo</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-success">${{ number_format($product->price, 2) }}</strong>
                                        @if($product->taxes > 0)
                                            <br><small class="text-muted">+${{ number_format($product->taxes, 2) }} tax</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($product->cost)
                                        <span class="text-muted">${{ number_format($product->cost, 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if($product->shouldTrackInventory())
                                            <span class="badge bg-info">
                                                <i class="fas fa-chart-bar me-1"></i>
                                                Tracked
                                            </span>
                                            @if($product->current_stock_quantity !== null)
                                                <small class="text-muted mt-1">
                                                    Stock: {{ $product->current_stock_quantity }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times me-1"></i>
                                                Not Tracked
                                            </span>
                                            @if($product->isService())
                                                <small class="text-muted mt-1">Service Product</small>
                                            @endif
                                        @endif
                                        @if($product->is_shrink)
                                            <span class="badge bg-warning mt-1">
                                                <i class="fas fa-arrow-down me-1"></i>
                                                Shrink
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('products.edit')
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit Product">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('products.delete')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Product"
                                                onclick="confirmDelete({{ $product->id }}, '{{ $product->name }}')">
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
                @else
                <div class="text-center py-5">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Products Found</h5>
                    <p class="text-muted">Start by adding your first product to the system.</p>
                    @can('products.create')
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add First Product
                    </a>
                    @endcan
                </div>
                @endif
            </div>
            @if($products->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        <small>
                            Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                            @if($products->total() > 0)
                                (Page {{ $products->currentPage() }} of {{ $products->lastPage() }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($products->total() > 15)
                            <div class="me-3">
                                <small class="text-muted">Items per page: 15</small>
                            </div>
                        @endif
                        <div class="pagination-wrapper">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
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
                <p>Are you sure you want to delete the product "<strong id="productName"></strong>"?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
function confirmDelete(productId, productName) {
    document.getElementById('productName').textContent = productName;
    document.getElementById('deleteForm').action = `/products/${productId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
