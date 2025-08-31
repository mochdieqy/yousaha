@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Internal Transfer
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('internal-transfers.index') }}">Internal Transfers</a></li>
                        <li class="breadcrumb-item active">Edit Transfer</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('internal-transfers.show', $internalTransfer) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Transfer
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ Auth::user()->currentCompany->name ?? 'No Company' }}
                    <br>
                    <small class="text-muted">Transfer belongs to this company's accounts</small>
                </div>
            </div>
        </div>

        <!-- Transfer Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Transfer Information
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('internal-transfers.update', $internalTransfer) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="number" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>
                                    Transfer Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('number') is-invalid @enderror" 
                                       id="number" name="number" value="{{ old('number', $internalTransfer->number) }}" 
                                       placeholder="Enter transfer number" required>
                                <div class="form-text">Enter a unique transfer number</div>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>
                                    Transfer Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', $internalTransfer->date->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="account_out" class="form-label">
                                    <i class="fas fa-arrow-up text-danger me-1"></i>
                                    From Account <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('account_out') is-invalid @enderror" 
                                        id="account_out" name="account_out" required>
                                    <option value="">Select Source Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account_out', $internalTransfer->account_out) == $account->id ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Select the account to transfer from</div>
                                @error('account_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="account_in" class="form-label">
                                    <i class="fas fa-arrow-down text-success me-1"></i>
                                    To Account <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('account_in') is-invalid @enderror" 
                                        id="account_in" name="account_in" required>
                                    <option value="">Select Destination Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account_in', $internalTransfer->account_in) == $account->id ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Select the account to transfer to</div>
                                @error('account_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="value" class="form-label">
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    Transfer Amount <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" value="{{ old('value', $internalTransfer->value) }}" 
                                       placeholder="Enter transfer amount" required>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fee" class="form-label">
                                    <i class="fas fa-percentage me-1"></i>
                                    Transfer Fee
                                </label>
                                <input type="number" step="0.01" class="form-control @error('fee') is-invalid @enderror" 
                                       id="fee" name="fee" value="{{ old('fee', $internalTransfer->fee) }}" min="0" 
                                       placeholder="Enter transfer fee">
                                <div class="form-text">Optional transfer fee amount</div>
                                @error('fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fee_charged_to" class="form-label">
                                    <i class="fas fa-credit-card me-1"></i>
                                    Fee Charged To
                                </label>
                                <select class="form-select @error('fee_charged_to') is-invalid @enderror" 
                                        id="fee_charged_to" name="fee_charged_to">
                                    <option value="out" {{ old('fee_charged_to', $internalTransfer->fee_charged_to) == 'out' ? 'selected' : '' }}>Source Account (From)</option>
                                    <option value="in" {{ old('fee_charged_to', $internalTransfer->fee_charged_to) == 'in' ? 'selected' : '' }}>Destination Account (To)</option>
                                </select>
                                <div class="form-text">Select which account pays the transfer fee</div>
                                @error('fee_charged_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">
                            <i class="fas fa-sticky-note me-1"></i>
                            Note
                        </label>
                        <textarea class="form-control @error('note') is-invalid @enderror" 
                                  id="note" name="note" rows="3" 
                                  placeholder="Enter transfer notes">{{ old('note', $internalTransfer->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info border-0 shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle text-info me-3 mt-1"></i>
                            <div>
                                <strong>Note:</strong> This will update the balanced journal entry with:
                                <ul class="mb-0 mt-2">
                                    <li><i class="fas fa-arrow-down text-success me-1"></i>Debit to the destination account (To Account)</li>
                                    <li><i class="fas fa-arrow-up text-danger me-1"></i>Credit to the source account (From Account)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('internal-transfers.show', $internalTransfer) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Validation Error
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
// Ensure different accounts are selected
document.getElementById('account_out').addEventListener('change', function() {
    const accountOut = this.value;
    const accountIn = document.getElementById('account_in').value;
    
    if (accountOut === accountIn && accountOut !== '') {
        // Show Bootstrap modal instead of alert
        const modal = new bootstrap.Modal(document.getElementById('errorModal'));
        document.getElementById('errorMessage').textContent = 'Source and destination accounts must be different.';
        modal.show();
        this.value = '';
    }
});

document.getElementById('account_in').addEventListener('change', function() {
    const accountIn = this.value;
    const accountOut = document.getElementById('account_out').value;
    
    if (accountIn === accountOut && accountIn !== '') {
        // Show Bootstrap modal instead of alert
        const modal = new bootstrap.Modal(document.getElementById('errorModal'));
        document.getElementById('errorMessage').textContent = 'Source and destination accounts must be different.';
        modal.show();
        this.value = '';
    }
});

// Form submission handling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        
        // Re-enable after a delay (in case of errors)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 15000);
    });
    
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
    const inputs = document.querySelectorAll('input, select, textarea');
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
</script>
@endsection
