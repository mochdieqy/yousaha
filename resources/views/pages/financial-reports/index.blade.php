@extends('layouts.home')

@section('title', 'Financial Reports - ' . $company->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-0">Financial Reports</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Financial Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Generate Financial Reports
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Statement of Financial Position -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100 mx-2">
                                <div class="card-body text-center p-4">
                                    <div class="mb-4">
                                        <i class="fas fa-balance-scale text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title mb-3">Statement of Financial Position</h5>
                                    <p class="card-text text-muted mb-4">Generate balance sheet showing assets, liabilities, and equity as of a specific date.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#balanceSheetModal">
                                        <i class="fas fa-file-pdf me-2"></i>Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Profit and Loss Statement -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100 mx-2">
                                <div class="card-body text-center p-4">
                                    <div class="mb-4">
                                        <i class="fas fa-chart-line text-success" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title mb-3">Profit and Loss Statement</h5>
                                    <p class="card-text text-muted mb-4">Generate income statement showing revenue, expenses, and net income for a period.</p>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#profitLossModal">
                                        <i class="fas fa-file-pdf me-2"></i>Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- General Ledger History -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100 mx-2">
                                <div class="card-body text-center p-4">
                                    <div class="mb-4">
                                        <i class="fas fa-book text-info" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="card-title mb-3">General Ledger History</h5>
                                    <p class="card-text text-muted mb-4">Generate detailed transaction history for all accounts or specific accounts.</p>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#ledgerHistoryModal">
                                        <i class="fas fa-file-pdf me-2"></i>Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Period Selection -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm mx-2">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-alt text-secondary me-2"></i>
                                        Quick Period Selection
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('financial-reports.statement-of-financial-position', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'pdf']) }}" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-file-pdf me-2"></i>Current Month
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('financial-reports.statement-of-financial-position', ['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d'), 'format' => 'pdf']) }}" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-file-pdf me-2"></i>Last Month
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('financial-reports.statement-of-financial-position', ['start_date' => now()->startOfYear()->format('Y-m-d'), 'end_date' => now()->endOfYear()->format('Y-m-d'), 'format' => 'pdf']) }}" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-file-pdf me-2"></i>Current Year
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('financial-reports.statement-of-financial-position', ['start_date' => now()->subYear()->startOfYear()->format('Y-m-d'), 'end_date' => now()->subYear()->endOfYear()->format('Y-m-d'), 'format' => 'pdf']) }}" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-file-pdf me-2"></i>Last Year
                                            </a>
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
</div>

<!-- Statement of Financial Position Modal -->
<div class="modal fade" id="balanceSheetModal" tabindex="-1" aria-labelledby="balanceSheetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="balanceSheetModalLabel">Generate Statement of Financial Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('financial-reports.statement-of-financial-position') }}" method="GET">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="format" class="form-label">Output Format</label>
                        <select class="form-select" id="format" name="format">
                            <option value="view">View in Browser</option>
                            <option value="pdf">Download PDF</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf me-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Profit and Loss Modal -->
<div class="modal fade" id="profitLossModal" tabindex="-1" aria-labelledby="profitLossModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profitLossModalLabel">Generate Profit and Loss Statement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('financial-reports.profit-and-loss') }}" method="GET">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pl_start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="pl_start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="pl_end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="pl_end_date" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="pl_format" class="form-label">Output Format</label>
                        <select class="form-select" id="pl_format" name="format">
                            <option value="view">View in Browser</option>
                            <option value="pdf">Download PDF</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-pdf me-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- General Ledger History Modal -->
<div class="modal fade" id="ledgerHistoryModal" tabindex="-1" aria-labelledby="ledgerHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ledgerHistoryModalLabel">Generate General Ledger History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('financial-reports.general-ledger-history') }}" method="GET">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="gl_start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="gl_start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="gl_end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="gl_end_date" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="account_id" class="form-label">Account (Optional)</label>
                        <select class="form-select" id="account_id" name="account_id">
                            <option value="">All Accounts</option>
                            @foreach($company->accounts()->orderBy('code')->get() as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="gl_format" class="form-label">Output Format</label>
                        <select class="form-select" id="gl_format" name="format">
                            <option value="view">View in Browser</option>
                            <option value="pdf">Download PDF</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-file-pdf me-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Set default dates for modals
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    // Format dates for input fields
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    // Set default dates for all modals
    $('#start_date, #pl_start_date, #gl_start_date').val(formatDate(firstDay));
    $('#end_date, #pl_end_date, #gl_end_date').val(formatDate(lastDay));
    
    // Date validation
    $('form').on('submit', function(e) {
        const startDate = new Date($(this).find('input[name="start_date"]').val());
        const endDate = new Date($(this).find('input[name="end_date"]').val());
        
        if (startDate > endDate) {
            e.preventDefault();
            alert('Start date cannot be after end date.');
            return false;
        }
    });
});
</script>
@endsection
