# Human Resources Sequence Diagrams

This document contains sequence diagrams for HR management and employee operations flows in the Yousaha ERP system.

## 🏢 Department Management Flow

### Department Listing Process
**Description**: Display department list with company isolation

```sequence
title Department Listing Flow

User->Frontend: Access departments page
Frontend->DepartmentController: GET /departments
DepartmentController->Auth: Check company access
Auth->DepartmentController: Company status

alt No company access
    DepartmentController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    DepartmentController->Department: Query departments by company
    Department->DepartmentController: Department list
    DepartmentController->Frontend: Return department view
    Frontend->User: Display department list
end
```

**Key Features**:
- Company-based data isolation
- Department data display
- Access control

### Department Creation Process
**Description**: Create new department with validation

```sequence
title Department Creation Flow

User->Frontend: Access create department form
Frontend->DepartmentController: GET /departments/create
DepartmentController->Auth: Check company access
Auth->DepartmentController: Company status

alt No company access
    DepartmentController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    DepartmentController->Frontend: Return create form
    Frontend->User: Display department creation form
end

User->Frontend: Submit department data
Frontend->DepartmentController: POST /departments
DepartmentController->Validator: Validate department data
Validator->DepartmentController: Validation result

alt Validation fails
    DepartmentController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    DepartmentController->Department: Create department record
    Department->DepartmentController: Department created
    DepartmentController->Frontend: Success redirect
    Frontend->User: Redirect to department list
end
```

**Key Features**:
- Department data validation
- Company association
- Success confirmation

### Department Editing Process
**Description**: Edit existing department with access control

```sequence
title Department Edit Flow

User->Frontend: Access edit department form
Frontend->DepartmentController: GET /departments/{id}/edit
DepartmentController->Auth: Check company access
Auth->DepartmentController: Company status

alt No company access
    DepartmentController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    DepartmentController->Department: Get department by ID
    Department->DepartmentController: Department data
    
    alt Department not found
        DepartmentController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Department found
        DepartmentController->Department: Verify company ownership
        Department->DepartmentController: Ownership status
        
        alt Wrong company
            DepartmentController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            DepartmentController->Frontend: Return edit form
            Frontend->User: Display edit form with data
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Department data retrieval
- Form pre-population

### Department Update Process
**Description**: Save department changes with validation

```sequence
title Department Update Flow

User->Frontend: Submit department updates
Frontend->DepartmentController: PUT /departments/{id}
DepartmentController->Auth: Check company access
Auth->DepartmentController: Company status

alt No company access
    DepartmentController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    DepartmentController->Department: Get department by ID
    Department->DepartmentController: Department data
    
    alt Department not found
        DepartmentController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Department found
        DepartmentController->Department: Verify company ownership
        Department->DepartmentController: Ownership status
        
        alt Wrong company
            DepartmentController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            DepartmentController->Validator: Validate update data
            Validator->DepartmentController: Validation result
            
            alt Validation fails
                DepartmentController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                DepartmentController->Department: Update department fields
                Department->DepartmentController: Changes saved
                DepartmentController->Frontend: Success redirect
                Frontend->User: Redirect to department list
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Data validation
- Secure update process

## 👥 Employee Management Flow

### Employee Listing Process
**Description**: Display employee list with company isolation

```sequence
title Employee Listing Flow

User->Frontend: Access employees page
Frontend->EmployeeController: GET /employees
EmployeeController->Auth: Check company access
Auth->EmployeeController: Company status

alt No company access
    EmployeeController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    EmployeeController->Employee: Query employees by company
    Employee->EmployeeController: Employee list with relationships
    EmployeeController->Frontend: Return employee view
    Frontend->User: Display employee list
end
```

**Key Features**:
- Company-based data isolation
- Relationship loading (user, department, manager)
- Access control

### Employee Creation Process
**Description**: Create new employee with validation

