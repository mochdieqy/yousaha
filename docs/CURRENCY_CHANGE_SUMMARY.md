# Currency Change Summary: Dollar to Indonesian Rupiah (IDR)

## Overview
This document summarizes all the changes made to convert the Yousaha ERP system from using US Dollar ($) to Indonesian Rupiah (IDR) as the default currency.

## Changes Made

### 1. Visual Icons and Symbols
- **Home Page**: Replaced dollar sign icons (`fa-dollar-sign`) with more appropriate icons:
  - Finance section: `fa-money-bill-wave` instead of `fa-dollar-sign`
  - Expenses: `fa-file-invoice` instead of `fa-file-invoice-dollar`
  - Financial Reports: `fa-chart-line` instead of `fa-file-invoice-dollar`

### 2. Currency Formatting Functions
- **Sales Order Create/Edit**: Updated `formatCurrency()` function to use Indonesian locale (`id-ID`) with IDR currency
- **Delivery Create/Edit**: Enhanced existing IDR formatting to be consistent with zero decimal places
- **Purchase Order Create**: Changed default display from `$0.00` to `Rp 0`

### 3. Display Format Updates
All monetary values throughout the application now display as:
- **Format**: `Rp 1,000,000` (Indonesian format with comma as thousand separator)
- **Decimal places**: 0 (no decimal places for Rupiah)
- **Symbol**: `Rp` prefix instead of `$` suffix

### 4. Updated Views

#### Sales Orders
- **Index**: `Rp {{ number_format($salesOrder->total, 0, ',', '.') }}`
- **Show**: `Rp {{ number_format($salesOrder->total, 0, ',', '.') }}`
- **Edit**: `Rp {{ number_format($salesOrder->total, 0, ',', '.') }}`
- **Product prices**: `Rp {{ number_format($productLine->product->price, 0, ',', '.') }}`
- **Line totals**: `Rp {{ number_format($productLine->line_total, 0, ',', '.') }}`

#### Purchase Orders
- **Index**: `Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}`
- **Show**: `Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}`
- **Edit**: `Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}`
- **Product costs**: `Rp {{ number_format($line->product->cost ?? $line->product->price, 0, ',', '.') }}`
- **Line totals**: `Rp {{ number_format($line->line_total, 0, ',', '.') }}`

#### Products
- **Index**: `Rp {{ number_format($product->price, 0, ',', '.') }}`
- **Edit**: `Rp {{ number_format($product->total_price, 0, ',', '.') }}`
- **Costs**: `Rp {{ number_format($product->cost, 0, ',', '.') }}`
- **Taxes**: `Rp {{ number_format($product->taxes, 0, ',', '.') }}`

#### Finance
- **General Ledger**: `Rp {{ number_format($ledger->total, 0, ',', '.') }}`
- **Accounts**: `Rp {{ number_format($account->balance, 0, ',', '.') }}`
- **Expenses**: `Rp {{ number_format($expense->total, 0, ',', '.') }}`
- **Incomes**: `Rp {{ number_format($income->total, 0, ',', '.') }}`
- **Internal Transfers**: `Rp {{ number_format($transfer->amount, 0, ',', '.') }}`
- **Assets**: `Rp {{ number_format($asset->amount, 0, ',', '.') }}`

#### Other Modules
- **Deliveries**: `Rp {{ number_format($productLine->product->price, 0, ',', '.') }}`
- **Receipts**: `Rp {{ number_format($productLine->product->cost ?? 0, 0, ',', '.') }}`
- **Stock**: `Rp {{ number_format($detail->cost, 0, ',', '.') }}`
- **Warehouses**: Updated quantity formatting to use Indonesian number format

### 5. PDF Templates
- **Invoice**: `Rp {{ number_format($salesOrder->total, 0, ',', '.') }}`
- **Quotation**: `Rp {{ number_format($salesOrder->total, 0, ',', '.') }}`

### 6. Database Seeders
Updated all product prices in `ProductSeeder.php` to realistic Indonesian Rupiah values:

