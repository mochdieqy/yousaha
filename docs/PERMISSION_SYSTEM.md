# Yousaha ERP Permission System

This document describes the implementation of the Spatie Laravel Permission package for managing roles and permissions in the Yousaha ERP system.

## Overview

The permission system provides granular access control to different modules and features based on user roles and permissions. Company owners automatically have access to all features, while other users are restricted based on their assigned roles and permissions.

## Architecture

### Core Components

1. **Spatie Laravel Permission Package** - Handles role and permission management
2. **Custom Middleware** - Checks permissions for route access
3. **Blade Directives** - Provides permission checking in views
4. **Helper Traits** - Offers permission checking methods in controllers and models
5. **Service Provider** - Registers permission helpers globally

### Database Tables

- `permissions` - Stores individual permissions
- `roles` - Stores user roles
- `role_has_permissions` - Links roles to permissions
- `model_has_roles` - Links users to roles
- `model_has_permissions` - Links users to permissions (direct)

## Roles and Permissions

### Predefined Roles

#### 1. Company Owner
- **Description**: Full system access
- **Permissions**: All permissions automatically granted
- **Use Case**: Company founders, system administrators

#### 2. Finance Manager
- **Description**: Financial data and reporting access
- **Permissions**: 
  - Accounts management
  - General ledger
  - Expenses and income
  - Internal transfers
  - Asset management
  - Company view

#### 3. Sales Manager
- **Description**: Sales and customer management
- **Permissions**:
  - Product management
  - Customer management
  - Sales orders (full access)
  - Delivery management
  - Company view

#### 4. Purchase Manager
- **Description**: Purchase and supplier management
- **Permissions**:
  - Product management
  - Supplier management
  - Purchase orders (full access)
  - Receipt management
  - Company view

#### 5. Inventory Manager
- **Description**: Inventory and warehouse management
- **Permissions**:
  - Product management
  - Warehouse management
  - Stock management
  - Receipt and delivery management
  - Company view

#### 6. HR Manager
- **Description**: Human resources management
- **Permissions**:
  - Department management
  - Employee management
  - Attendance management
  - Time off management
  - Payroll management
  - Company management (employees)

#### 7. Employee
- **Description**: Basic daily work permissions
- **Permissions**:
  - View products, customers, suppliers, stocks
  - Create sales and purchase orders
  - Create receipts and deliveries
  - Manage own attendance and time off
  - Company view

#### 8. Viewer
- **Description**: Read-only access to most modules
- **Permissions**: View access to most data (no create/edit/delete)

### Permission Categories

#### Master Data Management
- `products.*` - Product CRUD operations
- `customers.*` - Customer CRUD operations
- `suppliers.*` - Supplier CRUD operations
- `accounts.*` - Account CRUD operations

#### Inventory Management
- `warehouses.*` - Warehouse CRUD operations
- `stocks.*` - Stock CRUD operations
- `receipts.*` - Receipt CRUD operations
- `deliveries.*` - Delivery CRUD operations

#### Sales Management
- `sales-orders.*` - Sales order CRUD operations
- `sales-orders.approve` - Approve sales orders
- `sales-orders.generate-quotation` - Generate quotations
- `sales-orders.generate-invoice` - Generate invoices

#### Purchase Management
- `purchase-orders.*` - Purchase order CRUD operations
- `purchase-orders.approve` - Approve purchase orders

#### Finance Management
- `general-ledger.*` - General ledger CRUD operations
- `expenses.*` - Expense CRUD operations
- `incomes.*` - Income CRUD operations
- `internal-transfers.*` - Internal transfer CRUD operations
- `assets.*` - Asset CRUD operations

#### Human Resources Management
- `departments.*` - Department CRUD operations
- `employees.*` - Employee CRUD operations
- `attendances.*` - Attendance CRUD operations
- `attendances.approve` - Approve attendance
- `time-offs.*` - Time off CRUD operations
- `time-offs.approve` - Approve time off requests
- `payrolls.*` - Payroll CRUD operations

#### Company Management
- `company.view` - View company information
- `company.edit` - Edit company information
- `company.manage-employees` - Manage company employees
- `company.invite-employees` - Invite new employees

## Implementation

### 1. Route Protection

Use the `permission` middleware to protect routes:

```php
Route::middleware(['permission:products.view'])->group(function () {
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
});

Route::middleware(['permission:products.create'])->group(function () {
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
});
```

### 2. Controller Permission Checking

Use the permission helpers in controllers:

```php
use App\Traits\HasPermissionHelpers;

class ProductController extends Controller
{
    use HasPermissionHelpers;

    public function index()
    {
        if (!self::userCan('products.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Controller logic here
    }

    public function store(Request $request)
    {
        if (!self::userCan('products.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Controller logic here
    }
}
```

### 3. Blade Template Permission Checking

Use Blade directives in views:

```blade
{{-- Check single permission --}}
@can('products.view')
    <div class="products-list">
        {{-- Products content --}}
    </div>
@endcan

{{-- Check multiple permissions (any) --}}
@canany(['customers.view', 'suppliers.view'])
    <div class="business-partners">
        {{-- Business partners content --}}
    </div>
@endcanany

{{-- Check multiple permissions (all) --}}
@canall(['sales-orders.view', 'sales-orders.create', 'sales-orders.approve'])
    <div class="sales-management">
        {{-- Full sales management content --}}
    </div>
@endcanall

{{-- Check role --}}
@role('Company Owner')
    <div class="admin-panel">
        {{-- Admin panel content --}}
    </div>
@endrole
```

### 4. JavaScript Permission Checking

Access permissions in JavaScript:

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const userPermissions = @json(app('permissions')->getUserPermissions());
    const userRoles = @json(app('permissions')->getUserRoles());
    
    // Check permissions before enabling features
    if (userPermissions.includes('sales-orders.create')) {
        document.getElementById('create-order-btn').disabled = false;
    }
    
    // Show admin features for Company Owners
    if (userRoles.includes('Company Owner')) {
        document.getElementById('admin-panel').style.display = 'block';
    }
});
```

## Usage Examples

### Creating a New Module with Permissions

1. **Add permissions to the seeder:**

```php
// In RolePermissionSeeder.php
private function createNewModulePermissions()
{
    Permission::create(['name' => 'new-module.view']);
    Permission::create(['name' => 'new-module.create']);
    Permission::create(['name' => 'new-module.edit']);
    Permission::create(['name' => 'new-module.delete']);
}
```

2. **Assign permissions to roles:**

```php
$managerRole = Role::where('name', 'Manager')->first();
$managerRole->givePermissionTo([
    'new-module.view',
    'new-module.create',
    'new-module.edit'
]);
```

3. **Protect routes:**

```php
Route::middleware(['permission:new-module.view'])->group(function () {
    Route::get('new-module', [NewModuleController::class, 'index']);
});

Route::middleware(['permission:new-module.create'])->group(function () {
    Route::get('new-module/create', [NewModuleController::class, 'create']);
    Route::post('new-module', [NewModuleController::class, 'store']);
});
```

4. **Use in controllers:**

```php
public function index()
{
    if (!self::userCan('new-module.view')) {
        abort(403, 'Unauthorized action.');
    }
    
    // Controller logic
}
```

5. **Use in views:**

```blade
@can('new-module.view')
    <div class="new-module-content">
        @can('new-module.create')
            <button class="btn btn-primary">Create New</button>
        @endcan
    </div>
@endcan
```

## Management Commands

### Assign Roles to Existing Users

```bash
php artisan users:assign-roles
```

This command automatically assigns appropriate roles to existing users based on their company relationships.

### Database Seeding

```bash
php artisan db:seed --class=RolePermissionSeeder
```

This creates all roles and permissions in the database.

## Security Considerations

1. **Always check permissions on the server side** - Client-side checks are for UX only
2. **Use middleware for route protection** - Don't rely solely on controller checks
3. **Validate permissions in controllers** - Double-check permissions before performing actions
4. **Regular permission audits** - Review and update permissions as needed
5. **Principle of least privilege** - Only grant necessary permissions

## Troubleshooting

### Common Issues

1. **Permissions not working after role assignment:**
   - Clear permission cache: `php artisan permission:cache-reset`
   - Check if user is properly authenticated

2. **Middleware not working:**
   - Verify middleware is registered in `app/Http/Kernel.php`
   - Check route middleware syntax

3. **Blade directives not working:**
   - Ensure `PermissionServiceProvider` is registered
   - Clear view cache: `php artisan view:clear`

### Debugging

1. **Check user roles and permissions:**
```php
$user = Auth::user();
dd([
    'roles' => $user->getRoleNames(),
    'permissions' => $user->getAllPermissions()->pluck('name'),
    'direct_permissions' => $user->getDirectPermissions()->pluck('name')
]);
```

2. **Check role permissions:**
```php
$role = Role::where('name', 'Manager')->first();
dd($role->getAllPermissions()->pluck('name'));
```

## Best Practices

1. **Use descriptive permission names** - e.g., `products.view` instead of `view`
2. **Group related permissions** - Use consistent naming patterns
3. **Test permission system thoroughly** - Ensure all access controls work correctly
4. **Document permission requirements** - Keep track of which features need which permissions
5. **Regular permission reviews** - Audit permissions periodically
6. **Use role hierarchies** - Assign permissions to roles, not directly to users

## Future Enhancements

1. **Dynamic permission management** - Admin interface for managing permissions
2. **Permission inheritance** - Hierarchical permission structures
3. **Time-based permissions** - Temporary access grants
4. **Audit logging** - Track permission changes and usage
5. **API permissions** - Extend permission system to API endpoints
