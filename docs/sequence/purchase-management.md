# Purchase Management Sequence Diagrams

## Purchase Order Management

### Show Purchase Order List

![Show Purchase Order List Sequence Diagram](images/Show%20Purchase%20Order%20List.png)
```
title Show Purchase Order List

User->Application: Sign In
Application->User: Show home page
User->Application: Click purchase
Application->DB: Get purchase order list
DB->Application: Return purchase order list
Application->User: Show purchase order list page
```
### Create Purchase Order

![Create Purchase Order Sequence Diagram](images/Create%20Purchase%20Order.png)
```
title Create Purchase Order

User->Application: Sign In
Application->User: Show home page
User->Application: Click purchase
Application->DB: Get purchase order list
DB->Application: Return purchase order list
Application->User: Show purchase order list page
User->Application: Click create
Application->User: Show purchase order form page
User->Application: Input form
Application->DB: Begin transaction
Application->DB: Insert purchase order
DB->Application: Return status
Application->DB: Insert purchase order product line
DB->Application: Return status
Application->DB: Insert purchase order status log
DB->Application: Return status

alt Transaction failed
    Application->DB: Rollback transaction
    Application->User: Send error message
else Transaction success
    Application->DB: Commit transaction
    Application->User: Show successful create purchase order message
end
```
### Update Purchase Order

![Update Purchase Order Sequence Diagram](images/Update%20Purchase%20Order.png)
```
title Update Purchase Order

User->Application: Sign In
Application->User: Show home page
User->Application: Click purchase
Application->DB: Get purchase order list
DB->Application: Return purchase order list
Application->User: Show purchase order list 
User->Application: Click edit
Application->DB: Get purchase order
DB->Application: Return purchase order

alt Purchase order status (done, cancel)
    Application->User: Show error message
else Purchase order status (draft, accepted, sent)
    Application->Application: Auto form fill
    Application->User: Show purchase order form page
    User->Application: Input form
    Application->DB: Begin transaction
    Application->DB: Update purchase order
    DB->Application: Return status
    Application->DB: Insert purchase order status log
    DB->Application: Return status
    
    alt Purchase order status (draft)
        Application->DB: Upsert or delete purchase order product line
        DB->Application: Return status
    end
    
    alt Update sales order status to accepted/sent/done/cancel
        Application->DB: Update stock
        DB->Application: Return status
        Application->DB: Upsert receipt
        DB->Application: Return status
        Application->DB: Insert expense
        DB->Application: Return status
        Application->DB: Insert general ledger
        DB->Application: Return status
    end

    alt Transaction failed
        Application->DB: Rollback transaction
        Application->User: Send error message
    else Transaction success
        Application->DB: Commit transaction
        Application->User: Show successful update purchase order message
    end
end
```