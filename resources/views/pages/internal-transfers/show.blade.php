@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Internal Transfer Details
                    </h5>
                    <div>
                        @can('internal-transfers.edit')
                        <a href="{{ route('internal-transfers.edit', $internalTransfer) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>
                            Edit
                        </a>
                        @endcan
                        
                        <a href="{{ route('internal-transfers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Transfer Number:</td>
                                    <td>{{ $internalTransfer->number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Transfer Date:</td>
                                    <td>{{ $internalTransfer->date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Amount:</td>
                                    <td class="fw-bold text-primary">Rp {{ number_format($internalTransfer->value, 0, ',', '.') }}</td>
                                </tr>
                                @if($internalTransfer->fee > 0)
                                <tr>
                                    <td class="fw-bold">Transfer Fee:</td>
                                    <td class="text-warning">Rp {{ number_format($internalTransfer->fee, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Fee Charged To:</td>
                                    <td>
                                        @if($internalTransfer->fee_charged_to == 'in')
                                            <span class="badge bg-success">Destination Account ({{ $internalTransfer->accountIn->code ?? 'N/A' }})</span>
                                        @else
                                            <span class="badge bg-danger">Source Account ({{ $internalTransfer->accountOut->code ?? 'N/A' }})</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">From Account:</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ $internalTransfer->accountOut->code ?? 'N/A' }} - {{ $internalTransfer->accountOut->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">To Account:</td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $internalTransfer->accountIn->code ?? 'N/A' }} - {{ $internalTransfer->accountIn->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>{{ $internalTransfer->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Last Updated:</td>
                                    <td>{{ $internalTransfer->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($internalTransfer->note)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        Note
                                    </h6>
                                    <p class="card-text mb-0">{{ $internalTransfer->note }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-book me-2"></i>
                                        Journal Entry Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Account</th>
                                                    <th>Type</th>
                                                    <th class="text-end">Debit</th>
                                                    <th class="text-end">Credit</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            {{ $internalTransfer->accountIn->code ?? 'N/A' }} - {{ $internalTransfer->accountIn->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td><span class="badge bg-primary">Debit</span></td>
                                                    <td class="text-end">Rp {{ number_format($internalTransfer->value, 0, ',', '.') }}</td>
                                                    <td class="text-end">-</td>
                                                    <td>Transfer in: {{ $internalTransfer->number }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-danger">
                                                            {{ $internalTransfer->accountOut->code ?? 'N/A' }} - {{ $internalTransfer->accountOut->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td><span class="badge bg-warning">Credit</span></td>
                                                    <td class="text-end">-</td>
                                                    <td class="text-end">Rp {{ number_format($internalTransfer->value, 0, ',', '.') }}</td>
                                                    <td>Transfer out: {{ $internalTransfer->number }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
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
