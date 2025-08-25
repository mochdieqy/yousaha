# Finance Management Sequence Diagrams

This document contains sequence diagrams for complete financial management flows in the Yousaha ERP system.

## 📊 Chart of Accounts Management Flow

### Account Listing Process
**Description**: Display chart of accounts with company isolation

```sequence
title Account Listing Flow

User->Frontend: Access accounts page
Frontend->AccountController: GET /accounts
AccountController->Auth: Check company access
Auth->AccountController: Company status

alt No company access
    AccountController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AccountController->Account: Query accounts by company
    Account->AccountController: Account list
    AccountController->Frontend: Return account view
    Frontend->User: Display chart of accounts
end
```

**Key Features**:
- Company-based data isolation
- Account categorization
- Balance display
- Access control

### Account Creation Process
**Description**: Create new account with validation

```sequence
title Account Creation Flow

User->Frontend: Access create account form
Frontend->AccountController: GET /accounts/create
AccountController->Auth: Check company access
Auth->AccountController: Company status

alt No company access
    AccountController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AccountController->Frontend: Return create form
    Frontend->User: Display account creation form
end

User->Frontend: Submit account data
Frontend->AccountController: POST /accounts
AccountController->Validator: Validate account data
Validator->AccountController: Validation result

alt Validation fails
    AccountController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    AccountController->Account: Create account record
    Account->AccountController: Account created
    AccountController->Frontend: Success redirect
    Frontend->User: Redirect to account list
end
```

**Key Features**:
- Account data validation
- Company association
- Success confirmation

### Account Editing Process
**Description**: Edit existing account with access control

```sequence
title Account Edit Flow

User->Frontend: Access edit account form
Frontend->AccountController: GET /accounts/{id}/edit
AccountController->Auth: Check company access
Auth->AccountController: Company status

alt No company access
    AccountController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AccountController->Account: Get account by ID
    Account->AccountController: Account data
    
    alt Account not found
        AccountController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Account found
        AccountController->Account: Verify company ownership
        Account->AccountController: Ownership status
        
        alt Wrong company
            AccountController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            AccountController->Account: Check if critical account
            Account->AccountController: Critical status
            
            alt Critical account
                AccountController->Frontend: Return with error
                Frontend->User: Display critical account message
            else Not critical account
                AccountController->Frontend: Return edit form
                Frontend->User: Display edit form with data
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Critical account protection
- Account data retrieval
- Form pre-population

### Account Update Process
**Description**: Save account changes with validation

```sequence
title Account Update Flow

User->Frontend: Submit account updates
Frontend->AccountController: PUT /accounts/{id}
AccountController->Auth: Check company access
Auth->AccountController: Company status

alt No company access
    AccountController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AccountController->Account: Get account by ID
    Account->AccountController: Account data
    
    alt Account not found
        AccountController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Account found
        AccountController->Account: Verify company ownership
        Account->AccountController: Ownership status
        
        alt Wrong company
            AccountController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            AccountController->Account: Check if critical account
            Account->AccountController: Critical status
            
            alt Critical account
                AccountController->Frontend: Return with error
                Frontend->User: Display critical account message
            else Not critical account
                AccountController->Validator: Validate update data
                Validator->AccountController: Validation result
                
                alt Validation fails
                    AccountController->Frontend: Return with errors
                    Frontend->User: Display error messages
                else Validation passes
                    AccountController->Account: Update account fields
                    Account->AccountController: Changes saved
                    AccountController->Frontend: Success redirect
                    Frontend->User: Redirect to account list
                end
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Critical account protection
- Data validation
- Secure update process

### Account Deletion Process
**Description**: Delete account with dependency checking

```sequence
title Account Deletion Flow

User->Frontend: Request account deletion
Frontend->AccountController: DELETE /accounts/{id}
AccountController->Auth: Check company access
Auth->AccountController: Company status

alt No company access
    AccountController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AccountController->Account: Get account by ID
    Account->AccountController: Account data
    
    alt Account not found
        AccountController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Account found
        AccountController->Account: Verify company ownership
        Account->AccountController: Ownership status
        
        alt Wrong company
            AccountController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            AccountController->Account: Check if critical account
            Account->AccountController: Critical status
            
            alt Critical account
                AccountController->Frontend: Return with error
                Frontend->User: Display critical account message
            else Not critical account
                AccountController->Account: Check dependencies
                Account->AccountController: Dependency status
                
                alt Dependencies exist
                    AccountController->Frontend: Return with error
                    Frontend->User: Display dependency error
                else No dependencies
                    AccountController->Account: Delete account
                    Account->AccountController: Deletion complete
                    AccountController->Frontend: Success redirect
                    Frontend->User: Redirect to account list
                end
            end
        end
    end
end
```

