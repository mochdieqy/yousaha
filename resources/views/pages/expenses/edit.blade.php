@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Expense
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
                        <li class="breadcrumb-item active">Edit {{ $expense->number }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Expenses
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ Auth::user()->currentCompany->name }}
                    <br>
                    <small class="text-muted">Editing expense for this company's financial records</small>
                </div>
            </div>
        </div>

        <!-- Expense Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Expense Information
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

                <form action="{{ route('expenses.update', $expense) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="number" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>
                                    Expense Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('number') is-invalid @enderror" 
                                       id="number" 
                                       name="number" 
                                       value="{{ old('number', $expense->number) }}" 
                                       placeholder="Enter expense number"
                                       required>
                                <div class="form-text">Enter a unique expense number</div>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>
                                    Expense Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('date') is-invalid @enderror" 
                                       id="date" 
                                       name="date" 
                                       value="{{ old('date', $expense->date->format('Y-m-d')) }}" 
                                       required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">
                                    <i class="fas fa-truck me-1"></i>
                                    Supplier
                                </label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" 
                                        name="supplier_id">
                                    <option value="">Select Supplier (Optional)</option>
                                    @foreach($suppliers ?? [] as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $expense->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Select the supplier if this expense is related to a purchase</div>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_account_id" class="form-label">
                                    <i class="fas fa-credit-card me-1"></i>
                                    Payment Account <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('payment_account_id') is-invalid @enderror" 
                                        id="payment_account_id" 
                                        name="payment_account_id" 
                                        required>
                                    <option value="">Select Payment Account</option>
                                    @foreach($paymentAccounts as $account)
                                        <option value="{{ $account->id }}" {{ old('payment_account_id', $expense->payment_account_id) == $account->id ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Select the account where expense will be paid from</div>
                                @error('payment_account_id')
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
                                  id="note" 
                                  name="note" 
                                  rows="3" 
                                  placeholder="Enter any additional notes about this expense">{{ old('note', $expense->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h6 class="mb-3">
                        <i class="fas fa-list me-2"></i>
                        Expense Details
                    </h6>
                    <div id="expense-details">
                        @foreach($expense->details as $index => $detail)
                        <div class="row expense-detail-row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="fas fa-chart-pie me-1"></i>
                                    Account <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="details[{{ $index }}][account_id]" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $detail->account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill me-1"></i>
                                    Amount <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control detail-amount" 
                                       name="details[{{ $index }}][amount]" 
                                       value="{{ $detail->value }}"
                                       placeholder="0.00"
                                       required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="fas fa-align-left me-1"></i>
                                    Description
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       name="details[{{ $index }}][description]"
                                       value="{{ $detail->description }}"
                                       placeholder="Description">
                            </div>
                            
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                @if($index > 0)
                                <button type="button" class="btn btn-danger btn-sm remove-detail">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="add-detail">
                            <i class="fas fa-plus me-1"></i>Add Detail
                        </button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <strong>Total Details: <span id="total-display">Rp {{ number_format($expense->total, 0, ',', '.') }}</span></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning mb-0">
                                <strong>Expense Total: <span id="expense-total">Rp {{ number_format($expense->total, 0, ',', '.') }}</span></strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Expenses
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let detailIndex = {{ count($expense->details) }};
    
    // Add new detail row
    $('#add-detail').click(function() {
        const newRow = `
            <div class="row expense-detail-row mb-3">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-chart-pie me-1"></i>
                        Account <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" name="details[${detailIndex}][account_id]" required>
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-money-bill me-1"></i>
                        Amount <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           class="form-control detail-amount" 
                           name="details[${detailIndex}][amount]" 
                           placeholder="0.00"
                           required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-align-left me-1"></i>
                        Description
                    </label>
                    <input type="text" 
                           class="form-control" 
                           name="details[${detailIndex}][description]"
                           placeholder="Description">
                </div>
                
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-detail">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#expense-details').append(newRow);
        detailIndex++;
        updateRemoveButtons();
    });
    
    // Remove detail row
    $(document).on('click', '.remove-detail', function() {
        $(this).closest('.expense-detail-row').remove();
        updateRemoveButtons();
        calculateTotal();
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const rows = $('.expense-detail-row');
        rows.each(function(index) {
            const removeBtn = $(this).find('.remove-detail');
            if (rows.length === 1) {
                removeBtn.hide();
            } else {
                removeBtn.show();
            }
        });
    }
    
    // Calculate total
    function calculateTotal() {
        let total = 0;
        $('.detail-amount').each(function() {
            const amount = parseFloat($(this).val()) || 0;
            total += amount;
        });
        
        $('#total-display').text('Rp ' + total.toLocaleString('id-ID'));
        $('#expense-total').text('Rp ' + total.toLocaleString('id-ID'));
    }
    
    // Update total when amounts change
    $(document).on('input', '.detail-amount', calculateTotal);
    
    // Initial setup
    updateRemoveButtons();
    
    // Auto-hide success/error messages after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        }
    });
});
</script>
@endsection
