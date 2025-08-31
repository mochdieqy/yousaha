@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-edit text-primary me-2"></i>
                    Edit Customer
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                        <li class="breadcrumb-item active">Edit {{ $customer->name }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Customers
            </a>
        </div>

        <!-- Company Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-building text-white fa-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold">{{ $company->name }}</h6>
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $company->address ?? 'No address specified' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Current Customer Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                @if($customer->isIndividual())
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @else
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-building text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $customer->name }}</h6>
                                <small class="text-muted">
                                    @if($customer->isIndividual())
                                        <i class="fas fa-user me-1"></i>Individual Customer
                                    @else
                                        <i class="fas fa-building me-1"></i>Company Customer
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-md-end">
                            <span class="badge bg-secondary">ID: {{ $customer->id }}</span>
                            <span class="badge bg-info">Created: {{ $customer->created_at->format('M d, Y') }}</span>
                            @if($customer->updated_at != $customer->created_at)
                                <span class="badge bg-warning">Updated: {{ $customer->updated_at->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Edit Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Edit Customer Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Customer Type -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label fw-bold">
                                <i class="fas fa-tag me-2"></i>Customer Type <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select customer type</option>
                                <option value="individual" {{ old('type', $customer->type) === 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="company" {{ old('type', $customer->type) === 'company' ? 'selected' : '' }}>Company</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Choose whether this customer is an individual person or a company.
                            </small>
                        </div>

                        <!-- Customer Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-bold">
                                <i class="fas fa-user me-2"></i>Customer Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $customer->name) }}" 
                                   placeholder="Enter customer name" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Enter the full name of the individual or company name.
                            </small>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label fw-bold">
                            <i class="fas fa-map-marker-alt me-2"></i>Address
                        </label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="3" 
                                  placeholder="Enter customer address">{{ old('address', $customer->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Enter the complete address of the customer.
                        </small>
                    </div>

                    <div class="row">
                        <!-- Phone -->
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-bold">
                                <i class="fas fa-phone me-2"></i>Phone Number
                            </label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $customer->phone) }}" 
                                   placeholder="Enter phone number">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Enter the customer's contact phone number.
                            </small>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-bold">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $customer->email) }}" 
                                   placeholder="Enter email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Enter the customer's email address for communications.
                            </small>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Customer Statistics -->
        @if($customer->salesOrders()->exists())
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Customer Activity
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-12">
                        <div>
                            <h4 class="text-warning mb-1">{{ $customer->salesOrders()->count() }}</h4>
                            <p class="text-muted mb-0">Sales Orders</p>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        This customer has associated transactions and cannot be deleted.
                    </small>
                </div>
            </div>
        </div>
        @endif
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
// Auto-format phone number
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 0) {
        if (value.length <= 4) {
            value = value;
        } else if (value.length <= 8) {
            value = value.slice(0, 4) + '-' + value.slice(4);
        } else {
            value = value.slice(0, 4) + '-' + value.slice(4, 8) + '-' + value.slice(8, 12);
        }
    }
    e.target.value = value;
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const type = document.getElementById('type').value;
    const name = document.getElementById('name').value.trim();
    
    if (!type) {
        e.preventDefault();
        showErrorModal('Please select a customer type.');
        document.getElementById('type').focus();
        return false;
    }
    
    if (!name) {
        e.preventDefault();
        showErrorModal('Please enter a customer name.');
        document.getElementById('name').focus();
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
