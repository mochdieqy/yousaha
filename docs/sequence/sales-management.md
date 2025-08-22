# Sales Management Sequence Diagrams

## Sales Order Management

### Show Sales Order List

![Show Sales Order List Sequence Diagram](images/Show%20Sales%20Order%20List.png)
```
title Show Sales Order List

User->Application: Sign In
Application->User: Show home page
User->Application: Click sales
Application->DB: Get sales order list
DB->Application: Return sales order list
Application->User: Show sales order list page
```
### Create Sales Order

![Create Sales Order Sequence Diagram](images/Create%20Sales%20Order.png)
```
title Create Sales Order

User->Application: Sign In
Application->User: Show home page
User->Application: Click sales
Application->DB: Get sales order list
DB->Application: Return sales order list
Application->User: Show sales order list page
User->Application: Click create
Application->User: Show sales order form page
User->Application: Input form
Application->DB: Begin transaction
Application->DB: Insert sales order
DB->Application: Return status
Application->DB: Insert sales order product line
DB->Application: Return status
Application->DB: Insert sales order status log
DB->Application: Return status

alt Transaction failed
    Application->DB: Rollback transaction
    Application->User: Send error message
else Transaction success
    Application->DB: Commit transaction
    Application->User: Show successful create sales order status message
end
```
### Update Sales Order

![Update Sales Order Sequence Diagram](images/Update%20Sales%20Order.png)
```
title Update Sales Order

User->Application: Sign In
Application->User: Show home page
User->Application: Click sales
Application->DB: Get sales order list
DB->Application: Return sales order list
Application->User: Show sales order list page
User->Application: Click edit
Application->DB: Get sales order
DB->Application: Return sales order

alt Sales order status (done, cancel)
    Application->User: Show error message
else Sales order status (draft, waiting, accepted, sent)
    Application->Application: Auto form fill
    Application->User: Show sales order form page
    User->Application: Input form
    Application->DB: Begin transaction
    Application->DB: Update sales order
    DB->Application: Return status
    Application->DB: Insert sales order status log
    DB->Application: Return status
    
    alt Sales order status (draft, waiting)
        Application->DB: Upsert or delete sales order product line
        DB->Application: Return status
    end
    
    alt Update sales order status to accepted/sent/done/cancel
        Application->DB: Update stock
        DB->Application: Return status
        Application->DB: Upsert delivery
        DB->Application: Return status
        Application->DB: Insert income
        DB->Application: Return status
        Application->DB: Insert general ledger
        DB->Application: Return status
    end

    alt Transaction failed
        Application->DB: Rollback transaction
        Application->User: Send error message
    else Transaction success
        Application->DB: Commit transaction
        Application->User: Show successful update sales order message
    end
end
```
## Document Generation

### Generate Quotation

![Generate Quotation Sequence Diagram](images/Generate%20Quotation.png)
```
title Generate Quotation

User->Application: Sign In
Application->User: Show home page
User->Application: Click sales
Application->DB: Get sales order list
DB->Application: Return sales order list
Application->User: Show sales order list page
User->Application: Click quotation
Application->DB: Get sales order
DB->Application: Return sales order

alt Sales order status (waiting, accepted, sent, done, cancel)
    Application->User: Show error message
else Sales order status (draft)
    Application->Application: Generate quotation
    Application->User: Download quotation
end
```
### Generate Invoice

![Generate Invoice Sequence Diagram](images/Generate%20Invoice.png)
```
title Generate Invoice

User->Application: Sign In
Application->User: Show home page
User->Application: Click sales
Application->DB: Get sales order list
DB->Application: Return sales order list
Application->User: Show sales order list page
User->Application: Click invoice
Application->DB: Get sales order
DB->Application: Return sales order

alt Sales order status (draft)
    Application->User: Show error message
else Sales order status (waiting, accepted, sent, done, cancel)
    Application->Application: Generate invoice
    Application->User: Download invoice
end
```