```sequence
title Employee Creation Flow

User->Frontend: Access create employee form
Frontend->EmployeeController: GET /employees/create
EmployeeController->Auth: Check company access
Auth->EmployeeController: Company status

alt No company access
    EmployeeController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    EmployeeController->Department: Get departments by company
    Department->EmployeeController: Department list
    EmployeeController->Frontend: Return create form with departments
    Frontend->User: Display employee creation form
end

User->Frontend: Submit employee data
Frontend->EmployeeController: POST /employees
EmployeeController->Validator: Validate employee data
Validator->EmployeeController: Validation result

alt Validation fails
    EmployeeController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    EmployeeController->User: Find user by email
    User->EmployeeController: User data
    
    alt User not found
        EmployeeController->Frontend: Return with error
        Frontend->User: Display user not found message
    else User found
        EmployeeController->Employee: Check if user already employed
        Employee->EmployeeController: Employment status
        
        alt Already employed
            EmployeeController->Frontend: Return with error
            Frontend->User: Display already employed message
        else Not employed
            EmployeeController->Employee: Create employee record
            Employee->EmployeeController: Employee created
            EmployeeController->Role: Assign Employee role
            Role->EmployeeController: Role assigned
            EmployeeController->Frontend: Success redirect
            Frontend->User: Redirect to employee list
        end
    end
end
```

**Key Features**:
- User existence validation
- Duplicate employment prevention
- Role assignment
- Success confirmation

### Employee Editing Process
**Description**: Edit existing employee with access control

```sequence
title Employee Edit Flow

User->Frontend: Access edit employee form
Frontend->EmployeeController: GET /employees/{id}/edit
EmployeeController->Auth: Check company access
Auth->EmployeeController: Company status

alt No company access
    EmployeeController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    EmployeeController->Employee: Get employee by ID
    Employee->EmployeeController: Employee data
    
    alt Employee not found
        EmployeeController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Employee found
        EmployeeController->Employee: Verify company ownership
        Employee->EmployeeController: Ownership status
        
        alt Wrong company
            EmployeeController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            EmployeeController->Department: Get departments by company
            Department->EmployeeController: Department list
            EmployeeController->Employee: Get manager options
            Employee->EmployeeController: Manager list
            EmployeeController->Frontend: Return edit form with data
            Frontend->User: Display edit form with current data
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Employee data retrieval
- Form pre-population
- Manager selection

### Employee Update Process
**Description**: Save employee changes with validation

```sequence
title Employee Update Flow

User->Frontend: Submit employee updates
Frontend->EmployeeController: PUT /employees/{id}
EmployeeController->Auth: Check company access
Auth->EmployeeController: Company status

alt No company access
    EmployeeController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    EmployeeController->Employee: Get employee by ID
    Employee->EmployeeController: Employee data
    
    alt Employee not found
        EmployeeController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Employee found
        EmployeeController->Employee: Verify company ownership
        Employee->EmployeeController: Ownership status
        
        alt Wrong company
            EmployeeController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            EmployeeController->Validator: Validate update data
            Validator->EmployeeController: Validation result
            
            alt Validation fails
                EmployeeController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                EmployeeController->Employee: Update employee fields
                Employee->EmployeeController: Changes saved
                EmployeeController->Frontend: Success redirect
                Frontend->User: Redirect to employee list
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Data validation
- Secure update process

## ⏰ Attendance Management Flow

### Attendance Listing Process
**Description**: Display attendance list with company isolation

```sequence
title Attendance Listing Flow

User->Frontend: Access attendance page
Frontend->AttendanceController: GET /attendances
AttendanceController->Auth: Check company access
Auth->AttendanceController: Company status

alt No company access
    AttendanceController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AttendanceController->Attendance: Query attendance by company
    Attendance->AttendanceController: Attendance list with relationships
    AttendanceController->Frontend: Return attendance view
    Frontend->User: Display attendance list
end
```

**Key Features**:
- Company-based data isolation
- Relationship loading
- Access control

### Clock In Process
**Description**: Record employee clock in with validation

