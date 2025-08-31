@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Create New Warehouse
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('warehouses.index') }}">Warehouses</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Warehouses
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Warehouse will be added to this company's inventory system</small>
                </div>
            </div>
        </div>

        <!-- Create Warehouse Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-warehouse me-2"></i>
                    Warehouse Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('warehouses.store') }}" method="POST">
                    @csrf
                    
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
                                   value="{{ old('code') }}" 
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
                                   value="{{ old('name') }}" 
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
                                  maxlength="1000">{{ old('address') }}</textarea>
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
                            Create Warehouse
                        </button>
                    </div>
                </form>
            </div>
        </div>

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
                            <li><i class="fas fa-check text-success me-2"></i>Create unique codes for easy identification</li>
                            <li><i class="fas fa-check text-success me-2"></i>Include complete address for logistics</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-info-circle me-2"></i>
                            What's Next?
                        </h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-arrow-right text-primary me-2"></i>Add products to manage inventory</li>
                            <li><i class="fas fa-arrow-right text-primary me-2"></i>Create stock records for tracking</li>
                            <li><i class="fas fa-arrow-right text-primary me-2"></i>Set up delivery and receipt processes</li>
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
                <h5 class="modal-title text-danger" id="errorModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Validation Error
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
// Auto-generate warehouse code if user types in name field
document.getElementById('name').addEventListener('input', function() {
    const name = this.value.trim();
    const codeField = document.getElementById('code');
    
    if (name && !codeField.value) {
        // Generate a simple code from the name
        let code = name.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
        if (code.length > 8) {
            code = code.substring(0, 8);
        }
        codeField.value = code;
    }
});

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
});

// Show error modal
function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('errorModal')).show();
}
</script>
@endsection
