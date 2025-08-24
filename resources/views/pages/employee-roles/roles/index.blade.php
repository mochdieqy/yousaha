@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-user-tag me-2"></i>
                    Available Roles
                </h2>
                <a href="{{ route('employee-roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to Employee Roles
                </a>
            </div>

            <div class="row">
                @foreach($roles as $role)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-shield me-2"></i>
                                    {{ $role->name }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="text-muted">Description</h6>
                                    <p class="mb-0">
                                        @if($role->name === 'Company Owner')
                                            Full system access with all permissions. Can manage company settings, employees, and assign roles.
                                        @elseif($role->name === 'Finance Manager')
                                            Manages financial operations including accounts, general ledger, expenses, incomes, and financial reports.
                                        @elseif($role->name === 'Sales Manager')
                                            Handles sales operations, customer management, sales orders, and delivery management.
                                        @elseif($role->name === 'Purchase Manager')
                                            Manages procurement, supplier relationships, purchase orders, and goods receiving.
                                        @elseif($role->name === 'Inventory Manager')
                                            Controls inventory, warehouse management, stock tracking, and goods movement.
                                        @elseif($role->name === 'HR Manager')
                                            Manages human resources including employees, departments, attendance, time-offs, and payroll.
                                        @elseif($role->name === 'Employee')
                                            Basic access for daily operations including attendance tracking and time-off requests.
                                        @elseif($role->name === 'Viewer')
                                            Read-only access to most modules for monitoring and reporting purposes.
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-muted">Key Permissions</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($role->name === 'Company Owner')
                                            <span class="badge bg-success">All Permissions</span>
                                        @elseif($role->name === 'Finance Manager')
                                            <span class="badge bg-primary">Accounts</span>
                                            <span class="badge bg-primary">General Ledger</span>
                                            <span class="badge bg-primary">Expenses</span>
                                            <span class="badge bg-primary">Incomes</span>
                                            <span class="badge bg-primary">Reports</span>
                                        @elseif($role->name === 'Sales Manager')
                                            <span class="badge bg-success">Sales Orders</span>
                                            <span class="badge bg-success">Customers</span>
                                            <span class="badge bg-success">Deliveries</span>
                                            <span class="badge bg-success">Products</span>
                                        @elseif($role->name === 'Purchase Manager')
                                            <span class="badge bg-info">Purchase Orders</span>
                                            <span class="badge bg-info">Suppliers</span>
                                            <span class="badge bg-info">Receipts</span>
                                            <span class="badge bg-info">Products</span>
                                        @elseif($role->name === 'Inventory Manager')
                                            <span class="badge bg-warning">Warehouses</span>
                                            <span class="badge bg-warning">Stocks</span>
                                            <span class="badge bg-warning">Receipts</span>
                                            <span class="badge bg-warning">Deliveries</span>
                                        @elseif($role->name === 'HR Manager')
                                            <span class="badge bg-secondary">Employees</span>
                                            <span class="badge bg-secondary">Departments</span>
                                            <span class="badge bg-secondary">Attendance</span>
                                            <span class="badge bg-secondary">Payroll</span>
                                        @elseif($role->name === 'Employee')
                                            <span class="badge bg-light text-dark">Attendance</span>
                                            <span class="badge bg-light text-dark">Time Off</span>
                                            <span class="badge bg-light text-dark">Basic View</span>
                                        @elseif($role->name === 'Viewer')
                                            <span class="badge bg-light text-dark">Read Only</span>
                                            <span class="badge bg-light text-dark">Reports</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('employee-roles.create') }}" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-plus me-1"></i>
                                    Assign This Role
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