```sequence
title Clock In Flow

User->Frontend: Request clock in
Frontend->AttendanceController: POST /attendances/clock-in
AttendanceController->Auth: Check company access
Auth->AttendanceController: Company status

alt No company access
    AttendanceController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AttendanceController->Employee: Get employee by user
    Employee->AttendanceController: Employee data
    
    alt Employee not found
        AttendanceController->Frontend: Return with error
        Frontend->User: Display employee not found message
    else Employee found
        AttendanceController->Attendance: Check existing attendance
        Attendance->AttendanceController: Existing attendance
        
        alt Already clocked in today
            AttendanceController->Frontend: Return with error
            Frontend->User: Display already clocked in message
        else Not clocked in today
            AttendanceController->Attendance: Create attendance record
            Attendance->AttendanceController: Attendance created
            AttendanceController->Frontend: Success response
            Frontend->User: Display clock in success
        end
    end
end
```

**Key Features**:
- Duplicate clock in prevention
- Employee validation
- Success confirmation

### Clock Out Process
**Description**: Record employee clock out with validation

```sequence
title Clock Out Flow

User->Frontend: Request clock out
Frontend->AttendanceController: POST /attendances/clock-out
AttendanceController->Auth: Check company access
Auth->AttendanceController: Company status

alt No company access
    AttendanceController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AttendanceController->Employee: Get employee by user
    Employee->AttendanceController: Employee data
    
    alt Employee not found
        AttendanceController->Frontend: Return with error
        Frontend->User: Display employee not found message
    else Employee found
        AttendanceController->Attendance: Get today's attendance
        Attendance->AttendanceController: Attendance data
        
        alt No attendance found
            AttendanceController->Frontend: Return with error
            Frontend->User: Display no clock in found message
        else Attendance found
            alt Already clocked out
                AttendanceController->Frontend: Return with error
                Frontend->User: Display already clocked out message
            else Not clocked out
                AttendanceController->Attendance: Update clock out time
                Attendance->AttendanceController: Clock out recorded
                AttendanceController->Attendance: Calculate work hours
                Attendance->AttendanceController: Hours calculated
                AttendanceController->Frontend: Success response
                Frontend->User: Display clock out success
            end
        end
    end
end
```

**Key Features**:
- Clock in requirement validation
- Duplicate clock out prevention
- Work hours calculation
- Success confirmation

## 🏖️ Time Off Management Flow

### Time Off Listing Process
**Description**: Display time off requests with company isolation

```sequence
title Time Off Listing Flow

User->Frontend: Access time off page
Frontend->TimeOffController: GET /time-offs
TimeOffController->Auth: Check company access
Auth->TimeOffController: Company status

alt No company access
    TimeOffController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    TimeOffController->TimeOff: Query time off requests by company
    TimeOff->TimeOffController: Time off list with relationships
    TimeOffController->Frontend: Return time off view
    Frontend->User: Display time off list
end
```

**Key Features**:
- Company-based data isolation
- Relationship loading
- Access control

### Time Off Creation Process
**Description**: Create new time off request with validation

```sequence
title Time Off Creation Flow

User->Frontend: Access create time off form
Frontend->TimeOffController: GET /time-offs/create
TimeOffController->Auth: Check company access
Auth->TimeOffController: Company status

alt No company access
    TimeOffController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    TimeOffController->Frontend: Return create form
    Frontend->User: Display time off creation form
end

User->Frontend: Submit time off data
Frontend->TimeOffController: POST /time-offs
TimeOffController->Validator: Validate time off data
Validator->TimeOffController: Validation result

alt Validation fails
    TimeOffController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    TimeOffController->TimeOff: Create time off request
    TimeOff->TimeOffController: Request created
    TimeOffController->Frontend: Success redirect
    Frontend->User: Redirect to time off list
end
```

**Key Features**:
- Time off data validation
- Company association
- Success confirmation

### Time Off Approval Process
**Description**: Approve or reject time off requests

