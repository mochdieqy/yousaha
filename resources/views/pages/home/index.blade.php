@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Company Information Header -->
        @if(isset($company))
        <div class="card text-white mb-4 border-0 shadow company-header-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
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
                    <div class="col-md-4 text-md-end">
                        @if(Auth::user()->companies->contains('id', $company->id))
                            <span class="badge bg-warning text-dark fw-bold shadow-sm">
                                <i class="fas fa-crown me-1"></i>
                                Company Owner
                            </span>
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
        @endif

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Quick Stats Row -->
        <div class="row mb-4">
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
        </div>

        <!-- Main Features Grid -->
        <div class="row">
            <!-- Inventory Management -->
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
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-box text-muted me-2"></i>
                                    <span>Product Management</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-warehouse text-muted me-2"></i>
                                    <span>Warehouse Management</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-bar text-muted me-2"></i>
                                    <span>Stock Tracking</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exchange-alt text-muted me-2"></i>
                                    <span>Stock Transfers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Management -->
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
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-invoice text-muted me-2"></i>
                                    <span>Sales Orders</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-truck text-muted me-2"></i>
                                    <span>Delivery Management</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-users text-muted me-2"></i>
                                    <span>Customer Management</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-muted me-2"></i>
                                    <span>Quotations & Invoices</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Management -->
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
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-purchase text-muted me-2"></i>
                                    <span>Purchase Orders</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-receipt text-muted me-2"></i>
                                    <span>Goods Receiving</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-building text-muted me-2"></i>
                                    <span>Supplier Management</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-pie text-muted me-2"></i>
                                    <span>Expense Tracking</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Finance Management -->
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
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-book text-muted me-2"></i>
                                    <span>General Ledger</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-area text-muted me-2"></i>
                                    <span>Chart of Accounts</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exchange-alt text-muted me-2"></i>
                                    <span>Internal Transfers</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-invoice-dollar text-muted me-2"></i>
                                    <span>Financial Reports</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Human Resources -->
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
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-tie text-muted me-2"></i>
                                    <span>Employee Management</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span>Attendance Tracking</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span>Time Off Management</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-robot text-muted me-2"></i>
                                    <span>AI Evaluation</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assets & Reporting -->
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
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-building text-muted me-2"></i>
                                    <span>Asset Management</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-export text-muted me-2"></i>
                                    <span>Data Export</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-pie text-muted me-2"></i>
                                    <span>Analytics</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-download text-muted me-2"></i>
                                    <span>Report Generation</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
