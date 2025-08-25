# Purchase Management Sequence Diagrams

This document contains sequence diagrams for purchase order processing and management flows in the Yousaha ERP system.

## 🛒 Purchase Order Management Flow

### Purchase Order Listing Process
**Description**: Display paginated purchase orders with company isolation

```sequence
title Purchase Order Listing Flow

User->Frontend: Access purchase orders page
Frontend->PurchaseOrderController: GET /purchase-orders
PurchaseOrderController->Auth: Check company access
Auth->PurchaseOrderController: Company status

alt No company access
    PurchaseOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    PurchaseOrderController->PurchaseOrder: Query orders by company
    PurchaseOrder->PurchaseOrderController: Order list with relationships
    PurchaseOrderController->Frontend: Return purchase order view
    Frontend->User: Display order list with pagination
end
```

**Key Features**:
- Company-based data isolation
- Pagination (15 items per page)
- Relationship loading (supplier, warehouse, products)
- Order by creation date

### Purchase Order Creation Process
**Description**: Create new purchase order with product lines and validation

```sequence
title Purchase Order Creation Flow

User->Frontend: Access create purchase order form
Frontend->PurchaseOrderController: GET /purchase-orders/create
PurchaseOrderController->Auth: Check company access
Auth->PurchaseOrderController: Company status

alt No company access
    PurchaseOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    PurchaseOrderController->Supplier: Get suppliers by company
    Supplier->PurchaseOrderController: Supplier list
    PurchaseOrderController->Product: Get products by company
    Product->PurchaseOrderController: Product list
    PurchaseOrderController->Warehouse: Get warehouses by company
    Warehouse->PurchaseOrderController: Warehouse list
    PurchaseOrderController->Frontend: Return create form with data
    Frontend->User: Display creation form with options
end

User->Frontend: Submit purchase order data
Frontend->PurchaseOrderController: POST /purchase-orders
PurchaseOrderController->Validator: Validate order data
Validator->PurchaseOrderController: Validation result

alt Validation fails
    PurchaseOrderController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    PurchaseOrderController->DB: Begin transaction
    DB->PurchaseOrderController: Transaction started
    
    PurchaseOrderController->PurchaseOrder: Generate order number
    PurchaseOrder->PurchaseOrderController: Order number (PO-{company}-{sequence})
    
    PurchaseOrderController->PurchaseOrder: Calculate total amount
    PurchaseOrder->PurchaseOrderController: Total calculated
    
    PurchaseOrderController->PurchaseOrder: Create purchase order record
    PurchaseOrder->PurchaseOrderController: Order created
    
    PurchaseOrderController->PurchaseOrderProductLine: Create product lines
    PurchaseOrderProductLine->PurchaseOrderController: Product lines created
    
    PurchaseOrderController->PurchaseOrderStatusLog: Create status log
    PurchaseOrderStatusLog->PurchaseOrderController: Status log created
    
    PurchaseOrderController->DB: Commit transaction
    DB->PurchaseOrderController: Transaction committed
    
    PurchaseOrderController->Frontend: Success redirect
    Frontend->User: Redirect to order list
end
```

**Key Features**:
- Comprehensive validation rules
- Automatic order numbering
- Total calculation
- Transaction safety
- Status logging

### Purchase Order Viewing Process
**Description**: Display purchase order details with relationships

```sequence
title Purchase Order View Flow

User->Frontend: Access purchase order details
Frontend->PurchaseOrderController: GET /purchase-orders/{id}
PurchaseOrderController->Auth: Check company access
Auth->PurchaseOrderController: Company status

alt No company access
    PurchaseOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    PurchaseOrderController->PurchaseOrder: Get order by ID
    PurchaseOrder->PurchaseOrderController: Order data
    
    alt Order not found
        PurchaseOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        PurchaseOrderController->PurchaseOrder: Verify company ownership
        PurchaseOrder->PurchaseOrderController: Ownership status
        
        alt Wrong company
            PurchaseOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            PurchaseOrderController->PurchaseOrder: Load relationships
            PurchaseOrder->PurchaseOrderController: Relationships loaded
            PurchaseOrderController->Frontend: Return show view
            Frontend->User: Display order details
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Relationship loading
- Detailed order display
- Access control

### Purchase Order Editing Process
**Description**: Edit purchase order with status validation

```sequence
title Purchase Order Edit Flow

