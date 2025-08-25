# Company Management Sequence Diagrams

This document contains sequence diagrams for company setup and management flows in the Yousaha ERP system.

## 🏢 Company Setup Flow

### Company Choice Process
**Description**: User selection between creating a new company or joining existing one

```sequence
title Company Choice Flow

User->Frontend: Access company choice page
Frontend->HomeController: GET /company/choice
HomeController->Frontend: Return choice view
Frontend->User: Display company options

alt Create new company
    User->Frontend: Click create company
    Frontend->HomeController: GET /company/create
    HomeController->Frontend: Return create form
    Frontend->User: Display company creation form
else Join existing company
    User->Frontend: Click join company
    Frontend->User: Redirect to employee invitation
end
```

**Key Features**:
- Clear company setup options
- Guided user experience
- Employee invitation support

## 🆕 Company Creation Flow

### Create Company Process
**Description**: Complete company setup with automatic finance account creation

```sequence
title Company Creation Flow

User->Frontend: Fill company form
Frontend->HomeController: POST /company/store
HomeController->Validator: Validate company data
Validator->HomeController: Validation result

alt Validation fails
    HomeController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    HomeController->Company: Create company record
    Company->HomeController: Company created
    HomeController->Account: Create basic finance accounts
    Account->HomeController: Accounts created (21 accounts)
    HomeController->Role: Assign Company Owner role
    Role->HomeController: Role assigned
    HomeController->Frontend: Success redirect
    Frontend->User: Redirect to home page
end
```

**Key Features**:
- Company information validation
- Automatic finance account setup
- Role assignment
- Success confirmation

### Basic Finance Accounts Creation
**Description**: Automatic creation of 21 standard finance accounts

```sequence
title Finance Accounts Creation Flow

HomeController->Account: Create Asset accounts
Account->HomeController: Cash, AR, Inventory, etc.
HomeController->Account: Create Liability accounts
Account->HomeController: AP, Accrued Expenses, Loans
HomeController->Account: Create Equity accounts
Account->HomeController: Owner's Equity, Retained Earnings
HomeController->Account: Create Revenue accounts
Account->HomeController: Sales Revenue, Other Income
HomeController->Account: Create Expense accounts
Account->HomeController: COGS, Operating Expenses, Payroll
```

**Account Types Created**:
- **Asset Accounts**: Cash (1000), Accounts Receivable (1100), Inventory (1200), Prepaid Expenses (1300), Fixed Assets (1400), Accumulated Depreciation (1500)
- **Liability Accounts**: Accounts Payable (2000), Accrued Expenses (2100), Short-term Loans (2200), Long-term Loans (2300)
- **Equity Accounts**: Owner's Equity (3000), Retained Earnings (3100), Current Year Earnings (3200)
- **Revenue Accounts**: Sales Revenue (4000), Other Income (4100)
- **Expense Accounts**: Cost of Goods Sold (5000), Operating Expenses (5100), Payroll Expenses (5200), Marketing Expenses (5300), Administrative Expenses (5400), Depreciation Expense (5500)

## ✏️ Company Editing Flow

### Edit Company Process
**Description**: Update company profile information

```sequence
title Company Edit Flow

User->Frontend: Access edit company page
Frontend->HomeController: GET /company/edit
HomeController->Auth: Check company ownership
Auth->HomeController: Ownership status

alt Not company owner
    HomeController->Frontend: Redirect with error
    Frontend->User: Display access denied
else Company owner
    HomeController->Company: Get company data
    Company->HomeController: Company information
    HomeController->Frontend: Return edit form
    Frontend->User: Display edit form with data
end
```

**Key Features**:
- Ownership verification
- Company data retrieval
- Form pre-population

### Update Company Process
**Description**: Save company profile changes

```sequence
title Company Update Flow

User->Frontend: Submit company updates
Frontend->HomeController: PUT /company/update
HomeController->Auth: Check company ownership
Auth->HomeController: Ownership status

alt Not company owner
    HomeController->Frontend: Redirect with error
    Frontend->User: Display access denied
else Company owner
    HomeController->Validator: Validate update data
    Validator->HomeController: Validation result
    
    alt Validation fails
        HomeController->Frontend: Return with errors
        Frontend->User: Display error messages
    else Validation passes
        HomeController->Company: Update company fields
        Company->HomeController: Changes saved
        HomeController->Frontend: Success message
        Frontend->User: Display update success
    end
end
```

**Key Features**:
- Ownership verification
- Data validation
- Secure update process
- Success confirmation

## 👥 Employee Management Flow

### Employee Invitation Process
**Description**: Invite users to join company as employees

```sequence
title Employee Invitation Flow

User->Frontend: Access employee invitation
Frontend->HomeController: GET /company/employee-invitation
HomeController->Frontend: Return invitation view
Frontend->User: Display invitation form

User->Frontend: Submit invitation
Frontend->User: Process invitation logic
```

**Key Features**:
- Employee invitation system
- Role assignment support
- Company access management

## 🏠 Home Page Flow

### Home Page Access
**Description**: Company-based home page with role assignment

```sequence
title Home Page Flow

User->Frontend: Access home page
Frontend->HomeController: GET /home
HomeController->Company: Check company ownership
Company->HomeController: Company status

alt User owns company
    HomeController->Role: Assign Company Owner role
    Role->HomeController: Role assigned
    HomeController->Company: Get company data
    Company->HomeController: Company information
    HomeController->Frontend: Return home view
    Frontend->User: Display company home page
else User is employee
    HomeController->Employee: Get employee data
    Employee->HomeController: Employee information
    HomeController->Role: Assign Employee role
    Role->HomeController: Role assigned
    HomeController->Company: Get company data
    Company->HomeController: Company information
    HomeController->Frontend: Return home view
    Frontend->User: Display employee home page
else No company association
    HomeController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
end
```

**Key Features**:
- Automatic role assignment
- Company ownership detection
- Employee status handling
- Guided company setup

## 🔐 Access Control

### Company Ownership Verification
- Owner-only company editing
- Employee role assignment
- Company data isolation
- Permission-based access

### Role Management
- Automatic Company Owner role
- Employee role assignment
- Permission inheritance
- Security enforcement

## 📊 Data Management

### Company Information
- Company profile data
- Contact information
- Address details
- Website information

### Finance Account Setup
- Automatic account creation
- Standard chart of accounts
- Account numbering system
- Balance initialization

## 🔄 Business Logic

### Company Creation Rules
- Single company per owner
- Unique company identification
- Required field validation
- Automatic setup processes

### Employee Management
- User invitation system
- Role-based access control
- Company association
- Permission management

## 📱 User Experience

### Guided Setup Process
- Clear company options
- Step-by-step creation
- Automatic configuration
- Success confirmation

### Form Handling
- Input validation
- Error messaging
- Success feedback
- Data persistence

---

**Note**: Company management includes automatic finance account setup, role assignment, and comprehensive access control to ensure proper system initialization and security.