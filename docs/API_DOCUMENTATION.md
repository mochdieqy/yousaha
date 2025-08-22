# Yousaha ERP API Documentation

## Table of Contents

1. [Authentication Endpoints](#authentication-endpoints)
2. [Company Management](#company-management)
3. [Master Data APIs](#master-data-apis)
4. [Inventory Management APIs](#inventory-management-apis)
5. [Sales Management APIs](#sales-management-apis)
6. [Purchase Management APIs](#purchase-management-apis)
7. [Finance Management APIs](#finance-management-apis)
8. [Human Resources APIs](#human-resources-apis)
9. [Reporting APIs](#reporting-apis)
10. [Data Models](#data-models)
11. [Error Responses](#error-responses)

---

## Authentication Endpoints

### POST /register
**Description:** User registration with email verification

**Request Body:**```json
{
  "email": "user@example.com",
  "password": "securepassword",
  "name": "John Doe"
}```
**Response:**```json
{
  "success": true,
  "message": "Registration successful. Please check your email for verification.",
  "data": {
    "user_id": 1,
    "email": "user@example.com",
    "verification_sent": true
  }
}```
**Process Flow:**
1. Validate email uniqueness
2. Create user record (transaction)
3. Create email verification record
4. Send verification email via SMTP
5. Return success response

### POST /login
**Description:** User authentication

**Request Body:**```json
{
  "email": "user@example.com",
  "password": "securepassword"
}```
**Response:**```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "email": "user@example.com",
      "name": "John Doe",
      "verified": true
    },
    "session_token": "abc123...",
    "redirect_url": "/home"
  }
}```
### GET /verify-email/{token}
**Description:** Email verification

**Response:**```json
{
  "success": true,
  "message": "Email verified successfully",
  "redirect_url": "/login"
}```
### POST /forgot-password
**Description:** Password reset request

**Request Body:**```json
{
  "email": "user@example.com"
}```
### POST /reset-password
**Description:** Password reset with token

**Request Body:**```json
{
  "token": "reset_token_here",
  "password": "newpassword"
}```
---

## Company Management

### POST /company
**Description:** Create company profile

**Request Body:**```json
{
  "name": "My Company Ltd",
  "address": "123 Business Street",
  "phone": "+1234567890",
  "website": "https://mycompany.com"
}```
### PUT /company/{id}
**Description:** Update company information

**Request Body:**```json
{
  "name": "Updated Company Name",
  "address": "New Address",
  "phone": "+0987654321",
  "website": "https://newwebsite.com"
}```
---

## Master Data APIs

### Products

#### GET /products
**Description:** List all products

**Query Parameters:**
- `page` (optional): Page number
- `per_page` (optional): Items per page
- `search` (optional): Search term

**Response:**```json
{
  "success": true,
  "data": {
    "products": [
      {
        "id": 1,
        "name": "Product Name",
        "sku": "PRD001",
        "type": "goods",
        "price": 100.00,
        "cost": 80.00,
        "is_track_inventory": true,
        "is_shrink": false
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 50
    }
  }
}```
#### POST /products
**Description:** Create new product

**Request Body:**```json
{
  "name": "New Product",
  "sku": "PRD002",
  "type": "goods",
  "price": 150.00,
  "cost": 120.00,
  "taxes": 15.00,
  "barcode": "1234567890123",
  "is_track_inventory": true,
  "is_shrink": false
}```
#### PUT /products/{id}
**Description:** Update product

#### DELETE /products/{id}
**Description:** Delete product

### Customers

#### GET /customers
**Description:** List all customers

#### POST /customers
**Description:** Create new customer

**Request Body:**```json
{
  "type": "company",
  "name": "Customer Company",
  "address": "Customer Address",
  "phone": "+1234567890",
  "email": "customer@example.com"
}```
#### PUT /customers/{id}
#### DELETE /customers/{id}

### Suppliers

#### GET /suppliers
#### POST /suppliers
#### PUT /suppliers/{id}
#### DELETE /suppliers/{id}

---

## Inventory Management APIs

### Warehouses

#### GET /warehouses
**Description:** List all warehouses

#### POST /warehouses
**Request Body:**```json
{
  "code": "WH001",
  "name": "Main Warehouse",
  "address": "Warehouse Address"
}```
#### PUT /warehouses/{id}
#### DELETE /warehouses/{id}

### Stock Management

#### GET /stocks
**Description:** List all stock items

**Response:**```json
{
  "success": true,
  "data": {
    "stocks": [
      {
        "id": 1,
        "product": {
          "id": 1,
          "name": "Product Name",
          "sku": "PRD001"
        },
        "warehouse": {
          "id": 1,
          "name": "Main Warehouse"
        },
        "quantity_total": 100,
        "quantity_reserve": 20,
        "quantity_saleable": 80,
        "quantity_incoming": 50
      }
    ]
  }
}```
#### POST /stocks
**Description:** Create or update stock

**Request Body:**```json
{
  "warehouse_id": 1,
  "product_id": 1,
  "quantity_total": 100,
  "quantity_reserve": 0,
  "quantity_saleable": 100,
  "quantity_incoming": 0,
  "stock_details": [
    {
      "quantity": 50,
      "code": "BATCH001",
      "cost": 80.00,
      "expiration_date": "2024-12-31"
    }
  ]
}```
**Process:** Transaction-based with stock history creation

#### DELETE /stocks/{id}

### Receipts (Goods Receiving)

#### GET /receipts
**Description:** List all receipts

#### POST /receipts
**Description:** Create new receipt

**Request Body:**```json
{
  "receive_from": 1,
  "scheduled_at": "2024-01-15 10:00:00",
  "reference": "PO001",
  "product_lines": [
    {
      "product_id": 1,
      "quantity": 50
    }
  ]
}```
#### PUT /receipts/{id}
**Description:** Update receipt (status-dependent)

**Business Logic:**
- Draft/Waiting: Full editing allowed
- Ready/Done/Cancel: No editing allowed

#### POST /receipts/{id}/receive
**Description:** Perform goods receiving

**Request Body:**```json
{
  "received_items": [
    {
      "product_id": 1,
      "quantity_received": 45,
      "stock_details": [
        {
          "quantity": 45,
          "code": "BATCH002",
          "cost": 82.00,
          "expiration_date": "2024-11-30"
        }
      ]
    }
  ]
}```
#### DELETE /receipts/{id}
**Restriction:** Only draft receipts can be deleted

### Deliveries (Goods Issue)

#### GET /deliveries
#### POST /deliveries
#### PUT /deliveries/{id}
#### POST /deliveries/{id}/issue
#### DELETE /deliveries/{id}

---

## Sales Management APIs

### Sales Orders

#### GET /sales-orders
**Description:** List all sales orders

#### POST /sales-orders
**Description:** Create new sales order

**Request Body:**```json
{
  "customer_id": 1,
  "salesperson": "John Sales",
  "activities": "Follow up call scheduled",
  "deadline": "2024-02-15",
  "product_lines": [
    {
      "product_id": 1,
      "quantity": 10
    }
  ]
}```
#### PUT /sales-orders/{id}
**Description:** Update sales order

**Status-Based Logic:**
- Draft/Waiting: Product lines can be modified
- Accepted/Sent/Done/Cancel: Product lines locked

#### GET /sales-orders/{id}/quotation
**Description:** Generate quotation PDF

**Restriction:** Only draft orders

#### GET /sales-orders/{id}/invoice
**Description:** Generate invoice PDF

**Restriction:** Waiting/Accepted/Sent/Done/Cancel orders only

---

## Purchase Management APIs

### Purchase Orders

#### GET /purchase-orders
#### POST /purchase-orders
#### PUT /purchase-orders/{id}

**Process Flow for Status Updates:**
1. Status change triggers financial transactions
2. Expense records created automatically
3. General ledger entries generated
4. Receipt records created if applicable

---

## Finance Management APIs

### Chart of Accounts

#### GET /accounts
**Description:** List all accounts

#### POST /accounts
**Request Body:**```json
{
  "code": "1001",
  "name": "Cash",
  "type": "asset",
  "balance": 10000.00
}```
#### PUT /accounts/{id}
#### DELETE /accounts/{id}
**Restriction:** Cannot delete accounts with transactions

### General Ledger

#### GET /general-ledgers
#### POST /general-ledgers

**Request Body:**```json
{
  "number": "GL001",
  "type": "manual",
  "date": "2024-01-15",
  "note": "Manual adjustment",
  "total": 1000.00,
  "reference": "ADJ001",
  "details": [
    {
      "account_id": 1,
      "type": "debit",
      "value": 1000.00
    },
    {
      "account_id": 2,
      "type": "credit",
      "value": 1000.00
    }
  ]
}```
**Validation:** Total debits must equal total credits

#### DELETE /general-ledgers/{id}

### Expenses

#### GET /expenses
#### POST /expenses
#### PUT /expenses/{id}
#### DELETE /expenses/{id}
**Restriction:** Cannot delete expenses linked to general ledger

### Income

#### GET /incomes
#### POST /incomes
#### PUT /incomes/{id}
#### DELETE /incomes/{id}

### Internal Transfers

#### GET /internal-transfers
#### POST /internal-transfers

**Request Body:**```json
{
  "number": "TRF001",
  "date": "2024-01-15",
  "account_in": 1,
  "account_out": 2,
  "value": 5000.00,
  "fee": 25.00,
  "fee_charged_to": "out",
  "note": "Transfer for operations"
}```
#### PUT /internal-transfers/{id}
#### DELETE /internal-transfers/{id}

### Assets

#### GET /assets
#### POST /assets
#### PUT /assets/{id}
#### DELETE /assets/{id}

---

## Human Resources APIs

### Departments

#### GET /departments
#### POST /departments

**Request Body:**```json
{
  "code": "IT",
  "name": "Information Technology",
  "manager_id": 5,
  "description": "IT Department",
  "location": "Building A, Floor 3",
  "parent_id": null
}```
#### PUT /departments/{id}
#### DELETE /departments/{id}
**Restriction:** Cannot delete departments with employees

### Employees

#### GET /employees
#### POST /employees

**Request Body:**```json
{
  "user_id": 10,
  "department_id": 1,
  "number": "EMP001",
  "position": "Software Developer",
  "level": "Senior",
  "join_date": "2024-01-01",
  "manager": 5,
  "work_location": "Jakarta Office",
  "work_arrangement": "WFH"
}```
#### PUT /employees/{id}
#### DELETE /employees/{id}

### Attendance

#### GET /attendances
**Description:** List attendance records

#### POST /attendances/clock-in
**Description:** Clock in

**Response:**```json
{
  "success": true,
  "message": "Clock in successful",
  "data": {
    "attendance_id": 1,
    "clock_in": "2024-01-15 08:30:00",
    "status": "pending"
  }
}```
#### POST /attendances/clock-out
**Description:** Clock out

### Time Off

#### GET /time-offs
#### POST /time-offs

**Request Body:**```json
{
  "date": "2024-01-20",
  "reason": "Personal leave"
}```
#### GET /time-offs/approvals
**Description:** Get time-offs pending approval (for managers)

#### PUT /time-offs/{id}/approve
**Description:** Approve/reject time off

**Request Body:**```json
{
  "status": "approved"
}```
### AI Evaluation

#### POST /evaluations/generate-annual
**Description:** Generate AI-powered annual evaluation

**Request Body:**```json
{
  "employee_id": 1,
  "evaluation_year": 2023
}```
**Process:**
1. Check if evaluation already exists
2. Gather employee data from previous year
3. Send data to LLM for analysis
4. Store evaluation results

---

## Reporting APIs

### Financial Reports

#### GET /reports/general-ledger
**Query Parameters:**
- `start_date`: Start date (YYYY-MM-DD)
- `end_date`: End date (YYYY-MM-DD)
- `format`: Export format (excel, pdf, csv)

#### GET /reports/income-statement
**Query Parameters:**
- `start_date`: Start date
- `end_date`: End date
- `format`: Export format

#### GET /reports/assets
**Query Parameters:**
- `format`: Export format

---

## Data Models

### User Model```json
{
  "id": 1,
  "email": "user@example.com",
  "name": "John Doe",
  "phone": "+1234567890",
  "birthday": "1990-01-01",
  "gender": "male",
  "marital_status": "single",
  "identity_number": "1234567890",
  "address": "User Address",
  "verify_at": "2024-01-01T10:00:00Z",
  "created_at": "2024-01-01T09:00:00Z",
  "updated_at": "2024-01-01T09:00:00Z"
}```
### Product Model```json
{
  "id": 1,
  "company_id": 1,
  "name": "Product Name",
  "sku": "PRD001",
  "type": "goods",
  "is_track_inventory": true,
  "price": 100.00,
  "taxes": 10.00,
  "cost": 80.00,
  "barcode": "1234567890123",
  "reference": "REF001",
  "is_shrink": false,
  "created_at": "2024-01-01T09:00:00Z",
  "updated_at": "2024-01-01T09:00:00Z"
}```
### Sales Order Model```json
{
  "id": 1,
  "company_id": 1,
  "number": "SO001",
  "customer_id": 1,
  "salesperson": "John Sales",
  "activities": "Follow up scheduled",
  "total": 1000.00,
  "status": "draft",
  "deadline": "2024-02-15",
  "product_lines": [
    {
      "id": 1,
      "product_id": 1,
      "quantity": 10
    }
  ],
  "created_at": "2024-01-01T09:00:00Z",
  "updated_at": "2024-01-01T09:00:00Z"
}```
---

## Error Responses

### Standard Error Format```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  },
  "code": "ERROR_CODE"
}```
### Common Error Codes
- `VALIDATION_ERROR`: Input validation failed
- `NOT_FOUND`: Resource not found
- `UNAUTHORIZED`: Authentication required
- `FORBIDDEN`: Access denied
- `BUSINESS_RULE_VIOLATION`: Business logic constraint violated
- `TRANSACTION_FAILED`: Database transaction failed

### HTTP Status Codes
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Unprocessable Entity
- `500`: Internal Server Error

---

## Rate Limiting & Security

### Authentication
- Session-based authentication
- CSRF protection enabled
- Email verification required

### Rate Limiting
- API rate limiting implemented
- Different limits for authenticated/unauthenticated users

### Data Security
- Company data isolation
- Role-based access control
- Input sanitization and validation

---

## Webhooks & Events

### Available Events
- `user.registered`
- `company.created`
- `order.status_changed`
- `stock.low_inventory`
- `payment.received`

### Webhook Format```json
{
  "event": "order.status_changed",
  "data": {
    "order_id": 1,
    "old_status": "draft",
    "new_status": "accepted",
    "timestamp": "2024-01-15T10:00:00Z"
  }
}```
This API documentation provides comprehensive coverage of all system endpoints based on the sequence diagrams, including request/response formats, business logic, and error handling.
