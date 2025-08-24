@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Add New Employee
                    </h5>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Employees
                    </a>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('employees.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Employee Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Employee Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="Enter employee email address" required>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    The user must be registered in the system first.
                                </small>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Employee Number -->
                            <div class="col-md-6 mb-3">
                                <label for="number" class="form-label">Employee Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('number') is-invalid @enderror" 
                                       id="number" name="number" value="{{ old('number') }}" 
                                       placeholder="e.g., EMP001" required>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Position -->
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position') }}" 
                                       placeholder="e.g., Software Engineer" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Level -->
                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                                <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                    <option value="">Select Level</option>
                                    <option value="Junior" {{ old('level') == 'Junior' ? 'selected' : '' }}>Junior</option>
                                    <option value="Middle" {{ old('level') == 'Middle' ? 'selected' : '' }}>Middle</option>
                                    <option value="Senior" {{ old('level') == 'Senior' ? 'selected' : '' }}>Senior</option>
                                    <option value="Lead" {{ old('level') == 'Lead' ? 'selected' : '' }}>Lead</option>
                                    <option value="Manager" {{ old('level') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Director" {{ old('level') == 'Director' ? 'selected' : '' }}>Director</option>
                                    <option value="VP" {{ old('level') == 'VP' ? 'selected' : '' }}>VP</option>
                                    <option value="C-Level" {{ old('level') == 'C-Level' ? 'selected' : '' }}>C-Level</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Join Date -->
                            <div class="col-md-6 mb-3">
                                <label for="join_date" class="form-label">Join Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('join_date') is-invalid @enderror" 
                                       id="join_date" name="join_date" value="{{ old('join_date') }}" required>
                                @error('join_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Work Arrangement -->
                            <div class="col-md-6 mb-3">
                                <label for="work_arrangement" class="form-label">Work Arrangement <span class="text-danger">*</span></label>
                                <select class="form-select @error('work_arrangement') is-invalid @enderror" id="work_arrangement" name="work_arrangement" required>
                                    <option value="">Select Work Arrangement</option>
                                    <option value="WFO" {{ old('work_arrangement') == 'WFO' ? 'selected' : '' }}>Work From Office (WFO)</option>
                                    <option value="WFH" {{ old('work_arrangement') == 'WFH' ? 'selected' : '' }}>Work From Home (WFH)</option>
                                    <option value="WFA" {{ old('work_arrangement') == 'WFA' ? 'selected' : '' }}>Work From Anywhere (WFA)</option>
                                </select>
                                @error('work_arrangement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Work Location -->
                            <div class="col-md-6 mb-3">
                                <label for="work_location" class="form-label">Work Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('work_location') is-invalid @enderror" 
                                       id="work_location" name="work_location" value="{{ old('work_location') }}" 
                                       placeholder="e.g., Jakarta Office, Remote, etc." required>
                                @error('work_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Create Employee
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
    // Set default join date to today
    if (!document.getElementById('join_date').value) {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('join_date').value = today;
    }
});
</script>
@endsection
