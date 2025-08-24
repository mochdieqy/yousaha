<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Ledger History - {{ $company->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            color: #333;
            font-size: 8px;
            line-height: 1.2;
        }
        .header {
            border-bottom: 2px solid #28a745;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-info {
            float: left;
            width: 60%;
        }
        .report-info {
            float: right;
            width: 35%;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 3px;
        }
        .company-address {
            margin-bottom: 3px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .report-subtitle {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #28a745;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        .date-header {
            font-size: 11px;
            font-weight: bold;
            color: #007bff;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #007bff;
            padding-bottom: 3px;
        }
        .transaction-card {
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-left: 4px solid #28a745;
        }
        .transaction-header {
            background-color: #f8f9fa;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .transaction-body {
            padding: 8px;
        }
        .transaction-details {
            margin-top: 8px;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            font-weight: bold;
            font-size: 7px;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 7px;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-section {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 15px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 8px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .summary-label {
            font-size: 8px;
            color: #666;
        }
        .debit-badge {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
        }
        .credit-badge {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 6px;
            color: #666;
            text-align: center;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $company->name }}</div>
            <div class="company-address">{{ $company->address }}</div>
        </div>
        <div class="report-info">
            <div class="report-title">General Ledger History</div>
            <div class="report-subtitle">Period: {{ $startDate->format('M j, Y') }} - {{ $endDate->format('M j, Y') }}</div>
            @if($selectedAccountId)
            <div class="report-subtitle">
                Account: 
                @php
                    $selectedAccount = $accounts->firstWhere('id', $selectedAccountId);
                @endphp
                {{ $selectedAccount ? $selectedAccount->code . ' - ' . $selectedAccount->name : 'Unknown Account' }}
            </div>
            @endif
            <div class="report-subtitle">Generated: {{ now()->format('M j, Y g:i A') }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Transaction Summary -->
    <div class="summary-section">
        <div class="section-title">TRANSACTION SUMMARY</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value text-primary">{{ $generalLedgers->count() }}</div>
                <div class="summary-label">Total Transactions</div>
            </div>
            <div class="summary-item">
                <div class="summary-value text-success">
                    {{ number_format($generalLedgers->sum(function($ledger) { 
                        return $ledger->details->where('type', 'debit')->sum('value'); 
                    }), 2) }}
                </div>
                <div class="summary-label">Total Debits</div>
            </div>
            <div class="summary-item">
                <div class="summary-value text-danger">
                    {{ number_format($generalLedgers->sum(function($ledger) { 
                        return $ledger->details->where('type', 'credit')->sum('value'); 
                    }), 2) }}
                </div>
                <div class="summary-label">Total Credits</div>
            </div>
            <div class="summary-item">
                <div class="summary-value text-secondary">{{ $startDate->diffInDays($endDate) + 1 }} days</div>
                <div class="summary-label">Period Duration</div>
            </div>
        </div>
    </div>

    <!-- General Ledger Entries -->
    <div class="section-title">TRANSACTION DETAILS</div>
    
    @if($groupedEntries->count() > 0)
        @foreach($groupedEntries as $date => $entries)
        <div class="date-header">
            <i class="fas fa-calendar me-2"></i>
            {{ \Carbon\Carbon::parse($date)->format('F j, Y (l)') }}
        </div>
        
        @foreach($entries as $ledger)
        <div class="transaction-card">
            <div class="transaction-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Transaction #{{ $ledger->number }}</strong> | 
                        <span class="{{ $ledger->type === 'debit' ? 'debit-badge' : 'credit-badge' }}">
                            {{ ucfirst($ledger->type) }}
                        </span> | 
                        <strong>Total: {{ number_format($ledger->total, 2) }}</strong>
                    </div>
                    <div>
                        <small>Ref: {{ $ledger->reference ?: 'N/A' }}</small>
                    </div>
                </div>
                @if($ledger->description)
                <div style="margin-top: 5px; font-style: italic;">
                    {{ $ledger->description }}
                </div>
                @endif
            </div>
            
            @if($ledger->details->count() > 0)
            <div class="transaction-body">
                <div class="transaction-details">
                    <small><strong>Account Details:</strong></small>
                    <table class="table">
                        <thead>
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
                                    <small>{{ $detail->account->name }}</small>
                                </td>
                                <td>
                                    <span class="{{ $detail->type === 'debit' ? 'debit-badge' : 'credit-badge' }}">
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
        @endforeach
        @endforeach
    @else
        <div style="text-align: center; padding: 40px 20px;">
            <h5 style="color: #666;">No transactions found</h5>
            <p style="color: #999;">No general ledger entries were found for the selected period and criteria.</p>
        </div>
    @endif

    <div class="footer">
        <p>This report was generated by Yousaha ERP System on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>For questions about this report, please contact your system administrator.</p>
    </div>
</body>
</html>
