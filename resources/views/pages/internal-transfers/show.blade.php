@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-exchange-alt text-primary me-2"></i>
                    Internal Transfer Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('internal-transfers.index') }}">Internal Transfers</a></li>
                        <li class="breadcrumb-item active">{{ $internalTransfer->number }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                @can('internal-transfers.edit')
                <a href="{{ route('internal-transfers.edit', $internalTransfer) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i>
                    Edit
                </a>
                @endcan
                
                <a href="{{ route('internal-transfers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Transfers
                </a>
            </div>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ Auth::user()->currentCompany->name ?? 'No Company' }}
                    <br>
                    <small class="text-muted">Transfer belongs to this company's accounts</small>
                </div>
            </div>
        </div>

        <!-- Transfer Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Transfer Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">
                                    <i class="fas fa-hashtag me-1"></i>Transfer Number:
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6">{{ $internalTransfer->number }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Transfer Date:
                                </td>
                                <td>
                                    <strong>{{ $internalTransfer->date->format('Y-m-d') }}</strong>
                                    <br><small class="text-muted">{{ $internalTransfer->date->format('l, F j, Y') }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">
                                    <i class="fas fa-money-bill-wave me-1"></i>Amount:
                                </td>
                                <td>
                                    <span class="h5 text-primary mb-0">Rp {{ number_format($internalTransfer->value, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                            @if($internalTransfer->fee > 0)
                            <tr>
                                <td class="fw-bold">
                                    <i class="fas fa-percentage me-1"></i>Transfer Fee:
                                </td>
                                <td>
                                    <span class="badge bg-warning fs-6">Rp {{ number_format($internalTransfer->fee, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">
                                    <i class="fas fa-credit-card me-1"></i>Fee Charged To:
                                </td>
                                <td>
                                    @if($internalTransfer->fee_charged_to == 'in')
                                        <span class="badge bg-success">
                                            <i class="fas fa-arrow-down me-1"></i>
                                            Destination Account ({{ $internalTransfer->accountIn->code ?? 'N/A' }})
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-arrow-up me-1"></i>
                                            Source Account ({{ $internalTransfer->accountOut->code ?? 'N/A' }})
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">
                                    <i class="fas fa-arrow-up text-danger me-1"></i>From Account:
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-arrow-up text-danger"></i>
                                        </div>
                                        <div>
                                            <span class="badge bg-danger fs-6">{{ $internalTransfer->accountOut->code ?? 'N/A' }}</span>
                                            <br><small class="text-muted">{{ $internalTransfer->accountOut->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">
                                    <i class="fas fa-arrow-down text-success me-1"></i>To Account:
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-arrow-down text-success"></i>
                                        </div>
                                        <div>
                                            <span class="badge bg-success fs-6">{{ $internalTransfer->accountIn->code ?? 'N/A' }}</span>
                                            <br><small class="text-muted">{{ $internalTransfer->accountIn->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">
                                    <i class="fas fa-clock me-1"></i>Created:
                                </td>
                                <td>
                                    <strong>{{ $internalTransfer->created_at->format('Y-m-d H:i:s') }}</strong>
                                    <br><small class="text-muted">{{ $internalTransfer->created_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">
                                    <i class="fas fa-edit me-1"></i>Last Updated:
                                </td>
                                <td>
                                    <strong>{{ $internalTransfer->updated_at->format('Y-m-d H:i:s') }}</strong>
                                    <br><small class="text-muted">{{ $internalTransfer->updated_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($internalTransfer->note)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light border-0">
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
            </div>
        </div>

        <!-- Journal Entry Details -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>
                    Journal Entry Details
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
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
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-arrow-down text-success"></i>
                                        </div>
                                        <div>
                                            <span class="badge bg-success">{{ $internalTransfer->accountIn->code ?? 'N/A' }}</span>
                                            <br><small class="text-muted">{{ $internalTransfer->accountIn->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary">Debit</span></td>
                                <td class="text-end">
                                    <strong class="text-success">Rp {{ number_format($internalTransfer->value, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">-</td>
                                <td>Transfer in: {{ $internalTransfer->number }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-arrow-up text-danger"></i>
                                        </div>
                                        <div>
                                            <span class="badge bg-danger">{{ $internalTransfer->accountOut->code ?? 'N/A' }}</span>
                                            <br><small class="text-muted">{{ $internalTransfer->accountOut->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-warning">Credit</span></td>
                                <td class="text-end">-</td>
                                <td class="text-end">
                                    <strong class="text-danger">Rp {{ number_format($internalTransfer->value, 0, ',', '.') }}</strong>
                                </td>
                                <td>Transfer out: {{ $internalTransfer->number }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="fw-bold">Total</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($internalTransfer->value, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-danger">Rp {{ number_format($internalTransfer->value, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