```sequence
title Time Off Approval Flow

User->Frontend: Request time off approval
Frontend->TimeOffController: POST /time-offs/{id}/approve
TimeOffController->Auth: Check company access
Auth->TimeOffController: Company status

alt No company access
    TimeOffController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    TimeOffController->TimeOff: Get time off request by ID
    TimeOff->TimeOffController: Request data
    
    alt Request not found
        TimeOffController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Request found
        TimeOffController->TimeOff: Verify company ownership
        TimeOff->TimeOffController: Ownership status
        
        alt Wrong company
            TimeOffController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            TimeOffController->Validator: Validate approval data
            Validator->TimeOffController: Validation result
            
            alt Validation fails
                TimeOffController->Frontend: Return with errors
                Frontend->User: Display error messages
            else Validation passes
                TimeOffController->TimeOff: Update approval status
                TimeOff->TimeOffController: Status updated
                TimeOffController->Frontend: Success response
                Frontend->User: Display approval success
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Approval validation
- Status updates
- Success confirmation

## 💰 Payroll Management Flow

### Payroll Listing Process
**Description**: Display payroll list with company isolation

```sequence
title Payroll Listing Flow

User->Frontend: Access payroll page
Frontend->PayrollController: GET /payrolls
PayrollController->Auth: Check company access
Auth->PayrollController: Company status

alt No company access
    PayrollController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    PayrollController->Payroll: Query payroll by company
    Payroll->PayrollController: Payroll list with relationships
    PayrollController->Frontend: Return payroll view
    Frontend->User: Display payroll list
end
```

**Key Features**:
- Company-based data isolation
- Relationship loading
- Access control

### Payroll Creation Process
**Description**: Create new payroll with calculations

```sequence
title Payroll Creation Flow

User->Frontend: Access create payroll form
Frontend->PayrollController: GET /payrolls/create
PayrollController->Auth: Check company access
Auth->PayrollController: Company status

alt No company access
    PayrollController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    PayrollController->Employee: Get employees by company
    Employee->PayrollController: Employee list
    PayrollController->Frontend: Return create form with employees
    Frontend->User: Display payroll creation form
end

User->Frontend: Submit payroll data
Frontend->PayrollController: POST /payrolls
PayrollController->Validator: Validate payroll data
Validator->PayrollController: Validation result

alt Validation fails
    PayrollController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    PayrollController->DB: Begin transaction
    DB->PayrollController: Transaction started
    
    PayrollController->Payroll: Create payroll record
    Payroll->PayrollController: Payroll created
    
    PayrollController->Attendance: Calculate work hours
    Attendance->PayrollController: Hours calculated
    
    PayrollController->Payroll: Calculate salary
    Payroll->PayrollController: Salary calculated
    
    PayrollController->DB: Commit transaction
    DB->PayrollController: Transaction committed
    
    PayrollController->Frontend: Success redirect
    Frontend->User: Redirect to payroll list
end
```

**Key Features**:
- Payroll data validation
- Work hours calculation
- Salary computation
- Transaction safety

## 🤖 AI-Powered Evaluation Flow

### AI Evaluation Listing Process
**Description**: Display AI evaluations with company isolation

```sequence
title AI Evaluation Listing Flow

User->Frontend: Access AI evaluations page
Frontend->AIEvaluationController: GET /ai-evaluations
AIEvaluationController->Auth: Check company access
Auth->AIEvaluationController: Company status

alt No company access
    AIEvaluationController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AIEvaluationController->AIEvaluation: Query evaluations by company
    AIEvaluation->AIEvaluationController: Evaluation list
    AIEvaluationController->AIEvaluation: Get evaluation categories
    AIEvaluation->AIEvaluationController: Categories
    AIEvaluationController->Frontend: Return evaluation view
    Frontend->User: Display AI evaluation list
end
```

**Key Features**:
- Company-based data isolation
- Category management
- Access control

### AI Evaluation Creation Process
**Description**: Create new AI evaluation with AI service integration

```sequence
title AI Evaluation Creation Flow

User->Frontend: Access create AI evaluation form
Frontend->AIEvaluationController: GET /ai-evaluations/create
AIEvaluationController->Auth: Check company access
Auth->AIEvaluationController: Company status

alt No company access
    AIEvaluationController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AIEvaluationController->AIEvaluation: Get evaluation categories
    AIEvaluation->AIEvaluationController: Categories
    AIEvaluationController->Frontend: Return create form with categories
    Frontend->User: Display AI evaluation creation form
end

