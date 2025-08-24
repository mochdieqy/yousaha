@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Add Employee Payroll Information
                    </h5>
                    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This form is for setting up employee payroll information (bank details, tax numbers, insurance) for payroll processing setup. 
                        It does NOT handle salary calculations or payments.
                    </div>

                    <form action="{{ route('payrolls.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->user->name }} ({{ $employee->number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_account_bank" class="form-label">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('payment_account_bank') is-invalid @enderror" 
                                           id="payment_account_bank" name="payment_account_bank" 
                                           value="{{ old('payment_account_bank') }}" required>
                                    @error('payment_account_bank')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_account_number" class="form-label">Account Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('payment_account_number') is-invalid @enderror" 
                                           id="payment_account_number" name="payment_account_number" 
                                           value="{{ old('payment_account_number') }}" required>
                                    @error('payment_account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tax_number" class="form-label">Tax Number</label>
                                    <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                           id="tax_number" name="tax_number" 
                                           value="{{ old('tax_number') }}">
                                    @error('tax_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Employee's tax identification number</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="employment_insurance_number" class="form-label">Employment Insurance Number</label>
                                    <input type="text" class="form-control @error('employment_insurance_number') is-invalid @enderror" 
                                           id="employment_insurance_number" name="employment_insurance_number" 
                                           value="{{ old('employment_insurance_number') }}">
                                    @error('employment_insurance_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Employment insurance policy number</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="health_insurance_number" class="form-label">Health Insurance Number</label>
                                    <input type="text" class="form-control @error('health_insurance_number') is-invalid @enderror" 
                                           id="health_insurance_number" name="health_insurance_number" 
                                           value="{{ old('health_insurance_number') }}">
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
                                Save Payroll Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
