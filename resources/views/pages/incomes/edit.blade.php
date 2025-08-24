@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Income
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

                    <form action="{{ route('incomes.update', $income) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="number" class="form-label">Income Number *</label>
                                    <input type="text" class="form-control @error('number') is-invalid @enderror" 
                                           id="number" name="number" value="{{ old('number', $income->number) }}" required>
                                    <div class="form-text">Enter a unique income number</div>
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Income Date *</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                           id="date" name="date" value="{{ old('date', $income->date->format('Y-m-d')) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="receipt_account_id" class="form-label">Receipt Account *</label>
                                    <select class="form-select @error('receipt_account_id') is-invalid @enderror" 
                                            id="receipt_account_id" name="receipt_account_id" required>
                                        <option value="">Select Receipt Account</option>
                                        @foreach($receiptAccounts as $account)
                                            <option value="{{ $account->id }}" {{ old('receipt_account_id', $income->receipt_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Select the account where income will be received</div>
                                    @error('receipt_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total" class="form-label">Total Amount *</label>
                                    <input type="number" step="0.01" class="form-control @error('total') is-invalid @enderror" 
                                           id="total" name="total" value="{{ old('total', $income->total) }}" required>
                                    @error('total')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" 
                                      id="note" name="note" rows="3">{{ old('note', $income->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $income->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h6 class="mb-3">Income Details</h6>
                        <div id="income-details">
                            @foreach($income->details as $index => $detail)
                            <div class="row income-detail-row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Account *</label>
                                    <select class="form-select @error('details.'.$index.'.account_id') is-invalid @enderror" 
                                            name="details[{{ $index }}][account_id]" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ $detail->account_id == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('details.'.$index.'.account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label">Amount *</label>
                                    <input type="number" step="0.01" class="form-control detail-amount @error('details.'.$index.'.amount') is-invalid @enderror" 
                                           name="details[{{ $index }}][amount]" value="{{ $detail->amount }}" required>
                                    @error('details.'.$index.'.amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control @error('details.'.$index.'.description') is-invalid @enderror" 
                                           name="details[{{ $index }}][description]" value="{{ $detail->description }}">
                                    @error('details.'.$index.'.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm remove-detail" style="display: {{ $index > 0 ? 'block' : 'none' }};">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="add-detail">
                                <i class="fas fa-plus me-1"></i>Add Detail
                            </button>
                        </div>

                        <div class="alert alert-info">
                            <strong>Total Amount:</strong> <span id="total-display">Rp {{ number_format($income->total, 0, ',', '.') }}</span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('incomes.show', $income) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Income
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
$(document).ready(function() {
    let detailIndex = {{ count($income->details) }};
    
    // Add new detail row
    $('#add-detail').click(function() {
        const newRow = `
            <div class="row income-detail-row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Account *</label>
                    <select class="form-select" name="details[${detailIndex}][account_id]" required>
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" step="0.01" class="form-control detail-amount" 
                           name="details[${detailIndex}][amount]" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-control" 
                           name="details[${detailIndex}][description]">
                </div>
                
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-detail">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#income-details').append(newRow);
        detailIndex++;
        updateRemoveButtons();
    });
    
    // Remove detail row
    $(document).on('click', '.remove-detail', function() {
        $(this).closest('.income-detail-row').remove();
        updateRemoveButtons();
        calculateTotal();
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const rows = $('.income-detail-row');
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
        $('#total').val(total.toFixed(2));
    }
    
    // Update total when amounts change
    $(document).on('input', '.detail-amount', calculateTotal);
    
    // Initial setup
    updateRemoveButtons();
});
</script>
@endsection
