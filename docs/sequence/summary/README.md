# Yousaha ERP - Summary Sequence Diagrams

This directory contains simplified summary sequence diagrams for the main functional modules of the Yousaha ERP system. All diagrams are formatted for [websequencediagrams.com](https://www.websequencediagrams.com/) and provide high-level overviews of system flows.

## 📋 Summary Sequence Diagram Categories

### 📦 [Inventory Management](inventory-management-summary.md)
Simplified inventory and warehouse operations flow:
- **Warehouse Management**: CRUD operations for warehouse setup
- **Stock Management**: Stock tracking, adjustments, and history
- **Receipt Management**: Goods receiving with stock updates
- **Delivery Management**: Goods issue with stock reduction

### 🛒 [Purchase Order Management](purchase-order-summary.md)
Simplified purchase order processing flow:
- **Order Management**: Complete CRUD operations for purchase orders
- **Product Lines**: Multiple products per order with quantities and prices
- **Status Workflow**: Status-based progression with validation
- **Financial Integration**: Automatic GL entries when status changes to "done"

### 📋 [Sales Order Management](sales-order-summary.md)
Simplified sales order processing flow:
- **Order Management**: Complete CRUD operations for sales orders
- **Product Lines**: Multiple products per order with quantities and prices
- **Status Workflow**: Status-based progression with validation
- **Financial Integration**: Automatic GL entries when status changes to "done"

### 👥 [Human Resources Management](human-resources-summary.md)
Simplified HR management operations flow:
- **Department Management**: CRUD operations for organizational structure
- **Employee Management**: Complete employee lifecycle management
- **Attendance Tracking**: Clock in/out with time tracking
- **Time Off Management**: Leave requests and approvals
- **Payroll Processing**: Salary calculations and processing

### 💰 [Finance Management](finance-management-summary.md)
Simplified financial management operations flow:
- **Chart of Accounts**: Complete account management with protection
- **General Ledger**: Double-entry bookkeeping system
- **Expense Management**: Expense tracking with GL integration
- **Income Management**: Income recording with GL integration
- **Internal Transfer**: Inter-account transfers
- **Financial Reports**: Report generation and PDF export

### 🤖 [AI Evaluation Management](ai-evaluation-summary.md)
Simplified AI-powered evaluation operations flow:
- **AI-Powered Evaluation**: Automated performance assessment
- **Evaluation Categories**: Multiple evaluation types and criteria
- **AI Content Generation**: LLM-based evaluation content
- **Content Regeneration**: Ability to regenerate AI insights

## 🔄 Simplified Architecture Overview

### System Components
- **Frontend**: User interface (Bootstrap-based views)
- **Backend**: Laravel controllers and business logic
- **Database**: MySQL database with transactions
- **Auth**: Authentication and company access control
- **Validator**: Input validation and business rules
- **Services**: External service integrations (AI, PDF, etc.)

### Common Flow Pattern
All modules follow a consistent pattern:
1. **Access Control**: Company-based authentication
2. **Data Retrieval**: Query database with relationships
3. **User Action**: Perform CRUD or workflow operations
4. **Validation**: Input validation and business rules
5. **Transaction**: Database operations with rollback
6. **Response**: Success/error feedback to user

### Key Business Rules
- **Company Isolation**: Multi-tenant data separation
- **Transaction Safety**: Database transactions with rollback
- **Validation**: Comprehensive input validation
- **Dependency Checks**: Prevents deletion of referenced data
- **Status Workflows**: Status-based progression control
- **Financial Integration**: Automatic GL entries for transactions

## 🚀 Usage Instructions

### For Developers
1. Use these summary diagrams to understand high-level system flows
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

- **Total Summary Sequences**: 6 major functional areas
- **Modules Covered**: All core ERP functions
- **Process Types**: CRUD operations, workflows, integrations
- **Error Scenarios**: Comprehensive error handling coverage
- **Format Support**: Both documentation and editable (text) formats

## 🔗 Related Documentation

- [Detailed Sequence Diagrams](../README.md) - Comprehensive sequence diagrams
- [System Documentation](../../SYSTEM_DOCUMENTATION.md) - Business process details
- [Technical Implementation](../../TECHNICAL_IMPLEMENTATION.md) - Code implementation
- [API Documentation](../../API_DOCUMENTATION.md) - REST API specifications
- [User Guide](../../USER_GUIDE.md) - End-user instructions

---

**Note**: These summary sequence diagrams provide high-level overviews of the Yousaha ERP system flows. For detailed implementation specifics, refer to the comprehensive sequence diagrams in the parent directory. All diagrams are based on the current implementation in the Controllers and Views.
