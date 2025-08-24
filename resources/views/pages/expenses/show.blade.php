@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Expense Details
                    </h5>
                    <div>
                        @can('expenses.edit')
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endcan
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Expense Number:</td>
                                    <td>{{ $expense->number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date:</td>
                                    <td>{{ $expense->date->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Supplier:</td>
                                    <td>{{ $expense->supplier ? $expense->supplier->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Payment Account:</td>
                                    <td>
                                        @if($expense->paymentAccount)
                                            <strong>{{ $expense->paymentAccount->code }}</strong><br>
                                            <small class="text-muted">{{ $expense->paymentAccount->name }}</small>
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
                                    <td class="h5 text-danger">Rp {{ number_format($expense->total, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Company:</td>
                                    <td>{{ $expense->company->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>{{ $expense->created_at->format('F j, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Last Updated:</td>
                                    <td>{{ $expense->updated_at->format('F j, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($expense->note)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Note</h6>
                                    <p class="card-text mb-0">{{ $expense->note }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($expense->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Description</h6>
                                    <p class="card-text mb-0">{{ $expense->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <h6 class="mb-3">Expense Details</h6>
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
                                @foreach($expense->details as $detail)
                                <tr>
                                    <td>
                                        <strong>{{ $detail->account->code }}</strong><br>
                                        <small class="text-muted">{{ $detail->account->name }}</small>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        Rp {{ number_format($detail->value, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $detail->description ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-danger">
                                    <td class="fw-bold">Total Expense:</td>
                                    <td class="text-end fw-bold">
                                        Rp {{ number_format($expense->total, 0, ',', '.') }}
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
                                            @foreach($expense->details->groupBy('account_id') as $accountId => $details)
                                            @php
                                                $account = $details->first()->account;
                                                $totalAmount = $details->sum('value');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $account->code }}</strong><br>
                                                    <small class="text-muted">{{ $account->name }}</small>
                                                </td>
                                                <td class="text-end text-danger">
                                                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Expense Information</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Total Details:</strong> {{ $expense->details->count() }}</li>
                                    <li><strong>Payment Account Type:</strong> {{ $expense->paymentAccount ? $expense->paymentAccount->type : '-' }}</li>
                                    @if($expense->supplier)
                                        <li><strong>Supplier:</strong> {{ $expense->supplier->name }}</li>
                                        <li><strong>Supplier Code:</strong> {{ $expense->supplier->code }}</li>
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