User->Frontend: Submit evaluation data
Frontend->AIEvaluationController: POST /ai-evaluations
AIEvaluationController->Validator: Validate evaluation data
Validator->AIEvaluationController: Validation result

alt Validation fails
    AIEvaluationController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    AIEvaluationController->DB: Begin transaction
    DB->AIEvaluationController: Transaction started
    
    AIEvaluationController->AIEvaluation: Create evaluation record
    AIEvaluation->AIEvaluationController: Evaluation created
    
    AIEvaluationController->AIEvaluationService: Generate AI evaluation
    AIEvaluationService->AIEvaluationController: Evaluation generated
    
    AIEvaluationController->AIEvaluation: Update with AI content
    AIEvaluation->AIEvaluationController: Content updated
    
    AIEvaluationController->DB: Commit transaction
    DB->AIEvaluationController: Transaction committed
    
    AIEvaluationController->Frontend: Success redirect
    Frontend->User: Redirect to evaluation list
end
```

**Key Features**:
- Evaluation data validation
- AI service integration
- Content generation
- Transaction safety

### AI Evaluation Editing Process
**Description**: Edit existing AI evaluation with access control

```sequence
title AI Evaluation Edit Flow

User->Frontend: Access edit AI evaluation form
Frontend->AIEvaluationController: GET /ai-evaluations/{id}/edit
AIEvaluationController->Auth: Check company access
Auth->AIEvaluationController: Company status

alt No company access
    AIEvaluationController->Frontend: Redirect to company choice
    Frontend->User: Company setup required
else Company access granted
    AIEvaluationController->AIEvaluation: Get evaluation by ID
    AIEvaluation->AIEvaluationController: Evaluation data
    
    alt Evaluation not found
        AIEvaluationController->Frontend: Return 404 error
        Frontend->User: Display not found message
    else Evaluation found
        AIEvaluationController->AIEvaluation: Verify company ownership
        AIEvaluation->AIEvaluationController: Ownership status
        
        alt Wrong company
            AIEvaluationController->Frontend: Return 403 error
            Frontend->User: Display access denied
        else Correct company
            AIEvaluationController->AIEvaluation: Check editability
            AIEvaluation->AIEvaluationController: Editability status
            
            alt Evaluation not editable
                AIEvaluationController->Frontend: Return with error
                Frontend->User: Display edit restriction message
            else Evaluation editable
                AIEvaluationController->AIEvaluation: Get evaluation categories
                AIEvaluation->AIEvaluationController: Categories
                AIEvaluationController->Frontend: Return edit form with data
                Frontend->User: Display edit form with current data
            end
        end
    end
end
```

**Key Features**:
- Company ownership verification
- Editability validation
- Evaluation data retrieval
- Form pre-population

## 🔐 Access Control

### Company-Based Isolation
- All HR data scoped to user's company
- Automatic company association
- Cross-company access prevention
- Permission enforcement

### Role-Based Permissions
- Employee role assignment
- Manager access levels
- Approval workflows
- Data visibility control

## 📊 Business Logic

### Employee Management Rules
- User existence validation
- Duplicate employment prevention
- Department assignment
- Manager relationships

### Attendance Rules
- Single clock in per day
- Clock out requirement
- Work hours calculation
- Time tracking accuracy

### Time Off Rules
- Request validation
- Approval workflows
- Status management
- Company policies

### Payroll Rules
- Work hours calculation
- Salary computation
- Tax calculations
- Payment processing

### AI Evaluation Rules
- Category validation
- Content generation
- Edit restrictions
- Quality assurance

## 🔄 Data Relationships

### HR Components
- Department structure
- Employee profiles
- Attendance records
- Time off requests
- Payroll calculations
- AI evaluations

### Integration Points
- User management
- Company structure
- Financial system
- AI services
- Reporting system

## 📱 User Experience

### Form Handling
- Dynamic data loading
- Real-time validation
- Error handling
- Success feedback

### Workflow Management
- Approval processes
- Status tracking
- Notification system
- User guidance

---

**Note**: Human resources management provides comprehensive employee lifecycle management with attendance tracking, time off management, payroll processing, and AI-powered performance evaluations.