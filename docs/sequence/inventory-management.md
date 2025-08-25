# Inventory Management Sequence Diagrams

This document contains sequence diagrams for complete inventory and warehouse operations in the Yousaha ERP system.

## 🏗️ Warehouse Management Flow

### Warehouse Listing Process
**Description**: Display warehouse list with company isolation

```sequence
title Warehouse Listing Flow

User->Frontend: Access warehouses page
Frontend->WarehouseController: GET /warehouses
WarehouseController->Auth: Check company access
Auth->WarehouseController: Company status

alt No company access
    WarehouseController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    WarehouseController->Warehouse: Query warehouses by company
    Warehouse->WarehouseController: Warehouse list
    WarehouseController->Frontend: Return warehouse view
    Frontend->User: Display warehouse list
end
```

**Key Features**:
- Company-based data isolation
- Warehouse data display
- Access control

### Warehouse Creation Process
**Description**: Create new warehouse with validation

```sequence
title Warehouse Creation Flow

User->Frontend: Access create warehouse form
Frontend->WarehouseController: GET /warehouses/create
WarehouseController->Auth: Check company access
Auth->WarehouseController: Company status

alt No company access
    WarehouseController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    WarehouseController->Frontend: Return create form
    Frontend->User: Display warehouse creation form
end

User->Frontend: Submit warehouse data
Frontend->WarehouseController: POST /warehouses
WarehouseController->Validator: Validate warehouse data
Validator->WarehouseController: Validation result

alt Validation fails
    WarehouseController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    WarehouseController->Warehouse: Create warehouse record
    Warehouse->WarehouseController: Warehouse created
    WarehouseController->Frontend: Success redirect
    Frontend->User: Redirect to warehouse list
end
```

**Key Features**:
- Warehouse data validation
- Company association
- Success confirmation

### Warehouse Editing Process
**Description**: Edit existing warehouse with access control

```sequence
title Warehouse Edit Flow

User->Frontend: Access edit warehouse form
Frontend->WarehouseController: GET /warehouses/{id}/edit
WarehouseController->Auth: Check company access
Auth->WarehouseController: Company status

alt No company access
    WarehouseController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    WarehouseController->Warehouse: Get warehouse by ID
    Warehouse->WarehouseController: Warehouse data
    
    alt Warehouse not found
        WarehouseController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Warehouse found
        WarehouseController->Warehouse: Verify company ownership
        Warehouse->WarehouseController: Ownership status
        
        alt Wrong company
            WarehouseController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            WarehouseController->Frontend: Return edit form
            Frontend->User: Display edit form with data
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Warehouse data retrieval
- Form pre-population

### Warehouse Update Process
**Description**: Save warehouse changes with validation

```sequence
title Warehouse Update Flow

User->Frontend: Submit warehouse updates
Frontend->WarehouseController: PUT /warehouses/{id}
WarehouseController->Auth: Check company access
Auth->WarehouseController: Company status

alt No company access
    WarehouseController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    WarehouseController->Warehouse: Get warehouse by ID
    Warehouse->WarehouseController: Warehouse data
    
    alt Warehouse not found
        WarehouseController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Warehouse found
        WarehouseController->Warehouse: Verify company ownership
        Warehouse->WarehouseController: Ownership status
        
        alt Wrong company
            WarehouseController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            WarehouseController->Validator: Validate update data
            Validator->WarehouseController: Validation result
            
            alt Validation fails
                WarehouseController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                WarehouseController->Warehouse: Update warehouse fields
                Warehouse->WarehouseController: Changes saved
                WarehouseController->Frontend: Success redirect
                Frontend->User: Redirect to warehouse list
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Data validation
- Secure update process

## 📦 Stock Management Flow

### Stock Listing Process
**Description**: Display stock list with search and filtering

```sequence
title Stock Listing Flow

User->Frontend: Access stock page
Frontend->StockController: GET /stock
StockController->Auth: Check company access
Auth->StockController: Company status

alt No company access
    StockController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    StockController->Stock: Query stock by company
    Stock->StockController: Stock list with relationships
    StockController->Warehouse: Get warehouse options
    Warehouse->StockController: Warehouse list
    StockController->Product: Get product options
    Product->StockController: Product list
    StockController->Frontend: Return stock view with filters
    Frontend->User: Display stock list with search and filters
end
```

**Key Features**:
- Company-based data isolation
- Advanced search functionality
- Warehouse and product filtering
- Stock status filtering (low, out, normal)

### Stock Creation Process
**Description**: Create new stock record with validation

```sequence
title Stock Creation Flow

User->Frontend: Access create stock form
Frontend->StockController: GET /stock/create
StockController->Auth: Check company access
Auth->StockController: Company status

alt No company access
    StockController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    StockController->Product: Get products by company
    Product->StockController: Product list
    StockController->Warehouse: Get warehouses by company
    Warehouse->StockController: Warehouse list
    StockController->Frontend: Return create form with options
    Frontend->User: Display stock creation form
end

User->Frontend: Submit stock data
Frontend->StockController: POST /stock
StockController->Validator: Validate stock data
Validator->StockController: Validation result

alt Validation fails
    StockController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    StockController->Stock: Create stock record
    Stock->StockController: Stock created
    StockController->StockDetail: Create stock detail records
    StockDetail->StockController: Details created
    StockController->StockHistory: Create history record
    StockHistory->StockController: History created
    StockController->Frontend: Success redirect
    Frontend->User: Redirect to stock list
end
```

