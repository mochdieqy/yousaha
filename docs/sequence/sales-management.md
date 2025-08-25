# Sales Management Sequence Diagrams

This document contains sequence diagrams for sales order processing and document generation flows in the Yousaha ERP system.

## 📋 Sales Order Management Flow

### Sales Order Listing Process
**Description**: Display paginated sales orders with company isolation

```sequence
title Sales Order Listing Flow

User->Frontend: Access sales orders page
Frontend->SalesOrderController: GET /sales-orders
SalesOrderController->Auth: Check company access
Auth->SalesOrderController: Company status

alt No company access
    SalesOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SalesOrderController->SalesOrder: Query orders by company
    SalesOrder->SalesOrderController: Order list with relationships
    SalesOrderController->Frontend: Return sales order view
    Frontend->User: Display order list with pagination
end
```

**Key Features**:
- Company-based data isolation
- Pagination (15 items per page)
- Relationship loading (customer, warehouse, products)
- Order by creation date

### Sales Order Creation Process
**Description**: Create new sales order with product lines and validation

```sequence
title Sales Order Creation Flow

User->Frontend: Access create sales order form
Frontend->SalesOrderController: GET /sales-orders/create
SalesOrderController->Auth: Check company access
Auth->SalesOrderController: Company status

alt No company access
    SalesOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SalesOrderController->Customer: Get customers by company
    Customer->SalesOrderController: Customer list
    SalesOrderController->Product: Get products by company
    Product->SalesOrderController: Product list
    SalesOrderController->Warehouse: Get warehouses by company
    Warehouse->SalesOrderController: Warehouse list
    SalesOrderController->Frontend: Return create form with data
    Frontend->User: Display creation form with options
end

User->Frontend: Submit sales order data
Frontend->SalesOrderController: POST /sales-orders
SalesOrderController->Validator: Validate order data
Validator->SalesOrderController: Validation result

alt Validation fails
    SalesOrderController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    SalesOrderController->DB: Begin transaction
    DB->SalesOrderController: Transaction started
    
    SalesOrderController->SalesOrder: Generate order number
    SalesOrder->SalesOrderController: Order number (SO-{company}-{sequence})
    
    SalesOrderController->SalesOrder: Calculate total amount
    SalesOrder->SalesOrderController: Total calculated
    
    SalesOrderController->SalesOrder: Create sales order record
    SalesOrder->SalesOrderController: Order created
    
    SalesOrderController->SalesOrderProductLine: Create product lines
    SalesOrderProductLine->SalesOrderController: Product lines created
    
    SalesOrderController->SalesOrderStatusLog: Create status log
    SalesOrderStatusLog->SalesOrderController: Status log created
    
    SalesOrderController->DB: Commit transaction
    DB->SalesOrderController: Transaction committed
    
    SalesOrderController->Frontend: Success redirect
    Frontend->User: Redirect to order list
end
```

**Key Features**:
- Comprehensive validation rules
- Automatic order numbering
- Total calculation
- Transaction safety
- Status logging

### Sales Order Viewing Process
**Description**: Display sales order details with relationships

```sequence
title Sales Order View Flow

User->Frontend: Access sales order details
Frontend->SalesOrderController: GET /sales-orders/{id}
SalesOrderController->Auth: Check company access
Auth->SalesOrderController: Company status

alt No company access
    SalesOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SalesOrderController->SalesOrder: Get order by ID
    SalesOrder->SalesOrderController: Order data
    
    alt Order not found
        SalesOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        SalesOrderController->SalesOrder: Verify company ownership
        SalesOrder->SalesOrderController: Ownership status
        
        alt Wrong company
            SalesOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            SalesOrderController->SalesOrder: Load relationships
            SalesOrder->SalesOrderController: Relationships loaded
            SalesOrderController->Frontend: Return show view
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

### Sales Order Editing Process
**Description**: Edit sales order with status validation

```sequence
title Sales Order Edit Flow

