# Inventory Management Sequence Diagrams

## Warehouse Management

### Show Warehouse List

![Show Warehouse List Sequence Diagram](images/Show%20Warehouse%20List.png)
```
title Show Warehouse List

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click warehouse
Application->DB: Get warehouse list
DB->Application: Return warehouse list
Application->User: Show warehouse list page
```
### Upsert Warehouse

![Upsert Warehouse Sequence Diagram](images/Upsert%20Warehouse.png)
```
title Upsert Warehouse

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click warehouse
Application->DB: Get warehouse list
DB->Application: Return warehouse list
Application->User: Show warehouse list page

alt Update
    User->Application: Click edit
    Application->DB: Get warehouse
    DB->Application: Return warehouse
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show warehouse form page
User->Application: Input form
Application->DB: Upsert warehouse
DB->Application: Return status

alt Upsert failed
    Application->User: Show error message
else Upsert success
    Application->User: Show successful upsert warehouse message
end
```
### Delete Warehouse

![Delete Warehouse Sequence Diagram](images/Delete%20Warehouse.png)
```
title Delete Warehouse

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click warehouse
Application->DB: Get warehouse list
DB->Application: Return warehouse list
Application->User: Show warehouse list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Delete warehouse
    DB->Application: Return status
    
    alt Delete failed
        Application->User: Show error message
    else Delete success
        Application->User: Show successful delete warehouse message
    end
end
```
## Stock Management

### Show Stock List

![Show Stock List Sequence Diagram](images/Show%20Stock%20List.png)
```
title Show Stock List

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click stock
Application->DB: Get stock list
DB->Application: Return stock list
Application->User: Show stock list page
```
### Upsert Stock

![Upsert Stock Sequence Diagram](images/Upsert%20Stock.png)
```
title Upsert Stock

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click stock
Application->DB: Get stock list
DB->Application: Return stock list
Application->User: Show stock list page

alt Update
    User->Application: Click edit
    Application->DB: Get stock
    DB->Application: Return stock
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show stock form page
User->Application: Input form
Application->Application: Calculate quantity total
Application->DB: Begin transaction
Application->DB: Upsert stock
DB->Application: Return status
Application->DB: Insert or delete stock detail
DB->Application: Return status
Application->DB: Insert stock history
DB->Application: Return status

alt Transaction failed
    Application->DB: Rollback transaction
    Application->User: Send error message
else Transaction success
    Application->DB: Commit transaction
    Application->User: Show successful upsert stock message
end
```
### Delete Stock

![Delete Stock Sequence Diagram](images/Delete%20Stock.png)
```
title Delete Stock

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click stock
Application->DB: Get stock list
DB->Application: Retuen stock list
Application->User: Show stock list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Delete stock
    DB->Application: Return status
    
    alt Delete failed
        Application->User: Send error message
    else Delete success
        Application->User: Show successful delete stock message
    end
end
```
## Receipt Management (Goods Receiving)

### Show Receipt List

![Show Receipt List Sequence Diagram](images/Show%20Receipt%20List.png)
```
title Show Receipt List

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click receipt
Application->DB: Get receipt list
DB->Application: Return receipt list
Application->User: Show receipt list page
```
### Create Receipt

![Create Receipt Sequence Diagram](images/Create%20Receipt.png)
```
title Create Receipt

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click receipt
Application->DB: Get receipt list
DB->Application: Return receipt list
Application->User: Show receipt list page
User->Application: Click create
Application->User: Show receipt form page
User->Application: Input form
Application->DB: Begin transaction
Application->DB: Insert receipt
DB->Application: Return status
Application->DB: Insert receipt product line
DB->Application: Return status
Application->DB: Insert receipt status log
DB->Application: Return status

alt Transaction failed
    Application->DB: Rollback transaction
    Application->User: Send error message
else Transaction success
    Application->DB: Commit transaction
    Application->User: Show successful create receipt message
end
```
### Update Receipt

![Update Receipt Sequence Diagram](images/Update%20Receipt.png)
```
title Update Receipt

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click receipt
Application->DB: Get receipt list
DB->Application: Return receipt list
Application->User: Show receipt list page
User->Application: Click edit
Application->DB: Get receipt
DB->Application: Return receipt

alt Receipt status (ready, done, cancel)
    Application->User: Show error message
else Receipt status (draft, waiting)
    Application->Application: Auto form fill
    Application->User: Show receipt form page
    User->Application: Input form
    Application->DB: Begin transaction
    Application->DB: Update receipt
    DB->Application: Return status
    Application->DB: Upsert or delete receipt product line
    DB->Application: Return status
    Application->DB: Insert receipt status log
    DB->Application: Return status
    
    alt Update receipt status to ready
        Application->DB: Update stock
        DB->Application: Return status
    end

    alt Transaction failed
        Application->DB: Rollback transaction
        Application->User: Send error message
    else Transaction success
        Application->DB: Commit transaction
        Application->User: Show successful update receipt message
    end
end
```
### Goods Receive

