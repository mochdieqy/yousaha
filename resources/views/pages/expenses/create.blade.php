@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Create New Expense
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

                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="number" class="form-label">Expense Number *</label>
                                    <input type="text" class="form-control @error('number') is-invalid @enderror" 
                                           id="number" name="number" value="{{ old('number') }}" required>
                                    <div class="form-text">Enter a unique expense number</div>
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Expense Date *</label>
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
                                    <label for="payment_account_id" class="form-label">Payment Account *</label>
                                    <select class="form-select @error('payment_account_id') is-invalid @enderror" 
                                            id="payment_account_id" name="payment_account_id" required>
                                        <option value="">Select Payment Account</option>
                                        @foreach($paymentAccounts as $account)
                                            <option value="{{ $account->id }}" {{ old('payment_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Select the account from which payment will be made</div>
                                    @error('payment_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total" class="form-label">Total Amount *</label>
                                    <input type="number" step="0.01" class="form-control @error('total') is-invalid @enderror" 
                                           id="total" name="total" value="{{ old('total') }}" required>
                                    @error('total')
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

                        <hr>

                        <h6 class="mb-3">Expense Details</h6>
                        <div id="details-container">
                            <div class="detail-row row mb-3">
                                <div class="col-md-5">
                                    <label class="form-label">Expense Account *</label>
                                    <select class="form-select" name="details[0][account_id]" required>
                                        <option value="">Select Expense Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Amount *</label>
                                    <input type="number" step="0.01" class="form-control detail-amount" 
                                           name="details[0][amount]" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" name="details[0][description]">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDetail()">
                                <i class="fas fa-plus me-1"></i>Add Detail
                            </button>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Total Details: <span id="total-details">0.00</span></strong>
                            </div>
                            <div class="col-md-6">
                                <strong>Expense Total: <span id="expense-total">0.00</span></strong>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create Expense
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
let detailIndex = 1;

function addDetail() {
    const container = document.getElementById('details-container');
    const newDetail = document.createElement('div');
    newDetail.className = 'detail-row row mb-3';
    newDetail.innerHTML = `
        <div class="col-md-5">
            <label class="form-label">Expense Account *</label>
            <select class="form-select" name="details[${detailIndex}][account_id]" required>
                <option value="">Select Expense Account</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">
                        {{ $account->code }} - {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Amount *</label>
            <input type="number" step="0.01" class="form-control detail-amount" 
                   name="details[${detailIndex}][amount]" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Description</label>
            <input type="text" class="form-control" name="details[${detailIndex}][description]">
            <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="removeDetail(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newDetail);
    detailIndex++;
}

function removeDetail(button) {
    button.closest('.detail-row').remove();
    updateTotals();
}

// Update totals when amounts change
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('detail-amount')) {
        updateTotals();
    }
});

function updateTotals() {
    let totalDetails = 0;
    
    const details = document.querySelectorAll('.detail-amount');
    details.forEach(detail => {
        totalDetails += parseFloat(detail.value) || 0;
    });
    
    document.getElementById('total-details').textContent = totalDetails.toFixed(2);
    document.getElementById('expense-total').textContent = totalDetails.toFixed(2);
}
</script>
@endsection
