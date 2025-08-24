@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Income Details
                    </h5>
                    <div>
                        @can('incomes.edit')
                        <a href="{{ route('incomes.edit', $income) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endcan
                        <a href="{{ route('incomes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Income Number:</td>
                                    <td>{{ $income->number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date:</td>
                                    <td>{{ $income->date->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Customer:</td>
                                    <td>{{ $income->customer ? $income->customer->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Receipt Account:</td>
                                    <td>
                                        @if($income->receiptAccount)
                                            <strong>{{ $income->receiptAccount->code }}</strong><br>
                                            <small class="text-muted">{{ $income->receiptAccount->name }}</small>
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
                                    <td class="h5 text-success">Rp {{ number_format($income->total, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Company:</td>
                                    <td>{{ $income->company->name }}</td>
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
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Note</h6>
                                    <p class="card-text mb-0">{{ $income->note }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($income->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Description</h6>
                                    <p class="card-text mb-0">{{ $income->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <h6 class="mb-3">Income Details</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($income->details as $detail)
                                <tr>
                                    <td>
                                        <strong>{{ $detail->account->code }}</strong><br>
                                        <small class="text-muted">{{ $detail->account->name }}</small>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        Rp {{ number_format($detail->value, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $detail->description ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td class="fw-bold">Total Income:</td>
                                    <td class="text-end fw-bold">
                                        Rp {{ number_format($income->total, 0, ',', '.') }}
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
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($income->details->groupBy('account_id') as $accountId => $details)
                                            @php
                                                $account = $details->first()->account;
                                                $totalAmount = $details->sum('value');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $account->code }}</strong><br>
                                                    <small class="text-muted">{{ $account->name }}</small>
                                                </td>
                                                <td class="text-end text-success">
                                                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Income Information</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Total Details:</strong> {{ $income->details->count() }}</li>
                                    <li><strong>Receipt Account Type:</strong> {{ $income->receiptAccount ? $income->receiptAccount->type : '-' }}</li>
                                    @if($income->customer)
                                        <li><strong>Customer:</strong> {{ $income->customer->name }}</li>
                                        <li><strong>Customer Code:</strong> {{ $income->customer->code }}</li>
                                    @endif
                                    <li><strong>Status:</strong> <span class="text-success">✓ Active</span></li>
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
