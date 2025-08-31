@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-gavel text-warning me-2"></i>
                    Review Time Off Request
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('time-offs.index') }}">Time Offs</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('time-offs.approval') }}">Approval Queue</a></li>
                        <li class="breadcrumb-item active">Review Request</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-info text-white me-3">
                    <i class="fas fa-building me-1"></i>
                    {{ $company->name }}
                </span>
                <a href="{{ route('time-offs.approval') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Approval Queue
                </a>
            </div>
        </div>

        <!-- Time Off Request Review -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-gavel text-warning me-2"></i>
                    Review Time Off Request
                    @if($isCompanyOwner)
                        <small class="text-muted d-block">(Company Owner Approval)</small>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <!-- Request Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Request Details
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Employee:</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $timeOff->employee->user->name }}</strong>
                                                <br><small class="text-muted">{{ $timeOff->employee->number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Department:</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $timeOff->employee->department->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Date:</td>
                                    <td>
                                        <strong class="text-primary">{{ $timeOff->date->format('M d, Y (l)') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Status:</td>
                                    <td>
                                        @if($timeOff->status === 'pending')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>
                                                Pending
                                            </span>
                                        @elseif($timeOff->status === 'approved')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>
                                                Approved
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>
                                                Rejected
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Approval Authority:</td>
                                    <td>
                                        @if($isCompanyOwner)
                                            <span class="badge bg-primary">
                                                <i class="fas fa-crown me-1"></i>
                                                Company Owner
                                            </span>
                                            @if($timeOff->employee->managerUser)
                                                <br><small class="text-muted mt-1">
                                                    Direct Manager: {{ $timeOff->employee->managerUser->name }}
                                                </small>
                                            @else
                                                <br><small class="text-muted mt-1">No Direct Manager Assigned</small>
                                            @endif
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fas fa-user-tie me-1"></i>
                                                Direct Manager
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-comment me-2"></i>
                            Reason
                        </h6>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0">{{ $timeOff->reason }}</p>
                        </div>
                        
                        <div class="mt-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-calendar me-2"></i>
                                Request Information
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold text-muted">Request Date:</td>
                                        <td>{{ $timeOff->created_at->format('M d, Y \a\t g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Days Ago:</td>
                                        <td>{{ $timeOff->created_at->diffForHumans() }}</td>
                                    </tr>
                                </table>
                            </div>
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
                                    <label for="status" class="form-label fw-bold">Decision <span class="text-danger">*</span></label>
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
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Choose whether to approve or reject this time off request
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Current Status</label>
                                    <div class="form-control-plaintext bg-light p-3 rounded">
                                        <span class="badge bg-warning fs-6">
                                            <i class="fas fa-clock me-1"></i>
                                            Pending Review
                                        </span>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        This request is waiting for your decision
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('time-offs.approval') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>
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
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Approval Queue
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Decision</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeConfirmationModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage"></p>
                <p class="text-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    This action will update the time off request status immediately.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeConfirmationModal()">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let confirmationModalInstance = null;

function closeConfirmationModal() {
    // Method 1: Use stored instance
    if (confirmationModalInstance) {
        confirmationModalInstance.hide();
        return;
    }
    
    // Method 2: Try to get existing instance
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
    if (modal) {
        modal.hide();
        return;
    }
    
    // Method 3: Create new instance and hide immediately
    try {
        const newModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        newModal.hide();
    } catch (error) {
        console.error('Error closing modal:', error);
    }
    
    // Method 4: Manual hide using CSS classes
    const modalElement = document.getElementById('confirmationModal');
    if (modalElement) {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
}

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
            confirmationModalInstance = new bootstrap.Modal(confirmationModal);
            confirmationModalInstance.show();
            
            // Handle confirmation
            confirmSubmitBtn.onclick = function() {
                confirmationModalInstance.hide();
                form.submit();
            };
        });
    }
    
    // Close modal when clicking outside or pressing ESC
    const modalElement = document.getElementById('confirmationModal');
    
    // Close modal when clicking outside
    modalElement.addEventListener('click', function(event) {
        if (event.target === modalElement) {
            closeConfirmationModal();
        }
    });
    
    // Close modal when pressing ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeConfirmationModal();
        }
    });
    
    // Auto-hide success/error messages after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });
});
</script>
@endsection
