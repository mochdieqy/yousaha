@extends('layouts.home')

@section('title', 'Statement of Financial Position - ' . $company->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-0">Statement of Financial Position</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('financial-reports.index') }}">Financial Reports</a></li>
                        <li class="breadcrumb-item active">Statement of Financial Position</li>
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
                            <i class="fas fa-balance-scale text-primary me-2"></i>
                            Statement of Financial Position
                        </h5>
                        <div>
                            <a href="{{ route('financial-reports.statement-of-financial-position', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'format' => 'pdf']) }}" class="btn btn-primary">
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

    <!-- Assets Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>
                        ASSETS
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
                                @forelse($assets as $asset)
                                <tr>
                                    <td><strong>{{ $asset->code }}</strong></td>
                                    <td>{{ $asset->name }}</td>
                                    <td class="text-end">{{ number_format($asset->opening_balance, 2) }}</td>
                                    <td class="text-end">{{ number_format($asset->period_balance - $asset->opening_balance, 2) }}</td>
                                    <td class="text-end"><strong>{{ number_format($asset->period_balance, 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No asset accounts found</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="4">TOTAL ASSETS</th>
                                    <th class="text-end">{{ number_format($totalAssets, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liabilities Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        LIABILITIES
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
                                @forelse($liabilities as $liability)
                                <tr>
                                    <td><strong>{{ $liability->code }}</strong></td>
                                    <td>{{ $liability->name }}</td>
                                    <td class="text-end">{{ number_format(-abs($liability->opening_balance), 2) }}</td>
                                    <td class="text-end">{{ number_format(-abs($liability->period_balance - $liability->opening_balance), 2) }}</td>
                                    <td class="text-end"><strong>{{ number_format(-abs($liability->period_balance), 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No liability accounts found</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-warning">
                                    <th colspan="4">TOTAL LIABILITIES</th>
                                    <th class="text-end">{{ number_format($totalLiabilities, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equity Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        EQUITY
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
                                @forelse($equity as $equityAccount)
                                <tr>
                                    <td><strong>{{ $equityAccount->code }}</strong></td>
                                    <td>{{ $equityAccount->name }}</td>
                                    <td class="text-end">{{ number_format($equityAccount->opening_balance, 2) }}</td>
                                    <td class="text-end">{{ number_format($equityAccount->period_balance - $equityAccount->opening_balance, 2) }}</td>
                                    <td class="text-end"><strong>{{ number_format($equityAccount->period_balance, 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No equity accounts found</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <th colspan="4">TOTAL EQUITY</th>
                                    <th class="text-end">{{ number_format($totalEquity, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mx-2 mb-3">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        FINANCIAL POSITION SUMMARY
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Total Assets</h6>
                                <h3 class="text-primary">{{ number_format($totalAssets, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Total Liabilities</h6>
                                <h3 class="text-warning">{{ number_format($totalLiabilities, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Total Equity</h6>
                                <h3 class="text-success">{{ number_format($totalEquity, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Net Income Calculation Section -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Net Income Calculation (Included in Equity)</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">Revenue</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($revenueAccounts as $revenue)
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $revenue->name }}</span>
                                                    <span class="text-success">{{ number_format($revenue->period_balance, 2) }}</span>
                                                </div>
                                            @endforeach
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Total Revenue</span>
                                                <span class="text-success">{{ number_format($revenueAccounts->sum('period_balance'), 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0">Expenses</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($expenseAccounts as $expense)
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $expense->name }}</span>
                                                    <span class="text-danger">{{ number_format($expense->period_balance, 2) }}</span>
                                                </div>
                                            @endforeach
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Total Expenses</span>
                                                <span class="text-danger">{{ number_format($expenseAccounts->sum('period_balance'), 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">Net Income</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <h4 class="text-{{ $netIncome >= 0 ? 'success' : 'danger' }}">
                                                {{ number_format($netIncome, 2) }}
                                            </h4>
                                            <small class="text-muted">
                                                Net Income = Total Revenue - Total Expenses
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="alert {{ ($totalAssets == ($totalLiabilities + $totalEquity)) ? 'alert-success' : 'alert-warning' }}">
                                <h6 class="mb-0">
                                    <i class="fas {{ ($totalAssets == ($totalLiabilities + $totalEquity)) ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-2"></i>
                                    @if($totalAssets == ($totalLiabilities + $totalEquity))
                                        Balance Sheet is balanced: Assets = Liabilities + Equity
                                    @else
                                        Balance Sheet is not balanced. Please review the data.
                                    @endif
                                </h6>
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
    console.log('Statement of Financial Position report loaded');
});
</script>
@endsection
