@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Warehouse
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('warehouses.index') }}">Warehouses</a></li>
                        <li class="breadcrumb-item active">Edit {{ $warehouse->name }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Warehouses
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

        <!-- General Error Messages -->
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Success Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Company Info -->
        <div class="alert alert-info" role="alert">
            <i class="fas fa-building me-2"></i>
            <strong>Company:</strong> {{ $company->name }}
        </div>

        <!-- Warehouse Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Current Warehouse Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Code:</strong>
                        <span class="badge bg-primary">{{ $warehouse->code }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Products:</strong>
                        <span class="badge bg-info">{{ $warehouse->total_products }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Total Quantity:</strong>
                        <span class="badge bg-success">{{ number_format($warehouse->total_quantity) }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Created:</strong>
                        <small class="text-muted">{{ $warehouse->created_at->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Warehouse Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-warehouse me-2"></i>
                    Update Warehouse Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('warehouses.update', $warehouse) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Warehouse Code -->
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">
                                <i class="fas fa-hashtag me-1"></i>
                                Warehouse Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code', $warehouse->code) }}" 
                                   placeholder="e.g., WH001, MAIN, NORTH"
                                   maxlength="50"
                                   required>
                            @error('code')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Unique identifier for the warehouse (max 50 characters)
                            </div>
                        </div>

                        <!-- Warehouse Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-warehouse me-1"></i>
                                Warehouse Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $warehouse->name) }}" 
                                   placeholder="e.g., Main Warehouse, North Branch, Storage Facility"
                                   maxlength="255"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Descriptive name for the warehouse
                            </div>
                        </div>
                    </div>

                    <!-- Warehouse Address -->
                    <div class="mb-4">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            Address
                        </label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="4" 
                                  placeholder="Enter the complete address of the warehouse (optional)"
                                  maxlength="1000">{{ old('address', $warehouse->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Physical location of the warehouse (optional, max 1000 characters)
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Warehouse
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Warning Information -->
        @if($warehouse->stocks()->exists())
        <div class="card border-warning border-0 shadow-sm mt-4">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Important Notice
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-2">
                            <strong>This warehouse has {{ $warehouse->total_products }} products with a total quantity of {{ number_format($warehouse->total_quantity) }}.</strong>
                        </p>
                        <p class="mb-0 text-muted">
                            Changing the warehouse code or name may affect inventory tracking and reporting. 
                            Make sure all team members are aware of these changes.
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('stocks.index') }}?warehouse={{ $warehouse->id }}" class="btn btn-outline-warning">
                            <i class="fas fa-eye me-2"></i>
                            View Stock Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Help Information -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-question-circle me-2"></i>
                    Help & Guidelines
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-lightbulb me-2"></i>
                            Best Practices
                        </h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Use clear, descriptive names</li>
                            <li><i class="fas fa-check text-success me-2"></i>Maintain unique codes for identification</li>
                            <li><i class="fas fa-check text-success me-2"></i>Update address for accurate logistics</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-info-circle me-2"></i>
                            What Happens After Update?
                        </h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-arrow-right text-primary me-2"></i>Changes are immediately reflected</li>
                            <li><i class="fas fa-arrow-right text-primary me-2"></i>Stock records remain unchanged</li>
                            <li><i class="fas fa-arrow-right text-primary me-2"></i>Reports will show updated information</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="handleConfirm()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden input for form reference -->
<input type="hidden" id="confirmForm" value="">
@endsection

@section('script')
<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const code = document.getElementById('code').value.trim();
    const name = document.getElementById('name').value.trim();
    
    if (!code || !name) {
        e.preventDefault();
        showErrorModal('Please fill in all required fields.');
        return false;
    }
    
    // Check if code contains only valid characters
    if (!/^[a-zA-Z0-9-_]+$/.test(code)) {
        e.preventDefault();
        showErrorModal('Warehouse code can only contain letters, numbers, hyphens, and underscores.');
        return false;
    }
    
    // Confirm update if warehouse has stock
    @if($warehouse->stocks()->exists())
    e.preventDefault();
    showConfirmModal('This warehouse has stock records. Are you sure you want to update the warehouse information?', this);
    return false;
    @endif
});

// Show error modal
function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('errorModal')).show();
}

// Show confirmation modal
function showConfirmModal(message, form) {
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmForm').value = form;
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}

// Handle confirmation
function handleConfirm() {
    const form = document.getElementById('confirmForm').value;
    if (form) {
        form.submit();
    }
    bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
}
</script>
@endsection
