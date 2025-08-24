@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Expenses
                    </h5>
                    @can('expenses.create')
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        New Expense
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
                                    <th>Date</th>
                                    <th>Number</th>
                                    <th>Total</th>
                                    <th>Payment Account</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->date->format('Y-m-d') }}</td>
                                    <td>{{ $expense->number }}</td>
                                    <td class="text-end text-danger">Rp {{ number_format($expense->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($expense->paymentAccount)
                                            <span class="badge bg-info">{{ $expense->paymentAccount->code }} - {{ $expense->paymentAccount->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('expenses.view')
                                            <a href="{{ route('expenses.show', $expense) }}" 
                                               class="btn btn-outline-info" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('expenses.edit')
                                            <a href="{{ route('expenses.edit', $expense) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('expenses.delete')
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="confirmDelete('{{ route('expenses.delete', $expense) }}', '{{ $expense->number }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-file-invoice-dollar fa-2x mb-3"></i>
                                        <p>No expenses found.</p>
                                        @can('expenses.create')
                                        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                            Create First Expense
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($expenses->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $expenses->links() }}
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
                <p>Are you sure you want to delete the expense "<span id="deleteItemName"></span>"?</p>
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
