@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Asset Management
                    </h5>
                    @can('assets.create')
                    <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        New Asset
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Purchase Date</th>
                                    <th>Quantity</th>
                                    <th>Location</th>
                                    <th>Reference</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $asset)
                                <tr>
                                    <td><strong>{{ $asset->number }}</strong></td>
                                    <td>{{ $asset->name }}</td>
                                    <td>
                                        @if($asset->purchased_date)
                                            @if(is_string($asset->purchased_date))
                                                {{ \Carbon\Carbon::parse($asset->purchased_date)->format('Y-m-d') }}
                                            @else
                                                {{ $asset->purchased_date->format('Y-m-d') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $asset->quantity }}</td>
                                    <td>{{ $asset->location ?? '-' }}</td>
                                    <td>{{ $asset->reference ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('assets.view')
                                            <a href="{{ route('assets.show', $asset) }}" 
                                               class="btn btn-outline-info" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('assets.edit')
                                            <a href="{{ route('assets.edit', $asset) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('assets.delete')
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="confirmDelete('{{ route('assets.delete', $asset) }}', '{{ $asset->code }} - {{ $asset->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-building fa-2x mb-3"></i>
                                        <p>No assets found.</p>
                                        @can('assets.create')
                                        <a href="{{ route('assets.create') }}" class="btn btn-primary">
                                            Create First Asset
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($assets->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $assets->links() }}
                    </div>
                    @endif
                </div>
            </div>
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
                <p>Are you sure you want to delete the asset "<span id="deleteItemName"></span>"?</p>
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
function confirmDelete(deleteUrl, itemName) {
    document.getElementById('deleteItemName').textContent = itemName;
    document.getElementById('deleteForm').action = deleteUrl;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
