# Yousaha ERP - Sequence Diagrams

This directory contains sequence diagrams organized by functional modules. All diagrams are formatted for [websequencediagrams.com](https://www.websequencediagrams.com/) and include comprehensive documentation for easy reference.

## 📋 Sequence Diagram Categories

### 🔐 [Authentication](authentication.md)
User authentication and account management flows:
- **User Registration** - Complete registration with email verification
- **User Login** - Authentication with verification check
- **Email Verification** - Token-based email verification
- **Password Reset** - Secure password reset workflow
- **Profile Management** - User profile and password updates

### 🏢 [Company Management](company-management.md)
Company setup and profile management:
- **Company Choice** - User selection between create/join
- **Company Creation** - Initial company setup with finance accounts
- **Company Editing** - Company profile modifications
- **Employee Invitation** - Employee invitation system

### 📊 [Master Data Management](master-data.md)
Core business data management:
- **Product Management** - CRUD operations with business rules
- **Customer Management** - Customer data management
- **Supplier Management** - Supplier data management
- **Warehouse Management** - Warehouse setup and management

### 📦 [Inventory Management](inventory-management.md)
Complete inventory and warehouse operations:
- **Warehouse Management** - Warehouse CRUD operations
- **Stock Management** - Stock tracking and adjustments
- **Receipt Management** - Goods receiving processes
- **Delivery Management** - Goods issue and delivery

### 💰 [Sales Management](sales-management.md)
Sales order processing and document generation:
- **Sales Order Management** - Complete sales order lifecycle
- **Status Management** - Status-based workflow control
- **Document Generation** - Quotation and invoice creation

### 🛒 [Purchase Management](purchase-management.md)
Purchase order processing and management:
- **Purchase Order Management** - Complete purchase order lifecycle
- **Status Management** - Status-based workflow control
- **Receipt Integration** - Seamless order-to-receipt flow

### 💼 [Finance Management](finance-management.md)
Complete financial management system:
- **Chart of Accounts** - Account management with protection
- **General Ledger** - Double-entry bookkeeping
- **Expense Management** - Expense tracking and GL integration
- **Income Management** - Income recording and GL integration
- **Internal Transfer Management** - Inter-account transfers
- **Financial Reports** - Report generation and PDF export

### 👥 [Human Resources](human-resources.md)
HR management and employee operations:
- **Department Management** - Department setup and management
- **Employee Management** - Employee lifecycle management
- **Attendance Management** - Clock in/out and time tracking
- **Time Off Management** - Leave requests and approvals
- **Payroll Management** - Salary calculation and processing
- **AI-Powered Evaluation** - Performance evaluations with AI

## 🔄 Process Flow Overview

### Core Business Processes

1. **User Onboarding**
   - Authentication → Company Setup → Master Data Setup

2. **Daily Operations**
   - Sales Orders → Deliveries → Income Recording
   - Purchase Orders → Receipts → Expense Recording
   - Attendance Tracking → Time Off Management

3. **Financial Management**
   - Transaction Recording → General Ledger → Financial Reports

4. **HR Operations**
   - Employee Management → Attendance → Performance Evaluation

## 📝 Diagram Format

Each sequence diagram includes:
- **Comprehensive documentation** with detailed descriptions
- **Source code** in websequencediagrams.com format for editing
- **Key features** highlighting important functionality
- **Business logic** explanations
- **Integration points** with other systems

All sequence diagrams use the websequencediagrams.com format:
```
title Diagram Title

Actor->System: Action
System->Database: Query
Database->System: Response

alt Condition
    System->Actor: Success Response
else Alternative
    System->Actor: Error Response
end
```

## 🔍 Key Features Demonstrated

### Transaction Management
- Database transactions with rollback capabilities
- ACID compliance in critical operations
- Error handling and recovery

### Business Logic
- Status-based workflow controls
- Validation and business rule enforcement
- Automated process triggers

### Integration Points
- SMTP email integration
- AI/LLM integration for evaluations
- Multi-system data synchronization

### Security & Access Control
- Authentication and authorization flows
- Data validation and sanitization
- Company-based data isolation

## 🚀 Usage Instructions

### For Developers
1. Use these diagrams to understand system flows
2. Reference for implementing business logic
3. Validate transaction boundaries and error handling

### For Business Analysts
1. Review process flows for business requirements
2. Validate business rules and workflows
3. Document process improvements

### For Testing
1. Create test scenarios based on sequence flows
2. Validate error conditions and edge cases
3. Ensure complete process coverage

## 📊 Diagram Statistics

- **Total Sequences**: 50+ individual sequence diagrams
- **Modules Covered**: 8 major functional areas  
- **Process Types**: CRUD operations, workflows, integrations
- **Error Scenarios**: Comprehensive error handling coverage
- **Format Support**: Both documentation and editable (text) formats

## 🔗 Related Documentation

- [System Documentation](../SYSTEM_DOCUMENTATION.md) - Business process details
- [Technical Implementation](../TECHNICAL_IMPLEMENTATION.md) - Code implementation
- [API Documentation](../API_DOCUMENTATION.md) - REST API specifications
- [User Guide](../USER_GUIDE.md) - End-user instructions

---

**Note**: These sequence diagrams represent the complete business process flows for the Yousaha ERP system and serve as the foundation for system implementation and testing. All diagrams are based on the current implementation in the Controllers and Views.
