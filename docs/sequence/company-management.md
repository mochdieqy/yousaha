# Company Management Sequence Diagrams

## Create Company

![Create Company Sequence Diagram](images/Create%20Company.png)
```
title Create Company

User->Application: Sign In
Application->DB: Get user
DB->Application: Return user
Application->DB: Get company by user id

alt Company exist
    Application->User: Show home page
else Company not exist
    Application->DB: Get employee by user id
    
    alt Employee exist
        Application->User: Show home page
    else Employee not exist
        Application->User: Show create company page
        User->Application: Input form
        Application->DB: Insert company
        DB->Application: Return status
        
        alt Status failed
            Application->User: Show error message
        else Status success
            Application->User: Show home page
        end
    end
end
```
## Update Company

![Update Company Sequence Diagram](images/Update%20Company.png)
```
title Update Company

User->Application: Sign In
Application->User: Show home page
User->Application: Click my company
Application->DB: Get company
DB->Application: Return company
Application->Application: Auto form fill
Application->User: Show company form page
User->Application: Input form
Application->DB: Update company
DB->Application: Return status

alt Update failed
    Application->User: Send error message
else Update 
    Application->User: Show successful update company
end
```