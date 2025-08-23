# Sales Order Status Change Implementation

## Overview

This document describes the implementation of the sales order status change functionality with automatic stock validation, delivery creation, and financial entry generation.

## Business Rules

### Status Change to "Accepted"
1. **Stock Validation**: System checks if sufficient saleable stock exists for all products in the sales order
2. **Success Path**: If stock is sufficient:
   - Sales order status changes to "accepted"
   - Automatic delivery is created with status "ready"
   - Stock quantities are updated:
     - `quantity_reserve` increases by required amount
     - `quantity_saleable` decreases by required amount
   - Stock history is recorded for audit trail
3. **Failure Path**: If stock is insufficient:
   - Sales order status automatically changes to "waiting"
   - User receives notification about insufficient stock
   - No delivery is created

### Status Change to "Done"
1. **Delivery Management**: 
   - Creates delivery if none exists, or updates existing delivery to "done"
   - Sets delivery status to "done"
2. **Stock Updates**:
   - Decreases `quantity_reserve` by the sold quantity
   - Decreases `quantity_total` by the sold quantity
   - Creates stock history for goods issue
3. **Financial Entries**:
   - Creates income record
   - Creates general ledger entry
   - Creates general ledger details (debit/credit entries)

## Implementation Details

### New Route
```php
POST /sales-orders/{salesOrder}/status
```
- Route: `sales-orders.update-status`
- Controller: `SalesOrderController@updateStatus`
- Middleware: `permission:sales-orders.edit`

### New Controller Methods

#### `updateStatus(Request $request, SalesOrder $salesOrder)`
- Main method for handling status changes
- Validates new status
- Calls appropriate handler methods based on status
- Creates status logs for audit trail

#### `handleAcceptedStatus(SalesOrder $salesOrder, $company)`
- Checks stock availability
- Creates delivery and updates stock if successful
- Changes status to "waiting" if stock insufficient

#### `checkStockAvailability(SalesOrder $salesOrder, $company)`
- Validates stock for all products in sales order
- Returns availability status and stock data

#### `processAcceptedSalesOrder(SalesOrder $salesOrder, $company, $stockData)`
- Creates delivery with status "ready"
- Updates stock quantities (reserve and saleable)
- Creates stock history records

#### `handleDoneStatus(SalesOrder $salesOrder, $company)`
- Manages delivery creation/update
- Updates stock quantities
- Creates financial entries (income, general ledger)

### Database Changes
No new database migrations required. Uses existing tables:
- `sales_orders` - Main sales order data
- `deliveries` - Delivery records
- `stocks` - Stock quantities
- `stock_histories` - Stock movement audit trail
- `incomes` - Income records
- `general_ledgers` - General ledger entries
- `general_ledger_details` - GL detail lines

### UI Enhancements

#### Edit View (`resources/views/pages/sales-order/edit.blade.php`)
- Added "Quick Status Change" section
- Separate form for status changes
- Status change rules explanation
- JavaScript validation and confirmation dialogs

#### Index View (`resources/views/pages/sales-order/index.blade.php`)
- Added "Quick Status Change" button for each sales order
- Bootstrap modal for status change
- Real-time status validation

## Usage Examples

### Changing Status to Accepted
1. User clicks "Quick Status Change" button
2. Selects "Accepted" from dropdown
3. System validates stock availability
4. If successful: Creates delivery, reserves stock, updates status
5. If failed: Changes status to "waiting" with explanation

### Changing Status to Done
1. User selects "Done" status
2. System creates/updates delivery
3. Updates stock quantities
4. Generates financial entries
5. Updates status to "done"

## Error Handling

### Stock Insufficiency
- Automatic status change to "waiting"
- Clear error message listing insufficient products
- No partial processing

### Validation Errors
- Form validation with proper error messages
- User-friendly error display
- Input preservation on validation failure

### Database Transaction Safety
- All operations wrapped in database transactions
- Automatic rollback on any failure
- Consistent data state

## Testing

### Unit Tests
- `tests/Unit/SalesOrderStatusChangeTest.php`
- Tests business logic directly
- Covers all status change scenarios
- Validates stock updates and financial entries

### Test Coverage
- Stock availability checking
- Accepted status processing
- Done status processing
- Insufficient stock handling
- Database integrity validation

## Security Considerations

### Permission Control
- Status changes require `sales-orders.edit` permission
- Company-level data isolation
- User authentication required

### Data Validation
- Input sanitization and validation
- SQL injection prevention
- XSS protection through proper escaping

## Performance Considerations

### Database Queries
- Efficient stock queries with proper indexing
- Batch operations for multiple products
- Optimized financial entry creation

### User Experience
- Real-time validation feedback
- Loading states during processing
- Clear success/error messages

## Future Enhancements

### Potential Improvements
1. **Email Notifications**: Send emails when status changes
2. **Workflow Integration**: Connect with approval workflows
3. **Bulk Operations**: Allow multiple sales order status changes
4. **Advanced Stock Management**: FIFO/LIFO stock allocation
5. **Integration**: Connect with external ERP systems

### Monitoring
- Status change audit logs
- Performance metrics
- Error tracking and alerting

## Conclusion

This implementation provides a robust, secure, and user-friendly way to manage sales order status changes with automatic stock validation and financial entry generation. The system ensures data consistency through database transactions and provides comprehensive audit trails for all operations.