**Key Features**:
- Critical account protection
- Dependency checking
- Company ownership verification
- Success confirmation

## 📝 General Ledger Management Flow

### General Ledger Listing Process
**Description**: Display general ledger entries with company isolation

```sequence
title General Ledger Listing Flow

User->Frontend: Access general ledger page
Frontend->GeneralLedgerController: GET /general-ledger
GeneralLedgerController->Auth: Check company access
Auth->GeneralLedgerController: Company status

alt No company access
    GeneralLedgerController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    GeneralLedgerController->GeneralLedger: Query entries by company
    GeneralLedger->GeneralLedgerController: Entry list with details
    GeneralLedgerController->Frontend: Return general ledger view
    Frontend->User: Display general ledger with pagination
end
```

**Key Features**:
- Company-based data isolation
- Pagination (15 items per page)
- Relationship loading
- Order by date

### General Ledger Creation Process
**Description**: Create new general ledger entry with double-entry validation

```sequence
title General Ledger Creation Flow

User->Frontend: Access create general ledger form
Frontend->GeneralLedgerController: GET /general-ledger/create
GeneralLedgerController->Auth: Check company access
Auth->GeneralLedgerController: Company status

alt No company access
    GeneralLedgerController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    GeneralLedgerController->Account: Get accounts by company
    Account->GeneralLedgerController: Account list
    GeneralLedgerController->Frontend: Return create form with accounts
    Frontend->User: Display general ledger creation form
end

User->Frontend: Submit general ledger data
Frontend->GeneralLedgerController: POST /general-ledger
GeneralLedgerController->Validator: Validate entry data
Validator->GeneralLedgerController: Validation result

alt Validation fails
    GeneralLedgerController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    GeneralLedgerController->GeneralLedger: Validate double-entry balance
    GeneralLedger->GeneralLedgerController: Balance validation
    
    alt Debits != Credits
        GeneralLedgerController->Frontend: Return with error
        Frontend->User: Display balance error
    else Debits = Credits
        GeneralLedgerController->DB: Begin transaction
        DB->GeneralLedgerController: Transaction started
        
        GeneralLedgerController->GeneralLedger: Create general ledger record
        GeneralLedger->GeneralLedgerController: Entry created
        
        GeneralLedgerController->GeneralLedgerDetail: Create detail entries
        GeneralLedgerDetail->GeneralLedgerController: Details created
        
        GeneralLedgerController->Account: Update account balances
        Account->GeneralLedgerController: Balances updated
        
        GeneralLedgerController->DB: Commit transaction
        DB->GeneralLedgerController: Transaction committed
        
        GeneralLedgerController->Frontend: Success redirect
        Frontend->User: Redirect to general ledger list
    end
end
```

**Key Features**:
- Double-entry validation
- Account balance updates
- Transaction safety
- Success confirmation

### General Ledger Viewing Process
**Description**: Display general ledger entry details

```sequence
title General Ledger View Flow

User->Frontend: Access general ledger details
Frontend->GeneralLedgerController: GET /general-ledger/{id}
GeneralLedgerController->Auth: Check company access
Auth->GeneralLedgerController: Company status

alt No company access
    GeneralLedgerController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    GeneralLedgerController->GeneralLedger: Get entry by ID
    GeneralLedger->GeneralLedgerController: Entry data
    
    alt Entry not found
        GeneralLedgerController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Entry found
        GeneralLedgerController->GeneralLedger: Verify company ownership
        GeneralLedger->GeneralLedgerController: Ownership status
        
        alt Wrong company
            GeneralLedgerController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            GeneralLedgerController->GeneralLedger: Load relationships
            GeneralLedger->GeneralLedgerController: Relationships loaded
            GeneralLedgerController->Frontend: Return show view
            Frontend->User: Display entry details
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Relationship loading
- Detailed entry display
- Access control

## 💰 Expense Management Flow

### Expense Listing Process
**Description**: Display expense list with company isolation

```sequence
title Expense Listing Flow

User->Frontend: Access expenses page
Frontend->ExpenseController: GET /expenses
ExpenseController->Auth: Check company access
Auth->ExpenseController: Company status

alt No company access
    ExpenseController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ExpenseController->Expense: Query expenses by company
    Expense->ExpenseController: Expense list with relationships
    ExpenseController->Frontend: Return expense view
    Frontend->User: Display expense list
end
```

**Key Features**:
- Company-based data isolation
- Relationship loading
- Access control

### Expense Creation Process
**Description**: Create new expense with validation

```sequence
title Expense Creation Flow

