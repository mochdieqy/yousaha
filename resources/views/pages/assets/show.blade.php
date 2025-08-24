@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Asset Details: {{ $asset->name }}
                    </h5>
                    <div>
                        @can('assets.edit')
                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endcan
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Asset Number:</th>
                                    <td><strong>{{ $asset->number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Asset Name:</th>
                                    <td>{{ $asset->name }}</td>
                                </tr>
                                <tr>
                                    <th>Purchase Date:</th>
                                    <td>
                                        @if($asset->purchased_date)
                                            @if(is_string($asset->purchased_date))
                                                {{ \Carbon\Carbon::parse($asset->purchased_date)->format('F j, Y') }}
                                            @else
                                                {{ $asset->purchased_date->format('F j, Y') }}
                                            @endif
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quantity:</th>
                                    <td>{{ $asset->quantity }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Asset Account:</th>
                                    <td>
                                        @if($asset->accountAsset)
                                            <span class="badge bg-info">{{ $asset->accountAsset->code }}</span>
                                            {{ $asset->accountAsset->name }}
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $asset->location ?? '<span class="text-muted">Not specified</span>' }}</td>
                                </tr>
                                <tr>
                                    <th>Reference:</th>
                                    <td>{{ $asset->reference ?? '<span class="text-muted">Not specified</span>' }}</td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $asset->created_at->format('F j, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $asset->updated_at->format('F j, Y \a\t g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>Asset Information</h6>
                        <p class="text-muted">
                            This asset is tracked in the system for inventory and accounting purposes. 
                            Financial transactions related to this asset are managed through the general ledger system.
                        </p>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Actions</h6>
                            <div>
                                @can('assets.edit')
                                <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i>Edit Asset
                                </a>
                                @endcan
                                @can('assets.delete')
                                <button type="button" 
                                        class="btn btn-danger" 
                                        onclick="confirmDelete('{{ route('assets.delete', $asset) }}', '{{ $asset->number }} - {{ $asset->name }}')">
                                    <i class="fas fa-trash me-1"></i>Delete Asset
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>
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
