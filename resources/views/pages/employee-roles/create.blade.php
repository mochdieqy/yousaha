@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Assign New Role
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee-roles.index') }}">Employee Roles</a></li>
                        <li class="breadcrumb-item active">Assign New</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('employee-roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Employee Roles
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Role will be assigned to an employee in this company</small>
                </div>
            </div>
        </div>

        <!-- Role Assignment Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Role Assignment Form
                </h5>
            </div>
            <div class="card-body">
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

                <form action="{{ route('employee-roles.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    Select Employee <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" 
                                        id="employee_id" 
                                        name="employee_id" 
                                        required>
                                    <option value="">Choose an employee...</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                                {{ old('employee_id', request('employee_id')) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->email }})
                                            @if($employee->employee && $employee->employee->department)
                                                - {{ $employee->employee->department->name }}
                                            @endif
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
                                <label for="role_id" class="form-label">
                                    <i class="fas fa-user-shield me-1"></i>
                                    Select Role <span class="text-danger">*</span>
                                </label>
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
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Available Roles
                                </label>
                                <div class="row">
                                    @foreach($roles as $role)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border h-100">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-2 text-primary">{{ $role->name }}</h6>
                                                    <small class="text-muted">
                                                        @if($role->name === 'Company Owner')
                                                            <i class="fas fa-crown text-warning me-1"></i>
                                                            Full system access and company management
                                                        @elseif($role->name === 'Finance Manager')
                                                            <i class="fas fa-chart-line text-success me-1"></i>
                                                            Finance and accounting operations
                                                        @elseif($role->name === 'Sales Manager')
                                                            <i class="fas fa-handshake text-info me-1"></i>
                                                            Sales and customer management
                                                        @elseif($role->name === 'Purchase Manager')
                                                            <i class="fas fa-shopping-cart text-primary me-1"></i>
                                                            Procurement and supplier management
                                                        @elseif($role->name === 'Inventory Manager')
                                                            <i class="fas fa-warehouse text-secondary me-1"></i>
                                                            Inventory and warehouse management
                                                        @elseif($role->name === 'HR Manager')
                                                            <i class="fas fa-users-cog text-warning me-1"></i>
                                                            Human resources and employee management
                                                        @elseif($role->name === 'Employee')
                                            <i class="fas fa-user text-muted me-1"></i>
                                            Basic daily operations
                                        @elseif($role->name === 'Viewer')
                                            <i class="fas fa-eye text-info me-1"></i>
                                            Read-only access to reports
                                        @else
                                            <i class="fas fa-user-tag text-muted me-1"></i>
                                            Custom role with specific permissions
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
            <i class="fas fa-times me-1"></i>
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
@endsection
