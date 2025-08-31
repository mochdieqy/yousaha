@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-eye text-primary me-2"></i>
                    Income Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('incomes.index') }}">Incomes</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </nav>
            </div>
            <div>
                @can('incomes.edit')
                <a href="{{ route('incomes.edit', $income) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                @endcan
                <a href="{{ route('incomes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        <!-- Income Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Income Information
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <span class="badge bg-info text-white">
                                <i class="fas fa-building me-1"></i>
                                {{ $company->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">Income Number:</td>
                                <td><span class="badge bg-primary fs-6">{{ $income->number }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Date:</td>
                                <td><span class="badge bg-light text-dark">{{ $income->date->format('F j, Y') }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Customer:</td>
                                <td>
                                    @if($income->customer)
                                        <span class="badge bg-success">{{ $income->customer->name }} ({{ $income->customer->type }})</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Receipt Account:</td>
                                <td>
                                    @if($income->receiptAccount)
                                        <span class="badge bg-info">
                                            <strong>{{ $income->receiptAccount->code }}</strong><br>
                                            <small>{{ $income->receiptAccount->name }}</small>
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">Total Amount:</td>
                                <td class="h4 text-success mb-0">Rp {{ number_format($income->total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td><span class="badge bg-success">Posted</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Created:</td>
                                <td>{{ $income->created_at->format('F j, Y g:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Last Updated:</td>
                                <td>{{ $income->updated_at->format('F j, Y g:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($income->note)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-sticky-note me-2"></i>Note
                                </h6>
                                <p class="card-text mb-0">{{ $income->note }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($income->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-align-left me-2"></i>Description
                                </h6>
                                <p class="card-text mb-0">{{ $income->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Income Details -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Income Details
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Account</th>
                                <th class="border-0">Amount</th>
                                <th class="border-0">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($income->details as $detail)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-chart-line text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $detail->account->code }}</h6>
                                            <small class="text-muted">{{ $detail->account->name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="h6 text-success mb-0">Rp {{ number_format($detail->value, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if($detail->description)
                                        <span class="text-muted">{{ $detail->description }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
