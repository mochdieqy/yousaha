@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Create New Internal Transfer
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

                    <form action="{{ route('internal-transfers.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="number" class="form-label">Transfer Number *</label>
                                    <input type="text" class="form-control @error('number') is-invalid @enderror" 
                                           id="number" name="number" value="{{ old('number') }}" required>
                                    <div class="form-text">Enter a unique transfer number</div>
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Transfer Date *</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                           id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_out" class="form-label">From Account *</label>
                                    <select class="form-select @error('account_out') is-invalid @enderror" 
                                            id="account_out" name="account_out" required>
                                        <option value="">Select Source Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_out') == $account->id ? 'selected' : '' }}>
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
                                    <label for="account_in" class="form-label">To Account *</label>
                                    <select class="form-select @error('account_in') is-invalid @enderror" 
                                            id="account_in" name="account_in" required>
                                        <option value="">Select Destination Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_in') == $account->id ? 'selected' : '' }}>
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
                                    <label for="value" class="form-label">Transfer Amount *</label>
                                    <input type="number" step="0.01" class="form-control @error('value') is-invalid @enderror" 
                                           id="value" name="value" value="{{ old('value') }}" required>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fee" class="form-label">Transfer Fee</label>
                                    <input type="number" step="0.01" class="form-control @error('fee') is-invalid @enderror" 
                                           id="fee" name="fee" value="{{ old('fee', 0) }}" min="0">
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
                                    <label for="fee_charged_to" class="form-label">Fee Charged To</label>
                                    <select class="form-select @error('fee_charged_to') is-invalid @enderror" 
                                            id="fee_charged_to" name="fee_charged_to">
                                        <option value="out" {{ old('fee_charged_to', 'out') == 'out' ? 'selected' : '' }}>Source Account (From)</option>
                                        <option value="in" {{ old('fee_charged_to', 'out') == 'in' ? 'selected' : '' }}>Destination Account (To)</option>
                                    </select>
                                    <div class="form-text">Select which account pays the transfer fee</div>
                                    @error('fee_charged_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" 
                                      id="note" name="note" rows="3">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> This will create a balanced journal entry with:
                            <ul class="mb-0 mt-2">
                                <li>Debit to the destination account (To Account)</li>
                                <li>Credit to the source account (From Account)</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('internal-transfers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create Transfer
                            </button>
                        </div>
                    </form>
                </div>
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
</script>

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
