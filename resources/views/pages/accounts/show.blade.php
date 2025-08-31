@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-chart-area text-primary me-2"></i>
                    Account Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Accounts</a></li>
                        <li class="breadcrumb-item active">{{ $account->code }} - {{ $account->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Accounts
                </a>
                @can('accounts.edit')
                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>
                    Edit Account
                </a>
                @endcan
            </div>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ Auth::user()->currentCompany->name }}
                    <br>
                    <small class="text-muted">Account information from this company's chart of accounts</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Account Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Account Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Account Code</label>
                                    <p class="mb-0 fs-5">{{ $account->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Account Type</label>
                                    <div>
                                        @switch($account->type)
                                            @case('Asset')
                                                <span class="badge bg-primary fs-6">{{ $account->type }}</span>
                                                @break
                                            @case('Liability')
                                                <span class="badge bg-danger fs-6">{{ $account->type }}</span>
                                                @break
                                            @case('Equity')
                                                <span class="badge bg-info fs-6">{{ $account->type }}</span>
                                                @break
                                            @case('Revenue')
                                                <span class="badge bg-success fs-6">{{ $account->type }}</span>
                                                @break
                                            @case('Expense')
                                                <span class="badge bg-warning fs-6">{{ $account->type }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary fs-6">{{ $account->type }}</span>
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Account Name</label>
                            <p class="mb-0 fs-5">{{ $account->name }}</p>
                        </div>

                        @if($account->isCriticalAccount())
                            <div class="alert alert-warning border-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Critical System Account:</strong> This account is automatically used in sales orders and purchase orders and cannot be deleted.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Account Balance -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-balance-scale me-2"></i>
                            Current Balance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <h3 class="mb-2">
                                @if($account->type === 'Asset' || $account->type === 'Expense')
                                    <span class="text-danger">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</span>
                                @endif
                            </h3>
                            <p class="text-muted mb-0">
                                @if($account->type === 'Asset' || $account->type === 'Expense')
                                    Debit Balance (Normal Balance)
                                @else
                                    Credit Balance (Normal Balance)
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Account Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">General Ledger Entries</span>
                            <span class="badge bg-primary">{{ $account->generalLedgerDetails->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Expense Records</span>
                            <span class="badge bg-warning">{{ $account->expenseDetails->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Income Records</span>
                            <span class="badge bg-success">{{ $account->incomeDetails->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Internal Transfers</span>
                            <span class="badge bg-info">{{ $account->internalTransfersIn->count() + $account->internalTransfersOut->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Asset Records</span>
                            <span class="badge bg-secondary">{{ $account->assets->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @can('accounts.edit')
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-edit me-2"></i>
                                Edit Account
                            </a>
                            @endcan
                            
                            @can('accounts.delete')
                            @if(!$account->isCriticalAccount())
                            <button type="button" 
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="confirmDelete('{{ route('accounts.delete', $account) }}', '{{ $account->code }} - {{ $account->name }}')">
                                <i class="fas fa-trash me-2"></i>
                                Delete Account
                            </button>
                            @else
                            <button type="button" 
                                    class="btn btn-outline-danger btn-sm" 
                                    disabled
                                    title="Critical account cannot be deleted">
                                <i class="fas fa-trash me-2"></i>
                                Delete Account
                            </button>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the account "<span id="deleteItemName"></span>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function confirmDelete(deleteUrl, itemName) {
    document.getElementById('deleteItemName').textContent = itemName;
    document.getElementById('deleteForm').action = deleteUrl;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
