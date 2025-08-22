# Yousaha ERP User Guide

## Table of Contents

1. [Getting Started](#getting-started)
2. [Account Setup](#account-setup)
3. [Company Management](#company-management)
4. [Master Data Management](#master-data-management)
5. [Inventory Operations](#inventory-operations)
6. [Sales Management](#sales-management)
7. [Purchase Management](#purchase-management)
8. [Financial Management](#financial-management)
9. [Human Resources](#human-resources)
10. [Reports and Analytics](#reports-and-analytics)
11. [Troubleshooting](#troubleshooting)

---

## Getting Started

### System Requirements
- Modern web browser (Chrome, Firefox, Safari, Edge)
- Stable internet connection
- Valid email address for account verification

### First Time Access
1. Navigate to the Yousaha ERP login page
2. Click "Sign Up" if you don't have an account
3. Complete the registration process
4. Verify your email address
5. Set up your company profile

---

## Account Setup

### Creating Your Account

1. **Registration Process**
   - Click "Sign Up" on the login page
   - Enter your email address, password, and full name
   - Click "Create Account"
   - Check your email for verification link
   - Click the verification link to activate your account

2. **Email Verification**
   - Check your inbox for verification email
   - Click "Verify Email" in the email
   - You'll be redirected to the login page
   - Your account is now active

3. **First Login**
   - Enter your email and password
   - Click "Sign In"
   - You'll be prompted to create your company profile

### Password Recovery

If you forget your password:
1. Click "Forgot Password" on the login page
2. Enter your email address
3. Check your email for reset instructions
4. Click the reset link in the email
5. Enter your new password
6. Click "Update Password"

---

## Company Management

### Setting Up Your Company

After your first login, you'll need to create your company profile:

1. **Company Creation**
   - Fill in your company name
   - Enter your business address
   - Provide contact phone number
   - Add website URL (optional)
   - Click "Create Company"

2. **Company Profile Updates**
   - Navigate to "My Company" from the main menu
   - Update any company information
   - Click "Save Changes"
   - Changes are saved immediately

### Multi-Company Access

- As a company owner: You have full access to your company data
- As an employee: You can access the company that hired you
- The system automatically determines your access level

---

## Master Data Management

### Product Management

Products are the foundation of your inventory and sales operations.

#### Creating Products

1. **Navigation**
   - Click "Master Data" from the main menu
   - Select "Products"
   - Click "Create New Product"

2. **Product Information**
   - **Product Name**: Enter descriptive product name
   - **SKU**: Unique product identifier (auto-generated if left blank)
   - **Product Type**: Choose from:
     - *Goods*: Physical products with inventory
     - *Service*: Non-physical services
     - *Combo*: Combination packages
   - **Pricing**: Set selling price and cost
   - **Inventory Tracking**: Enable if you want to track stock levels
   - **Tax Information**: Enter applicable tax amounts
   - **Barcode**: Enter or scan product barcode
   - **Shrinkage**: Enable if product is subject to wastage

3. **Saving Products**
   - Click "Save Product"
   - Product appears in your product list
   - You can edit anytime by clicking the edit icon

#### Managing Products

- **View All Products**: Master Data → Products
- **Edit Product**: Click edit icon next to product name
- **Delete Product**: Click delete icon and confirm
- **Search Products**: Use search bar to find specific products

### Customer Management

#### Adding Customers

1. **Navigation**
   - Master Data → Customers → Create New Customer

2. **Customer Details**
   - **Customer Type**: Individual or Company
   - **Name**: Customer or company name
   - **Contact Information**: Address, phone, email
   - Click "Save Customer"

3. **Managing Customers**
   - View customer list in Master Data → Customers
   - Edit customer details anytime
   - Delete customers not associated with orders

### Supplier Management

#### Adding Suppliers

1. **Navigation**
   - Master Data → Suppliers → Create New Supplier

2. **Supplier Information**
   - **Supplier Type**: Individual or Company
   - **Name**: Supplier name
   - **Contact Details**: Address, phone, email
   - Click "Save Supplier"

---

## Inventory Operations

### Warehouse Setup

Before managing inventory, set up your warehouses:

1. **Create Warehouse**
   - Inventory → Warehouses → Create New
   - Enter warehouse code (e.g., "WH001")
   - Provide warehouse name and address
   - Click "Save Warehouse"

### Stock Management

#### Adding Initial Stock

1. **Navigation**
   - Inventory → Stock → Create New Stock Entry

2. **Stock Details**
   - Select warehouse and product
   - Enter quantities:
     - *Total Quantity*: Overall stock amount
     - *Reserved*: Stock allocated to orders
     - *Saleable*: Available for immediate sale
     - *Incoming*: Expected stock from purchases
   - Add stock details (batch codes, expiration dates)
   - Click "Save Stock"

#### Stock Adjustments

1. **Making Adjustments**
   - Find stock item in Inventory → Stock
   - Click "Edit" next to the item
   - Adjust quantities as needed
   - System automatically creates stock history
   - Click "Save Changes"

2. **Stock History**
   - View complete audit trail of stock changes
   - Track who made changes and when
   - Monitor stock movements over time

### Goods Receiving (Receipts)

#### Creating Receipts

1. **Navigation**
   - Inventory → Receipts → Create New Receipt

2. **Receipt Information**
   - Select supplier (who you're receiving from)
   - Set scheduled receiving date and time
   - Add reference number (optional)
   - Add products and expected quantities
   - Click "Save Receipt"

3. **Receipt Status Workflow**
   - **Draft**: Initial creation, fully editable
   - **Waiting**: Submitted for approval
   - **Ready**: Approved, ready to receive goods
   - **Done**: Goods received and stock updated
   - **Cancel**: Receipt cancelled

#### Receiving Goods

1. **When Goods Arrive**
   - Find receipt in "Ready" status
   - Click "Receive Goods"
   - Enter actual quantities received
   - Add batch information if applicable
   - Click "Complete Receiving"

2. **Automatic Updates**
   - Stock levels automatically updated
   - Stock history created
   - Receipt status changed to "Done"

### Goods Issue (Deliveries)

#### Creating Deliveries

1. **Navigation**
   - Inventory → Deliveries → Create New Delivery

2. **Delivery Setup**
   - Enter delivery address
   - Set scheduled delivery date/time
   - Add products and quantities
   - Click "Save Delivery"

#### Processing Deliveries

1. **Delivery Workflow**
   - **Draft**: Initial creation
   - **Waiting**: Awaiting approval
   - **Ready**: Approved for delivery
   - **Done**: Goods delivered

2. **Issuing Goods**
   - Find delivery in "Ready" status
   - Click "Issue Goods"
   - Confirm quantities being delivered
   - Stock automatically reduced
   - Delivery marked as "Done"

---

## Sales Management

### Creating Sales Orders

1. **Navigation**
   - Click "Sales" from main menu
   - Click "Create New Sales Order"

2. **Order Information**
   - Select customer
   - Enter salesperson name
   - Add any special activities or notes
   - Set order deadline
   - Add products and quantities
   - System calculates totals automatically
   - Click "Save Order"

### Sales Order Workflow

#### Order Statuses
- **Draft**: Initial creation, fully editable
- **Waiting**: Sent to customer for approval
- **Accepted**: Customer confirmed the order
- **Sent**: Order dispatched to customer
- **Done**: Order completed
- **Cancel**: Order cancelled

#### Managing Orders

1. **Editing Orders**
   - Only Draft and Waiting orders can be fully edited
   - Accepted/Sent orders have limited editing
   - Use status updates to progress orders

2. **Status Updates**
   - Click "Edit" next to sales order
   - Change status as appropriate
   - System automatically:
     - Reserves stock for accepted orders
     - Creates deliveries for sent orders
     - Records income for completed orders
     - Updates financial records

### Document Generation

#### Creating Quotations
1. Find sales order in "Draft" status
2. Click "Generate Quotation"
3. System creates PDF quotation
4. Download and send to customer

#### Creating Invoices
1. Order must be in "Waiting" or later status
2. Click "Generate Invoice"
3. System creates PDF invoice
4. Download for your records or customer billing

---

## Purchase Management

### Creating Purchase Orders

1. **Navigation**
   - Click "Purchase" from main menu
   - Click "Create New Purchase Order"

2. **Order Setup**
   - Select supplier
   - Enter requestor name
   - Add activities or special instructions
   - Set order deadline
   - Add products and quantities
   - Review calculated totals
   - Click "Save Order"

### Purchase Order Workflow

#### Order Statuses
- **Draft**: Initial creation, editable
- **Accepted**: Supplier confirmed order
- **Sent**: Order sent to supplier
- **Done**: Order completed
- **Cancel**: Order cancelled

#### Order Processing
1. **Status Updates**
   - Progress orders through statuses
   - System automatically:
     - Creates receipts for accepted orders
     - Records expenses for completed orders
     - Updates financial ledgers

2. **Receiving Goods**
   - Purchase orders automatically create receipt records
   - Follow goods receiving process in Inventory section
   - Stock and financial records updated automatically

---

## Financial Management

### Chart of Accounts Setup

Before recording financial transactions, set up your chart of accounts:

1. **Navigation**
   - Finance → Accounts → Create New Account

2. **Account Information**
   - **Account Code**: Unique identifier (e.g., "1001")
   - **Account Name**: Descriptive name (e.g., "Cash")
   - **Account Type**: Asset, Liability, Equity, Income, Expense
   - **Opening Balance**: Starting balance amount
   - Click "Save Account"

### General Ledger Management

#### Creating Manual Entries

1. **Navigation**
   - Finance → General Ledger → Create New Entry

2. **Journal Entry**
   - Enter entry number and date
   - Add description/notes
   - Create debit and credit lines:
     - Select account
     - Choose debit or credit
     - Enter amount
   - Ensure debits equal credits
   - Click "Save Entry"

#### Automated Entries
The system automatically creates general ledger entries for:
- Sales order completions (income entries)
- Purchase order completions (expense entries)
- Asset purchases
- Internal transfers

### Expense Management

#### Recording Expenses

1. **Navigation**
   - Finance → Expenses → Create New Expense

2. **Expense Details**
   - Enter expense number and date
   - Set due date for payment
   - Enter total amount
   - Set payment status
   - Add notes
   - Click "Save Expense"

3. **Expense Tracking**
   - View all expenses in Finance → Expenses
   - Track payment status
   - Monitor overdue expenses
   - Link to general ledger entries

### Income Management

#### Recording Income

1. **Navigation**
   - Finance → Income → Create New Income

2. **Income Entry**
   - Enter income number and date
   - Set expected receipt date
   - Enter total amount
   - Set receipt status
   - Add notes
   - Click "Save Income"

### Internal Transfers

#### Making Transfers

1. **Navigation**
   - Finance → Internal Transfers → Create New Transfer

2. **Transfer Setup**
   - Enter transfer number and date
   - Select source account (money coming from)
   - Select destination account (money going to)
   - Enter transfer amount
   - Add any transfer fees
   - Choose who pays the fee (sender or receiver)
   - Add notes
   - Click "Save Transfer"

3. **Transfer Processing**
   - System calculates net amounts
   - Updates account balances
   - Creates general ledger entries

### Asset Management

#### Recording Assets

1. **Navigation**
   - Finance → Assets → Create New Asset

2. **Asset Information**
   - Enter asset number and name
   - Set purchase date
   - Link to asset account in chart of accounts
   - Enter quantity
   - Specify asset location
   - Add reference information
   - Click "Save Asset"

---

## Human Resources

### Department Setup

#### Creating Departments

1. **Navigation**
   - Human Resources → Departments → Create New Department

2. **Department Details**
   - Enter department code (optional)
   - Provide department name
   - Assign department manager
   - Add description
   - Set department location
   - Select parent department (for hierarchical structure)
   - Click "Save Department"

### Employee Management

#### Adding Employees

1. **Navigation**
   - Human Resources → Employees → Create New Employee

2. **Employee Information**
   - Link to existing user account
   - Assign to department
   - Enter employee number
   - Set position and level
   - Enter join date
   - Assign manager
   - Set work location
   - Choose work arrangement:
     - *WFO*: Work From Office
     - *WFH*: Work From Home
     - *WFA*: Work From Anywhere
   - Click "Save Employee"

### Attendance Management

#### Daily Attendance

1. **Clocking In**
   - Navigate to Human Resources → Attendance
   - Click "Clock In"
   - System records current time
   - Attendance status set to "Pending"

2. **Clocking Out**
   - Return to Attendance page
   - Click "Clock Out"
   - System calculates working hours
   - Attendance ready for approval

#### Attendance Approval (Managers)

1. **Review Process**
   - Managers see pending attendances
   - Review employee clock in/out times
   - Check for late arrivals or early departures
   - Approve or reject attendance records

2. **Approval Actions**
   - Click on attendance record
   - Choose "Approve" or "Reject"
   - Add comments if needed
   - Employee receives notification

### Time Off Management

#### Requesting Time Off

1. **Submitting Requests**
   - Human Resources → Time Off → Request Time Off
   - Select date for time off
   - Enter reason for absence
   - Click "Submit Request"
   - Request sent to manager for approval

#### Time Off Approval (Managers)

1. **Managing Requests**
   - Navigate to Human Resources → Time Off Approvals
   - View all pending requests from team members
   - Click on request to review details
   - Choose "Approve" or "Reject"
   - Add approval comments
   - Employee receives notification

### AI-Powered Evaluations

#### Annual Performance Evaluation

1. **Generating Evaluations**
   - Navigate to Evaluation section
   - Click "Generate Annual Evaluation"
   - Select employee and evaluation year
   - System analyzes performance data
   - AI generates comprehensive evaluation

2. **Evaluation Process**
   - System gathers attendance data
   - Analyzes productivity metrics
   - Reviews goal achievements
   - Generates performance insights
   - Creates development recommendations

---

## Reports and Analytics

### Financial Reports

#### General Ledger Reports

1. **Accessing Reports**
   - Finance → Reports → General Ledger Export
   - Select date range
   - Choose export format (Excel, PDF, CSV)
   - Click "Generate Report"
   - Download completed report

#### Income Statement

1. **Creating Income Statements**
   - Finance → Reports → Income Statement
   - Select reporting period
   - System calculates:
     - Total income for period
     - Total expenses for period
     - Net profit/loss
   - Export in preferred format

#### Asset Reports

1. **Asset Listings**
   - Finance → Reports → Asset Export
   - System generates complete asset register
   - Includes purchase dates, values, locations
   - Export for asset management

### Inventory Reports

- Stock level reports
- Low stock alerts
- Stock movement history
- Warehouse utilization

### Sales Reports

- Sales performance by period
- Customer analysis
- Product sales ranking
- Sales team performance

---

## Troubleshooting

### Common Issues

#### Login Problems

**Issue**: Cannot log in
**Solutions**:
- Verify email address spelling
- Check if account is email verified
- Use password reset if needed
- Clear browser cache and cookies

**Issue**: Account not verified
**Solutions**:
- Check spam/junk email folders
- Request new verification email
- Contact system administrator

#### Stock Issues

**Issue**: Stock quantities incorrect
**Solutions**:
- Check stock history for unauthorized changes
- Verify receipt and delivery processing
- Review stock adjustments
- Ensure proper transaction completion

**Issue**: Cannot update stock
**Solutions**:
- Check if stock is reserved for orders
- Verify warehouse access permissions
- Ensure product inventory tracking is enabled

#### Order Processing

**Issue**: Cannot change order status
**Solutions**:
- Check current order status restrictions
- Verify sufficient stock availability
- Ensure all required fields completed
- Check user permissions

**Issue**: Documents not generating
**Solutions**:
- Verify order status requirements
- Check if all product information complete
- Ensure customer/supplier details filled
- Try refreshing browser

#### Financial Records

**Issue**: General ledger not balancing
**Solutions**:
- Verify all entries have equal debits and credits
- Check for incomplete transactions
- Review automated entries from orders
- Contact system administrator for assistance

### Getting Help

#### System Support

1. **Documentation**: Refer to this user guide
2. **Help Section**: Built-in help within the application
3. **Support Contact**: Contact system administrator
4. **Training**: Request additional user training

#### Best Practices

1. **Regular Backups**: Ensure data is regularly backed up
2. **User Training**: Train all users on their specific functions
3. **Data Validation**: Regular review of key data for accuracy
4. **Security**: Use strong passwords and log out when finished
5. **Updates**: Keep system updated with latest features

---

## Tips for Efficient Use

### Daily Operations

1. **Start with Master Data**: Ensure products, customers, and suppliers are properly set up
2. **Process Orders Promptly**: Keep sales and purchase orders moving through statuses
3. **Monitor Stock Levels**: Regular review of inventory levels
4. **Update Financial Records**: Keep general ledger current
5. **Review Reports**: Regular analysis of business performance

### System Maintenance

1. **Regular Data Review**: Monthly review of key data accuracy
2. **User Access**: Regular review of user permissions
3. **Performance Monitoring**: Monitor system performance and speed
4. **Backup Verification**: Ensure backups are working properly

This user guide provides comprehensive instructions for using all features of the Yousaha ERP system. For additional help or training, contact your system administrator.
