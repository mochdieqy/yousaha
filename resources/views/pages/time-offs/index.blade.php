@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        @if($isCompanyOwner)
                            Company Time Off Management
                            <small class="text-muted d-block">(Company Owner - All Employees)</small>
                        @else
                            My Time Off Requests
                        @endif
                    </h5>
                    <div class="d-flex gap-2">
                        @can('time-offs.approve')
                        <a href="{{ route('time-offs.approval') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-check-circle me-1"></i>
                            Approval Queue
                        </a>
                        @endcan
                        @if(!$isCompanyOwner)
                            @can('time-offs.create')
                            <a href="{{ route('time-offs.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Request Time Off
                            </a>
                            @endcan
                        @endif
                    </div>
                </div>
                <div class="card-body">


                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    @if($isCompanyOwner)
                                        <th>Employee</th>
                                    @endif
                                    <th>Date</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($timeOffs as $timeOff)
                                <tr>
                                    @if($isCompanyOwner)
                                        <td>
                                            <strong>{{ $timeOff->employee->user->name }}</strong>
                                            <br><small class="text-muted">{{ $timeOff->employee->number }} - {{ $timeOff->employee->department->name ?? 'N/A' }}</small>
                                        </td>
                                    @endif
                                    <td>
                                        <strong>{{ $timeOff->date->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $timeOff->date->format('l') }}</small>
                                    </td>

                                    <td>
                                        {{ Str::limit($timeOff->reason, 100) }}
                                    </td>
                                    <td>
                                        @if($timeOff->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($timeOff->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($timeOff->status === 'pending')
                                                @if($isCompanyOwner)
                                                    <!-- Company owner can only approve/reject, not edit/delete -->
                                                    <span class="text-muted">Use Approval Queue</span>
                                                @else
                                                    @can('time-offs.edit')
                                                    <a href="{{ route('time-offs.edit', $timeOff) }}" class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endcan
                                                    @can('time-offs.delete')
                                                    <button type="button" class="btn btn-outline-danger" title="Delete" 
                                                            onclick="confirmDelete('{{ route('time-offs.delete', $timeOff) }}', '{{ $timeOff->date->format('M d, Y') }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endcan
                                                @endif
                                            @else
                                                <span class="text-muted">No actions</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $isCompanyOwner ? '5' : '4' }}" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                        <p>
                                            @if($isCompanyOwner)
                                                No time off requests found in the company.
                                            @else
                                                You haven't requested any time off yet.
                                            @endif
                                        </p>
                                        @if($isCompanyOwner)
                                            <p class="text-muted small">Employees can request time off, and you'll be able to approve them here.</p>
                                        @endif
                                        @if(!$isCompanyOwner)
                                            @can('time-offs.create')
                                            <a href="{{ route('time-offs.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus me-1"></i>
                                                Request Your First Time Off
                                            </a>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                <p>Are you sure you want to delete the time off request "<strong id="deleteTimeOffName"></strong>"?</p>
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
function confirmDelete(deleteUrl, timeOffName) {
    document.getElementById('deleteTimeOffName').textContent = timeOffName;
    document.getElementById('deleteForm').action = deleteUrl;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