User->Frontend: Access edit purchase order form
Frontend->PurchaseOrderController: GET /purchase-orders/{id}/edit
PurchaseOrderController->Auth: Check company access
Auth->PurchaseOrderController: Company status

alt No company access
    PurchaseOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    PurchaseOrderController->PurchaseOrder: Get order by ID
    PurchaseOrder->PurchaseOrderController: Order data
    
    alt Order not found
        PurchaseOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        PurchaseOrderController->PurchaseOrder: Verify company ownership
        PurchaseOrder->PurchaseOrderController: Ownership status
        
        alt Wrong company
            PurchaseOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            PurchaseOrderController->PurchaseOrder: Check editability
            PurchaseOrder->PurchaseOrderController: Editability status
            
            alt Order not editable
                PurchaseOrderController->Frontend: Return with error
                Frontend->User: Display edit restriction message
            else Order editable
                PurchaseOrderController->Supplier: Get suppliers by company
                Supplier->PurchaseOrderController: Supplier list
                PurchaseOrderController->Product: Get products by company
                Product->PurchaseOrderController: Product list
                PurchaseOrderController->Warehouse: Get warehouses by company
                Warehouse->PurchaseOrderController: Warehouse list
                PurchaseOrderController->PurchaseOrder: Load product lines
                PurchaseOrder->PurchaseOrderController: Product lines loaded
                PurchaseOrderController->Frontend: Return edit form with data
                Frontend->User: Display edit form with current data
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Status-based editability
- Data pre-population
- Access control

### Purchase Order Update Process
**Description**: Update purchase order with validation and business rules

```sequence
title Purchase Order Update Flow

User->Frontend: Submit purchase order updates
Frontend->PurchaseOrderController: PUT /purchase-orders/{id}
PurchaseOrderController->Auth: Check company access
Auth->PurchaseOrderController: Company status

alt No company access
    PurchaseOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    PurchaseOrderController->PurchaseOrder: Get order by ID
    PurchaseOrder->PurchaseOrderController: Order data
    
    alt Order not found
        PurchaseOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        PurchaseOrderController->PurchaseOrder: Verify company ownership
        PurchaseOrder->PurchaseOrderController: Ownership status
        
        alt Wrong company
            PurchaseOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            PurchaseOrderController->PurchaseOrder: Check editability
            PurchaseOrder->PurchaseOrderController: Editability status
            
            alt Order not editable
                PurchaseOrderController->Frontend: Return with error
                Frontend->User: Display edit restriction message
            else Order editable
                PurchaseOrderController->Validator: Validate update data
                Validator->PurchaseOrderController: Validation result
                
                alt Validation fails
                    PurchaseOrderController->Frontend: Return with errors
                    Frontend->User: Display error messages
                else Validation passes
                    PurchaseOrderController->DB: Begin transaction
                    DB->PurchaseOrderController: Transaction started
                    
                    PurchaseOrderController->PurchaseOrder: Update order fields
                    PurchaseOrder->PurchaseOrderController: Order updated
                    
                    PurchaseOrderController->PurchaseOrderProductLine: Update product lines
                    PurchaseOrderProductLine->PurchaseOrderController: Product lines updated
                    
                    PurchaseOrderController->DB: Commit transaction
                    DB->PurchaseOrderController: Transaction committed
                    
                    PurchaseOrderController->Frontend: Success redirect
                    Frontend->User: Redirect to order list
                end
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Status-based editability
- Data validation
- Transaction safety

## 🔄 Purchase Order Status Management Flow

### Status Change Process
**Description**: Change purchase order status with business logic validation

```sequence
title Purchase Order Status Change Flow

User->Frontend: Request status change
Frontend->PurchaseOrderController: POST /purchase-orders/{id}/change-status
PurchaseOrderController->Auth: Check company access
Auth->PurchaseOrderController: Company status

