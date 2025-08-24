@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Edit Employee: {{ $employee->user->name }}
                    </h5>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Employees
                    </a>
                </div>
                <div class="card-body">
                    <!-- Employee Info (Read-only) -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Employee Name</label>
                            <p class="form-control-plaintext">{{ $employee->user->name }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-plaintext">{{ $employee->user->email }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Manager</label>
                            <p class="form-control-plaintext">
                                @if($employee->managerUser)
                                    {{ $employee->managerUser->name }}
                                @else
                                    <span class="text-muted">No manager assigned</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('employees.update', $employee) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Department -->
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Employee Number -->
                            <div class="col-md-6 mb-3">
                                <label for="number" class="form-label">Employee Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('number') is-invalid @enderror" 
                                       id="number" name="number" value="{{ old('number', $employee->number) }}" 
                                       placeholder="e.g., EMP001" required>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Position -->
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position', $employee->position) }}" 
                                       placeholder="e.g., Software Engineer" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Level -->
                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                                <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                    <option value="">Select Level</option>
                                    <option value="Junior" {{ old('level', $employee->level) == 'Junior' ? 'selected' : '' }}>Junior</option>
                                    <option value="Middle" {{ old('level', $employee->level) == 'Middle' ? 'selected' : '' }}>Middle</option>
                                    <option value="Senior" {{ old('level', $employee->level) == 'Senior' ? 'selected' : '' }}>Senior</option>
                                    <option value="Lead" {{ old('level', $employee->level) == 'Lead' ? 'selected' : '' }}>Lead</option>
                                    <option value="Manager" {{ old('level', $employee->level) == 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Director" {{ old('level', $employee->level) == 'Director' ? 'selected' : '' }}>Director</option>
                                    <option value="VP" {{ old('level', $employee->level) == 'VP' ? 'selected' : '' }}>VP</option>
                                    <option value="C-Level" {{ old('level', $employee->level) == 'C-Level' ? 'selected' : '' }}>C-Level</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Join Date -->
                            <div class="col-md-6 mb-3">
                                <label for="join_date" class="form-label">Join Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('join_date') is-invalid @enderror" 
                                       id="join_date" name="join_date" value="{{ old('join_date', $employee->join_date->format('Y-m-d')) }}" required>
                                @error('join_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Work Arrangement -->
                            <div class="col-md-6 mb-3">
                                <label for="work_arrangement" class="form-label">Work Arrangement <span class="text-danger">*</span></label>
                                <select class="form-select @error('work_arrangement') is-invalid @enderror" id="work_arrangement" name="work_arrangement" required>
                                    <option value="">Select Work Arrangement</option>
                                    <option value="WFO" {{ old('work_arrangement', $employee->work_arrangement) == 'WFO' ? 'selected' : '' }}>Work From Office (WFO)</option>
                                    <option value="WFH" {{ old('work_arrangement', $employee->work_arrangement) == 'WFH' ? 'selected' : '' }}>Work From Home (WFH)</option>
                                    <option value="WFA" {{ old('work_arrangement', $employee->work_arrangement) == 'WFA' ? 'selected' : '' }}>Work From Anywhere (WFA)</option>
                                </select>
                                @error('work_arrangement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Work Location -->
                            <div class="col-md-6 mb-3">
                                <label for="work_location" class="form-label">Work Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('work_location') is-invalid @enderror" 
                                       id="work_location" name="work_location" value="{{ old('work_location', $employee->work_location) }}" 
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
                                Update Employee
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
    // Any additional JavaScript for the edit form can go here
});
</script>
@endsection
