@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-warehouse text-primary me-2"></i>
                    Warehouse Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Warehouses</li>
                    </ol>
                </nav>
            </div>
            @can('warehouses.create')
            <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Warehouse
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

        <!-- Warehouses Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Warehouse List
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
                    <form method="GET" action="{{ route('warehouses.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search warehouses by name, code, or address..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            @if(request('search'))
                                <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Warehouses Table -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">
                                    <i class="fas fa-hashtag me-1"></i>Code
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-warehouse me-1"></i>Name
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>Address
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-boxes me-1"></i>Products
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-chart-bar me-1"></i>Total Quantity
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-calendar me-1"></i>Created
                                </th>
                                <th class="border-0 text-center">
                                    <i class="fas fa-cogs me-1"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($warehouses as $warehouse)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $warehouse->code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $warehouse->name }}</strong>
                                </td>
                                <td>
                                    @if($warehouse->address)
                                        <span class="text-muted">{{ Str::limit($warehouse->address, 50) }}</span>
                                    @else
                                        <span class="text-muted">No address</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $warehouse->total_products }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ number_format($warehouse->total_quantity) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $warehouse->created_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('warehouses.edit')
                                        <a href="{{ route('warehouses.edit', $warehouse) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit Warehouse">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('warehouses.delete')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Warehouse"
                                                onclick="confirmDelete({{ $warehouse->id }}, '{{ $warehouse->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-warehouse fa-3x mb-3"></i>
                                        <h5>No warehouses found</h5>
                                        <p>Start by creating your first warehouse to manage your inventory locations.</p>
                                        @can('warehouses.create')
                                        <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Create First Warehouse
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($warehouses->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $warehouses->firstItem() }} to {{ $warehouses->lastItem() }} 
                            of {{ $warehouses->total() }} warehouses
                        </div>
                        <div>
                            {{ $warehouses->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the warehouse <strong id="warehouseName"></strong>?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
function confirmDelete(warehouseId, warehouseName) {
    document.getElementById('warehouseName').textContent = warehouseName;
    document.getElementById('deleteForm').action = `/warehouses/${warehouseId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