**Key Features**:
- Stock data validation
- Stock detail creation
- History tracking
- Success confirmation

### Stock Adjustment Process
**Description**: Adjust stock quantities with history tracking

```sequence
title Stock Adjustment Flow

User->Frontend: Request stock adjustment
Frontend->StockController: POST /stock/{id}/adjust
StockController->Auth: Check company access
Auth->StockController: Company status

alt No company access
    StockController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    StockController->Stock: Get stock by ID
    Stock->StockController: Stock data
    
    alt Stock not found
        StockController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Stock found
        StockController->Stock: Verify company ownership
        Stock->StockController: Ownership status
        
        alt Wrong company
            StockController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            StockController->Validator: Validate adjustment data
            Validator->StockController: Validation result
            
            alt Validation fails
                StockController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                StockController->DB: Begin transaction
                DB->StockController: Transaction started
                
                StockController->Stock: Update stock quantities
                Stock->StockController: Quantities updated
                
                StockController->StockDetail: Create adjustment detail
                StockDetail->StockController: Detail created
                
                StockController->StockHistory: Create history record
                StockHistory->StockController: History created
                
                StockController->DB: Commit transaction
                DB->StockController: Transaction committed
                
                StockController->Frontend: Success response
                Frontend->User: Display adjustment success
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Adjustment validation
- History tracking
- Transaction safety

## 📥 Receipt Management Flow

### Receipt Listing Process
**Description**: Display receipt list with company isolation

```sequence
title Receipt Listing Flow

User->Frontend: Access receipts page
Frontend->ReceiptController: GET /receipts
ReceiptController->Auth: Check company access
Auth->ReceiptController: Company status

alt No company access
    ReceiptController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ReceiptController->Receipt: Query receipts by company
    Receipt->ReceiptController: Receipt list with relationships
    ReceiptController->Frontend: Return receipt view
    Frontend->User: Display receipt list
end
```

**Key Features**:
- Company-based data isolation
- Relationship loading
- Access control

### Receipt Creation Process
**Description**: Create goods receipt with inventory updates

```sequence
title Receipt Creation Flow

User->Frontend: Access create receipt form
Frontend->ReceiptController: GET /receipts/create
ReceiptController->Auth: Check company access
Auth->ReceiptController: Company status

alt No company access
    ReceiptController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ReceiptController->PurchaseOrder: Get completed purchase orders
    PurchaseOrder->ReceiptController: Order list
    ReceiptController->Warehouse: Get warehouses by company
    Warehouse->ReceiptController: Warehouse list
    ReceiptController->Frontend: Return create form with data
    Frontend->User: Display receipt creation form
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
    
    ReceiptController->StockDetail: Create stock details
    StockDetail->ReceiptController: Stock details created
    
    ReceiptController->StockHistory: Create history records
    StockHistory->ReceiptController: History created
    
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
- Stock detail creation
- History tracking
- Transaction safety

### Receipt Status Management
**Description**: Manage receipt status with business logic

```sequence
title Receipt Status Management Flow

User->Frontend: Request status change
Frontend->ReceiptController: POST /receipts/{id}/change-status
ReceiptController->Auth: Check company access
Auth->ReceiptController: Company status

alt No company access
    ReceiptController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ReceiptController->Receipt: Get receipt by ID
    Receipt->ReceiptController: Receipt data
    
    alt Receipt not found
        ReceiptController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Receipt found
        ReceiptController->Receipt: Verify company ownership
        Receipt->ReceiptController: Ownership status
        
        alt Wrong company
            ReceiptController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            ReceiptController->Validator: Validate status change
            Validator->ReceiptController: Validation result
            
            alt Validation fails
                ReceiptController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                ReceiptController->DB: Begin transaction
                DB->ReceiptController: Transaction started
                
                ReceiptController->Receipt: Update receipt status
                Receipt->ReceiptController: Status updated
                
                ReceiptController->ReceiptStatusLog: Create status log
                ReceiptStatusLog->ReceiptController: Status log created
                
                ReceiptController->DB: Commit transaction
                DB->ReceiptController: Transaction committed
                
                ReceiptController->Frontend: Success response
                Frontend->User: Display status change success
            end
        end
    end
end
```

**Key Features**:
- Status validation rules
- Business logic enforcement
- Transaction safety

## 📤 Delivery Management Flow

### Delivery Listing Process
**Description**: Display delivery list with company isolation

```sequence
title Delivery Listing Flow

User->Frontend: Access deliveries page
Frontend->DeliveryController: GET /deliveries
DeliveryController->Auth: Check company access
Auth->DeliveryController: Company status

alt No company access
    DeliveryController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    DeliveryController->Delivery: Query deliveries by company
    Delivery->DeliveryController: Delivery list with relationships
    DeliveryController->Frontend: Return delivery view
    Frontend->User: Display delivery list
end
```

