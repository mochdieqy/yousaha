# Yousaha ERP System Documentation

## Table of Contents

1. [Overview](#overview)
2. [Authentication & User Management](#authentication--user-management)
3. [Company Management](#company-management)
4. [Master Data Management](#master-data-management)
5. [Inventory Management](#inventory-management)
6. [Sales Management](#sales-management)
7. [Purchase Management](#purchase-management)
8. [Finance Management](#finance-management)
9. [Human Resources Management](#human-resources-management)
10. [Reporting & Analytics](#reporting--analytics)
11. [Technical Architecture](#technical-architecture)
12. [Business Rules & Validations](#business-rules--validations)

---

## Overview

Yousaha is a comprehensive ERP (Enterprise Resource Planning) system designed for small and medium enterprises. The system provides integrated modules for managing business operations including inventory, sales, purchases, finance, and human resources.

### Key Features
- Multi-company support
- Real-time inventory tracking
- Complete sales and purchase order management
- Financial accounting with general ledger
- HR management with attendance tracking
- Automated reporting and document generation
- AI-powered annual employee evaluation

---

## Authentication & User Management

### 1. User Registration (Sign Up)

**Process Flow:**
1. User accesses sign-up page
2. User provides email, password, and name
3. System validates email uniqueness
4. If email exists → Error message displayed
5. If email is new:
   - Database transaction begins
   - User record created
   - Email verification record created
   - If transaction fails → Rollback and error message
   - If successful → Verification email sent via SMTP
   - User receives verification email
   - User clicks verification link
   - System validates token and updates user status

**Business Rules:**
- Email addresses must be unique
- Password requirements enforced
- Email verification required before account activation
- Transaction rollback on any failure

### 2. User Authentication (Sign In)

**Process Flow:**
1. User provides email and password
2. System retrieves user by email
3. If email not found → Error message
4. If email found:
   - Password hash comparison
   - If password incorrect → Error message
   - If password correct:
     - Check user verification status
     - If not verified → Error message
     - If verified → Create session and redirect to home

**Security Features:**
- Password hashing for secure storage
- Session management
- Email verification requirement

### 3. Password Recovery (Forgot Password)

**Process Flow:**
1. User requests password reset
2. System validates email existence
3. If email not found → Error message
4. If email found:
   - Password reset token generated
   - Reset email sent to user
   - User clicks reset link
   - System validates token
   - User sets new password
   - Password updated in database

---

## Company Management

### Company Creation & Setup

**Process Flow:**
1. After sign-in, system checks for existing company association
2. If company exists → Redirect to home page
3. If no company but user is employee → Redirect to home page
4. If no association → Display company creation form
5. User completes company information
6. Company record created in database

**Multi-tenancy Support:**
- Each user can own one company
- Users can be employees of other companies
- Data isolation by company_id

### Company Profile Management

**Features:**
- Company information updates
- Auto-form filling for existing data
- Real-time validation and updates

---

## Master Data Management

### 1. Product Management

**Core Features:**
- Product creation, editing, and deletion
- Product categorization (goods, service, combo)
- Inventory tracking configuration
- Pricing and cost management
- Tax configuration
- Barcode support
- Shrinkage tracking

**Product Types:**
- **Goods**: Physical products with inventory tracking
- **Service**: Non-physical services
- **Combo**: Combination of goods and services

**Business Rules:**
- SKU uniqueness within company
- Inventory tracking toggle per product
- Cost and pricing flexibility
- Shrinkage percentage and limits

### 2. Customer Management

**Features:**
- Customer type classification (Individual/Company)
- Contact information management
- Sales history tracking
- Customer relationship management

**Data Fields:**
- Name, address, phone, email
- Customer type classification
- Company-specific customer lists

### 3. Supplier Management

**Features:**
- Supplier type classification (Individual/Company)
- Contact information management
- Purchase history tracking
- Supplier performance monitoring

**Integration Points:**
- Purchase order creation
- Receipt management
- Payment tracking

---

## Inventory Management

### 1. Warehouse Management

**Features:**
- Multiple warehouse support
- Warehouse location tracking
- Stock distribution across warehouses
- Warehouse-specific operations

**Core Operations:**
- Warehouse creation and configuration
- Location-based inventory management
- Multi-warehouse stock transfers

### 2. Stock Management

**Stock Types:**
- **Total Quantity**: Overall stock amount
- **Reserved Quantity**: Stock allocated to orders
- **Saleable Quantity**: Available for sale
- **Incoming Quantity**: Expected stock from purchases

**Stock Operations:**
- Stock adjustments with history tracking
- Automatic quantity calculations
- Stock detail management (batches, expiration dates)
- Low stock alerts

**Business Logic:**
- Transaction-based stock updates
- Complete audit trail via stock history
- Batch and expiration date tracking
- Cost tracking per stock detail

### 3. Receipt Management (Goods Receiving)

**Receipt Statuses:**
- **Draft**: Initial creation, editable
- **Waiting**: Awaiting approval
- **Ready**: Approved for receiving
- **Done**: Goods received
- **Cancel**: Cancelled receipt

**Process Flow:**
1. Create receipt from purchase order or manually
2. Add product lines with quantities
3. Update status to ready (reserves stock)
4. Perform goods receiving (updates actual stock)
5. Generate stock history and details

**Business Rules:**
- Only draft receipts can be deleted
- Status progression controls editing permissions
- Automatic stock updates on receiving
- Complete transaction rollback on failures

### 4. Delivery Management (Goods Issue)

**Delivery Statuses:**
- **Draft**: Initial creation
- **Waiting**: Awaiting approval
- **Ready**: Approved for delivery
- **Done**: Goods delivered
- **Cancel**: Cancelled delivery

**Process Flow:**
1. Create delivery from sales order or manually
2. Add product lines with quantities
3. Update status to ready (reserves stock)
4. Perform goods issue (reduces stock)
5. Update delivery status to done

**Integration:**
- Automatic delivery creation from sales orders
- Stock reservation and release
- Delivery address management

---

## Sales Management

### 1. Sales Order Management

**Order Statuses:**
- **Draft**: Initial creation, fully editable
- **Waiting**: Awaiting customer approval
- **Accepted**: Customer approved
- **Sent**: Order dispatched
- **Done**: Order completed
- **Cancel**: Order cancelled

**Process Flow:**
1. Create sales order with customer and product lines
2. Calculate totals and taxes
3. Progress through status workflow
4. Generate quotations and invoices
5. Create deliveries and update inventory
6. Record income and update general ledger

**Document Generation:**
- **Quotation**: Generated from draft orders
- **Invoice**: Generated from accepted/sent orders
- Automatic PDF generation and download

**Financial Integration:**
- Automatic income recording
- General ledger entries
- Account receivable management

### 2. Customer Relationship Management

**Features:**
- Customer order history
- Sales performance tracking
- Customer communication logs

---

## Purchase Management

### 1. Purchase Order Management

**Order Statuses:**
- **Draft**: Initial creation
- **Accepted**: Supplier confirmed
- **Sent**: Order sent to supplier
- **Done**: Order completed
- **Cancel**: Order cancelled

**Process Flow:**
1. Create purchase order with supplier and products
2. Calculate totals and submit to supplier
3. Track order progress through statuses
4. Create receipts for incoming goods
5. Record expenses and update general ledger

**Financial Integration:**
- Automatic expense recording
- General ledger entries
- Account payable management

**Business Rules:**
- Only draft orders allow product line modifications
- Status progression triggers financial transactions
- Automatic receipt generation

---

## Finance Management

### 1. Chart of Accounts

**Account Management:**
- Account code and name
- Account type classification
- Balance tracking
- Hierarchical account structure

**Account Types:**
- Assets
- Liabilities
- Equity
- Income
- Expenses

**Business Rules:**
- Unique account codes within company
- Balance validation
- Deletion restrictions for accounts with transactions

### 2. General Ledger

**Features:**
- Double-entry bookkeeping
- Debit and credit entries
- Transaction references
- Period-based reporting

**Entry Management:**
- Manual journal entries
- Automatic entries from business transactions
- Balance validation (debits = credits)
- Transaction history tracking

### 3. Expense Management

**Expense Tracking:**
- Expense categories
- Due date management
- Payment status tracking
- Approval workflows

**Integration:**
- Automatic expense creation from purchase orders
- General ledger integration
- Account payable management

### 4. Income Management

**Income Tracking:**
- Income categories
- Due date management
- Payment status tracking
- Customer invoicing

**Integration:**
- Automatic income creation from sales orders
- General ledger integration
- Account receivable management

### 5. Internal Transfers

**Transfer Management:**
- Inter-account transfers
- Transfer fees and charges
- Fee allocation options (to sender or receiver)
- Transfer documentation

**Business Logic:**
- Net amount calculations
- Fee distribution
- General ledger integration

### 6. Asset Management

**Asset Tracking:**
- Asset registration and numbering
- Purchase date and cost tracking
- Location management
- Depreciation tracking

**Integration:**
- General ledger integration
- Asset account management
- Purchase order integration

---

## Human Resources Management

### 1. Department Management

**Features:**
- Hierarchical department structure
- Department manager assignment
- Location tracking
- Employee assignment

**Structure:**
- Parent-child department relationships
- Manager delegation
- Department-specific permissions

### 2. Employee Management

**Employee Information:**
- Personal details and contact information
- Department and position assignment
- Manager hierarchy
- Work arrangement (WFO/WFH/WFA)
- Join date and service tracking

**Work Arrangements:**
- **WFO**: Work From Office
- **WFH**: Work From Home
- **WFA**: Work From Anywhere

### 3. Attendance Management

**Attendance Tracking:**
- Clock in/out functionality
- Daily attendance records
- Approval workflow
- Working hours calculation

**Attendance Statuses:**
- **Pending**: Awaiting approval
- **Approved**: Confirmed attendance
- **Rejected**: Invalid attendance

**Features:**
- Late arrival detection
- Working hours calculation
- Manager approval system

### 4. Time Off Management

**Leave Management:**
- Time off requests
- Approval workflow
- Leave balance tracking
- Manager notifications

**Process Flow:**
1. Employee submits time off request
2. Manager receives approval request
3. Manager approves/rejects request
4. System updates leave balances
5. Notifications sent to relevant parties

### 5. Payroll Management

**Payroll Information:**
- Bank account details
- Tax identification numbers
- Insurance information
- Salary calculations

### 6. AI-Powered Employee Evaluation

**Annual Evaluation Features:**
- Automated performance analysis
- Data-driven insights
- Historical performance tracking
- AI-generated recommendations

**Process:**
1. System analyzes employee data from previous year
2. AI generates comprehensive evaluation
3. Performance metrics calculated
4. Recommendations provided
5. Evaluation stored for future reference

---

## Reporting & Analytics

### 1. Financial Reports

**General Ledger Reports:**
- Period-based ledger exports
- Account balance summaries
- Transaction details

**Income Statement:**
- Revenue and expense analysis
- Period comparisons
- Profit/loss calculations

**Asset Reports:**
- Asset listings and valuations
- Depreciation schedules
- Asset location tracking

### 2. Export Functionality

**Supported Formats:**
- Excel spreadsheets
- PDF documents
- CSV data files

**Report Types:**
- General ledger by period
- Income statements
- Asset registers
- Custom date ranges

---

## Technical Architecture

### 1. Database Design

**Transaction Management:**
- ACID compliance
- Transaction rollback on failures
- Consistent data integrity

**Multi-tenancy:**
- Company-based data isolation
- Shared database architecture
- Row-level security

### 2. Security Features

**Authentication:**
- Email verification required
- Password hashing
- Session management

**Authorization:**
- Role-based access control
- Company data isolation
- Manager-subordinate permissions

### 3. Integration Points

**Email System:**
- SMTP integration for notifications
- Email verification
- Password reset emails

**AI Integration:**
- Large Language Model (LLM) integration
- Automated employee evaluations
- Performance analysis

---

## Business Rules & Validations

### 1. Data Integrity Rules

**User Management:**
- Unique email addresses
- Email verification mandatory
- Strong password requirements

**Company Data:**
- Company isolation enforced
- User-company associations validated
- Data access restricted by company

### 2. Financial Rules

**General Ledger:**
- Double-entry bookkeeping enforced
- Debit/credit balance validation
- Account deletion restrictions

**Transactions:**
- Transaction atomicity
- Rollback on any failure
- Audit trail maintenance

### 3. Inventory Rules

**Stock Management:**
- Negative stock prevention
- Reserved quantity validation
- Expiration date tracking

**Order Processing:**
- Status progression validation
- Quantity availability checks
- Automatic stock updates

### 4. HR Rules

**Attendance:**
- Single clock-in per day
- Manager approval required
- Working hours calculation

**Time Off:**
- Manager approval workflow
- Leave balance validation
- Future date restrictions

---

## Error Handling & User Experience

### 1. Error Management

**User-Friendly Messages:**
- Clear error descriptions
- Actionable guidance
- Consistent messaging

**System Reliability:**
- Transaction rollbacks
- Data consistency checks
- Graceful failure handling

### 2. User Interface

**Form Management:**
- Auto-fill for existing data
- Real-time validation
- Confirmation dialogs for deletions

**Navigation:**
- Intuitive menu structure
- Breadcrumb navigation
- Context-sensitive actions

---

## Conclusion

The Yousaha ERP system provides a comprehensive solution for small and medium enterprises, offering integrated modules for all major business operations. The system emphasizes data integrity, user experience, and scalability while maintaining security and compliance standards.

For technical implementation details, refer to the database schema documentation and API specifications.
