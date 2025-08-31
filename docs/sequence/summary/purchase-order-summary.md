# Purchase Order Management - Summary Sequence Diagram

This document contains a simplified summary sequence diagram for purchase order operations in the Yousaha ERP system.

## 🛒 Purchase Order Management Flow Summary

### Complete Purchase Order Operations Flow
**Description**: Simplified overview of all purchase order management operations

```sequence
title Purchase Order Management - Complete Flow Summary

User->Frontend: Access purchase orders module
Frontend->Backend: Request purchase order data
Backend->Auth: Verify company access
Auth->Backend: Access granted

Backend->Database: Query purchase orders with relationships
Database->Backend: Return orders, suppliers, products, warehouses
Backend->Frontend: Return purchase order view
Frontend->User: Display purchase order dashboard

User->Frontend: Perform purchase order action
Frontend->Backend: Submit action request
Backend->Validator: Validate input data
Validator->Backend: Validation result

alt Validation fails
    Backend->Frontend: Return errors
    Frontend->User: Display error messages
else Validation passes
    Backend->Database: Begin transaction
    Database->Backend: Transaction started
    
    alt Create Purchase Order
        Backend->Database: Generate order number (PO-{company}-{sequence})
        Database->Backend: Order number generated
        Backend->Database: Calculate total amount
        Database->Backend: Total calculated
        Backend->Database: Create purchase order record
        Database->Backend: Order created
        Backend->Database: Create product lines
        Database->Backend: Product lines created
        Backend->Database: Create status log
        Database->Backend: Status log created
        
    else Update Purchase Order
        Backend->Database: Check order status
        Database->Backend: Status returned
        
        alt Order can be modified
            Backend->Database: Update order and product lines
            Database->Backend: Updates completed
        else Order cannot be modified
            Backend->Frontend: Return status error
            Frontend->User: Display status message
        end
        
    else Status Change
        Backend->Database: Validate status transition
        Database->Backend: Transition valid
        
        alt Status to "done"
            Backend->Database: Create financial entries
            Database->Backend: Financial entries created
            Backend->Database: Update inventory stock
            Database->Backend: Stock updated
        end
        
        Backend->Database: Create status log
        Database->Backend: Status log created
    end
    
    Backend->Database: Commit transaction
    Database->Backend: Transaction committed
    Backend->Frontend: Success response
    Frontend->User: Show success message
end
```

**Key Features**:
- **Order Management**: Complete CRUD operations for purchase orders
- **Product Lines**: Multiple products per order with quantities and prices
- **Status Workflow**: Status-based progression with validation
- **Financial Integration**: Automatic GL entries when status changes to "done"
- **Inventory Integration**: Stock updates through receipt management
- **Supplier Management**: Supplier selection and information
- **Warehouse Assignment**: Destination warehouse specification
- **Order Numbering**: Automatic sequence generation

**Business Rules**:
- All operations require company access
- Status changes follow defined workflow rules
- "Done" status triggers financial and inventory updates
- Order modifications restricted by status
- Automatic total calculations
- Product line validation

**Integration Points**:
- Supplier management system
- Product catalog
- Warehouse management
- Financial general ledger
- Inventory stock system
- Receipt management

**Critical Financial Accounts**:
- Account 5000 "Cost of Goods Sold" (Expense) - DEBIT entry
- Account 2000 "Accounts Payable" (Liability) - CREDIT entry