User->Frontend: Access edit sales order form
Frontend->SalesOrderController: GET /sales-orders/{id}/edit
SalesOrderController->Auth: Check company access
Auth->SalesOrderController: Company status

alt No company access
    SalesOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SalesOrderController->SalesOrder: Get order by ID
    SalesOrder->SalesOrderController: Order data
    
    alt Order not found
        SalesOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        SalesOrderController->SalesOrder: Verify company ownership
        SalesOrder->SalesOrderController: Ownership status
        
        alt Wrong company
            SalesOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            SalesOrderController->SalesOrder: Check editability
            SalesOrder->SalesOrderController: Editability status
            
            alt Order not editable
                SalesOrderController->Frontend: Return with error
                Frontend->User: Display edit restriction message
            else Order editable
                SalesOrderController->Customer: Get customers by company
                Customer->SalesOrderController: Customer list
                SalesOrderController->Product: Get products by company
                Product->SalesOrderController: Product list
                SalesOrderController->Warehouse: Get warehouses by company
                Warehouse->SalesOrderController: Warehouse list
                SalesOrderController->SalesOrder: Load product lines
                SalesOrder->SalesOrderController: Product lines loaded
                SalesOrderController->Frontend: Return edit form with data
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

### Sales Order Update Process
**Description**: Update sales order with validation and business rules

```sequence
title Sales Order Update Flow

User->Frontend: Submit sales order updates
Frontend->SalesOrderController: PUT /sales-orders/{id}
SalesOrderController->Auth: Check company access
Auth->SalesOrderController: Company status

alt No company access
    SalesOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SalesOrderController->SalesOrder: Get order by ID
    SalesOrder->SalesOrderController: Order data
    
    alt Order not found
        SalesOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        SalesOrderController->SalesOrder: Verify company ownership
        SalesOrder->SalesOrderController: Ownership status
        
        alt Wrong company
            SalesOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            SalesOrderController->SalesOrder: Check editability
            SalesOrder->SalesOrderController: Editability status
            
            alt Order not editable
                SalesOrderController->Frontend: Return with error
                Frontend->User: Display edit restriction message
            else Order editable
                SalesOrderController->Validator: Validate update data
                Validator->SalesOrderController: Validation result
                
                alt Validation fails
                    SalesOrderController->Frontend: Return with errors
                    Frontend->User: Display error messages
                else Validation passes
                    SalesOrderController->DB: Begin transaction
                    DB->SalesOrderController: Transaction started
                    
                    SalesOrderController->SalesOrder: Update order fields
                    SalesOrder->SalesOrderController: Order updated
                    
                    SalesOrderController->SalesOrderProductLine: Update product lines
                    SalesOrderProductLine->SalesOrderController: Product lines updated
                    
                    SalesOrderController->DB: Commit transaction
                    DB->SalesOrderController: Transaction committed
                    
                    SalesOrderController->Frontend: Success redirect
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

## 🔄 Sales Order Status Management Flow

### Status Change Process
**Description**: Change sales order status with business logic validation

```sequence
title Sales Order Status Change Flow

User->Frontend: Request status change
Frontend->SalesOrderController: POST /sales-orders/{id}/change-status
SalesOrderController->Auth: Check company access
Auth->SalesOrderController: Company status

alt No company access
    SalesOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SalesOrderController->SalesOrder: Get order by ID
    SalesOrder->SalesOrderController: Order data
    
    alt Order not found
        SalesOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        SalesOrderController->SalesOrder: Verify company ownership
        SalesOrder->SalesOrderController: Ownership status
        
        alt Wrong company
            SalesOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            SalesOrderController->Validator: Validate status change
            Validator->SalesOrderController: Validation result
            
            alt Validation fails
                SalesOrderController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                SalesOrderController->DB: Begin transaction
                DB->SalesOrderController: Transaction started
                
                SalesOrderController->SalesOrder: Update order status
                SalesOrder->SalesOrderController: Status updated
                
                SalesOrderController->SalesOrderStatusLog: Create status log
                SalesOrderStatusLog->SalesOrderController: Status log created
                
                alt Status changed to 'done'
                    SalesOrderController->GeneralLedger: Create sales revenue entry
                    GeneralLedger->SalesOrderController: Revenue entry created
                    SalesOrderController->GeneralLedger: Create accounts receivable entry
                    GeneralLedger->SalesOrderController: AR entry created
                end
                
                SalesOrderController->DB: Commit transaction
                DB->SalesOrderController: Transaction committed
                
                SalesOrderController->Frontend: Success response
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
- **In Progress**: Production/fulfillment started
- **Ready**: Ready for delivery
- **Done**: Completed, triggers financial entries
- **Cancel**: Cancelled, no further changes

