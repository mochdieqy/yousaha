@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Supplier
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                        <li class="breadcrumb-item active">Edit {{ $supplier->name }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Suppliers
            </a>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Supplier Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Supplier Information
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info text-white me-2">
                            <i class="fas fa-building me-1"></i>
                            {{ $company->name }}
                        </span>
                        @if($supplier->isIndividual())
                            <span class="badge bg-primary">Individual</span>
                        @else
                            <span class="badge bg-success">Company</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Supplier Type -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">
                                Supplier Type <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="individual" {{ (old('type', $supplier->type) === 'individual') ? 'selected' : '' }}>
                                    Individual
                                </option>
                                <option value="company" {{ (old('type', $supplier->type) === 'company') ? 'selected' : '' }}>
                                    Company
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Choose whether this supplier is an individual person or a company.
                            </div>
                        </div>

                        <!-- Supplier Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                Supplier Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $supplier->name) }}" 
                                   placeholder="Enter supplier name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Phone Number -->
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">
                                Phone Number
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $supplier->phone) }}" 
                                       placeholder="Enter phone number">
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                Email Address
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $supplier->email) }}" 
                                       placeholder="Enter email address">
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">
                            Address
                        </label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="3" 
                                  placeholder="Enter supplier address">{{ old('address', $supplier->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Supplier Usage Information -->
                    <div class="alert alert-warning">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Note:</strong> This supplier is currently associated with 
                                <strong>{{ $company->name }}</strong>. Changes will affect all related records.
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Supplier Details Card -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Supplier Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Created:</strong> {{ $supplier->created_at->format('M d, Y H:i') }}</p>
                        <p><strong>Last Updated:</strong> {{ $supplier->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $supplier->id }}</p>
                        <p><strong>Company ID:</strong> {{ $supplier->company_id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation enhancement
    const form = document.querySelector('form');
    const typeSelect = document.getElementById('type');
    const nameInput = document.getElementById('name');
    
    // Update placeholder based on type selection
    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        if (selectedType === 'individual') {
            nameInput.placeholder = 'Enter person name (e.g., John Doe)';
        } else if (selectedType === 'company') {
            nameInput.placeholder = 'Enter company name (e.g., ABC Corporation)';
        } else {
            nameInput.placeholder = 'Enter supplier name';
        }
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        if (!typeSelect.value) {
            e.preventDefault();
            typeSelect.focus();
            typeSelect.classList.add('is-invalid');
            return false;
        }
        
        if (!nameInput.value.trim()) {
            e.preventDefault();
            nameInput.focus();
            nameInput.classList.add('is-invalid');
            return false;
        }
    });
});
</script>
@endsection
