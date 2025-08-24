@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        General Ledger Entry Details
                    </h5>
                    <div>
                        @can('general-ledger.edit')
                        <a href="{{ route('general-ledger.edit', $generalLedger) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endcan
                        <a href="{{ route('general-ledger.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Entry Number:</td>
                                    <td>{{ $generalLedger->number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Entry Type:</td>
                                    <td>
                                        <span class="badge bg-info">{{ $generalLedger->type }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date:</td>
                                    <td>{{ $generalLedger->date->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @if($generalLedger->status === 'Posted')
                                            <span class="badge bg-success">{{ $generalLedger->status }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ $generalLedger->status }}</span>
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
                                    <td>{{ $generalLedger->company->name }}</td>
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
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Note</h6>
                                    <p class="card-text mb-0">{{ $generalLedger->note }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($generalLedger->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Description</h6>
                                    <p class="card-text mb-0">{{ $generalLedger->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <h6 class="mb-3">Journal Entries</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($generalLedger->details as $detail)
                                <tr>
                                    <td>
                                        <strong>{{ $detail->account->code }}</strong><br>
                                        <small class="text-muted">{{ $detail->account->name }}</small>
                                    </td>
                                    <td>
                                        @if($detail->type === 'debit')
                                            <span class="badge bg-danger">Debit</span>
                                        @else
                                            <span class="badge bg-success">Credit</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">
                                        Rp {{ number_format($detail->value, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $detail->description ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <td colspan="2" class="fw-bold">Total Debits:</td>
                                    <td class="text-end fw-bold">
                                        Rp {{ number_format($generalLedger->debits->sum('value'), 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="table-info">
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
                                            <span class="text-success">Balanced ✓</span>
                                        @else
                                            <span class="text-danger">
                                                Rp {{ number_format($generalLedger->debits->sum('value') - $generalLedger->credits->sum('value'), 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Account Summary</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
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
                                                        Rp {{ number_format($debitTotal, 0, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($creditTotal > 0)
                                                        Rp {{ number_format($creditTotal, 0, ',', '.') }}
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
                            <div class="col-md-6">
                                <h6>Entry Information</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Last Updated:</strong> {{ $generalLedger->updated_at->format('F j, Y g:i A') }}</li>
                                    @if($generalLedger->isBalanced())
                                        <li><strong>Status:</strong> <span class="text-success">✓ Balanced Entry</span></li>
                                    @else
                                        <li><strong>Status:</strong> <span class="text-danger">✗ Unbalanced Entry</span></li>
                                    @endif
                                    <li><strong>Total Entries:</strong> {{ $generalLedger->details->count() }}</li>
                                    <li><strong>Debit Entries:</strong> {{ $generalLedger->debits->count() }}</li>
                                    <li><strong>Credit Entries:</strong> {{ $generalLedger->credits->count() }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