User->Frontend: Access create expense form
Frontend->ExpenseController: GET /expenses/create
ExpenseController->Auth: Check company access
Auth->ExpenseController: Company status

alt No company access
    ExpenseController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    ExpenseController->Account: Get expense accounts by company
    Account->ExpenseController: Account list
    ExpenseController->Frontend: Return create form with accounts
    Frontend->User: Display expense creation form
end

User->Frontend: Submit expense data
Frontend->ExpenseController: POST /expenses
ExpenseController->Validator: Validate expense data
Validator->ExpenseController: Validation result

alt Validation fails
    ExpenseController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    ExpenseController->DB: Begin transaction
    DB->ExpenseController: Transaction started
    
    ExpenseController->Expense: Create expense record
    Expense->ExpenseController: Expense created
    
    ExpenseController->ExpenseDetail: Create expense details
    ExpenseDetail->ExpenseController: Details created
    
    ExpenseController->GeneralLedger: Create general ledger entry
    GeneralLedger->ExpenseController: GL entry created
    
    ExpenseController->GeneralLedgerDetail: Create GL details
    GeneralLedgerDetail->ExpenseController: GL details created
    
    ExpenseController->Account: Update account balances
    Account->ExpenseController: Balances updated
    
    ExpenseController->DB: Commit transaction
    DB->ExpenseController: Transaction committed
    
    ExpenseController->Frontend: Success redirect
    Frontend->User: Redirect to expense list
end
```

**Key Features**:
- Expense data validation
- General ledger integration
- Account balance updates
- Transaction safety

## 💵 Income Management Flow

### Income Listing Process
**Description**: Display income list with company isolation

```sequence
title Income Listing Flow

User->Frontend: Access incomes page
Frontend->IncomeController: GET /incomes
IncomeController->Auth: Check company access
Auth->IncomeController: Company status

alt No company access
    IncomeController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    IncomeController->Income: Query incomes by company
    Income->IncomeController: Income list with relationships
    IncomeController->Frontend: Return income view
    Frontend->User: Display income list
end
```

**Key Features**:
- Company-based data isolation
- Relationship loading
- Access control

### Income Creation Process
**Description**: Create new income with validation

```sequence
title Income Creation Flow

User->Frontend: Access create income form
Frontend->IncomeController: GET /incomes/create
IncomeController->Auth: Check company access
Auth->IncomeController: Company status

alt No company access
    IncomeController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    IncomeController->Account: Get income accounts by company
    Account->IncomeController: Account list
    IncomeController->Frontend: Return create form with accounts
    Frontend->User: Display income creation form
end

User->Frontend: Submit income data
Frontend->IncomeController: POST /incomes
IncomeController->Validator: Validate income data
Validator->IncomeController: Validation result

alt Validation fails
    IncomeController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    IncomeController->DB: Begin transaction
    DB->IncomeController: Transaction started
    
    IncomeController->Income: Create income record
    Income->IncomeController: Income created
    
    IncomeController->IncomeDetail: Create income details
    IncomeDetail->IncomeController: Details created
    
    IncomeController->GeneralLedger: Create general ledger entry
    GeneralLedger->IncomeController: GL entry created
    
    IncomeController->GeneralLedgerDetail: Create GL details
    GeneralLedgerDetail->IncomeController: GL details created
    
    IncomeController->Account: Update account balances
    Account->IncomeController: Balances updated
    
    IncomeController->DB: Commit transaction
    DB->IncomeController: Transaction committed
    
    IncomeController->Frontend: Success redirect
    Frontend->User: Redirect to income list
end
```

**Key Features**:
- Income data validation
- General ledger integration
- Account balance updates
- Transaction safety

## 🔄 Internal Transfer Management Flow

### Internal Transfer Listing Process
**Description**: Display internal transfer list with company isolation

```sequence
title Internal Transfer Listing Flow

User->Frontend: Access internal transfers page
Frontend->InternalTransferController: GET /internal-transfers
InternalTransferController->Auth: Check company access
Auth->InternalTransferController: Company status

alt No company access
    InternalTransferController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    InternalTransferController->InternalTransfer: Query transfers by company
    InternalTransfer->InternalTransferController: Transfer list with relationships
    InternalTransferController->Frontend: Return internal transfer view
    Frontend->User: Display internal transfer list
end
```

**Key Features**:
- Company-based data isolation
- Relationship loading
- Access control

### Internal Transfer Creation Process
**Description**: Create new internal transfer with validation

```sequence
title Internal Transfer Creation Flow

User->Frontend: Access create internal transfer form
Frontend->InternalTransferController: GET /internal-transfers/create
InternalTransferController->Auth: Check company access
Auth->InternalTransferController: Company status

