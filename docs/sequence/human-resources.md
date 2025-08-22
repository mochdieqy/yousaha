# Human Resources Management Sequence Diagrams

## Department Management

### Show Department List

![Show Department List Sequence Diagram](images/Show%20Department%20List.png)
```
title Show Department List

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click department
Application->DB: Get department list
DB->Application: Return department list
Application->User: Show department list page
```
### Upsert Department

![Upsert Department Sequence Diagram](images/Upsert%20Department.png)
```
title Upsert Department

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click department
Application->DB: Get department list
DB->Application: Return department list
Application->User: Show department list page

alt Update
    User->Application: Click edit
    Application->DB: Get department
    DB->Application: Return department
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show department form page
User->Application: Input form
Application->DB: Upsert department
DB->Application: Return status

alt Upsert failed
    Application->User: Show error message
else Upsert success
    Application->User: Show successful upsert department message
end
```
### Delete Department

![Delete Department Sequence Diagram](images/Delete%20Department.png)
```
title Delete Department

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click department
Application->DB: Get department list
DB->Application: Return department list
Application->User: Show department list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Get employee by department id
    DB->Application: Return employee
    
    alt Department exist in employee
        Application->User: Show error message
    else Department not exist in employee
        Application->DB: Delete department
        DB->Application: Return status
        
        alt Delete failed
            Application->User: Show error message
        else Delete success
            Application->User: Show successful delete department message
        end
    end
end
```
## Employee Management

### Show Employee List

![Show Employee List Sequence Diagram](images/Show%20Employee%20List.png)
```
title Show Employee List

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click employee
Application->DB: Get employee list
DB->Application: Return employee list
Application->User: Show employee list page
```
### Upsert Employee

![Upsert Employee Sequence Diagram](images/Upsert%20Employee.png)
```
title Upsert Employee

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click employee
Application->DB: Get employee list
DB->Application: Return employee list
Application->User: Show employee list page

alt Update
    User->Application: Click edit
    Application->DB: Get employee
    DB->Application: Return employee
    Application->Application: Auto form fill
else Insert
    User->Application: Click create
end

Application->User: Show employee form page
User->Application: Input form
Application->DB: Upsert employee
DB->Application: Return status

alt Upsert failed
    Application->User: Show error message
else Upsert success
    Application->User: Show successful upsert employee message
end
```
### Delete Employee

![Delete Employee Sequence Diagram](images/Delete%20Employee.png)
```
title Delete Employee

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click employee
Application->DB: Get employee list
DB->Application: Return employee list
Application->User: Show employee list page
User->Application: Click delete
Application->User: Show confirmation pop up
User->Application: Click button

alt User click cancel
    Application->User: Close confirmation pop up
else User click delete
    Application->DB: Delete employee
    DB->Application: Return status
    
    alt Delete failed
        Application->User: Show error message
    else Delete success
        Application->User: Show successful delete employee message
    end
end
```
## Attendance Management

### Show Attendance List

![Show Attendance List Sequence Diagram](images/Show%20Attendance%20List.png)
```
title Show Attendance List

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click attendance
Application->DB: Get attendance list
DB->Application: Return attendance list
Application->User: Show attendance list page
```
### Clock In

![Clock In Sequence Diagram](images/Clock%20In.png)
```
title Clock In

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click attendance
Application->DB: Get attendance list
DB->Application: Return attendance list
Application->User: Show attendance list page
User->Application: Click clock in
Application->DB: Insert attendance in by employee on that day
DB->Application: Return status

alt Insert failed
    Application->User: Show error message
else Insert success
    Application->User: Show successful clock in message
end
```
### Clock Out

![Clock Out Sequence Diagram](images/Clock%20Out.png)
```
title Clock Out

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click attendance
Application->DB: Get attendance list
DB->Application: Return attendance list
Application->User: Show attendance list page
User->Application: Click clock out
Application->DB: Insert attendance out by employee on that day
DB->Application: Return status

alt Insert failed
    Application->User: Show error message
else Insert success
    Application->User: Show successful clock out message
end
```
## Time Off Management

### Show Time Off List

![Show Time Off List Sequence Diagram](images/Show%20Time%20Off%20List.png)
```
title Show Time Off List

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click time off
Application->DB: Get time off list
DB->Application: Return time off list
Application->User: Show time off list page
```
### Request Time Off

![Request Time Off Sequence Diagram](images/Request%20Time%20Off.png)
```
title Request Time Off

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click time off
Application->DB: Get time off list
DB->Application: Return time off list
Application->User: Show time off list page
User->Application: Click request time off
Application->User: Show time off form page
User->Application: Input form
Application->DB: Insert time off
DB->Application: Return status

alt Insert failed
    Application->User: Show error message
else Insert success
    Application->User: Show successful insert time off message
end
```
### Time Off Approval

![Time Off Approval Sequence Diagram](images/Time%20Off%20Approval.png)
```
title Time Off Approval

User->Application: Sign In
Application->User: Show home page
User->Application: Click human resource
Application->User: Show human resource page
User->Application: Click time off approval
Application->DB: Get time off list by subordinate employee
DB->Application: Return time off list
Application->User: Show time off list for approval page
User->Application: Click detail
Application->User: Show time off approval form page
User->Application: Input form
Application->DB: Update time off status
DB->Application: Return status

alt Update failed
    Application->User: Show error message
else Update success
    Application->User: Show successful update time off status message
end
```
## AI-Powered Evaluation

### Annual Evaluation by AI

![Annual Evaluation by AI Sequence Diagram](images/Annual%20Evaluation%20by%20AI.png)
```
title Annual Evaluation by AI

User->Application: Sign In
Application->User: Show home page
User->Application: Click evaluation
Application->DB: Get evaluation list
DB->Application: Return evaluation list
Application->User: Show evaluation list page
User->Application: Click generate last year evaluation
Application->DB: Check last year evaluation
DB->Application: Return status

alt Exist
    Application->User: Show error message
else Not exist
    Application->LLM: Request evaluation by last year data
    LLM->Application: Return result
    Application->DB: Insert evaluation
    
    alt Insert failed
        Application->User: Show error message
    else Insert success
        Application->User: Show successful generate evaluation message
    end
end
```