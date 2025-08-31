@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-building text-primary me-2"></i>
                    Asset Details: {{ $asset->name }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
                        <li class="breadcrumb-item active">Asset Details</li>
                    </ol>
                </nav>
            </div>
            <div>
                @can('assets.edit')
                <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit Asset
                </a>
                @endcan
                <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Assets
                </a>
            </div>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Asset information for this company</small>
                </div>
            </div>
        </div>

        <!-- Asset Information -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Asset Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150" class="text-muted">Asset Number:</th>
                                <td><strong>{{ $asset->number }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Asset Name:</th>
                                <td>{{ $asset->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Purchase Date:</th>
                                <td>
                                    @if($asset->purchased_date)
                                        <span class="badge bg-info">{{ $asset->purchased_date->format('F j, Y') }}</span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Quantity:</th>
                                <td><span class="badge bg-success">{{ $asset->quantity }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150" class="text-muted">Asset Account:</th>
                                <td>
                                    @if($asset->accountAsset)
                                        <span class="badge bg-primary">{{ $asset->accountAsset->code }}</span>
                                        <br><small class="text-muted">{{ $asset->accountAsset->name }}</small>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Location:</th>
                                <td>
                                    @if($asset->location)
                                        <span class="badge bg-secondary">{{ $asset->location }}</span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Reference:</th>
                                <td>
                                    @if($asset->reference)
                                        <span class="badge bg-light text-dark">{{ $asset->reference }}</span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Created:</th>
                                <td><small class="text-muted">{{ $asset->created_at->format('F j, Y \a\t g:i A') }}</small></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Last Updated:</th>
                                <td><small class="text-muted">{{ $asset->updated_at->format('F j, Y \a\t g:i A') }}</small></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This asset is tracked in the system for inventory and accounting purposes. 
                        Financial transactions related to this asset are managed through the general ledger system.
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Asset Management</h6>
                        <small class="text-muted">Perform actions on this asset</small>
                    </div>
                    <div>
                        @can('assets.edit')
                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i>Edit Asset
                        </a>
                        @endcan
                        @can('assets.delete')
                        <button type="button" 
                                class="btn btn-danger" 
                                onclick="confirmDelete({{ $asset->id }}, '{{ addslashes($asset->name) }}')">
                            <i class="fas fa-trash me-1"></i>Delete Asset
                        </button>
                        @endcan
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeDeleteModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the asset "<strong id="assetName"></strong>"?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeDeleteModal()">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Asset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let deleteModalInstance = null;

function confirmDelete(assetId, assetName) {
    document.getElementById('assetName').textContent = assetName;
    document.getElementById('deleteForm').action = `/assets/${assetId}`;
    
    // Create modal instance and store it globally
    deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModalInstance.show();
}

function closeDeleteModal() {
    // Method 1: Use stored instance
    if (deleteModalInstance) {
        deleteModalInstance.hide();
        return;
    }
    
    // Method 2: Try to get existing instance
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    if (modal) {
        modal.hide();
        return;
    }
    
    // Method 3: Create new instance and hide immediately
    try {
        const newModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        newModal.hide();
    } catch (error) {
        console.error('Error closing modal:', error);
    }
    
    // Method 4: Manual hide using CSS classes
    const modalElement = document.getElementById('deleteModal');
    if (modalElement) {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
}

// Close modal when clicking outside or pressing ESC
document.addEventListener('DOMContentLoaded', function() {
    const deleteModalElement = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    
    // Close modal when clicking outside
    deleteModalElement.addEventListener('click', function(event) {
        if (event.target === deleteModalElement) {
            closeDeleteModal();
        }
    });
    
    // Close modal when pressing ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
        }
    });
    
    // Handle form submission with loading state
    deleteForm.addEventListener('submit', function() {
        const submitBtn = deleteForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
        
        // Re-enable after a delay (in case of errors)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 10000);
    });
});
</script>
@endsection
