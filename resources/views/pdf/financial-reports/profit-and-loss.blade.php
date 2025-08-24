<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit and Loss Statement - {{ $company->name }}</title>
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
            margin: 20px 0 10px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .revenue-title {
            color: #28a745;
        }
        .expense-title {
            color: #dc3545;
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
        .revenue-total {
            background-color: #d4edda;
            color: #155724;
        }
        .expense-total {
            background-color: #f8d7da;
            color: #721c24;
        }
        .net-income-section {
            margin-top: 30px;
            border: 2px solid #333;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .net-income-grid {
            display: table;
            width: 100%;
        }
        .net-income-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }
        .net-income-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .net-income-label {
            font-size: 11px;
            color: #666;
        }
        .financial-ratios {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .ratios-grid {
            display: table;
            width: 100%;
        }
        .ratio-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
        }
        .ratio-value {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .ratio-label {
            font-size: 9px;
            color: #666;
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
            <div class="report-title">Profit and Loss Statement</div>
            <div class="report-subtitle">For the period ending {{ $endDate->format('F j, Y') }}</div>
            <div class="report-subtitle">Period: {{ $startDate->format('M j, Y') }} - {{ $endDate->format('M j, Y') }}</div>
            <div class="report-subtitle">Generated: {{ now()->format('M j, Y g:i A') }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Revenue Section -->
    <div class="section-title revenue-title">REVENUE</div>
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
                <td colspan="5" class="text-center">No revenue accounts found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row revenue-total">
                <th colspan="4">TOTAL REVENUE</th>
                <th class="text-end">{{ number_format($totalRevenue, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <!-- Expenses Section -->
    <div class="section-title expense-title">EXPENSES</div>
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
                <td colspan="5" class="text-center">No expense accounts found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row expense-total">
                <th colspan="4">TOTAL EXPENSES</th>
                <th class="text-end">{{ number_format($totalExpenses, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <!-- Net Income Section -->
    <div class="net-income-section">
        <div class="net-income-grid">
            <div class="net-income-item">
                <div class="net-income-value text-success">{{ number_format($totalRevenue, 2) }}</div>
                <div class="net-income-label">Total Revenue</div>
            </div>
            <div class="net-income-item">
                <div class="net-income-value text-danger">{{ number_format($totalExpenses, 2) }}</div>
                <div class="net-income-label">Total Expenses</div>
            </div>
            <div class="net-income-item">
                <div class="net-income-value {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $netIncome >= 0 ? '+' : '' }}{{ number_format($netIncome, 2) }}
                </div>
                <div class="net-income-label">Net Income (Loss)</div>
            </div>
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            @if($netIncome >= 0)
                <div style="color: #28a745; font-weight: bold; font-size: 14px;">
                    The company generated a <strong>profit</strong> of {{ number_format($netIncome, 2) }} during this period.
                </div>
            @else
                <div style="color: #dc3545; font-weight: bold; font-size: 14px;">
                    The company incurred a <strong>loss</strong> of {{ number_format(abs($netIncome), 2) }} during this period.
                </div>
            @endif
        </div>
    </div>

    <!-- Financial Ratios Section -->
    @if($totalRevenue > 0)
    <div class="financial-ratios">
        <div class="section-title">FINANCIAL RATIOS</div>
        <div class="ratios-grid">
            <div class="ratio-item">
                <div class="ratio-value {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format(($netIncome / $totalRevenue) * 100, 2) }}%
                </div>
                <div class="ratio-label">Profit Margin</div>
            </div>
            <div class="ratio-item">
                <div class="ratio-value text-warning">
                    {{ number_format(($totalExpenses / $totalRevenue) * 100, 2) }}%
                </div>
                <div class="ratio-label">Expense Ratio</div>
            </div>
            <div class="ratio-item">
                <div class="ratio-value text-info">
                    {{ $totalRevenue > 0 ? '+' : '' }}{{ number_format($totalRevenue, 2) }}
                </div>
                <div class="ratio-label">Revenue Growth</div>
            </div>
            <div class="ratio-item">
                <div class="ratio-value text-secondary">
                    {{ $startDate->diffInDays($endDate) + 1 }} days
                </div>
                <div class="ratio-label">Period Duration</div>
            </div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated by Yousaha ERP System on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>For questions about this report, please contact your system administrator.</p>
    </div>
</body>
</html>
