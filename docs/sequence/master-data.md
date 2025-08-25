# Master Data Management Sequence Diagrams

This document contains sequence diagrams for core business data management flows in the Yousaha ERP system.

## 📦 Product Management Flow

### Product Listing Process
**Description**: Display paginated product list with search and filtering

```sequence
title Product Listing Flow

User->Frontend: Access products page
Frontend->ProductController: GET /products
ProductController->Auth: Check company access
Auth->ProductController: Company status

alt No company access
    ProductController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ProductController->Product: Query products by company
    Product->ProductController: Product list
    ProductController->Frontend: Return product view
    Frontend->User: Display product list with pagination
end
```

**Key Features**:
- Company-based data isolation
- Pagination (15 items per page)
- Search functionality
- Type filtering (goods, service, combo)

### Product Creation Process
**Description**: Create new product with validation and business rules

```sequence
title Product Creation Flow

User->Frontend: Access create product form
Frontend->ProductController: GET /products/create
ProductController->Auth: Check company access
Auth->ProductController: Company status

alt No company access
    ProductController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ProductController->Frontend: Return create form
    Frontend->User: Display product creation form
end

User->Frontend: Submit product data
Frontend->ProductController: POST /products
ProductController->Validator: Validate product data
Validator->ProductController: Validation result

alt Validation fails
    ProductController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    ProductController->Product: Create product record
    Product->ProductController: Product created
    ProductController->Frontend: Success redirect
    Frontend->User: Redirect to product list
end
```

**Key Features**:
- Comprehensive validation rules
- Business logic validation
- Company association
- Success confirmation

### Product Editing Process
**Description**: Edit existing product with validation and access control

```sequence
title Product Edit Flow

User->Frontend: Access edit product form
Frontend->ProductController: GET /products/{id}/edit
ProductController->Auth: Check company access
Auth->ProductController: Company status

alt No company access
    ProductController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ProductController->Product: Get product by ID
    Product->ProductController: Product data
    
    alt Product not found
        ProductController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Product found
        ProductController->Product: Verify company ownership
        Product->ProductController: Ownership status
        
        alt Wrong company
            ProductController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            ProductController->Frontend: Return edit form
            Frontend->User: Display edit form with data
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Product data retrieval
- Form pre-population
- Access control

### Product Update Process
**Description**: Save product changes with validation

```sequence
title Product Update Flow

User->Frontend: Submit product updates
Frontend->ProductController: PUT /products/{id}
ProductController->Auth: Check company access
Auth->ProductController: Company status

alt No company access
    ProductController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ProductController->Product: Get product by ID
    Product->ProductController: Product data
    
    alt Product not found
        ProductController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Product found
        ProductController->Product: Verify company ownership
        Product->ProductController: Ownership status
        
        alt Wrong company
            ProductController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            ProductController->Validator: Validate update data
            Validator->ProductController: Validation result
            
            alt Validation fails
                ProductController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                ProductController->Product: Update product fields
                Product->ProductController: Changes saved
                ProductController->Frontend: Success redirect
                Frontend->User: Redirect to product list
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Data validation
- Secure update process
- Success confirmation

### Product Deletion Process
**Description**: Delete product with dependency checking

```sequence
title Product Deletion Flow

User->Frontend: Request product deletion
Frontend->ProductController: DELETE /products/{id}
ProductController->Auth: Check company access
Auth->ProductController: Company status

alt No company access
    ProductController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ProductController->Product: Get product by ID
    Product->ProductController: Product data
    
    alt Product not found
        ProductController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Product found
        ProductController->Product: Verify company ownership
        Product->ProductController: Ownership status
        
        alt Wrong company
            ProductController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            ProductController->Product: Check dependencies
            Product->ProductController: Dependency status
            
            alt Dependencies exist
                ProductController->Frontend: Return with error
                Frontend->User: Display dependency error
            else No dependencies
                ProductController->Product: Delete product
                Product->ProductController: Deletion complete
                ProductController->Frontend: Success redirect
                Frontend->User: Redirect to product list
            end
        end
    end
end
```

