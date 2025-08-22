# Finance Management Sequence Diagrams

## Chart of Accounts

### Show Account List

![Show Account List Sequence Diagram](images/Show%20Account%20List.png)
```
title Show Account List

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click account
Application->DB: Get account list
DB->Application: Return account list
Application->User: Show account list page
```
### Upsert Account

![Upsert Account Sequence Diagram](images/Upsert%20Account.png)
```
title Upsert Account

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click account
Application->DB: Get account list
DB->Application: Return account list
Application->User: Show account list page

alt Update
    User->Application: Click edit
    Application->DB: Get account
    DB->Application: Return account
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show account form page
User->Application: Input form
Application->DB: Upsert account
DB->Application: Return status

alt Upsert failed
    Application->User: Show error message
else Upsert success
    Application->User: Show successful upsert account message
end
```
### Delete Account

![Delete Account Sequence Diagram](images/Delete%20Account.png)
```
title Delete Account

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click account
Application->DB: Get account list
DB->Application: Return account list
Application->User: Show account list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Get general ledger detail by account id
    DB->Application: Return general ledger detail
    Application->DB: Get expense detail by account id
    DB->Application: Return expense detail
    Application->DB: Get income detail by account id
    DB->Application: Return income detail
    Application->DB: Get internal transfer detail by account id
    DB->Application: Return internal transfer detail
    
    alt Account exist in detail
        Application->User: Show error message
    else Account not exist in detail
        Application->DB: Delete account
        DB->Application: Return status
        
        alt Delete failed
            Application->User: Show error message
        else Delete success
            Application->User: Show successful delete account message
        end
    end
end
```
## General Ledger

### Show General Ledger List

![Show General Ledger List Sequence Diagram](images/Show%20General%20Ledger%20List.png)
```
title Show General Ledger List

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click general ledger
Application->DB: Get general ledger list
DB->Application: Return general ledger list
Application->User: Show general ledger list page
```
### Upsert General Ledger

![Upsert General Ledger Sequence Diagram](images/Upsert%20General%20Ledger.png)
```
title Upsert General Ledger

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click general ledger
Application->DB: Get general ledger list
DB->Application: Return general ledger list
Application->User: Show general ledger list page

alt Update
    User->Application: Click edit
    Application->DB: Get general ledger
    DB->Application: Return general ledger
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show general ledger form page
User->Application: Input form
Application->DB: Upsert general ledger
DB->Application: Return status

alt Upsert failed
    Application->User: Show error message
else Upsert success
    Application->User: Show successful upsert general ledger message
end
```
### Delete General Ledger

![Delete General Ledger Sequence Diagram](images/Delete%20General%20Ledger.png)
```
title Delete General Ledger

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click general ledger
Application->DB: Get general ledger list
DB->Application: Return general ledger list
Application->User: Show general ledger list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Delete general ledger
    DB->Application: Return status
    
    alt Delete failed
        Application->User: Show error message
    else Delete success
        Application->User: Show successful delete general ledger message
    end
end
```
## Expense Management

### Show Expense List

![Show Expense List Sequence Diagram](images/Show%20Expense%20List.png)
```
title Show Expense List

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click expense
Application->DB: Get expense list
DB->Application: Return expense list
Application->User: Show expense list page
```
### Upsert Expense

![Upsert Expense Sequence Diagram](images/Upsert%20Expense.png)
```
title Upsert Expense

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click expense
Application->DB: Get expense list
DB->Application: Return expense list
Application->User: Show expense list page

alt Update
    User->Application: Click edit
    Application->DB: Get expense
    DB->Application: Return expense
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show expense form page
User->Application: Input form
Application->DB: Begin transaction

alt Insert
    Application->DB: Insert general ledger
    DB->Application: Return status
end

Application->DB: Upsert expense
DB->Application: Return status

alt Transaction failed
    Application->DB: Rollback transaction
    Application->User: Show error message
else Transaction success
    Application->DB: Commit transaction
    Application->User: Show successful upsert expense message
end
```
### Delete Expense

![Delete Expense Sequence Diagram](images/Delete%20Expense.png)
```
title Delete Expense

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click expense
Application->DB: Get expense list
DB->Application: Return expense list
Application->User: Show expense list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Get general ledger by expense id
    DB->Application: Return general ledger
    
    alt Expense exist in general ledger
        Application->User: Show error message
    else Expense not exist in general ledger
        Application->DB: Delete expense
        DB->Application: Return status
        
        alt Delete failed
            Application->User: Show error message
        else Delete success
            Application->User: Show successful delete expense message
        end
    end
end
```
## Income Management

### Show Income List

![Show Income List Sequence Diagram](images/Show%20Income%20List.png)
```
title Show Income List

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click income
Application->DB: Get income list
DB->Application: Return income list
Application->User: Show income list page
```
### Upsert Income

![Upsert Income Sequence Diagram](images/Upsert%20Income.png)
```
title Upsert Income

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click income
Application->DB: Get income list
DB->Application: Return income list
Application->User: Show income list page

alt Update
    User->Application: Click edit
    Application->DB: Get income
    DB->Application: Return income
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show income form page
User->Application: Input form
Application->DB: Begin transaction

alt Insert
    Application->DB: Insert general ledger
    DB->Application: Return status
end

Application->DB: Upsert income
DB->Application: Return status

alt Transaction failed
    Application->DB: Rollback transaction
    Application->User: Show error message
else Transaction success
    Application->DB: Commit transaction
    Application->User: Show successful upsert income message
end
```
### Delete Income

