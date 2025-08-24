@extends('layouts.home')

@section('title', 'General Ledger History - ' . $company->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-0">General Ledger History</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('financial-reports.index') }}">Financial Reports</a></li>
                        <li class="breadcrumb-item active">General Ledger History</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Header -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-book text-info me-2"></i>
                            General Ledger History
                        </h5>
                        <div>
                            <a href="{{ route('financial-reports.general-ledger-history', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'account_id' => $selectedAccountId, 'format' => 'pdf']) }}" class="btn btn-info">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </a>
                            <a href="{{ route('financial-reports.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Reports
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Company</h6>
                            <p class="mb-1"><strong>{{ $company->name }}</strong></p>
                            <p class="text-muted mb-0">{{ $company->address }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted">Report Period</h6>
                            <p class="mb-1"><strong>{{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</strong></p>
                            <p class="text-muted mb-0">Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($selectedAccountId)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="mb-0">
                                    <i class="fas fa-filter me-2"></i>
                                    Filtered by Account: 
                                    @php
                                        $selectedAccount = $accounts->firstWhere('id', $selectedAccountId);
                                    @endphp
                                    <strong>{{ $selectedAccount ? $selectedAccount->code . ' - ' . $selectedAccount->name : 'Unknown Account' }}</strong>
                                </h6>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-secondary me-2"></i>
                        Transaction Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h6 class="text-muted">Total Transactions</h6>
                                <h4 class="text-primary">{{ $generalLedgers->count() }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h6 class="text-muted">Total Debits</h6>
                                <h4 class="text-success">
                                    {{ number_format($generalLedgers->sum(function($ledger) { 
                                        return $ledger->details->where('type', 'debit')->sum('value'); 
                                    }), 2) }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h6 class="text-muted">Total Credits</h6>
                                <h4 class="text-danger">
                                    {{ number_format($generalLedgers->sum(function($ledger) { 
                                        return $ledger->details->where('type', 'credit')->sum('value'); 
                                    }), 2) }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h6 class="text-muted">Period Duration</h6>
                                <h4 class="text-secondary">{{ $startDate->diffInDays($endDate) + 1 }} days</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- General Ledger Entries -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        TRANSACTION DETAILS
                    </h5>
                </div>
                <div class="card-body">
                    @if($groupedEntries->count() > 0)
                        @foreach($groupedEntries as $date => $entries)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2">
                                <i class="fas fa-calendar me-2"></i>
                                {{ \Carbon\Carbon::parse($date)->format('F j, Y (l)') }}
                            </h6>
                            
                            @foreach($entries as $ledger)
                            <div class="card mb-3 border-left-{{ $ledger->type === 'debit' ? 'success' : 'danger' }}" style="border-left: 4px solid {{ $ledger->type === 'debit' ? '#28a745' : '#dc3545' }};">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <small class="text-muted">Transaction #</small>
                                            <p class="mb-0"><strong>{{ $ledger->number }}</strong></p>
                                        </div>
                                        <div class="col-md-2">
                                            <small class="text-muted">Type</small>
                                            <p class="mb-0">
                                                <span class="badge bg-{{ $ledger->type === 'debit' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($ledger->type) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-2">
                                            <small class="text-muted">Total Amount</small>
                                            <p class="mb-0"><strong>{{ number_format($ledger->total, 2) }}</strong></p>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Reference</small>
                                            <p class="mb-0">{{ $ledger->reference ?: 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Description</small>
                                            <p class="mb-0">{{ $ledger->description ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($ledger->details->count() > 0)
                                    <div class="mt-3">
                                        <small class="text-muted">Account Details:</small>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mt-2">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Account</th>
                                                        <th>Type</th>
                                                        <th class="text-end">Amount</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($ledger->details as $detail)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $detail->account->code }}</strong><br>
                                                            <small class="text-muted">{{ $detail->account->name }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $detail->type === 'debit' ? 'success' : 'danger' }}">
                                                                {{ ucfirst($detail->type) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <strong>{{ number_format($detail->value, 2) }}</strong>
                                                        </td>
                                                        <td>{{ $detail->description ?: 'N/A' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No transactions found</h5>
                            <p class="text-muted">No general ledger entries were found for the selected period and criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Add any additional JavaScript functionality here
    console.log('General Ledger History report loaded');
    
    // Auto-expand transaction details on click
    $('.card-body .card').on('click', function() {
        $(this).find('.table-responsive').toggleClass('d-none');
    });
});
</script>
@endsection
