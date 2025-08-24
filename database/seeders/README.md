# Database Seeders Documentation

This document describes the database seeders for the Yousaha ERP system and how to use them.

## Overview

The seeder system creates comprehensive demo data for testing and demonstration purposes. It generates realistic business data spanning the entire year of 2024 with proper relationships between all entities.

## Seeder Execution Order

The seeders must be run in the following order due to dependencies:

1. **RolePermissionSeeder** - Creates roles and permissions
2. **UserSeeder** - Creates demo users and company
3. **ProductSeeder** - Creates products (goods, services, combos)
4. **WarehouseSeeder** - Creates warehouses
5. **SupplierSeeder** - Creates suppliers
6. **CustomerSeeder** - Creates customers
7. **DeliverySeeder** - Creates initial delivery data
8. **FinanceAccountSeeder** - Creates chart of accounts
9. **InitialStockSeeder** - Creates initial stock levels
10. **TransactionDataSeeder** - Creates transaction data for 2024

## Running the Seeders

### Run All Seeders
```bash
php artisan db:seed
```

### Run Specific Seeder
```bash
php artisan db:seed --class=TransactionDataSeeder
```

### Reset and Seed
```bash
php artisan migrate:fresh --seed
```

## Data Generated

### Transaction Data (2024)
- **Purchase Orders**: 10-15 per month (120-180 total)
- **Receipts**: 1 per purchase order
- **Sales Orders**: 10-15 per month (120-180 total)
- **Deliveries**: 1 per sales order
- **Stock Movements**: Automatic updates based on transactions
- **General Ledger Entries**: Automatic double-entry bookkeeping
- **Expenses**: 30% chance per transaction (additional business expenses)
- **Incomes**: 20% chance per transaction (additional business income)

### Data Distribution
- **Monthly Distribution**: Evenly spread across all 12 months of 2024
- **Daily Distribution**: Random dates within each month
- **Time Distribution**: Business hours (9 AM - 5 PM) for scheduled activities
- **Quantities**: Realistic ranges based on product types and costs

## Financial Accounting

The system automatically creates proper double-entry bookkeeping entries:

### Purchase Orders (Expenses)
- **Debit**: Cost of Goods Sold (Account 5000)
- **Credit**: Accounts Payable (Account 2000)

### Sales Orders (Revenue)
- **Debit**: Accounts Receivable (Account 1100)
- **Credit**: Sales Revenue (Account 4000)

### Additional Expenses
- **Debit**: Operating Expenses (Account 5100)
- **Credit**: Cash (Account 1000)

### Additional Incomes
- **Debit**: Cash (Account 1000)
- **Credit**: Other Income (Account 4100)

## Stock Management

### Initial Stock
- Created for all inventory-tracked products
- Realistic quantities based on product cost and type
- Expiry dates for perishable products
- Batch numbers for tracking

### Stock Movements
- **Incoming**: From receipts (purchase orders)
- **Outgoing**: From deliveries (sales orders)
- **History**: Complete audit trail of all movements
- **Real-time Updates**: Automatic quantity calculations

## Data Relationships

### Purchase Order Flow
1. Purchase Order created with supplier
2. Product lines added with quantities and costs
3. Receipt created when goods received
4. Stock updated with incoming quantities
5. General ledger entries created

### Sales Order Flow
1. Sales Order created with customer
2. Product lines added with quantities and prices
3. Delivery created when goods shipped
4. Stock updated with outgoing quantities
5. General ledger entries created

## Customization

### Modifying Data Volume
Edit `TransactionDataSeeder.php`:
```php
// Change records per month
$recordsCount = rand(10, 15); // Modify these numbers
```

### Modifying Date Range
Edit `TransactionDataSeeder.php`:
```php
// Change year
for ($month = 1; $month <= 12; $month++) {
    $this->seedMonthData($company, $month, 2024, ...);
}
```

### Modifying Transaction Types
Edit the `createAdditionalTransactions` method to change:
- Expense creation probability (currently 30%)
- Income creation probability (currently 20%)
- Transaction types and amounts

## Data Validation

After running the seeders, verify:

1. **Balanced General Ledger**: All entries should have equal debits and credits
2. **Stock Consistency**: Stock quantities should match transaction history
3. **Reference Integrity**: All foreign keys should have valid references
4. **Date Consistency**: All dates should be within 2024

## Troubleshooting

### Common Issues

1. **Missing Dependencies**: Ensure all required seeders run first
2. **Memory Issues**: For large datasets, increase PHP memory limit
3. **Timeout Issues**: For large datasets, increase PHP execution time

### Reset Data
```bash
# Clear all data and start fresh
php artisan migrate:fresh --seed

# Or clear specific tables manually
php artisan tinker
>>> App\Models\PurchaseOrder::truncate();
>>> App\Models\SalesOrder::truncate();
>>> // ... etc
```

## Performance Notes

- **Large Datasets**: The seeder creates 300+ transactions, which may take several minutes
- **Memory Usage**: Monitor memory usage during seeding
- **Database Size**: Expect significant database growth with full dataset

## Business Logic

The seeders implement realistic business scenarios:

- **Seasonal Variations**: More transactions in business months
- **Product Mix**: Realistic combinations of products in orders
- **Financial Accuracy**: Proper double-entry bookkeeping
- **Inventory Tracking**: Real-time stock updates
- **Audit Trail**: Complete history of all transactions

This comprehensive seeding system provides a realistic business environment for testing, training, and demonstration purposes.