![Delete Income Sequence Diagram](images/Delete%20Income.png)
```
title Delete Income

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click income
Application->DB: Get income list
DB->Application: Return income list
Application->User: Show income list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Get general ledger by income id
    DB->Application: Return general ledger
    
    alt Income exist in general ledger
        Application->User: Show error message
    else Income not exist in general ledger
        Application->DB: Delete income
        DB->Application: Return status
        
        alt Delete failed
            Application->User: Show error message
        else Delete success
            Application->User: Show successful delete income message
        end
    end
end
```
## Internal Transfer Management

### Show Internal Transfer List

![Show Internal Transfer List Sequence Diagram](images/Show%20Internal%20Transfer%20List.png)
```
title Show Internal Transfer List

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click internal transfer
Application->DB: Get internal transfer list
DB->Application: Return internal transfer list
Application->User: Show internal transfer list page
```
### Upsert Internal Transfer

![Upsert Internal Transfer Sequence Diagram](images/Upsert%20Internal%20Transfer.png)
```
title Upsert Internal Transfer

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click internal transfer
Application->DB: Get internal transfer list
DB->Application: Return internal transfer list
Application->User: Show internal transfer list page

alt Update
    User->Application: Click edit
    Application->DB: Get internal transfer
    DB->Application: Return internal transfer
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show internal transfer form page
User->Application: Input form
Application->DB: Begin transaction

alt Insert
    Application->DB: Insert general ledger
    DB->Application: Return status
end

Application->DB: Upsert internal transfer
DB->Application: Return status

alt Transaction failed
    Application->DB: Rollback transaction
    Application->User: Show error message
else Transaction success
    Application->DB: Commit transaction
    Application->User: Show successful upsert internal transfer message
end
```
### Delete Internal Transfer

![Delete Internal Transfer Sequence Diagram](images/Delete%20Internal%20Transfer.png)
```
title Delete Internal Transfer

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click internal transfer
Application->DB: Get internal transfer list
DB->Application: Return internal transfer list
Application->User: Show internal transfer list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Get general ledger by internal transfer id
    DB->Application: Return general ledger
    
    alt Income exist in general ledger
        Application->User: Show error message
    else Income not exist in general ledger
        Application->DB: Delete internal transfer
        DB->Application: Return status
        
        alt Delete failed
            Application->User: Show error message
        else Delete success
            Application->User: Show successful delete internal transfer message
        end
    end
end
```
## Asset Management

### Show Asset List

![Show Asset List Sequence Diagram](images/Show%20Asset%20List.png)
```
title Show Asset List

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click asset
Application->DB: Get asset list
DB->Application: Return asset list
Application->User: Show asset list page
```
### Upsert Asset

![Upsert Asset Sequence Diagram](images/Upsert%20Asset.png)
```
title Upsert Asset

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click asset
Application->DB: Get asset list
DB->Application: Return asset list
Application->User: Show asset list page

alt Update
    User->Application: Click edit
    Application->DB: Get asset
    DB->Application: Return asset
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show asset form page
User->Application: Input form
Application->DB: Begin transaction

alt Insert
    Application->DB: Insert general ledger
    DB->Application: Return status
end

Application->DB: Upsert asset
DB->Application: Return status

alt Transaction failed
    Application->DB: Rollback transaction
    Application->User: Show error message
else Transaction success
    Application->DB: Commit transaction
    Application->User: Show successful upsert asset message
end
```
### Delete Asset

![Delete Asset Sequence Diagram](images/Delete%20Asset.png)
```
title Delete Asset

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click asset
Application->DB: Get asset list
DB->Application: Return asset list
Application->User: Show asset list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Get general ledger by asset id
    DB->Application: Return general ledger
    
    alt Asset exist in general ledger
        Application->User: Show error message
    else Asset not exist in general ledger
        Application->DB: Delete asset
        DB->Application: Return status
        
        alt Delete failed
            Application->User: Show error message
        else Delete success
            Application->User: Show successful delete asset message
        end
    end
end
```
## Financial Reports

### Export General Ledger

![Export General Ledger Sequence Diagram](images/Export%20General%20Ledger.png)
```
title Export General Ledger

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click report
Application->User: Show report page
User->Application: Click export general ledger
Application->User: Show input period pop up
User->Application: Click export
Application->DB: Get general ledger list by period
DB->Application: Return general ledger list
Application->Application: Generate file
Application->User: Download file
```
### Export Income Statement

![Export Income Statement Sequence Diagram](images/Export%20Income%20Statement.png)
```
title Export Income Statement

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click report
Application->User: Show report page
User->Application: Click export income statement
Application->User: Show input period pop up
User->Application: Click export
Application->DB: Get income list by period
DB->Application: Return income list
Application->DB: Get expense list by period
DB->Application: Return expense list
Application->Application: Generate file
Application->User: Download file
```
### Export Assets

![Export Assets Sequence Diagram](images/Export%20Assets.png)
```
title Export Assets

User->Application: Sign In
Application->User: Show home page
User->Application: Click finance
Application->User: Show finance page
User->Application: Click report
Application->User: Show report page
User->Application: Click export asset
Application->DB: Get asset list
DB->Application: Return asset list
Application->Application: Generate file
Application->User: Download file
```