## 📄 Document Generation Flow

### Quotation Generation Process
**Description**: Generate PDF quotation from sales order

```sequence
title Quotation Generation Flow

User->Frontend: Request quotation generation
Frontend->SalesOrderController: GET /sales-orders/{id}/quotation
SalesOrderController->Auth: Check company access
Auth->SalesOrderController: Company status

alt No company access
    SalesOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SalesOrderController->SalesOrder: Get order by ID
    SalesOrder->SalesOrderController: Order data
    
    alt Order not found
        SalesOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        SalesOrderController->SalesOrder: Verify company ownership
        SalesOrder->SalesOrderController: Ownership status
        
        alt Wrong company
            SalesOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            SalesOrderController->SalesOrder: Load relationships
            SalesOrder->SalesOrderController: Relationships loaded
            SalesOrderController->PDF: Generate quotation PDF
            PDF->SalesOrderController: PDF generated
            SalesOrderController->Frontend: Return PDF response
            Frontend->User: Download quotation PDF
        end
    end
end
```

**Key Features**:
- Company ownership verification
- PDF generation
- Professional formatting
- Download capability

### Invoice Generation Process
**Description**: Generate PDF invoice from completed sales order

```sequence
title Invoice Generation Flow

User->Frontend: Request invoice generation
Frontend->SalesOrderController: GET /sales-orders/{id}/invoice
SalesOrderController->Auth: Check company access
Auth->SalesOrderController: Company status

alt No company access
    SalesOrderController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SalesOrderController->SalesOrder: Get order by ID
    SalesOrder->SalesOrderController: Order data
    
    alt Order not found
        SalesOrderController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Order found
        SalesOrderController->SalesOrder: Verify company ownership
        SalesOrder->SalesOrderController: Ownership status
        
        alt Wrong company
            SalesOrderController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            SalesOrderController->SalesOrder: Check completion status
            SalesOrder->SalesOrderController: Completion status
            
            alt Order not completed
                SalesOrderController->Frontend: Return with error
                Frontend->User: Display completion required message
            else Order completed
                SalesOrderController->SalesOrder: Load relationships
                SalesOrder->SalesOrderController: Relationships loaded
                SalesOrderController->PDF: Generate invoice PDF
                PDF->SalesOrderController: PDF generated
                SalesOrderController->Frontend: Return PDF response
                Frontend->User: Download invoice PDF
            end
        end
    end
end
```

**Key Features**:
- Completion status validation
- PDF generation
- Professional formatting
- Download capability

## 🔐 Access Control

### Company-Based Isolation
- All sales orders scoped to user's company
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
- Company-specific prefixes
- Unique identification
- Audit trail support

### Total Calculation
- Product price × quantity
- Tax calculations
- Discount handling
- Currency support

### Financial Integration
- Automatic journal entries
- Revenue recognition
- Accounts receivable
- Double-entry bookkeeping

## 🔄 Data Relationships

### Order Components
- Sales order header
- Product line items
- Status history
- Customer information
- Warehouse assignment

### Integration Points
- Customer management
- Product catalog
- Warehouse operations
- Financial system
- Inventory tracking

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

---

**Note**: Sales management provides comprehensive order processing with status-based workflows, automatic financial integration, and professional document generation capabilities.