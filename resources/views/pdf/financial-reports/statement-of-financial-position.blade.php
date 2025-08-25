<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement of Financial Position - {{ $company->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 10px;
            line-height: 1.3;
        }
        .header {
            border-bottom: 2px solid #28a745;
            padding-bottom: 20px;
            margin-bottom: 30px;
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
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }
        .company-address {
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .report-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0 10px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 9px;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .summary-section {
            margin-top: 30px;
            border-top: 2px solid #333;
            padding-top: 20px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-label {
            font-size: 11px;
            color: #666;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #666;
            text-align: center;
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
            <div class="report-title">Statement of Financial Position</div>
            <div class="report-subtitle">As of {{ $endDate->format('F j, Y') }}</div>
            <div class="report-subtitle">Period: {{ $startDate->format('M j, Y') }} - {{ $endDate->format('M j, Y') }}</div>
            <div class="report-subtitle">Generated: {{ now()->format('M j, Y g:i A') }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Assets Section -->
    <div class="section-title">ASSETS</div>
    <table class="table">
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
                <td colspan="5" class="text-center">No asset accounts found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="4">TOTAL ASSETS</th>
                <th class="text-end">{{ number_format($totalAssets, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <!-- Liabilities Section -->
    <div class="section-title">LIABILITIES</div>
    <table class="table">
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
                <td colspan="5" class="text-center">No liability accounts found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="4">TOTAL LIABILITIES</th>
                <th class="text-end">{{ number_format($totalLiabilities, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <!-- Equity Section -->
    <div class="section-title">EQUITY</div>
    <table class="table">
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
                <td colspan="5" class="text-center">No equity accounts found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="4">TOTAL EQUITY</th>
                <th class="text-end">{{ number_format($totalEquity, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value text-primary">{{ number_format($totalAssets, 2) }}</div>
                <div class="summary-label">Total Assets</div>
            </div>
            <div class="summary-item">
                <div class="summary-value text-warning">{{ number_format($totalLiabilities, 2) }}</div>
                <div class="summary-label">Total Liabilities</div>
            </div>
            <div class="summary-item">
                <div class="summary-value text-success">{{ number_format($totalEquity, 2) }}</div>
                <div class="summary-label">Total Equity</div>
            </div>
        </div>
        
        <!-- Net Income Calculation -->
        <div style="margin-top: 20px; border-top: 1px solid #ddd; padding-top: 20px;">
            <h4 style="text-align: center; margin-bottom: 15px;">Net Income Calculation (Included in Equity)</h4>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <div style="flex: 1; text-align: center;">
                    <div style="font-weight: bold; color: #28a745;">Total Revenue</div>
                    <div style="font-size: 14px;">{{ number_format($revenueAccounts->sum('period_balance'), 2) }}</div>
                </div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-weight: bold; color: #dc3545;">Total Expenses</div>
                    <div style="font-size: 14px;">{{ number_format($expenseAccounts->sum('period_balance'), 2) }}</div>
                </div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-weight: bold; color: #007bff;">Net Income</div>
                    <div style="font-size: 16px; color: {{ $netIncome >= 0 ? '#28a745' : '#dc3545' }};">{{ number_format($netIncome, 2) }}</div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="footer">
        <p>This report was generated by Yousaha ERP System on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>For questions about this report, please contact your system administrator.</p>
    </div>
</body>
</html>
