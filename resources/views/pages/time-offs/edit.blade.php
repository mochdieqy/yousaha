@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit text-warning me-2"></i>
                        Edit My Time Off Request
                    </h5>
                    <a href="{{ route('time-offs.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Time Offs
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('time-offs.update', $timeOff) }}" method="POST" id="timeOffForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Employee</label>
                                    <div class="form-control-plaintext">
                                        <strong>{{ $timeOff->employee->user->name }}</strong>
                                        <br><small class="text-muted">{{ $timeOff->employee->number }} - {{ $timeOff->employee->position }}</small>
                                    </div>
                                    <input type="hidden" name="employee_id" value="{{ $timeOff->employee->id }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                           id="date" name="date" value="{{ old('date', $timeOff->date->format('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Select a date from today onwards</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" name="reason" rows="4" 
                                      placeholder="Please provide a detailed reason for your time off request..." 
                                      maxlength="500" required>{{ old('reason', $timeOff->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="charCount">{{ strlen(old('reason', $timeOff->reason)) }}</span>/500 characters
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Current Status</label>
                            <div class="form-control-plaintext">
                                @if($timeOff->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($timeOff->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </div>
                            <div class="form-text">Only pending requests can be edited</div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('time-offs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>
                                Update Request
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
document.addEventListener('DOMContentLoaded', function() {
    const reasonTextarea = document.getElementById('reason');
    const charCount = document.getElementById('charCount');
    
    // Character count update
    reasonTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 450) {
            charCount.classList.add('text-warning');
        } else {
            charCount.classList.remove('text-warning');
        }
        
        if (length > 480) {
            charCount.classList.add('text-danger');
        } else {
            charCount.classList.remove('text-danger');
        }
    });
    
    // Form validation
    const form = document.getElementById('timeOffForm');
    form.addEventListener('submit', function(e) {
        const employeeId = document.getElementById('employee_id').value;
        const date = document.getElementById('date').value;
        const reason = document.getElementById('reason').value.trim();
        
        if (!employeeId) {
            e.preventDefault();
            alert('Please select an employee.');
            return;
        }
        
        if (!date) {
            e.preventDefault();
            alert('Please select a date.');
            return;
        }
        
        if (!reason) {
            e.preventDefault();
            alert('Please provide a reason for your time off request.');
            return;
        }
        
        if (reason.length < 10) {
            e.preventDefault();
            alert('Please provide a more detailed reason (at least 10 characters).');
            return;
        }
    });
});
</script>
@endsection
