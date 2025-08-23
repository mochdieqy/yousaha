@extends('layouts.home')

@section('content')
        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Error Message -->
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

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
                                <i class="fas fa-dollar-sign fa-2x"></i>
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
                            @if(app('permissions')->userCanAny(['receipts.view', 'deliveries.view']))
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('receipts.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exchange-alt text-muted me-2"></i>
                                        <span>Stock Transfers</span>
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
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-invoice text-muted me-2"></i>
                                    <span>Sales Orders</span>
                                </div>
                            </div>
                            @endcan
                            @can('deliveries.view')
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-truck text-muted me-2"></i>
                                    <span>Delivery Management</span>
                                </div>
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
                            @if(app('permissions')->userCanAny(['sales-orders.generate-quotation', 'sales-orders.generate-invoice']))
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-muted me-2"></i>
                                    <span>Quotations & Invoices</span>
                                </div>
                            </div>
                            @endif
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
                            @can('receipts.view')
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('receipts.index') }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-receipt text-muted me-2"></i>
                                        <span>Goods Receiving</span>
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
                            @can('expenses.view')
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-pie text-muted me-2"></i>
                                    <span>Expense Tracking</span>
                                </div>
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
                            <i class="fas fa-dollar-sign text-warning me-2"></i>
                            Finance Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('general-ledger.view')
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-book text-muted me-2"></i>
                                    <span>General Ledger</span>
                                </div>
                            </div>
                            @endcan
                            @can('accounts.view')
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-area text-muted me-2"></i>
                                    <span>Chart of Accounts</span>
                                </div>
                            </div>
                            @endcan
                            @can('internal-transfers.view')
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exchange-alt text-muted me-2"></i>
                                    <span>Internal Transfers</span>
                                </div>
                            </div>
                            @endcan
                            @if(app('permissions')->userCanAny(['general-ledger.view', 'expenses.view', 'incomes.view']))
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-invoice-dollar text-muted me-2"></i>
                                    <span>Financial Reports</span>
                                </div>
                            </div>
                            @endif
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
                            @can('employees.view')
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-tie text-muted me-2"></i>
                                    <span>Employee Management</span>
                                </div>
                            </div>
                            @endcan
                            @can('attendances.view')
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span>Attendance Tracking</span>
                                </div>
                            </div>
                            @endcan
                            @can('time-offs.view')
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span>Time Off Management</span>
                                </div>
                            </div>
                            @endcan
                            @if(app('permissions')->userCanAny(['employees.view', 'attendances.view']))
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-robot text-muted me-2"></i>
                                    <span>AI Evaluation</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Assets & Reporting -->
            @if(app('permissions')->userCanAny(['assets.view', 'general-ledger.view', 'expenses.view', 'incomes.view']))
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar text-dark me-2"></i>
                            Assets & Reporting
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('assets.view')
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-building text-muted me-2"></i>
                                    <span>Asset Management</span>
                                </div>
                            </div>
                            @endcan
                            @if(app('permissions')->userCanAny(['general-ledger.view', 'expenses.view', 'incomes.view']))
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-export text-muted me-2"></i>
                                    <span>Data Export</span>
                                </div>
                            </div>
                            @endif
                            @if(app('permissions')->userCanAny(['general-ledger.view', 'expenses.view', 'incomes.view']))
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-pie text-muted me-2"></i>
                                    <span>Analytics</span>
                                </div>
                            </div>
                            @endif
                            @if(app('permissions')->userCanAny(['general-ledger.view', 'expenses.view', 'incomes.view']))
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-download text-muted me-2"></i>
                                    <span>Report Generation</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- System Information -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            System Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    <div>
                                        <strong>Multi-Company Support</strong>
                                        <br><small class="text-muted">Isolated data per company</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-sync-alt text-info me-2"></i>
                                    <div>
                                        <strong>Real-time Updates</strong>
                                        <br><small class="text-muted">Live inventory tracking</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-brain text-warning me-2"></i>
                                    <div>
                                        <strong>AI Integration</strong>
                                        <br><small class="text-muted">Smart employee evaluation</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