#### Goods Products
- Dell Laptop XPS 13: Rp 25,000,000 (was $1,299.99)
- iPhone 15 Pro: Rp 18,000,000 (was $999.99)
- Office Chair Premium: Rp 2,500,000 (was $299.99)
- Wireless Mouse Logitech: Rp 450,000 (was $49.99)
- Coffee Beans Premium: Rp 125,000 (was $24.99)
- Notebook A4 Spiral: Rp 25,000 (was $5.99)
- USB Cable Type-C: Rp 150,000 (was $19.99)
- Desk Lamp LED: Rp 750,000 (was $79.99)
- Printer Paper A4: Rp 65,000 (was $12.99)
- Monitor 24 inch 4K: Rp 4,500,000 (was $399.99)
- Mechanical Keyboard: Rp 1,500,000 (was $149.99)
- Headphones Wireless: Rp 2,000,000 (was $199.99)
- Water Bottle Steel: Rp 300,000 (was $29.99)
- Backpack Laptop: Rp 900,000 (was $89.99)
- Power Bank 20000mAh: Rp 600,000 (was $59.99)

#### Service Products
- Web Development Service: Rp 25,000,000 (was $2,500.00)
- Digital Marketing Consultation: Rp 5,000,000 (was $500.00)
- IT Support Monthly: Rp 3,000,000 (was $300.00)
- Graphic Design Service: Rp 1,500,000 (was $150.00)
- Data Analytics Consultation: Rp 8,000,000 (was $800.00)
- Cloud Migration Service: Rp 15,000,000 (was $1,500.00)
- SEO Optimization Service: Rp 4,000,000 (was $400.00)
- Training Session - 1 Day: Rp 2,500,000 (was $250.00)

#### Combo Products
- Laptop + Software Bundle: Rp 30,000,000 (was $1,599.99)
- Office Setup Package: Rp 9,000,000 (was $899.99)
- Website + Hosting Package: Rp 12,000,000 (was $1,200.00)
- Smart Home Starter Kit: Rp 8,000,000 (was $799.99)
- Marketing Campaign Package: Rp 20,000,000 (was $2,000.00)

### 7. Factory Updates
- **ProductFactory**: Updated price ranges from $10-$1,000 to Rp 10,000-Rp 50,000,000
- **Cost ranges**: Updated from $5-$500 to Rp 5,000-Rp 25,000,000
- **Tax ranges**: Updated from $0-$50 to Rp 0-Rp 2,500,000

### 8. New Helper Class
Created `app/Helpers/CurrencyHelper.php` with methods:
- `formatRupiah($amount, $decimals = 0)`: Formats with "Rp " prefix
- `formatRupiahOnly($amount, $decimals = 0)`: Formats without prefix
- `getJavaScriptFormatFunction()`: Returns JavaScript formatting function
- `parseCurrency($currencyString)`: Parses currency string to float

### 9. Blade Directives
Added custom Blade directives in `AppServiceProvider`:
- `@currency($amount)`: Formats amount as Rupiah with symbol
- `@currencyOnly($amount)`: Formats amount as Rupiah without symbol

### 10. Configuration Updates
- **Locale**: Already set to `'id'` (Indonesian)
- **Faker locale**: Updated from `'en_US'` to `'id_ID'`

## JavaScript Currency Formatting
All JavaScript `formatCurrency` functions now use:
```javascript
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}
```

## Benefits of Changes
1. **Localization**: Proper Indonesian currency formatting
2. **Consistency**: All monetary values use the same format
3. **Realism**: Product prices reflect actual Indonesian market values
4. **User Experience**: Indonesian users see familiar currency format
5. **Maintainability**: Centralized currency formatting through helper class

## Files Modified
- 25+ Blade template files
- 2 Database seeders
- 1 Factory file
- 1 Service provider
- 1 Configuration file
- 1 README file
- 1 New helper class

## Testing Recommendations
1. Verify all monetary displays show "Rp" prefix
2. Check number formatting uses Indonesian standards (comma as thousand separator)
3. Ensure JavaScript calculations work with new currency format
4. Test PDF generation with new currency format
5. Verify database seeding creates realistic Indonesian prices

## Future Considerations
1. **Multi-currency support**: Could add support for other currencies
2. **Exchange rate integration**: Real-time exchange rates for international transactions
3. **Tax calculations**: Indonesian tax rates and calculations
4. **Regional settings**: User preference for currency display