alt No company access
    InternalTransferController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    InternalTransferController->Account: Get accounts by company
    Account->InternalTransferController: Account list
    InternalTransferController->Frontend: Return create form with accounts
    Frontend->User: Display internal transfer creation form
end

User->Frontend: Submit internal transfer data
Frontend->InternalTransferController: POST /internal-transfers
InternalTransferController->Validator: Validate transfer data
Validator->InternalTransferController: Validation result

alt Validation fails
    InternalTransferController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    InternalTransferController->InternalTransfer: Validate double-entry balance
    InternalTransfer->InternalTransferController: Balance validation
    
    alt Debits != Credits
        InternalTransferController->Frontend: Return with error
        Frontend->User: Display balance error
    else Debits = Credits
        InternalTransferController->DB: Begin transaction
        DB->InternalTransferController: Transaction started
        
        InternalTransferController->InternalTransfer: Create transfer record
        InternalTransfer->InternalTransferController: Transfer created
        
        InternalTransferController->GeneralLedger: Create general ledger entry
        GeneralLedger->InternalTransferController: GL entry created
        
        InternalTransferController->GeneralLedgerDetail: Create GL details
        GeneralLedgerDetail->InternalTransferController: GL details created
        
        InternalTransferController->Account: Update account balances
        Account->InternalTransferController: Balances updated
        
        InternalTransferController->DB: Commit transaction
        DB->InternalTransferController: Transaction committed
        
        InternalTransferController->Frontend: Success redirect
        Frontend->User: Redirect to internal transfer list
    end
end
```

**Key Features**:
- Double-entry validation
- General ledger integration
- Account balance updates
- Transaction safety

## 📊 Financial Reports Flow

### Financial Report Generation Process
**Description**: Generate financial reports with data aggregation

```sequence
title Financial Report Generation Flow

User->Frontend: Access financial reports page
Frontend->FinancialReportController: GET /financial-reports
FinancialReportController->Auth: Check company access
Auth->FinancialReportController: Company status

alt No company access
    FinancialReportController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    FinancialReportController->Account: Get accounts by company
    Account->FinancialReportController: Account list
    FinancialReportController->GeneralLedger: Get GL entries by company
    GeneralLedger->FinancialReportController: GL entries
    FinancialReportController->Frontend: Return reports view with data
    Frontend->User: Display financial reports
end
```

**Key Features**:
- Company-based data isolation
- Data aggregation
- Report generation
- Access control

### Report Export Process
**Description**: Export financial reports to PDF format

```sequence
title Report Export Flow

User->Frontend: Request report export
Frontend->FinancialReportController: GET /financial-reports/{type}/export
FinancialReportController->Auth: Check company access
Auth->FinancialReportController: Company status

alt No company access
    FinancialReportController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    FinancialReportController->Account: Get accounts by company
    Account->FinancialReportController: Account list
    FinancialReportController->GeneralLedger: Get GL entries by company
    GeneralLedger->FinancialReportController: GL entries
    
    FinancialReportController->PDF: Generate PDF report
    PDF->FinancialReportController: PDF generated
    
    FinancialReportController->Frontend: Return PDF response
    Frontend->User: Download financial report
end
```

**Key Features**:
- PDF generation
- Professional formatting
- Download capability
- Company data isolation

## 🔐 Access Control

### Company-Based Isolation
- All financial data scoped to user's company
- Automatic company association
- Cross-company access prevention
- Permission enforcement

### Critical Account Protection
- Protected accounts (Sales Revenue, AR, COGS, AP)
- Deletion prevention
- Modification restrictions
- Business rule enforcement

## 📊 Business Logic

### Double-Entry Bookkeeping
- Debits must equal credits
- Account balance updates
- Transaction integrity
- Audit trail support

### Account Management Rules
- Critical account protection
- Dependency checking
- Balance validation
- Company isolation

### Financial Integration
- Automatic journal entries
- Balance updates
- Transaction safety
- Data consistency

## 🔄 Data Relationships

### Financial Components
- Chart of accounts
- General ledger entries
- Account balances
- Transaction details
- Financial reports

### Integration Points
- Sales orders
- Purchase orders
- Receipts
- Deliveries
- Expense management
- Income management

## 📱 User Experience

### Form Handling
- Dynamic account selection
- Real-time validation
- Balance checking
- Error handling

### Report Generation
- Multiple report types
- PDF export
- Data filtering
- Professional formatting

---

**Note**: Finance management provides comprehensive double-entry bookkeeping with automatic journal entries, account balance updates, and professional financial reporting capabilities.