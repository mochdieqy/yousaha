@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-tie text-primary me-2"></i>
                    Employee Payroll Information Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payrolls.index') }}">Payroll Information</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </nav>
            </div>
            <div>
                @can('payrolls.edit')
                <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i>
                    Edit
                </a>
                @endcan
                <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Viewing payroll information for this company's employee</small>
                </div>
            </div>
        </div>

        <!-- Employee Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Employee Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
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
            </div>
        </div>

        <!-- Insurance Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    Insurance Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
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
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Summary Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-university fa-2x mb-2"></i>
                                <h6>Bank</h6>
                                <p class="mb-0">{{ $payroll->payment_account_bank }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                <h6>Account</h6>
                                <p class="mb-0">{{ $payroll->payment_account_number }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white h-100">
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
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white h-100">
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

        <!-- Info Alert -->
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-lg"></i>
                <div>
                    <strong>Note:</strong> This system manages employee payroll information (bank details, tax numbers, insurance) for payroll setup purposes. 
                    It does NOT handle salary calculations or automatic payments.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
