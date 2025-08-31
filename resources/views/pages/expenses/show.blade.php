@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-eye text-primary me-2"></i>
                    Expense Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
                        <li class="breadcrumb-item active">{{ $expense->number }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                @can('expenses.edit')
                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                @endcan
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Expenses
                </a>
            </div>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $expense->company->name }}
                    <br>
                    <small class="text-muted">Viewing expense details for this company</small>
                </div>
            </div>
        </div>

        <!-- Expense Information -->
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold" style="width: 150px;">
                                            <i class="fas fa-hashtag me-2"></i>Expense Number:
                                        </td>
                                        <td>
                                            <span class="badge bg-primary fs-6">{{ $expense->number }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">
                                            <i class="fas fa-calendar me-2"></i>Date:
                                        </td>
                                        <td>
                                            <strong>{{ $expense->date->format('F j, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $expense->date->format('l') }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">
                                            <i class="fas fa-truck me-2"></i>Supplier:
                                        </td>
                                        <td>
                                            @if($expense->supplier)
                                                <span class="badge bg-success">{{ $expense->supplier->name }}</span>
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
                                        <td class="fw-bold" style="width: 150px;">
                                            <i class="fas fa-money-bill me-2"></i>Total Amount:
                                        </td>
                                        <td>
                                            <h4 class="text-danger mb-0">Rp {{ number_format($expense->total, 0, ',', '.') }}</h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">
                                            <i class="fas fa-credit-card me-2"></i>Payment Account:
                                        </td>
                                        <td>
                                            @if($expense->paymentAccount)
                                                <strong class="text-primary">{{ $expense->paymentAccount->code }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $expense->paymentAccount->name }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">
                                            <i class="fas fa-clock me-2"></i>Created:
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $expense->created_at->format('F j, Y g:i A') }}</small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($expense->note)
                        <div class="mt-3">
                            <h6 class="text-muted">
                                <i class="fas fa-sticky-note me-2"></i>
                                Note
                            </h6>
                            <div class="alert alert-light border">
                                {{ $expense->note }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Timeline
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Created</h6>
                                    <small class="text-muted">{{ $expense->created_at->format('M j, Y g:i A') }}</small>
                                </div>
                            </div>
                            @if($expense->updated_at != $expense->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Last Updated</h6>
                                    <small class="text-muted">{{ $expense->updated_at->format('M j, Y g:i A') }}</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Details -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Expense Details
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
                            @foreach($expense->details as $detail)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
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
                                    <span class="text-danger fw-bold">Rp {{ number_format($detail->value, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if($detail->description)
                                        {{ $detail->description }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td class="fw-bold">Total</td>
                                <td class="fw-bold text-danger">Rp {{ number_format($expense->total, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: -24px;
    top: 12px;
    width: 2px;
    height: calc(100% - 12px);
    background-color: #e9ecef;
}
</style>
@endsection
