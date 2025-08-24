@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Internal Transfers
                    </h5>
                    @can('internal-transfers.create')
                    <a href="{{ route('internal-transfers.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        New Transfer
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
                                    <th>From Account</th>
                                    <th>To Account</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($internalTransfers as $transfer)
                                <tr>
                                    <td>{{ $transfer->date->format('Y-m-d') }}</td>
                                    <td>{{ $transfer->number }}</td>
                                    <td>
                                        @if($transfer->accountOut)
                                            <span class="badge bg-danger">{{ $transfer->accountOut->code }} - {{ $transfer->accountOut->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transfer->accountIn)
                                            <span class="badge bg-success">{{ $transfer->accountIn->code }} - {{ $transfer->accountIn->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($transfer->value, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('internal-transfers.view')
                                            <a href="{{ route('internal-transfers.show', $transfer) }}" 
                                               class="btn btn-outline-info" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('internal-transfers.edit')
                                            <a href="{{ route('internal-transfers.edit', $transfer) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('internal-transfers.delete')
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="confirmDelete('{{ route('internal-transfers.delete', $transfer) }}', '{{ $transfer->number }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-exchange-alt fa-2x mb-3"></i>
                                        <p>No internal transfers found.</p>
                                        @can('internal-transfers.create')
                                        <a href="{{ route('internal-transfers.create') }}" class="btn btn-primary">
                                            Create First Transfer
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($internalTransfers->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $internalTransfers->links() }}
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
                <p>Are you sure you want to delete the internal transfer "<span id="deleteItemName"></span>"?</p>
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