**Key Features**:
- Company-based data isolation
- Relationship loading
- Access control

### Delivery Creation Process
**Description**: Create delivery with inventory reduction

```sequence
title Delivery Creation Flow

User->Frontend: Access create delivery form
Frontend->DeliveryController: GET /deliveries/create
DeliveryController->Auth: Check company access
Auth->DeliveryController: Company status

alt No company access
    DeliveryController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    DeliveryController->SalesOrder: Get ready sales orders
    SalesOrder->DeliveryController: Order list
    DeliveryController->Warehouse: Get warehouses by company
    Warehouse->DeliveryController: Warehouse list
    DeliveryController->Frontend: Return create form with data
    Frontend->User: Display delivery creation form
end

User->Frontend: Submit delivery data
Frontend->DeliveryController: POST /deliveries
DeliveryController->Validator: Validate delivery data
Validator->DeliveryController: Validation result

alt Validation fails
    DeliveryController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    DeliveryController->DB: Begin transaction
    DB->DeliveryController: Transaction started
    
    DeliveryController->Delivery: Create delivery record
    Delivery->DeliveryController: Delivery created
    
    DeliveryController->DeliveryProductLine: Create delivery lines
    DeliveryProductLine->DeliveryController: Delivery lines created
    
    DeliveryController->Stock: Reduce inventory
    Stock->DeliveryController: Inventory reduced
    
    DeliveryController->StockDetail: Create stock details
    StockDetail->DeliveryController: Stock details created
    
    DeliveryController->StockHistory: Create history records
    StockHistory->DeliveryController: History created
    
    DeliveryController->DeliveryStatusLog: Create status log
    DeliveryStatusLog->DeliveryController: Status log created
    
    DeliveryController->DB: Commit transaction
    DB->DeliveryController: Transaction committed
    
    DeliveryController->Frontend: Success redirect
    Frontend->User: Redirect to delivery list
end
```

**Key Features**:
- Sales order integration
- Inventory reduction
- Stock detail creation
- History tracking
- Transaction safety

### Delivery Status Management
**Description**: Manage delivery status with business logic

```sequence
title Delivery Status Management Flow

User->Frontend: Request status change
Frontend->DeliveryController: POST /deliveries/{id}/change-status
DeliveryController->Auth: Check company access
Auth->DeliveryController: Company status

alt No company access
    DeliveryController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    DeliveryController->Delivery: Get delivery by ID
    Delivery->DeliveryController: Delivery data
    
    alt Delivery not found
        DeliveryController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Delivery found
        DeliveryController->Delivery: Verify company ownership
        Delivery->DeliveryController: Ownership status
        
        alt Wrong company
            DeliveryController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            DeliveryController->Validator: Validate status change
            Validator->DeliveryController: Validation result
            
            alt Validation fails
                DeliveryController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                DeliveryController->DB: Begin transaction
                DB->DeliveryController: Transaction started
                
                DeliveryController->Delivery: Update delivery status
                Delivery->DeliveryController: Status updated
                
                DeliveryController->DeliveryStatusLog: Create status log
                DeliveryStatusLog->DeliveryController: Status log created
                
                DeliveryController->DB: Commit transaction
                DB->DeliveryController: Transaction committed
                
                DeliveryController->Frontend: Success response
                Frontend->User: Display status change success
            end
        end
    end
end
```

**Key Features**:
- Status validation rules
- Business logic enforcement
- Transaction safety

## 🔐 Access Control

### Company-Based Isolation
- All inventory data scoped to user's company
- Automatic company association
- Cross-company access prevention
- Permission enforcement

### Data Ownership Verification
- Company ownership checking
- Access control validation
- Secure data operations
- Error handling

## 📊 Business Logic

### Stock Tracking Rules
- Real-time quantity updates
- Saleable vs. reserved quantities
- Low stock alerts
- Stock status indicators

### Inventory Operations
- Receipt: Increase stock
- Delivery: Decrease stock
- Adjustment: Modify stock
- Transfer: Move between warehouses

### Stock Detail Management
- Lot/batch tracking
- Expiry date management
- Cost tracking
- Location management

## 🔄 Data Relationships

### Inventory Components
- Stock records
- Stock details
- Stock history
- Warehouse assignments
- Product associations

### Integration Points
- Purchase orders
- Sales orders
- Receipts
- Deliveries
- Financial system
- Product catalog

## 📱 User Experience

### Search and Filtering
- Product search
- Warehouse filtering
- Stock status filtering
- Advanced search options

### Stock Monitoring
- Low stock alerts
- Stock level indicators
- Status-based views
- Real-time updates

### Form Handling
- Dynamic product selection
- Quantity validation
- Stock availability checking
- Error handling

---

**Note**: Inventory management provides comprehensive stock tracking, warehouse operations, and inventory control with real-time updates, history tracking, and seamless integration with sales and purchase operations.