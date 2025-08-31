@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-calendar-plus text-primary me-2"></i>
                    Request Time Off
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('time-offs.index') }}">Time Offs</a></li>
                        <li class="breadcrumb-item active">Request Time Off</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-info text-white me-3">
                    <i class="fas fa-building me-1"></i>
                    {{ $company->name }}
                </span>
                <a href="{{ route('time-offs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Time Offs
                </a>
            </div>
        </div>

        <!-- Time Off Request Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-plus text-primary me-2"></i>
                    New Time Off Request
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('time-offs.store') }}" method="POST" id="timeOffForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employee</label>
                                <div class="form-control-plaintext bg-light p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-user text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $currentEmployee->user->name }}</strong>
                                            <br><small class="text-muted">{{ $currentEmployee->number }} - {{ $currentEmployee->position }}</small>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="employee_id" value="{{ $currentEmployee->id }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Select a date from today onwards
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="reason" class="form-label fw-bold">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" 
                                  id="reason" name="reason" rows="4" 
                                  placeholder="Please provide a detailed reason for your time off request..." 
                                  maxlength="500" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <span id="charCount" class="fw-bold">0</span>/500 characters
                            <span class="ms-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Minimum 10 characters required
                            </span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('time-offs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>
                            Submit Request
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
document.addEventListener('DOMContentLoaded', function() {
    const reasonTextarea = document.getElementById('reason');
    const charCount = document.getElementById('charCount');
    
    // Character count update
    reasonTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        // Update character count styling
        charCount.className = 'fw-bold';
        if (length < 10) {
            charCount.classList.add('text-danger');
        } else if (length > 450) {
            charCount.classList.add('text-warning');
        } else {
            charCount.classList.add('text-success');
        }
    });
    
    // Form validation
    const form = document.getElementById('timeOffForm');
    form.addEventListener('submit', function(e) {
        const employeeId = document.querySelector('input[name="employee_id"]').value;
        const date = document.getElementById('date').value;
        const reason = document.getElementById('reason').value.trim();
        
        let hasError = false;
        
        // Clear previous error states
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        if (!employeeId) {
            e.preventDefault();
            hasError = true;
        }
        
        if (!date) {
            e.preventDefault();
            document.getElementById('date').classList.add('is-invalid');
            hasError = true;
        }
        
        if (!reason) {
            e.preventDefault();
            document.getElementById('reason').classList.add('is-invalid');
            hasError = true;
        }
        
        if (reason.length < 10) {
            e.preventDefault();
            document.getElementById('reason').classList.add('is-invalid');
            hasError = true;
        }
        
        if (hasError) {
            // Show error message at the top
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger alert-dismissible fade show';
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                Please fix the errors below before submitting.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const formElement = document.getElementById('timeOffForm');
            formElement.insertBefore(errorDiv, formElement.firstChild);
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
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

