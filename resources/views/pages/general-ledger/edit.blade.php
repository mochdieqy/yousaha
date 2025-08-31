@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit General Ledger Entry
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('general-ledger.index') }}">General Ledger</a></li>
                        <li class="breadcrumb-item active">Edit Entry</li>
                    </ol>
                </nav>
            </div>
            <div>
                <span class="badge bg-info text-white">
                    <i class="fas fa-building me-1"></i>
                    {{ $company->name }}
                </span>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Entry Information
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
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('general-ledger.update', $generalLedger) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="number" class="form-label">Entry Number *</label>
                                <input type="text" class="form-control @error('number') is-invalid @enderror" 
                                       id="number" name="number" value="{{ old('number', $generalLedger->number) }}" required>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Entry Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    @foreach($generalLedgerTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('type', $generalLedger->type) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date *</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', $generalLedger->date->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="draft" {{ old('status', $generalLedger->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="posted" {{ old('status', $generalLedger->status) == 'posted' ? 'selected' : '' }}>Posted</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reference" class="form-label">Reference</label>
                                <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                       id="reference" name="reference" value="{{ old('reference', $generalLedger->reference) }}">
                                @error('reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total" class="form-label">Total Amount *</label>
                                <input type="number" step="0.01" class="form-control @error('total') is-invalid @enderror" 
                                       id="total" name="total" value="{{ old('total', $generalLedger->total) }}" required>
                                @error('total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" 
                                  id="note" name="note" rows="3">{{ old('note', $generalLedger->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $generalLedger->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h6 class="mb-3">
                        <i class="fas fa-list me-2"></i>
                        Journal Entries
                    </h6>
                    <div id="entries-container">
                        @foreach($generalLedger->details as $index => $detail)
                        <div class="entry-row row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Account *</label>
                                <select class="form-select" name="entries[{{ $index }}][account_id]" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $detail->account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type *</label>
                                <select class="form-select" name="entries[{{ $index }}][type]" required>
                                    <option value="">Select Type</option>
                                    <option value="debit" {{ $detail->type == 'debit' ? 'selected' : '' }}>Debit</option>
                                    <option value="credit" {{ $detail->type == 'credit' ? 'selected' : '' }}>Credit</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Amount *</label>
                                <input type="number" step="0.01" class="form-control entry-amount" 
                                       name="entries[{{ $index }}][value]" value="{{ $detail->value }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Description</label>
                                <input type="text" class="form-control" name="entries[{{ $index }}][description]" value="{{ $detail->description }}">
                                @if($index > 0)
                                <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="removeEntry(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addEntry()">
                            <i class="fas fa-plus me-1"></i>Add Entry
                        </button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <strong>Total Debits: <span id="total-debits">0.00</span></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <strong>Total Credits: <span id="total-credits">0.00</span></strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('general-ledger.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Entry
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
let entryIndex = {{ count($generalLedger->details) }};

function addEntry() {
    const container = document.getElementById('entries-container');
    const newEntry = document.createElement('div');
    newEntry.className = 'entry-row row mb-3';
    newEntry.innerHTML = `
        <div class="col-md-4">
            <label class="form-label">Account *</label>
            <select class="form-select" name="entries[${entryIndex}][account_id]" required>
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">
                        {{ $account->code }} - {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Type *</label>
            <select class="form-select" name="entries[${entryIndex}][type]" required>
                <option value="">Select Type</option>
                <option value="debit">Debit</option>
                <option value="credit">Credit</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Amount *</label>
            <input type="number" step="0.01" class="form-control entry-amount" 
                   name="entries[${entryIndex}][value]" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Description</label>
            <input type="text" class="form-control" name="entries[${entryIndex}][description]">
            <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="removeEntry(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newEntry);
    entryIndex++;
}

function removeEntry(button) {
    button.closest('.entry-row').remove();
    updateTotals();
}

// Update totals when amounts change
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('entry-amount')) {
        updateTotals();
    }
});

// Update totals when type changes
document.addEventListener('change', function(e) {
    if (e.target.name && e.target.name.includes('[type]')) {
        updateTotals();
    }
});

function updateTotals() {
    let totalDebits = 0;
    let totalCredits = 0;
    
    const entries = document.querySelectorAll('.entry-row');
    entries.forEach(entry => {
        const amount = parseFloat(entry.querySelector('.entry-amount').value) || 0;
        const type = entry.querySelector('select[name*="[type]"]').value;
        
        if (type === 'debit') {
            totalDebits += amount;
        } else if (type === 'credit') {
            totalCredits += amount;
        }
    });
    
    document.getElementById('total-debits').textContent = totalDebits.toFixed(2);
    document.getElementById('total-credits').textContent = totalCredits.toFixed(2);
    
    // Highlight if balanced
    const debitElement = document.getElementById('total-debits').closest('.alert');
    const creditElement = document.getElementById('total-credits').closest('.alert');
    
    if (Math.abs(totalDebits - totalCredits) < 0.01) {
        debitElement.className = 'alert alert-success mb-0';
        creditElement.className = 'alert alert-success mb-0';
    } else {
        debitElement.className = 'alert alert-info mb-0';
        creditElement.className = 'alert alert-info mb-0';
    }
}

// Initialize totals on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
});
</script>
@endsection
