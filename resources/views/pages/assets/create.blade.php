@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Add New Asset
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
                        <li class="breadcrumb-item active">Add New</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Assets
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Asset will be added to this company's inventory</small>
                </div>
            </div>
        </div>

        <!-- Asset Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Asset Information
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('assets.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="number" class="form-label">
                                        <i class="fas fa-hashtag me-1"></i>
                                        Asset Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('number') is-invalid @enderror" 
                                           id="number" 
                                           name="number" 
                                           value="{{ old('number') }}" 
                                           placeholder="Enter asset number"
                                           required>
                                    <div class="form-text">Enter a unique asset number</div>
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-tag me-1"></i>
                                        Asset Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Enter asset name"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="purchased_date" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>
                                        Purchase Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('purchased_date') is-invalid @enderror" 
                                           id="purchased_date" 
                                           name="purchased_date" 
                                           value="{{ old('purchased_date', date('Y-m-d')) }}" 
                                           required>
                                    @error('purchased_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label">
                                        <i class="fas fa-sort-numeric-up me-1"></i>
                                        Quantity <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           min="1" 
                                           class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" 
                                           name="quantity" 
                                           value="{{ old('quantity', 1) }}" 
                                           required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="account_asset" class="form-label">
                                        <i class="fas fa-chart-line me-1"></i>
                                        Asset Account <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('account_asset') is-invalid @enderror" 
                                            id="account_asset" 
                                            name="account_asset" 
                                            required>
                                        <option value="">Select Asset Account</option>
                                        @foreach($assetAccounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_asset') == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Select the asset account for this asset</div>
                                    @error('account_asset')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="location" class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        Asset Location
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('location') is-invalid @enderror" 
                                           id="location" 
                                           name="location" 
                                           value="{{ old('location') }}"
                                           placeholder="Enter asset location">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reference" class="form-label">
                                    <i class="fas fa-link me-1"></i>
                                    Reference
                                </label>
                                <input type="text" 
                                       class="form-control @error('reference') is-invalid @enderror" 
                                       id="reference" 
                                       name="reference" 
                                       value="{{ old('reference') }}"
                                       placeholder="Optional reference number or identifier">
                                <div class="form-text">Optional reference number or identifier</div>
                                @error('reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Information
                                    </h6>
                                    
                                    <div class="alert alert-info border-0 mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Note:</strong> Asset values and financial transactions are managed separately through the accounting system.
                                    </div>

                                    <hr>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-save me-2"></i>
                                            Create Asset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Auto-hide validation errors after 8 seconds
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide validation errors after 8 seconds
    const errorElements = document.querySelectorAll('.invalid-feedback');
    errorElements.forEach(error => {
        setTimeout(() => {
            error.style.transition = 'opacity 0.5s ease';
            error.style.opacity = '0';
            setTimeout(() => error.remove(), 500);
        }, 8000);
    });
    
    // Clear validation errors when user starts typing
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                const errorElement = this.parentNode.querySelector('.invalid-feedback');
                if (errorElement) {
                    errorElement.remove();
                }
            }
        });
    });
});

// Form submission handling
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
    
    // Re-enable after a delay (in case of errors)
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 15000);
});
</script>
@endsection
