{{-- Permission Helpers Examples --}}
{{-- This file shows how to use the permission system in Blade templates --}}

{{-- Check if user has a specific permission --}}
@can('products.view')
    <div class="permission-example">
        <h3>Products Module</h3>
        <p>You have permission to view products.</p>
        
        @can('products.create')
            <button class="btn btn-primary">Create Product</button>
        @endcan
        
        @can('products.edit')
            <button class="btn btn-warning">Edit Product</button>
        @endcan
        
        @can('products.delete')
            <button class="btn btn-danger">Delete Product</button>
        @endcan
    </div>
@endcan

{{-- Check if user has any of multiple permissions --}}
@canany(['customers.view', 'suppliers.view'])
    <div class="permission-example">
        <h3>Business Partners</h3>
        
        @can('customers.view')
            <div class="card">
                <div class="card-header">Customers</div>
                <div class="card-body">
                    <p>Customer management module</p>
                    @can('customers.create')
                        <button class="btn btn-sm btn-primary">Add Customer</button>
                    @endcan
                </div>
            </div>
        @endcan
        
        @can('suppliers.view')
            <div class="card">
                <div class="card-header">Suppliers</div>
                <div class="card-body">
                    <p>Supplier management module</p>
                    @can('suppliers.create')
                        <button class="btn btn-sm btn-primary">Add Supplier</button>
                    @endcan
                </div>
            </div>
        @endcan
    </div>
@endcanany

{{-- Check if user has all required permissions --}}
@canall(['sales-orders.view', 'sales-orders.create', 'sales-orders.approve'])
    <div class="permission-example">
        <h3>Sales Management</h3>
        <p>You have full access to sales management.</p>
        <div class="btn-group">
            <button class="btn btn-primary">View Orders</button>
            <button class="btn btn-success">Create Order</button>
            <button class="btn btn-warning">Approve Orders</button>
        </div>
    </div>
@endcanall

{{-- Check if user has a specific role --}}
@role('Company Owner')
    <div class="permission-example">
        <h3>Company Owner Dashboard</h3>
        <p>You have full access to all system features.</p>
        <div class="alert alert-info">
            <strong>Note:</strong> Company owners automatically have all permissions.
        </div>
    </div>
@endrole

{{-- Check if user has any of multiple roles --}}
@anyrole(['Finance Manager', 'HR Manager'])
    <div class="permission-example">
        <h3>Manager Dashboard</h3>
        <p>You have managerial access to specific modules.</p>
        
        @role('Finance Manager')
            <div class="card">
                <div class="card-header">Financial Reports</div>
                <div class="card-body">
                    <p>Access to financial data and reports</p>
                </div>
            </div>
        @endrole
        
        @role('HR Manager')
            <div class="card">
                <div class="card-header">HR Management</div>
                <div class="card-body">
                    <p>Access to employee and HR data</p>
                </div>
            </div>
        @endrole
    </div>
@endanyrole

{{-- Using permission helpers in JavaScript --}}
<script>
// You can also check permissions in JavaScript using the permissions helper
document.addEventListener('DOMContentLoaded', function() {
    // Example of checking permissions before showing certain UI elements
    const userPermissions = @json(app('permissions')->getUserPermissions());
    const userRoles = @json(app('permissions')->getUserRoles());
    
    console.log('User Permissions:', userPermissions);
    console.log('User Roles:', userRoles);
    
    // Example: Show admin panel only for Company Owners
    if (userRoles.includes('Company Owner')) {
        const adminPanel = document.getElementById('admin-panel');
        if (adminPanel) {
            adminPanel.style.display = 'block';
        }
    }
    
    // Example: Enable features based on permissions
    if (userPermissions.includes('sales-orders.create')) {
        const createOrderBtn = document.getElementById('create-order-btn');
        if (createOrderBtn) {
            createOrderBtn.disabled = false;
        }
    }
});
</script>

{{-- Permission-based navigation menu --}}
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Yousaha ERP</a>
        
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                @can('products.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                    </li>
                @endcan
                
                @can('customers.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customers.index') }}">Customers</a>
                    </li>
                @endcan
                
                @can('warehouses.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('warehouses.index') }}">Warehouses</a>
                    </li>
                @endcan
                
                @can('sales-orders.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sales-orders.index') }}">Sales Orders</a>
                    </li>
                @endcan
                
                @can('general-ledger.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('general-ledger.index') }}">General Ledger</a>
                    </li>
                @endcan
                
                @can('employees.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('employees.index') }}">Employees</a>
                    </li>
                @endcan
            </ul>
            
            <ul class="navbar-nav">
                @role('Company Owner')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">User Management</a></li>
                            <li><a class="dropdown-item" href="#">Role Management</a></li>
                            <li><a class="dropdown-item" href="#">System Settings</a></li>
                        </ul>
                    </li>
                @endrole
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('auth.sign-out') }}">Sign Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