![Goods Receive Sequence Diagram](images/Goods%20Receive.png)
```
title Goods Receive

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click receipt
Application->DB: Get receipt list
DB->Application: Return receipt list
Application->User: Show receipt list page
User->Application: Click edit
Application->DB: Get receipt
DB->Application: Return receipt

alt Receipt status (draft, waiting, done, cancel)
    Application->User: Show error message
else Receipt status (ready)
    Application->Application: Auto form fill
    Application->User: Show receipt form page
    User->Application: Input form
    Application->DB: Begin transaction
    Application->DB: Update receipt
    DB->Application: Return status
    Application->DB: Insert receipt status log
    DB->Application: Return status
    Application->DB: Update stock
    DB->Application: Return status
    Application->DB: Insert stock detail
    DB->Application: Return status
    Application->DB: Update stock history
    DB->Application: Return status

    alt Transaction failed
        Application->DB: Rollback transaction
        Application->User: Send error message
    else Transaction success
        Application->DB: Commit transaction
        Application->User: Show successful goods receive message
    end
end
```
### Delete Receipt

![Delete Receipt Sequence Diagram](images/Delete%20Receipt.png)
```
title Delete Receipt

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click receipt
Application->DB: Get receipt list
DB->Application: Return receipt list
Application->User: Show receipt list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete

    alt Receipt status is not draft
        Application->User: Send error message
    else Receipt status is draft
        Application->DB: Delete receipt
        DB->Application: Return status
        Application->User: Show successful delete receipt message
    end
end
```
## Delivery Management (Goods Issue)

### Show Delivery List

![Show Delivery List Sequence Diagram](images/Show%20Delivery%20List.png)
```
title Show Delivery List

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click delivery
Application->DB: Get delivery list
DB->Application: Return delivery list
Application->User: Show delivery list page
```
### Create Delivery

![Create Delivery Sequence Diagram](images/Create%20Delivery.png)
```
title Create Delivery

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click delivery
Application->DB: Get delivery list
DB->Application: Return delivery list
Application->User: Show delivery list page
User->Application: Click create
Application->User: Show delivery form page
User->Application: Input form
Application->DB: Begin transaction
Application->DB: Insert delivery
DB->Application: Return status
Application->DB: Insert delivery product line
DB->Application: Return status
Application->DB: Insert delivery status log
DB->Application: Return status

alt Transaction failed
    Application->DB: Rollback transaction
    Application->User: Send error message
else Transaction success
    Application->DB: Commit transaction
    Application->User: Show successful create delivery message
end
```
### Update Delivery

![Update Delivery Sequence Diagram](images/Update%20Delivery.png)
```
title Update Delivery

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click delivery
Application->DB: Get delivery list
DB->Application: Return delivery list
Application->User: Show delivery list page
User->Application: Click edit
Application->DB: Get delivery
DB->Application: Return delivery

alt Delivery status (ready, done, cancel)
    Application->User: Show error message
else Delivery status (draft, waiting)
    Application->Application: Auto form fill
    Application->User: Show delivery form page
    User->Application: Input form
    Application->DB: Begin transaction
    Application->DB: Update delivery
    DB->Application: Return status
    Application->DB: Upsert or delete delivery product line
    DB->Application: Return status
    Application->DB: Insert delivery status log
    DB->Application: Return status
    
    alt Update delivery status to ready
        Application->DB: Update stock
        DB->Application: Return status
    end

    alt Transaction failed
        Application->DB: Rollback transaction
        Application->User: Send error message
    else Transaction success
        Application->DB: Commit transaction
        Application->User: Show successful update delivery message
    end
end
```
### Goods Issue

![Goods Issue Sequence Diagram](images/Goods%20Issue.png)
```
title Goods Issue

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click delivery
Application->DB: Get delivery list
DB->Application: Return delivery list
Application->User: Show delivery list page
User->Application: Click goods issue
Application->DB: Get delivery
DB->Application: Return delivery

alt Delivery status (draft, waiting, done, cancel)
    Application->User: Show error message
else Delivery status (ready)
    Application->Application: Auto form fill
    Application->User: Show delivery form page
    User->Application: Input form (validate, cancel)
    Application->DB: Begin transaction
    Application->DB: Update delivery
    DB->Application: Return status
    Application->DB: Insert delivery status log
    DB->Application: Return status
    Application->DB: Update stock
    DB->Application: Return status
    Application->DB: Delete stock detail
    DB->Application: Return status
    Application->DB: Update stock history
    DB->Application: Return status

    alt Transaction failed
        Application->DB: Rollback transaction
        Application->User: Send error message
    else Transaction success
        Application->DB: Commit transaction
        Application->User: Show successful goods issue message
    end
end
```
### Delete Delivery

![Delete Delivery Sequence Diagram](images/Delete%20Delivery.png)
```
title Delete Delivery

User->Application: Sign In
Application->User: Show home page
User->Application: Click inventory
Application->User: Show inventory page
User->Application: Click delivery
Application->DB: Get delivery list
DB->Application: Return delivery list
Application->User: Show delivery list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete

    alt Delivery status is not draft
        Application->User: Send error message
    else Delivery status is draft
        Application->DB: Delete delivery
        DB->Application: Return status
        Application->User: Show successful delete delivery message
    end
end
```