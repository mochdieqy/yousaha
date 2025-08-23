# User Accounts Summary

## Overview

This document provides a summary of all user accounts created by the `UserSeeder` for testing and demonstration purposes in the Yousaha ERP system. All users have the password: **`satu23empat`**

## User Accounts

### 1. Company Owner
- **Email**: `company.owner@yousaha.com`
- **Name**: Company Owner
- **Role**: Company Owner
- **Permissions**: All permissions (full system access)
- **Phone**: +62-812-3456-7890
- **Description**: Has complete access to all system modules and can manage company settings

### 2. Finance Manager
- **Email**: `finance.manager@yousaha.com`
- **Name**: Finance Manager
- **Role**: Finance Manager
- **Permissions**: 
  - Accounts management (view, create, edit)
  - General ledger (view, create, edit)
  - Expenses (view, create, edit)
  - Incomes (view, create, edit)
  - Internal transfers (view, create, edit)
  - Assets (view, create, edit)
  - Company view
- **Phone**: +62-812-3456-7891
- **Description**: Manages all financial aspects of the company

### 3. Sales Manager
- **Email**: `sales.manager@yousaha.com`
- **Name**: Sales Manager
- **Role**: Sales Manager
- **Permissions**:
  - Products (view, create, edit)
  - Customers (view, create, edit)
  - Sales orders (view, create, edit, approve, generate quotation/invoice)
  - Deliveries (view, create, edit)
  - Company view
- **Phone**: +62-812-3456-7892
- **Description**: Manages sales operations, customer relationships, and order processing

### 4. Purchase Manager
- **Email**: `purchase.manager@yousaha.com`
- **Name**: Purchase Manager
- **Role**: Purchase Manager
- **Permissions**:
  - Products (view, create, edit)
  - Suppliers (view, create, edit)
  - Purchase orders (view, create, edit, approve)
  - Receipts (view, create, edit)
  - Company view
- **Phone**: +62-812-3456-7893
- **Description**: Manages procurement, supplier relationships, and purchase operations

### 5. Inventory Manager
- **Email**: `inventory.manager@yousaha.com`
- **Name**: Inventory Manager
- **Role**: Inventory Manager
- **Permissions**:
  - Products (view, create, edit)
  - Warehouses (view, create, edit)
  - Stocks (view, create, edit)
  - Receipts (view, create, edit)
  - Deliveries (view, create, edit)
  - Company view
- **Phone**: +62-812-3456-7894
- **Description**: Manages inventory, warehouses, stock levels, and stock movements

### 6. HR Manager
- **Email**: `hr.manager@yousaha.com`
- **Name**: HR Manager
- **Role**: HR Manager
- **Permissions**:
  - Departments (view, create, edit)
  - Employees (view, create, edit)
  - Attendances (view, create, edit, approve)
  - Time offs (view, create, edit, approve)
  - Payrolls (view, create, edit)
  - Company management (view, manage employees, invite employees)
- **Phone**: +62-812-3456-7895
- **Description**: Manages human resources, employee records, and HR processes

### 7. Regular Employee
- **Email**: `employee@yousaha.com`
- **Name**: Regular Employee
- **Role**: Employee
- **Permissions**:
  - Products (view only)
  - Customers (view only)
  - Suppliers (view only)
  - Stocks (view only)
  - Sales orders (view, create)
  - Purchase orders (view, create)
  - Receipts (view, create)
  - Deliveries (view, create)
  - Attendances (view, create)
  - Time offs (view, create)
  - Company view
- **Phone**: +62-812-3456-7896
- **Description**: Basic user with permissions for daily operational tasks

### 8. System Viewer
- **Email**: `viewer@yousaha.com`
- **Name**: System Viewer
- **Role**: Viewer
- **Permissions**: Read-only access to most modules
  - Products (view only)
  - Customers (view only)
  - Suppliers (view only)
  - Warehouses (view only)
  - Stocks (view only)
  - Sales orders (view only)
  - Purchase orders (view only)
  - Receipts (view only)
  - Deliveries (view only)
  - Accounts (view only)
  - General ledger (view only)
  - Expenses (view only)
  - Incomes (view only)
  - Departments (view only)
  - Employees (view only)
  - Attendances (view only)
  - Time offs (view only)
  - Company view
- **Phone**: +62-812-3456-7897
- **Description**: Read-only access for reporting and monitoring purposes

## Company Information

- **Company Name**: Yousaha Demo Company
- **Owner**: Company Owner (company.owner@yousaha.com)
- **Address**: Jl. Demo Street No. 123, Jakarta
- **Phone**: +62-21-1234-5678
- **Website**: https://demo.yousaha.com

## Testing Scenarios

### Product Management Testing
- **Full Access**: Company Owner, Inventory Manager
- **Limited Access**: Sales Manager, Purchase Manager (view, create, edit)
- **View Only**: Finance Manager, HR Manager, Employee, Viewer

### Sales Operations Testing
- **Full Access**: Sales Manager
- **Limited Access**: Employee (view, create)
- **View Only**: Company Owner, Inventory Manager, Viewer

### Purchase Operations Testing
- **Full Access**: Purchase Manager
- **Limited Access**: Employee (view, create)
- **View Only**: Company Owner, Inventory Manager, Viewer

### Financial Operations Testing
- **Full Access**: Finance Manager
- **View Only**: Company Owner, Viewer

### HR Operations Testing
- **Full Access**: HR Manager
- **Limited Access**: Employee (view, create for own records)
- **View Only**: Company Owner, Viewer

## Security Notes

1. **Password**: All users share the same password for testing purposes
2. **Production**: Change passwords immediately in production environment
3. **Permissions**: Each role has carefully defined permissions following the principle of least privilege
4. **Company Isolation**: All users are associated with the demo company
5. **Role Assignment**: Users can only perform actions allowed by their assigned role

## Usage Instructions

1. **Login**: Use any of the email addresses above with password `satu23empat`
2. **Role Testing**: Test different permission levels by logging in with different users
3. **Product Management**: Use Inventory Manager or Company Owner for full product management testing
4. **Module Testing**: Each role can test different modules based on their permissions
5. **Navigation**: Sidebar navigation will show only modules the user has access to

## Maintenance

- **User Updates**: Modify user information through the UserSeeder
- **Role Changes**: Update role assignments in the RolePermissionSeeder
- **Permission Updates**: Modify permissions in the RolePermissionSeeder
- **Database Reset**: Run `php artisan migrate:fresh --seed` to recreate all users and permissions

## Conclusion

This user setup provides comprehensive testing coverage for all system modules with different permission levels. Each role represents a realistic business position with appropriate access rights, allowing thorough testing of the ERP system's security and functionality.
