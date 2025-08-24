@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-gavel text-warning me-2"></i>
                        Review Time Off Request
                        @if($isCompanyOwner)
                            <small class="text-muted d-block">(Company Owner Approval)</small>
                        @endif
                    </h5>
                    <a href="{{ route('time-offs.approval') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Approval Queue
                    </a>
                </div>
                <div class="card-body">
                    <!-- Request Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Request Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Employee:</td>
                                    <td>{{ $timeOff->employee->user->name }} ({{ $timeOff->employee->number }})</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Department:</td>
                                    <td>{{ $timeOff->employee->department->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date:</td>
                                    <td>{{ $timeOff->date->format('M d, Y (l)') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @if($timeOff->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($timeOff->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Approval Authority:</td>
                                    <td>
                                        @if($isCompanyOwner)
                                            <span class="badge bg-primary">Company Owner</span>
                                            @if($timeOff->employee->managerUser)
                                                <br><small class="text-muted">Direct Manager: {{ $timeOff->employee->managerUser->name }}</small>
                                            @else
                                                <br><small class="text-muted">No Direct Manager Assigned</small>
                                            @endif
                                        @else
                                            <span class="badge bg-info">Direct Manager</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Reason</h6>
                            <div class="border rounded p-3 bg-light">
                                {{ $timeOff->reason }}
                            </div>
                        </div>
                    </div>

                    <!-- Approval Form -->
                    @if($timeOff->status === 'pending')
                        <form action="{{ route('time-offs.process-approval', $timeOff) }}" method="POST" id="approvalForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Decision <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="">Select Decision</option>
                                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>
                                                ✅ Approve
                                            </option>
                                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>
                                                ❌ Reject
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Request Date</label>
                                        <div class="form-control-plaintext">
                                            {{ $timeOff->created_at->format('M d, Y \a\t g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('time-offs.approval') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-1"></i>
                                    Submit Decision
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This request has already been processed and cannot be modified.
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('time-offs.approval') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Approval Queue
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Decision</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
            </div>
        </div>
    </div>
</div>

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('approvalForm');
    const confirmationModal = document.getElementById('confirmationModal');
    const confirmationMessage = document.getElementById('confirmationMessage');
    const confirmSubmitBtn = document.getElementById('confirmSubmit');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const status = document.getElementById('status').value;
            
            if (!status) {
                // Show error message in the form instead of modal
                const statusSelect = document.getElementById('status');
                statusSelect.classList.add('is-invalid');
                return;
            }
            
            // Remove any previous error styling
            const statusSelect = document.getElementById('status');
            statusSelect.classList.remove('is-invalid');
            
            // Set confirmation message based on status
            const message = status === 'approved' 
                ? 'Are you sure you want to approve this time off request?' 
                : 'Are you sure you want to reject this time off request?';
            
            confirmationMessage.textContent = message;
            
            // Show confirmation modal
            const modal = new bootstrap.Modal(confirmationModal);
            modal.show();
            
            // Handle confirmation
            confirmSubmitBtn.onclick = function() {
                modal.hide();
                form.submit();
            };
        });
    }
});
</script>
@endsection
