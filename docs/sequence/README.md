# Yousaha ERP - Sequence Diagrams

This directory contains sequence diagrams organized by functional modules. All diagrams are formatted for [websequencediagrams.com](https://www.websequencediagrams.com/) and include visual diagram images for easy reference.

## 📋 Sequence Diagram Categories

### 🔐 [Authentication](authentication.md)
User authentication and account management flows:
- **Sign Up** - User registration with email verification
- **Sign In** - User login with validation
- **Forgot Password** - Password reset workflow

### 🏢 [Company Management](company-management.md)
Company setup and profile management:
- **Create Company** - Initial company setup after registration
- **Update Company** - Company profile modifications

### 📊 [Master Data Management](master-data.md)
Core business data management:
- **Product Management** - CRUD operations for products
- **Customer Management** - Customer data management
- **Supplier Management** - Supplier data management

### 📦 [Inventory Management](inventory-management.md)
Complete inventory and warehouse operations:
- **Warehouse Management** - Warehouse setup and management
- **Stock Management** - Stock tracking and adjustments
- **Receipt Management** - Goods receiving processes
- **Delivery Management** - Goods issue and delivery

### 💰 [Sales Management](sales-management.md)
Sales order processing and document generation:
- **Sales Order Management** - Sales order lifecycle
- **Document Generation** - Quotation and invoice creation

### 🛒 [Purchase Management](purchase-management.md)
Purchase order processing:
- **Purchase Order Management** - Purchase order lifecycle

### 💼 [Finance Management](finance-management.md)
Complete financial management system:
- **Chart of Accounts** - Account management
- **General Ledger** - Double-entry bookkeeping
- **Expense Management** - Expense tracking
- **Income Management** - Income recording
- **Internal Transfer Management** - Inter-account transfers
- **Asset Management** - Asset tracking
- **Financial Reports** - Report generation and exports

### 👥 [Human Resources](human-resources.md)
HR management and employee operations:
- **Department Management** - Department setup
- **Employee Management** - Employee data management
- **Attendance Management** - Clock in/out and attendance tracking
- **Time Off Management** - Leave requests and approvals
- **AI-Powered Evaluation** - Annual performance evaluations

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
- **Visual diagram image** for quick reference and presentation
- **Source code** in websequencediagrams.com format for editing

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

- **Total Sequences**: 54 individual sequence diagrams
- **Visual Images**: 72+ diagram images included
- **Modules Covered**: 8 major functional areas  
- **Process Types**: CRUD operations, workflows, integrations
- **Error Scenarios**: Comprehensive error handling coverage
- **Format Support**: Both visual (PNG) and editable (text) formats

## 🔗 Related Documentation

- [System Documentation](../SYSTEM_DOCUMENTATION.md) - Business process details
- [Technical Implementation](../TECHNICAL_IMPLEMENTATION.md) - Code implementation
- [API Documentation](../API_DOCUMENTATION.md) - REST API specifications
- [User Guide](../USER_GUIDE.md) - End-user instructions

---

**Note**: These sequence diagrams represent the complete business process flows for the Yousaha ERP system and serve as the foundation for system implementation and testing.
