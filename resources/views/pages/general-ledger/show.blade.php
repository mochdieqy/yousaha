@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-eye text-primary me-2"></i>
                    General Ledger Entry Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('general-ledger.index') }}">General Ledger</a></li>
                        <li class="breadcrumb-item active">Entry Details</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                @can('general-ledger.edit')
                <a href="{{ route('general-ledger.edit', $generalLedger) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                @endcan
                <a href="{{ route('general-ledger.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        <!-- Entry Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Entry Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">Entry Number:</td>
                                <td><span class="badge bg-light text-dark">{{ $generalLedger->number }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Entry Type:</td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'adjustment' => 'bg-warning',
                                            'transfer' => 'bg-info',
                                            'expense' => 'bg-danger',
                                            'income' => 'bg-success',
                                            'asset' => 'bg-primary',
                                            'equity' => 'bg-secondary',
                                            'other' => 'bg-dark'
                                        ];
                                        $typeColor = $typeColors[$generalLedger->type] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $typeColor }}">{{ ucfirst($generalLedger->type) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Date:</td>
                                <td>
                                    <i class="fas fa-calendar text-primary me-1"></i>
                                    {{ $generalLedger->date->format('F j, Y') }}
                                    <br>
                                    <small class="text-muted">{{ $generalLedger->date->format('g:i A') }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    @if($generalLedger->status === 'posted')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Posted
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Draft
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">Total Amount:</td>
                                <td class="h5 text-primary">Rp {{ number_format($generalLedger->total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Reference:</td>
                                <td>{{ $generalLedger->reference ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Company:</td>
                                <td>
                                    <span class="badge bg-info text-white">
                                        <i class="fas fa-building me-1"></i>
                                        {{ $generalLedger->company->name }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Created:</td>
                                <td>{{ $generalLedger->created_at->format('F j, Y g:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($generalLedger->note)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-sticky-note me-2"></i>Note
                                </h6>
                                <p class="card-text mb-0">{{ $generalLedger->note }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($generalLedger->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-align-left me-2"></i>Description
                                </h6>
                                <p class="card-text mb-0">{{ $generalLedger->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Journal Entries -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Journal Entries
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Account</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Amount</th>
                                <th class="border-0">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($generalLedger->details as $detail)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-chart-pie text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $detail->account->code }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $detail->account->name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($detail->type === 'debit')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-arrow-down me-1"></i>Debit
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="fas fa-arrow-up me-1"></i>Credit
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($detail->value, 0, ',', '.') }}
                                </td>
                                <td>{{ $detail->description ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="fw-bold">Total Debits:</td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($generalLedger->debits->sum('value'), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="fw-bold">Total Credits:</td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($generalLedger->credits->sum('value'), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                            <tr class="table-{{ $generalLedger->isBalanced() ? 'success' : 'danger' }}">
                                <td colspan="2" class="fw-bold">Balance:</td>
                                <td class="text-end fw-bold">
                                    @if($generalLedger->isBalanced())
                                        <span class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>Balanced ✓
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Rp {{ number_format($generalLedger->debits->sum('value') - $generalLedger->credits->sum('value'), 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Information -->
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Account Summary
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Account</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($generalLedger->details->groupBy('account_id') as $accountId => $details)
                                    @php
                                        $account = $details->first()->account;
                                        $debitTotal = $details->where('type', 'debit')->sum('value');
                                        $creditTotal = $details->where('type', 'credit')->sum('value');
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $account->code }}</strong><br>
                                            <small class="text-muted">{{ $account->name }}</small>
                                        </td>
                                        <td class="text-end">
                                            @if($debitTotal > 0)
                                                <span class="text-danger">Rp {{ number_format($debitTotal, 0, ',', '.') }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($creditTotal > 0)
                                                <span class="text-success">Rp {{ number_format($creditTotal, 0, ',', '.') }}</span>
                                            @else
                                                -
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
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Entry Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>Last Updated:</strong><br>
                                <small class="text-muted">{{ $generalLedger->updated_at->format('F j, Y g:i A') }}</small>
                            </li>
                            @if($generalLedger->isBalanced())
                                <li class="mb-2">
                                    <strong>Status:</strong><br>
                                    <span class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>✓ Balanced Entry
                                    </span>
                                </li>
                            @else
                                <li class="mb-2">
                                    <strong>Status:</strong><br>
                                    <span class="text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>✗ Unbalanced Entry
                                    </span>
                                </li>
                            @endif
                            <li class="mb-2">
                                <strong>Total Entries:</strong><br>
                                <span class="badge bg-info">{{ $generalLedger->details->count() }}</span>
                            </li>
                            <li class="mb-2">
                                <strong>Debit Entries:</strong><br>
                                <span class="badge bg-danger">{{ $generalLedger->debits->count() }}</span>
                            </li>
                            <li class="mb-2">
                                <strong>Credit Entries:</strong><br>
                                <span class="badge bg-success">{{ $generalLedger->credits->count() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
