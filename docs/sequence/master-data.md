# Master Data Management Sequence Diagrams

## Product Management

### Show Product List

![Show Product List Sequence Diagram](images/Show%20Product%20List.png)
```
title Show Product List

User->Application: Sign In
Application->User: Show home page
User->Application: Click master data
Application->User: Show master data page
User->Application: Click product
Application->DB: Get product list
DB->Application: Return product list
Application->User: Show product list page
```
### Upsert Product

![Upsert Product Sequence Diagram](images/Upsert%20Product.png)
```
title Upsert Product

User->Application: Sign In
Application->User: Show home page
User->Application: Click master data
Application->User: Show master data page
User->Application: Click product
Application->DB: Get product list
DB->Application: Return product list
Application->User: Show product list page

alt Update
    User->Application: Click edit
    Application->DB: Get product
    DB->Application: Return product
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show product form page
User->Application: Input form
Application->DB: Upsert product
DB->Application: Return status

alt Upsert failed
    Application->User: Show error message
else Upsert success
    Application->User: Show successful upsert product message
end
```
### Delete Product

![Delete Product Sequence Diagram](images/Delete%20Product.png)
```
title Delete Product

User->Application: Sign In
Application->User: Show home page
User->Application: Click master data
Application->User: Show master data page
User->Application: Click product
Application->DB: Get product list
DB->Application: Return product list
Application->User: Show product list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Delete product
    DB->Application: Return status
    
    alt Delete failed
        Application->User: Show error message
    else Delete success
        Application->User: Show successful delete product message
    end
end
```
## Customer Management

### Show Customer List

![Show Customer List Sequence Diagram](images/Show%20Customer%20List.png)
```
title Show Customer List

User->Application: Sign In
Application->User: Show home page
User->Application: Click master data
Application->User: Show master data page
User->Application: Click customer
Application->DB: Get customer list
DB->Application: Return customer list
Application->User: Show customer list page
```
### Upsert Customer

![Upsert Customer Sequence Diagram](images/Upsert%20Customer.png)
```
title Upsert Customer

User->Application: Sign In
Application->User: Show home page
User->Application: Click master data
Application->User: Show master data page
User->Application: Click customer
Application->DB: Get customer list
DB->Application: Return customer list
Application->User: Show customer list page

alt Update
    User->Application: Click edit
    Application->DB: Get customer
    DB->Application: Return customer
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show customer form page
User->Application: Input form
Application->DB: Upsert customer
DB->Application: Return status

alt Upsert failed
    Application->User: Show error message
else Upsert success
    Application->User: Show successful upsert customer message
end
```
### Delete Customer

![Delete Customer Sequence Diagram](images/Delete%20Customer.png)
```
title Delete Customer

User->Application: Sign In
Application->User: Show home page
User->Application: Click master data
Application->User: Show master data page
User->Application: Click customer
Application->DB: Get customer list
DB->Application: Return customer list
Application->User: Show customer list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Delete customer
    DB->Application: Return status
    
    alt Delete failed
        Application->User: Show error message
    else Delete success
        Application->User: Show successful delete customer message
    end
end
```
## Supplier Management

### Show Supplier List

![Show Supplier List Sequence Diagram](images/Show%20Supplier%20List.png)
```
title Show Supplier List

User->Application: Sign In
Application->User: Show home page
User->Application: Click master data
Application->User: Show master data page
User->Application: Click supplier
Application->DB: Get supplier list
DB->Application: Return supplier list
Application->User: Show supplier list page
```
### Upsert Supplier

![Upsert Supplier Sequence Diagram](images/Upsert%20Supplier.png)
```
title Upsert Supplier

User->Application: Sign In
Application->User: Show home page
User->Application: Click master data
Application->User: Show master data page
User->Application: Click supplier
Application->DB: Get supplier list
DB->Application: Return supplier list
Application->User: Show supplier list page

alt Update
    User->Application: Click edit
    Application->DB: Get supplier
    DB->Application: Return supplier
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show supplier form page
User->Application: Input form
Application->DB: Upsert supplier
DB->Application: Return status

alt Upsert failed
    Application->User: Show error message
else Upsert success
    Application->User: Show successful upsert supplier message
end
```
### Delete Supplier

![Delete Supplier Sequence Diagram](images/Delete%20Supplier.png)
```
title Delete Supplier

User->Application: Sign In
Application->User: Show home page
User->Application: Click master data
Application->User: Show master data page
User->Application: Click supplier
Application->DB: Get supplier list
DB->Application: Return supplier list
Application->User: Show supplier list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Delete supplier
    DB->Application: Return status
    
    alt Delete failed
        Application->User: Show error message
    else Delete success
        Application->User: Show successful delete supplier message
    end
end
```