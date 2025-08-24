@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-area me-2"></i>
                        Chart of Accounts
                    </h5>
                    @can('accounts.create')
                    <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        New Account
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Balance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accounts as $account)
                                <tr>
                                    <td>
                                        <strong>{{ $account->code }}</strong>
                                        @if($account->isCriticalAccount())
                                            <span class="badge bg-warning ms-1" title="Critical System Account">!</span>
                                        @endif
                                    </td>
                                    <td>{{ $account->name }}</td>
                                    <td>
                                        @switch($account->type)
                                            @case('Asset')
                                                <span class="badge bg-primary">{{ $account->type }}</span>
                                                @break
                                            @case('Liability')
                                                <span class="badge bg-danger">{{ $account->type }}</span>
                                                @break
                                            @case('Equity')
                                                <span class="badge bg-info">{{ $account->type }}</span>
                                                @break
                                            @case('Revenue')
                                                <span class="badge bg-success">{{ $account->type }}</span>
                                                @break
                                            @case('Expense')
                                                <span class="badge bg-warning">{{ $account->type }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $account->type }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-end">
                                        @if($account->type === 'Asset' || $account->type === 'Expense')
                                            <span class="text-danger">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-success">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('accounts.view')
                                            <a href="{{ route('accounts.show', $account) }}" 
                                               class="btn btn-outline-info" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('accounts.edit')
                                            <a href="{{ route('accounts.edit', $account) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('accounts.delete')
                                            @if(!$account->isCriticalAccount())
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="confirmDelete('{{ route('accounts.delete', $account) }}', '{{ $account->code }} - {{ $account->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @else
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    title="Critical account cannot be deleted"
                                                    disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-chart-area fa-2x mb-3"></i>
                                        <p>No accounts found.</p>
                                        @can('accounts.create')
                                        <a href="{{ route('accounts.create') }}" class="btn btn-primary">
                                            Create First Account
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($accounts->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $accounts->links() }}
                    </div>
                    @endif
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
