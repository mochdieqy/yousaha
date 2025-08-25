@extends('layouts.home')

@section('content')
        

<div class="row">
    <div class="col-12">
        <!-- Company Information Header -->
        @if(isset($company))
        <div class="card text-white mb-4 border-0 shadow company-header-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-1 text-white fw-bold text-shadow-md">
                            <i class="fas fa-building me-2"></i>
                            {{ $company->name }}
                        </h4>
                        <p class="mb-0 text-white text-shadow-sm opacity-95">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            {{ $company->address }}
                            @if($company->phone)
                                <span class="ms-3">
                                    <i class="fas fa-phone me-1"></i>
                                    {{ $company->phone }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex flex-column align-items-md-end">
                            @if(Auth::user()->companies->contains('id', $company->id))
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark fw-bold shadow-sm">
                                        <i class="fas fa-crown me-1"></i>
                                        Company Owner
                                    </span>
                                </div>
                                @can('company.edit')
                                    <a href="{{ route('company.edit') }}" class="btn btn-outline-light btn-sm">
                                        <i class="fas fa-edit me-1"></i>
                                        Edit Company
                                    </a>
                                @endcan
                            @else
                                <span class="badge bg-info text-white fw-bold shadow-sm">
                                    <i class="fas fa-user-tie me-1"></i>
                                    Employee
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Stats Row - Only show if user has any relevant permissions -->
        @if(app('permissions')->userCanAny(['products.view', 'warehouses.view', 'stocks.view', 'sales-orders.view', 'purchase-orders.view', 'general-ledger.view', 'expenses.view', 'incomes.view', 'employees.view', 'attendances.view']))
        <div class="row mb-4">
            @can('products.view')
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-boxes fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">Inventory</h4>
                                <small>Stock Management</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            
            @can('sales-orders.view')
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">Sales</h4>
                                <small>Order Management</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            
            @can('purchase-orders.view')
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">Purchase</h4>
                                <small>Procurement</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            
            @can('general-ledger.view')
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">Finance</h4>
                                <small>Accounting</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>
        @endif

        <!-- Main Features Grid -->
        <div class="row">
            <!-- Inventory Management -->
            @if(app('permissions')->userCanAny(['products.view', 'warehouses.view', 'stocks.view', 'receipts.view', 'deliveries.view']))
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes text-primary me-2"></i>
                            Inventory Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('products.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('products.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-box text-muted me-2"></i>
                                        <span>Product Management</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('warehouses.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('warehouses.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-warehouse text-muted me-2"></i>
                                        <span>Warehouse Management</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('stocks.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('stocks.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-chart-bar text-muted me-2"></i>
                                        <span>Stock Tracking</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('receipts.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('receipts.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-truck text-muted me-2"></i>
                                        <span>Goods Receiving</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('deliveries.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('deliveries.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-truck text-muted me-2"></i>
                                        <span>Goods Issue</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Finance Management -->
            @if(app('permissions')->userCanAny(['general-ledger.view', 'accounts.view', 'expenses.view', 'incomes.view', 'internal-transfers.view', 'assets.view']))
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-money-bill-wave text-warning me-2"></i>
                            Finance Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('general-ledger.view')
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('general-ledger.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-book text-muted me-2"></i>
                                        <span>General Ledger</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('accounts.view')
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('accounts.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-chart-area text-muted me-2"></i>
                                        <span>Chart of Accounts</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('expenses.view')
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('expenses.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-invoice text-muted me-2"></i>
                                        <span>Expenses</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('incomes.view')
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('incomes.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-money-bill-wave text-muted me-2"></i>
                                        <span>Incomes</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('internal-transfers.view')
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('internal-transfers.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exchange-alt text-muted me-2"></i>
                                        <span>Internal Transfers</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('assets.view')
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('assets.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building text-muted me-2"></i>
                                        <span>Asset Management</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @if(app('permissions')->userCanAny(['general-ledger.view', 'expenses.view', 'incomes.view']))
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('financial-reports.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-chart-pie text-muted me-2"></i>
                                        <span>Financial Reports</span>
                                    </div>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Sales Management -->
            @if(app('permissions')->userCanAny(['sales-orders.view', 'customers.view', 'deliveries.view']))
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line text-success me-2"></i>
                            Sales Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('sales-orders.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('sales-orders.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-invoice text-muted me-2"></i>
                                        <span>Sales Orders</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('customers.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('customers.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users text-muted me-2"></i>
                                        <span>Customer Management</span>
                                    </div>
                                </a>
                            </div>
                            @endcan

                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Purchase Management -->
            @if(app('permissions')->userCanAny(['purchase-orders.view', 'suppliers.view', 'receipts.view', 'expenses.view']))
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart text-info me-2"></i>
                            Purchase Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('purchase-orders.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('purchase-orders.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-purchase text-muted me-2"></i>
                                        <span>Purchase Orders</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('suppliers.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('suppliers.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building text-muted me-2"></i>
                                        <span>Supplier Management</span>
                                    </div>
                                </a>
                            </div>
                            @endcan

                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Human Resources -->
            @if(app('permissions')->userCanAny(['employees.view', 'departments.view', 'attendances.view', 'time-offs.view', 'payrolls.view']))
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-users-cog text-secondary me-2"></i>
                            Human Resources
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('departments.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('departments.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-sitemap text-muted me-2"></i>
                                        <span>Department Management</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('employees.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('employees.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tie text-muted me-2"></i>
                                        <span>Employee Management</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('attendances.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('attendances.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-muted me-2"></i>
                                        <span>Attendance Tracking</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('time-offs.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('time-offs.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt text-muted me-2"></i>
                                        <span>Time Off Management</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('payrolls.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('payrolls.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-money-check-alt text-muted me-2"></i>
                                        <span>Payroll Management</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Advanced Settings -->
            @if(app('permissions')->userCanAny(['company.manage-employee-roles', 'attendances.view']))
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs text-primary me-2"></i>
                            Advanced Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('ai-evaluation.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('ai-evaluation.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-robot text-muted me-2"></i>
                                        <span>AI Evaluation</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                            @can('company.manage-employee-roles')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('employee-roles.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-shield text-muted me-2"></i>
                                        <span>Employee Access</span>
                                    </div>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            @endif


        </div>
    </div>
</div>

@endsection