**Key Features**:
- Dependency checking
- Transaction safety
- Company ownership verification
- Success confirmation

## 👥 Customer Management Flow

### Customer Listing Process
**Description**: Display customer list with company isolation

```sequence
title Customer Listing Flow

User->Frontend: Access customers page
Frontend->CustomerController: GET /customers
CustomerController->Auth: Check company access
Auth->CustomerController: Company status

alt No company access
    CustomerController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    CustomerController->Customer: Query customers by company
    Customer->CustomerController: Customer list
    CustomerController->Frontend: Return customer view
    Frontend->User: Display customer list
end
```

**Key Features**:
- Company-based data isolation
- Customer data display
- Access control

### Customer Creation Process
**Description**: Create new customer with validation

```sequence
title Customer Creation Flow

User->Frontend: Access create customer form
Frontend->CustomerController: GET /customers/create
CustomerController->Auth: Check company access
Auth->CustomerController: Company status

alt No company access
    CustomerController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    CustomerController->Frontend: Return create form
    Frontend->User: Display customer creation form
end

User->Frontend: Submit customer data
Frontend->CustomerController: POST /customers
CustomerController->Validator: Validate customer data
Validator->CustomerController: Validation result

alt Validation fails
    CustomerController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    CustomerController->Customer: Create customer record
    Customer->CustomerController: Customer created
    CustomerController->Frontend: Success redirect
    Frontend->User: Redirect to customer list
end
```

**Key Features**:
- Customer data validation
- Company association
- Success confirmation

### Customer Editing Process
**Description**: Edit existing customer with access control

```sequence
title Customer Edit Flow

User->Frontend: Access edit customer form
Frontend->CustomerController: GET /customers/{id}/edit
CustomerController->Auth: Check company access
Auth->CustomerController: Company status

alt No company access
    CustomerController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    CustomerController->Customer: Get customer by ID
    Customer->CustomerController: Customer data
    
    alt Customer not found
        CustomerController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Customer found
        CustomerController->Customer: Verify company ownership
        Customer->CustomerController: Ownership status
        
        alt Wrong company
            CustomerController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            CustomerController->Frontend: Return edit form
            Frontend->User: Display edit form with data
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Customer data retrieval
- Form pre-population

### Customer Update Process
**Description**: Save customer changes with validation

```sequence
title Customer Update Flow

User->Frontend: Submit customer updates
Frontend->CustomerController: PUT /customers/{id}
CustomerController->Auth: Check company access
Auth->CustomerController: Company status

alt No company access
    CustomerController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    CustomerController->Customer: Get customer by ID
    Customer->CustomerController: Customer data
    
    alt Customer not found
        CustomerController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Customer found
        CustomerController->Customer: Verify company ownership
        Customer->CustomerController: Ownership status
        
        alt Wrong company
            CustomerController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            CustomerController->Validator: Validate update data
            Validator->CustomerController: Validation result
            
            alt Validation fails
                CustomerController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                CustomerController->Customer: Update customer fields
                Customer->CustomerController: Changes saved
                CustomerController->Frontend: Success redirect
                Frontend->User: Redirect to customer list
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Data validation
- Secure update process

## 🏭 Supplier Management Flow

### Supplier Listing Process
**Description**: Display supplier list with company isolation

```sequence
title Supplier Listing Flow

User->Frontend: Access suppliers page
Frontend->SupplierController: GET /suppliers
SupplierController->Auth: Check company access
Auth->SupplierController: Company status

alt No company access
    SupplierController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SupplierController->Supplier: Query suppliers by company
    Supplier->SupplierController: Supplier list
    SupplierController->Frontend: Return supplier view
    Frontend->User: Display supplier list
end
```

