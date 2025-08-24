@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        General Ledger
                    </h5>
                    @can('general-ledger.create')
                    <a href="{{ route('general-ledger.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        New Entry
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
                                    <th>Type</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($generalLedgers as $ledger)
                                <tr>
                                    <td>{{ $ledger->date->format('Y-m-d') }}</td>
                                    <td>{{ $ledger->number }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $ledger->type }}</span>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($ledger->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($ledger->status === 'Posted')
                                            <span class="badge bg-success">{{ $ledger->status }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ $ledger->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $ledger->reference ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('general-ledger.view')
                                            <a href="{{ route('general-ledger.show', $ledger) }}" 
                                               class="btn btn-outline-info" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('general-ledger.edit')
                                            <a href="{{ route('general-ledger.edit', $ledger) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('general-ledger.delete')
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="confirmDelete('{{ route('general-ledger.delete', $ledger) }}', '{{ $ledger->number }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p>No general ledger entries found.</p>
                                        @can('general-ledger.create')
                                        <a href="{{ route('general-ledger.create') }}" class="btn btn-primary">
                                            Create First Entry
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($generalLedgers->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $generalLedgers->links() }}
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
                <p>Are you sure you want to delete the general ledger entry "<span id="deleteItemName"></span>"?</p>
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
