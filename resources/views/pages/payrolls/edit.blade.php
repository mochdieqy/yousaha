@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Employee Payroll Information
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payrolls.index') }}">Payroll Information</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Payroll Information
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Editing payroll information for this company's employee</small>
                </div>
            </div>
        </div>

        <!-- Payroll Information Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Employee Payroll Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payrolls.update', $payroll) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Employee Selection -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    Employee <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" 
                                        id="employee_id" 
                                        name="employee_id" 
                                        required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $payroll->employee_id) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user->name }} ({{ $employee->number }})
                                            @if($employee->department)
                                                - {{ $employee->department->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select an employee who doesn't have payroll information yet</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Bank Information -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_account_bank" class="form-label">
                                    <i class="fas fa-university me-1"></i>
                                    Bank Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('payment_account_bank') is-invalid @enderror" 
                                       id="payment_account_bank" 
                                       name="payment_account_bank" 
                                       value="{{ old('payment_account_bank', $payroll->payment_account_bank) }}" 
                                       placeholder="Enter bank name"
                                       required>
                                @error('payment_account_bank')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_account_number" class="form-label">
                                    <i class="fas fa-credit-card me-1"></i>
                                    Account Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('payment_account_number') is-invalid @enderror" 
                                       id="payment_account_number" 
                                       name="payment_account_number" 
                                       value="{{ old('payment_account_number', $payroll->payment_account_number) }}" 
                                       placeholder="Enter account number"
                                       required>
                                @error('payment_account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Tax and Insurance Information -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tax_number" class="form-label">
                                    <i class="fas fa-file-invoice-dollar me-1"></i>
                                    Tax Number
                                </label>
                                <input type="text" 
                                       class="form-control @error('tax_number') is-invalid @enderror" 
                                       id="tax_number" 
                                       name="tax_number" 
                                       value="{{ old('tax_number', $payroll->tax_number) }}"
                                       placeholder="Enter tax number">
                                @error('tax_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Employee's tax identification number</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="employment_insurance_number" class="form-label">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Employment Insurance Number
                                </label>
                                <input type="text" 
                                       class="form-control @error('employment_insurance_number') is-invalid @enderror" 
                                       id="employment_insurance_number" 
                                       name="employment_insurance_number" 
                                       value="{{ old('employment_insurance_number', $payroll->employment_insurance_number) }}"
                                       placeholder="Enter insurance number">
                                @error('employment_insurance_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Employment insurance policy number</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="health_insurance_number" class="form-label">
                                    <i class="fas fa-heartbeat me-1"></i>
                                    Health Insurance Number
                                </label>
                                <input type="text" 
                                       class="form-control @error('health_insurance_number') is-invalid @enderror" 
                                       id="health_insurance_number" 
                                       name="health_insurance_number" 
                                       value="{{ old('health_insurance_number', $payroll->health_insurance_number) }}"
                                       placeholder="Enter insurance number">
                                @error('health_insurance_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Health insurance policy number</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update Payroll Information
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info border-0 shadow-sm mt-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-lg"></i>
                <div>
                    <strong>Note:</strong> This form is for updating employee payroll information (bank details, tax numbers, insurance) for payroll processing setup. 
                    It does NOT handle salary calculations or payments.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