**Key Features**:
- Company-based data isolation
- Supplier data display
- Access control

### Supplier Creation Process
**Description**: Create new supplier with validation

```sequence
title Supplier Creation Flow

User->Frontend: Access create supplier form
Frontend->SupplierController: GET /suppliers/create
SupplierController->Auth: Check company access
Auth->SupplierController: Company status

alt No company access
    SupplierController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SupplierController->Frontend: Return create form
    Frontend->User: Display supplier creation form
end

User->Frontend: Submit supplier data
Frontend->SupplierController: POST /suppliers
SupplierController->Validator: Validate supplier data
Validator->SupplierController: Validation result

alt Validation fails
    SupplierController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    SupplierController->Supplier: Create supplier record
    Supplier->SupplierController: Supplier created
    SupplierController->Frontend: Success redirect
    Frontend->User: Redirect to supplier list
end
```

**Key Features**:
- Supplier data validation
- Company association
- Success confirmation

### Supplier Editing Process
**Description**: Edit existing supplier with access control

```sequence
title Supplier Edit Flow

User->Frontend: Access edit supplier form
Frontend->SupplierController: GET /suppliers/{id}/edit
SupplierController->Auth: Check company access
Auth->SupplierController: Company status

alt No company access
    SupplierController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SupplierController->Supplier: Get supplier by ID
    Supplier->SupplierController: Supplier data
    
    alt Supplier not found
        SupplierController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Supplier found
        SupplierController->Supplier: Verify company ownership
        Supplier->SupplierController: Ownership status
        
        alt Wrong company
            SupplierController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            SupplierController->Frontend: Return edit form
            Frontend->User: Display edit form with data
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Supplier data retrieval
- Form pre-population

### Supplier Update Process
**Description**: Save supplier changes with validation

```sequence
title Supplier Update Flow

User->Frontend: Submit supplier updates
Frontend->SupplierController: PUT /suppliers/{id}
SupplierController->Auth: Check company access
Auth->SupplierController: Company status

alt No company access
    SupplierController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    SupplierController->Supplier: Get supplier by ID
    Supplier->SupplierController: Supplier data
    
    alt Supplier not found
        SupplierController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Supplier found
        SupplierController->Supplier: Verify company ownership
        Supplier->SupplierController: Ownership status
        
        alt Wrong company
            SupplierController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            SupplierController->Validator: Validate update data
            Validator->SupplierController: Validation result
            
            alt Validation fails
                SupplierController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                SupplierController->Supplier: Update supplier fields
                Supplier->SupplierController: Changes saved
                SupplierController->Frontend: Success redirect
                Frontend->User: Redirect to supplier list
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Data validation
- Secure update process

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

## 🔐 Access Control

### Company-Based Isolation
- All data scoped to user's company
- Automatic company association
- Cross-company access prevention
- Permission enforcement

### Data Ownership Verification
- Company ownership checking
- Access control validation
- Secure data operations
- Error handling

## 📊 Data Validation

### Product Validation Rules
- Required fields: name, SKU, type, price
- SKU uniqueness per company
- Type validation (goods, service, combo)
- Business logic validation
- Inventory tracking rules

### Customer/Supplier Validation
- Required contact information
- Company association
- Data integrity checks
- Business rule enforcement

### Warehouse Validation
- Location information
- Company association
- Capacity management
- Operational rules

## 🔄 Business Logic

### Product Management Rules
- Service products cannot track inventory
- SKU uniqueness enforcement
- Type-based validation
- Dependency checking for deletion

### Data Relationships
- Company-based isolation
- Referential integrity
- Cascade operations
- Data consistency

## 📱 User Experience

### Form Handling
- Input validation
- Error messaging
- Success feedback
- Data persistence

### Navigation Flow
- Intuitive data access
- Clear success/error states
- Consistent messaging
- Responsive design

---

**Note**: Master data management ensures data integrity, company isolation, and proper validation across all business entities in the system.