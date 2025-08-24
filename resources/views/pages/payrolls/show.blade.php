@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Employee Payroll Information Details
                    </h5>
                    <div>
                        @can('payrolls.edit')
                        <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>
                            Edit
                        </a>
                        @endcan
                        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This system manages employee payroll information (bank details, tax numbers, insurance) for payroll setup purposes. 
                        It does NOT handle salary calculations or automatic payments.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Employee Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Name:</td>
                                    <td>{{ $payroll->employee->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Employee Number:</td>
                                    <td><span class="badge bg-secondary">{{ $payroll->employee->number }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Department:</td>
                                    <td>
                                        @if($payroll->employee->department)
                                            <span class="badge bg-info">{{ $payroll->employee->department->name }}</span>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Email:</td>
                                    <td>{{ $payroll->employee->user->email }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Payroll Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Bank:</td>
                                    <td><span class="fw-bold text-primary">{{ $payroll->payment_account_bank }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Account Number:</td>
                                    <td><code class="text-dark fs-6">{{ $payroll->payment_account_number }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tax Number:</td>
                                    <td>
                                        @if($payroll->tax_number)
                                            <span class="badge bg-success">{{ $payroll->tax_number }}</span>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Insurance Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 200px;">Employment Insurance:</td>
                                    <td>
                                        @if($payroll->employment_insurance_number)
                                            <span class="badge bg-info">{{ $payroll->employment_insurance_number }}</span>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Health Insurance:</td>
                                    <td>
                                        @if($payroll->health_insurance_number)
                                            <span class="badge bg-warning">{{ $payroll->health_insurance_number }}</span>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Record Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Created:</td>
                                    <td>{{ $payroll->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Last Updated:</td>
                                    <td>{{ $payroll->updated_at->format('d M Y, H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="text-primary mb-3">Summary</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-university fa-2x mb-2"></i>
                                        <h6>Bank</h6>
                                        <p class="mb-0">{{ $payroll->payment_account_bank }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-credit-card fa-2x mb-2"></i>
                                        <h6>Account</h6>
                                        <p class="mb-0">{{ $payroll->payment_account_number }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shield-alt fa-2x mb-2"></i>
                                        <h6>Insurance</h6>
                                        <p class="mb-0">
                                            @if($payroll->employment_insurance_number || $payroll->health_insurance_number)
                                                {{ $payroll->employment_insurance_number ? '1' : '0' }}/{{ $payroll->health_insurance_number ? '1' : '0' }}
                                            @else
                                                None
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                                        <h6>Tax</h6>
                                        <p class="mb-0">
                                            {{ $payroll->tax_number ? 'Provided' : 'Not provided' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
