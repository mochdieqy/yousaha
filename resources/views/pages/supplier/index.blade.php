@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-building text-primary me-2"></i>
                    Supplier Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Suppliers</li>
                    </ol>
                </nav>
            </div>
            @can('suppliers.create')
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Supplier
            </a>
            @endcan
        </div>

        <!-- Suppliers Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Supplier List
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
                    <form method="GET" action="{{ route('suppliers.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search suppliers..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="individual" {{ request('type') === 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="company" {{ request('type') === 'company' ? 'selected' : '' }}>Company</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            @if(request('search') || request('type'))
                                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Suppliers Table -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Name</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Contact Info</th>
                                <th class="border-0">Address</th>
                                <th class="border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            @if($supplier->isIndividual())
                                                <div class="avatar-title rounded-circle bg-primary text-white">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @else
                                                <div class="avatar-title rounded-circle bg-success text-white">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $supplier->name }}</h6>
                                            <small class="text-muted">
                                                @if($supplier->isIndividual())
                                                    Individual Supplier
                                                @else
                                                    Company Supplier
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($supplier->isIndividual())
                                        <span class="badge bg-primary">Individual</span>
                                    @else
                                        <span class="badge bg-success">Company</span>
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->phone || $supplier->email)
                                        <div>
                                            @if($supplier->phone)
                                                <div class="mb-1">
                                                    <i class="fas fa-phone text-muted me-2"></i>
                                                    <span>{{ $supplier->phone }}</span>
                                                </div>
                                            @endif
                                            @if($supplier->email)
                                                <div>
                                                    <i class="fas fa-envelope text-muted me-2"></i>
                                                    <span>{{ $supplier->email }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No contact info</span>
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->address)
                                        <span>{{ Str::limit($supplier->address, 50) }}</span>
                                    @else
                                        <span class="text-muted">No address</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        @can('suppliers.edit')
                                        <a href="{{ route('suppliers.edit', $supplier) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit Supplier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('suppliers.delete')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Supplier"
                                                onclick="confirmDelete('{{ $supplier->id }}', '{{ $supplier->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-building fa-3x mb-3"></i>
                                        <h5>No suppliers found</h5>
                                        <p>Start by adding your first supplier to manage your supply chain.</p>
                                        @can('suppliers.create')
                                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            Add First Supplier
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
                @if($suppliers->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} suppliers
                        </div>
                        <div>
                            {{ $suppliers->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@can('suppliers.delete')
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the supplier "<strong id="supplierName"></strong>"?</p>
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
                    <button type="submit" class="btn btn-danger">Delete Supplier</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection

@section('script')
<script>
function confirmDelete(supplierId, supplierName) {
    document.getElementById('supplierName').textContent = supplierName;
    document.getElementById('deleteForm').action = `/suppliers/${supplierId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