alt No company access
    PurchaseOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    PurchaseOrderController->PurchaseOrder: Get order by ID
    PurchaseOrder->PurchaseOrderController: Order data
    
    alt Order not found
        PurchaseOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        PurchaseOrderController->PurchaseOrder: Verify company ownership
        PurchaseOrder->PurchaseOrderController: Ownership status
        
        alt Wrong company
            PurchaseOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            PurchaseOrderController->Validator: Validate status change
            Validator->PurchaseOrderController: Validation result
            
            alt Validation fails
                PurchaseOrderController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                PurchaseOrderController->DB: Begin transaction
                DB->PurchaseOrderController: Transaction started
                
                PurchaseOrderController->PurchaseOrder: Update order status
                PurchaseOrder->PurchaseOrderController: Status updated
                
                PurchaseOrderController->PurchaseOrderStatusLog: Create status log
                PurchaseOrderStatusLog->PurchaseOrderController: Status log created
                
                alt Status changed to 'done'
                    PurchaseOrderController->GeneralLedger: Create COGS entry
                    GeneralLedger->PurchaseOrderController: COGS entry created
                    PurchaseOrderController->GeneralLedger: Create accounts payable entry
                    GeneralLedger->PurchaseOrderController: AP entry created
                end
                
                PurchaseOrderController->DB: Commit transaction
                DB->PurchaseOrderController: Transaction committed
                
                PurchaseOrderController->Frontend: Success response
                Frontend->User: Display status change success
            end
        end
    end
end
```

**Key Features**:
- Status validation rules
- Business logic enforcement
- Automatic journal entries
- Transaction safety

### Status Validation Rules
- **Draft**: Initial status, fully editable
- **Confirmed**: Order confirmed, limited editing
- **In Progress**: Production/supply started
- **Ready**: Ready for receipt
- **Done**: Completed, triggers financial entries
- **Cancel**: Cancelled, no further changes

## 📦 Receipt Management Integration

### Receipt Creation from Purchase Order
**Description**: Create goods receipt from completed purchase order

```sequence
title Receipt Creation Flow

User->Frontend: Request receipt creation
Frontend->ReceiptController: GET /receipts/create
ReceiptController->Auth: Check company access
Auth->ReceiptController: Company status

alt No company access
    ReceiptController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ReceiptController->PurchaseOrder: Get completed purchase orders
    PurchaseOrder->ReceiptController: Order list
    ReceiptController->Frontend: Return receipt creation form
    Frontend->User: Display receipt form with order options
end

User->Frontend: Submit receipt data
Frontend->ReceiptController: POST /receipts
ReceiptController->Validator: Validate receipt data
Validator->ReceiptController: Validation result

alt Validation fails
    ReceiptController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    ReceiptController->DB: Begin transaction
    DB->ReceiptController: Transaction started
    
    ReceiptController->Receipt: Create receipt record
    Receipt->ReceiptController: Receipt created
    
    ReceiptController->ReceiptProductLine: Create receipt lines
    ReceiptProductLine->ReceiptController: Receipt lines created
    
    ReceiptController->Stock: Update inventory
    Stock->ReceiptController: Inventory updated
    
    ReceiptController->ReceiptStatusLog: Create status log
    ReceiptStatusLog->ReceiptController: Status log created
    
    ReceiptController->DB: Commit transaction
    DB->ReceiptController: Transaction committed
    
    ReceiptController->Frontend: Success redirect
    Frontend->User: Redirect to receipt list
end
```

**Key Features**:
- Purchase order integration
- Inventory updates
- Transaction safety
- Status logging

## 🔐 Access Control

### Company-Based Isolation
- All purchase orders scoped to user's company
- Automatic company association
- Cross-company access prevention
- Permission enforcement

### Status-Based Permissions
- Draft orders: Full editing
- Confirmed orders: Limited editing
- Completed orders: Read-only
- Cancelled orders: No changes

## 📊 Business Logic

### Order Numbering
- Automatic sequential numbering
- Company-specific prefixes (PO-{company}-{sequence})
- Unique identification
- Audit trail support

### Total Calculation
- Product cost × quantity
- Tax calculations
- Discount handling
- Currency support

### Financial Integration
- Automatic journal entries
- Cost of goods sold recognition
- Accounts payable creation
- Double-entry bookkeeping

## 🔄 Data Relationships

### Order Components
- Purchase order header
- Product line items
- Status history
- Supplier information
- Warehouse assignment

### Integration Points
- Supplier management
- Product catalog
- Warehouse operations
- Financial system
- Inventory tracking
- Receipt management

## 📱 User Experience

### Form Handling
- Dynamic product selection
- Real-time calculations
- Validation feedback
- Error handling

### Status Management
- Clear status indicators
- Status change workflow
- Business rule enforcement
- User guidance

### Receipt Integration
- Seamless order-to-receipt flow
- Inventory updates
- Status synchronization
- Data consistency

---

**Note**: Purchase management provides comprehensive order processing with status-based workflows, automatic financial integration, and seamless receipt management for complete procurement lifecycle.