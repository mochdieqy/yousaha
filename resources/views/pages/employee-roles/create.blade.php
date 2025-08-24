@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-plus me-2"></i>
                    Assign New Role
                </h2>
                <a href="{{ route('employee-roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to Employee Roles
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Role Assignment Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('employee-roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Select Employee <span class="text-danger">*</span></label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            id="employee_id" 
                                            name="employee_id" 
                                            required>
                                        <option value="">Choose an employee...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                    {{ old('employee_id', request('employee_id')) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }} ({{ $employee->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role_id" class="form-label">Select Role <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role_id') is-invalid @enderror" 
                                            id="role_id" 
                                            name="role_id" 
                                            required>
                                        <option value="">Choose a role...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" 
                                                    {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Available Roles</label>
                                    <div class="row">
                                        @foreach($roles as $role)
                                            <div class="col-md-3 mb-2">
                                                <div class="card border">
                                                    <div class="card-body p-3">
                                                        <h6 class="card-title mb-1">{{ $role->name }}</h6>
                                                        <small class="text-muted">
                                                            @if($role->name === 'Company Owner')
                                                                Full system access
                                                            @elseif($role->name === 'Finance Manager')
                                                                Finance and accounting
                                                            @elseif($role->name === 'Sales Manager')
                                                                Sales and customer management
                                                            @elseif($role->name === 'Purchase Manager')
                                                                Procurement and supplier management
                                                            @elseif($role->name === 'Inventory Manager')
                                                                Inventory and warehouse management
                                                            @elseif($role->name === 'HR Manager')
                                                                Human resources and employee management
                                                            @elseif($role->name === 'Employee')
                                                                Basic daily operations
                                                            @elseif($role->name === 'Viewer')
                                                                Read-only access
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('employee-roles.index') }}" class="btn btn-secondary me-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Assign Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
