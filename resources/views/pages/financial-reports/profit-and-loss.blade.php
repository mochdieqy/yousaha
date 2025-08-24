@extends('layouts.home')

@section('title', 'Profit and Loss Statement - ' . $company->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-0">Profit and Loss Statement</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('financial-reports.index') }}">Financial Reports</a></li>
                        <li class="breadcrumb-item active">Profit and Loss Statement</li>
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
                            <i class="fas fa-chart-line text-success me-2"></i>
                            Profit and Loss Statement
                        </h5>
                        <div>
                            <a href="{{ route('financial-reports.profit-and-loss', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'format' => 'pdf']) }}" class="btn btn-success">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-arrow-up me-2"></i>
                        REVENUE
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th class="text-end">Opening Balance</th>
                                    <th class="text-end">Period Change</th>
                                    <th class="text-end">Period Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueAccounts as $revenue)
                                <tr>
                                    <td><strong>{{ $revenue->code }}</strong></td>
                                    <td>{{ $revenue->name }}</td>
                                    <td class="text-end">{{ number_format($revenue->opening_balance, 2) }}</td>
                                    <td class="text-end">{{ number_format($revenue->period_balance - $revenue->opening_balance, 2) }}</td>
                                    <td class="text-end"><strong>{{ number_format($revenue->period_balance, 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No revenue accounts found</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <th colspan="4">TOTAL REVENUE</th>
                                    <th class="text-end">{{ number_format($totalRevenue, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-arrow-down me-2"></i>
                        EXPENSES
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th class="text-end">Opening Balance</th>
                                    <th class="text-end">Period Change</th>
                                    <th class="text-end">Period Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenseAccounts as $expense)
                                <tr>
                                    <td><strong>{{ $expense->code }}</strong></td>
                                    <td>{{ $expense->name }}</td>
                                    <td class="text-end">{{ number_format($expense->opening_balance, 2) }}</td>
                                    <td class="text-end">{{ number_format($expense->period_balance - $expense->opening_balance, 2) }}</td>
                                    <td class="text-end"><strong>{{ number_format($expense->period_balance, 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No expense accounts found</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-danger">
                                    <th colspan="4">TOTAL EXPENSES</th>
                                    <th class="text-end">{{ number_format($totalExpenses, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Income Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header {{ $netIncome >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        NET INCOME (LOSS)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Total Revenue</h6>
                                <h3 class="text-success">{{ number_format($totalRevenue, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Total Expenses</h6>
                                <h3 class="text-danger">{{ number_format($totalExpenses, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Net Income (Loss)</h6>
                                <h3 class="{{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $netIncome >= 0 ? '+' : '' }}{{ number_format($netIncome, 2) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="alert {{ $netIncome >= 0 ? 'alert-success' : 'alert-danger' }}">
                                <h6 class="mb-0">
                                    <i class="fas {{ $netIncome >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-2"></i>
                                    @if($netIncome >= 0)
                                        The company generated a <strong>profit</strong> of {{ number_format($netIncome, 2) }} during this period.
                                    @else
                                        The company incurred a <strong>loss</strong> of {{ number_format(abs($netIncome), 2) }} during this period.
                                    @endif
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Ratios Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        FINANCIAL RATIOS
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($totalRevenue > 0)
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h6 class="text-muted">Profit Margin</h6>
                                <h4 class="{{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format(($netIncome / $totalRevenue) * 100, 2) }}%
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h6 class="text-muted">Expense Ratio</h6>
                                <h4 class="text-warning">
                                    {{ number_format(($totalExpenses / $totalRevenue) * 100, 2) }}%
                                </h4>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h6 class="text-muted">Revenue Growth</h6>
                                <h4 class="text-info">
                                    @if($totalRevenue > 0)
                                        {{ $totalRevenue > 0 ? '+' : '' }}{{ number_format($totalRevenue, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h6 class="text-muted">Period Duration</h6>
                                <h4 class="text-secondary">
                                    {{ $startDate->diffInDays($endDate) + 1 }} days
                                </h4>
                            </div>
                        </div>
                    </div>
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
    console.log('Profit and Loss Statement report loaded');
});
</script>
@endsection
