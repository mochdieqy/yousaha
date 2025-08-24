<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $salesOrder->number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
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
        .invoice-info {
            float: right;
            width: 35%;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }
        .company-address {
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
        }
        .invoice-date {
            margin-top: 5px;
            color: #666;
        }
        .customer-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .customer-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }
        .warehouse-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            margin-left: 4%;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .products-table th {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .products-table td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .products-table .text-right {
            text-align: right;
        }
        .products-table .text-center {
            text-align: center;
        }
        .total-section {
            text-align: right;
            margin-bottom: 30px;
        }
        .total-row {
            margin-bottom: 5px;
        }
        .total-label {
            font-weight: bold;
            color: #555;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .payment-terms {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .payment-terms-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #28a745;
        }
        .page-break {
            page-break-before: always;
        }
        .due-date {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $salesOrder->company->name ?? 'Company Name' }}</div>
            <div class="company-address">{{ $salesOrder->company->address ?? 'Company Address' }}</div>
            @if($salesOrder->company->phone)
                <div class="company-phone">Phone: {{ $salesOrder->company->phone }}</div>
            @endif
            @if($salesOrder->company->website)
                <div class="company-website">Website: {{ $salesOrder->company->website }}</div>
            @endif
        </div>
        <div class="invoice-info">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">{{ $salesOrder->number }}</div>
            <div class="invoice-date">Date: {{ $salesOrder->created_at->format('M d, Y') }}</div>
            <div class="invoice-date due-date">Due Date: {{ $salesOrder->deadline->format('M d, Y') }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="customer-section">
        <div class="section-title">BILL TO & SHIP FROM</div>
        <div class="customer-info">
            <div class="info-row">
                <span class="info-label">Customer:</span> {{ $salesOrder->customer->name }}
            </div>
            @if($salesOrder->customer->address)
                <div class="info-row">
                    <span class="info-label">Address:</span> {{ $salesOrder->customer->address }}
                </div>
            @endif
            @if($salesOrder->customer->phone)
                <div class="info-row">
                    <span class="info-label">Phone:</span> {{ $salesOrder->customer->phone }}
                </div>
            @endif
            @if($salesOrder->customer->email)
                <div class="info-row">
                    <span class="info-label">Email:</span> {{ $salesOrder->customer->email }}
                </div>
            @endif
        </div>
        <div class="warehouse-info">
            <div class="info-row">
                <span class="info-label">Warehouse:</span> {{ $salesOrder->warehouse->name }}
            </div>
            @if($salesOrder->warehouse->address)
                <div class="info-row">
                    <span class="info-label">Address:</span> {{ $salesOrder->warehouse->address }}
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">Salesperson:</span> {{ $salesOrder->salesperson }}
            </div>
        </div>
        <div class="clear"></div>
    </div>

    @if($salesOrder->activities)
    <div class="activities-section">
        <div class="section-title">DESCRIPTION</div>
        <div style="padding: 10px; background-color: #f8f9fa; border: 1px solid #ddd; margin-bottom: 20px;">
            {{ $salesOrder->activities }}
        </div>
    </div>
    @endif

    <div class="section-title">PRODUCTS & SERVICES</div>
    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 40%;">Product/Service</th>
                <th style="width: 15%; text-align: center;">SKU</th>
                <th style="width: 15%; text-align: center;">Quantity</th>
                <th style="width: 15%; text-align: right;">Unit Price</th>
                <th style="width: 15%; text-align: right;">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesOrder->productLines as $productLine)
            <tr>
                <td>
                    <strong>{{ $productLine->product->name }}</strong>
                    @if($productLine->product->description)
                        <br><small style="color: #666;">{{ $productLine->product->description }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $productLine->product->sku }}</td>
                <td class="text-center">{{ $productLine->quantity }}</td>
                                                <td class="text-right">Rp {{ number_format($productLine->product->price, 0, ',', '.') }}</td>
                                                <td class="text-right">Rp {{ number_format($productLine->line_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span class="total-label">Total Amount:</span>
                                    <span class="total-amount">Rp {{ number_format($salesOrder->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="payment-terms">
        <div class="payment-terms-title">PAYMENT TERMS</div>
        <ul style="margin: 0; padding-left: 20px;">
            <li>Payment is due within 30 days of invoice date</li>
            <li>Please include invoice number with your payment</li>
            <li>Late payments may incur additional charges</li>
            <li>For questions about this invoice, please contact us</li>
        </ul>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Please remit payment to the address above</p>
        <p>{{ $salesOrder->company->name ?? 'Company Name' }} - {{ $salesOrder->created_at->format('Y') }}</p>
    </div>
</body>
</html>
