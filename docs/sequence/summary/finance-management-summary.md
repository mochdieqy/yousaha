# Finance Management - Summary Sequence Diagram

This document contains a simplified summary sequence diagram for financial management operations in the Yousaha ERP system.

## 💰 Finance Management Flow Summary

### Complete Financial Operations Flow
**Description**: Simplified overview of all financial management operations

```sequence
title Finance Management - Complete Flow Summary

User->Frontend: Access finance module
Frontend->Backend: Request financial data
Backend->Auth: Verify company access
Auth->Backend: Access granted

Backend->Database: Query financial data
Database->Backend: Return financial information
Backend->Frontend: Return finance view
Frontend->User: Display finance dashboard

User->Frontend: Perform financial action
Frontend->Backend: Submit action request
Backend->Validator: Validate input data
Validator->Backend: Validation result

alt Validation fails
    Backend->Frontend: Return errors
    Frontend->User: Display error messages
else Validation passes
    Backend->Database: Begin transaction
    Database->Backend: Transaction started
    
    alt Chart of Accounts
        Backend->Database: Execute account operation
        Database->Backend: Account operation completed
        
        alt Delete Account
            Backend->Database: Check if critical account
            Database->Backend: Critical status returned
            
            alt Critical Account (4000, 1100, 5000, 2000)
                Backend->Frontend: Return deletion error
                Frontend->User: Display critical account message
            else Regular Account
                Backend->Database: Check dependencies
                Database->Backend: Dependency status
                
                alt Has dependencies
                    Backend->Frontend: Return dependency error
                    Frontend->User: Display dependency message
                else No dependencies
                    Backend->Database: Delete account
                    Database->Backend: Account deleted
                end
            end
        
    else General Ledger
        Backend->Database: Create GL entry
        Database->Backend: GL entry created
        Backend->Database: Create GL details
        Database->Backend: GL details created
        
    else Expense Management
        Backend->Database: Create expense record
        Database->Backend: Expense created
        Backend->Database: Create GL entries
        Database->Backend: GL entries created
        
    else Income Management
        Backend->Database: Create income record
        Database->Backend: Income created
        Backend->Database: Create GL entries
        Database->Backend: GL entries created
        
    else Internal Transfer
        Backend->Database: Create transfer record
        Database->Backend: Transfer created
        Backend->Database: Create GL entries
        Database->Backend: GL entries created
        
    else Financial Reports
        Backend->Database: Generate report data
        Database->Backend: Report data generated
        Backend->PDFService: Generate PDF report
        PDFService->Backend: PDF generated
    end
    
    Backend->Database: Commit transaction
    Database->Backend: Transaction committed
    Backend->Frontend: Success response
    Frontend->User: Show success message
end
```

**Key Features**:
- **Chart of Accounts**: Complete account management with protection
- **General Ledger**: Double-entry bookkeeping system
- **Expense Management**: Expense tracking with GL integration
- **Income Management**: Income recording with GL integration
- **Internal Transfer**: Inter-account transfers
- **Financial Reports**: Report generation and PDF export
- **Critical Account Protection**: Prevents deletion of essential accounts
- **Transaction Safety**: Database transactions with rollback

**Business Rules**:
- All operations require company access
- Critical accounts (4000, 1100, 5000, 2000) cannot be deleted
- Double-entry bookkeeping enforced
- Account dependencies prevent deletion
- Financial reports maintain audit trail
- GL entries maintain balance integrity

**Critical Financial Accounts**:
- **Account 4000 "Sales Revenue"** (Revenue type) - CREDIT for sales
- **Account 1100 "Accounts Receivable"** (Asset type) - DEBIT for sales
- **Account 5000 "Cost of Goods Sold"** (Expense type) - DEBIT for purchases
- **Account 2000 "Accounts Payable"** (Liability type) - CREDIT for purchases

**Integration Points**:
- Sales order system (automatic GL entries)
- Purchase order system (automatic GL entries)
- Inventory system (cost calculations)
- HR system (payroll expenses)
- Reporting and analytics
- PDF generation service

**Financial Workflows**:
- Sales orders → Revenue + Receivable entries
- Purchase orders → COGS + Payable entries
- Manual expenses → Expense + Payable entries
- Manual income → Income + Receivable entries
- Internal transfers → Debit/Credit entries
