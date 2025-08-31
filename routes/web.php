<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdditionalPageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'SignIn'])->name('auth.sign-in');
Route::post('/', [AuthController::class, 'SignInProcess'])->name('auth.sign-in-process');
Route::get('sign-out', [AuthController::class, 'SignOut'])->name('auth.sign-out');
Route::get('sign-up', [AuthController::class, 'SignUp'])->name('auth.sign-up');
Route::post('sign-up', [AuthController::class, 'SignUpProcess'])->name('auth.sign-up-process');

// Email verification routes
Route::get('verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('auth.verify-email');
Route::get('resend-verification', [AuthController::class, 'showResendVerification'])->name('auth.resend-verification');
Route::post('resend-verification', [AuthController::class, 'resendVerification'])->name('auth.resend-verification-process');

// Forgot password routes
Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('auth.forgot-password');
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password-process');
Route::get('reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('auth.reset-password');
Route::post('reset-password/{token}', [AuthController::class, 'resetPassword'])->name('auth.reset-password-process');

Route::get('about', [AdditionalPageController::class, 'About'])->name('additional-page.about');
Route::get('terms', [AdditionalPageController::class, 'Terms'])->name('additional-page.terms');
Route::get('privacy', [AdditionalPageController::class, 'Privacy'])->name('additional-page.privacy');

Route::middleware(['auth'])->group(function () {
    Route::get('home', [HomeController::class, 'Home'])->name('home');
    
    // Profile management routes
    Route::get('profile', [AuthController::class, 'showProfile'])->name('auth.profile');
    Route::post('profile/update', [AuthController::class, 'updateProfile'])->name('auth.profile-update');
    Route::post('profile/password', [AuthController::class, 'updatePassword'])->name('auth.password-update');
    
    // Company management routes
    Route::get('company/choice', [HomeController::class, 'companyChoice'])->name('company.choice');
    Route::get('company/create', [HomeController::class, 'createCompany'])->name('company.create');
    Route::post('company/store', [HomeController::class, 'storeCompany'])->name('company.store');
    Route::get('company/edit', [HomeController::class, 'editCompany'])->name('company.edit');
    Route::post('company/update', [HomeController::class, 'updateCompany'])->name('company.update');
    Route::get('company/employee-invitation', [HomeController::class, 'employeeInvitation'])->name('company.employee-invitation');
    
    // Master Data Management
    Route::middleware(['permission:products.view'])->group(function () {
        Route::get('products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    });
    
    Route::middleware(['permission:products.create'])->group(function () {
        Route::get('products/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
        Route::post('products', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    });
    
    Route::middleware(['permission:products.edit'])->group(function () {
        Route::get('products/{product}/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    });
    
    Route::middleware(['permission:products.delete'])->group(function () {
        Route::delete('products/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('products.delete');
    });
    
    Route::middleware(['permission:customers.view'])->group(function () {
        Route::get('customers', [App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
    });
    
    Route::middleware(['permission:customers.create'])->group(function () {
        Route::get('customers/create', [App\Http\Controllers\CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers', [App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
    });
    
    Route::middleware(['permission:customers.edit'])->group(function () {
        Route::get('customers/{customer}/edit', [App\Http\Controllers\CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
    });
    
    Route::middleware(['permission:customers.delete'])->group(function () {
        Route::delete('customers/{customer}', [App\Http\Controllers\CustomerController::class, 'destroy'])->name('customers.delete');
    });
    
    // Supplier Management
    Route::middleware(['permission:suppliers.view'])->group(function () {
        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    });
    
    Route::middleware(['permission:suppliers.create'])->group(function () {
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    });
    
    Route::middleware(['permission:suppliers.edit'])->group(function () {
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    });
    
    Route::middleware(['permission:suppliers.delete'])->group(function () {
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.delete');
    });
    
    // Inventory Management
    Route::middleware(['permission:warehouses.view'])->group(function () {
        Route::get('warehouses', [App\Http\Controllers\WarehouseController::class, 'index'])->name('warehouses.index');
    });
    
    Route::middleware(['permission:warehouses.create'])->group(function () {
        Route::get('warehouses/create', [App\Http\Controllers\WarehouseController::class, 'create'])->name('warehouses.create');
        Route::post('warehouses', [App\Http\Controllers\WarehouseController::class, 'store'])->name('warehouses.store');
    });
    
    Route::middleware(['permission:warehouses.edit'])->group(function () {
        Route::get('warehouses/{warehouse}/edit', [App\Http\Controllers\WarehouseController::class, 'edit'])->name('warehouses.edit');
        Route::put('warehouses/{warehouse}', [App\Http\Controllers\WarehouseController::class, 'update'])->name('warehouses.update');
    });
    
    Route::middleware(['permission:warehouses.delete'])->group(function () {
        Route::delete('warehouses/{warehouse}', [App\Http\Controllers\WarehouseController::class, 'destroy'])->name('warehouses.delete');
    });
    
    Route::middleware(['permission:stocks.view'])->group(function () {
        Route::get('stocks', [App\Http\Controllers\StockController::class, 'index'])->name('stocks.index');
    });
    
    Route::middleware(['permission:stocks.create'])->group(function () {
        Route::get('stocks/create', [App\Http\Controllers\StockController::class, 'create'])->name('stocks.create');
        Route::post('stocks', [App\Http\Controllers\StockController::class, 'store'])->name('stocks.store');
    });
    
    Route::middleware(['permission:stocks.edit'])->group(function () {
        Route::get('stocks/{stock}/edit', [App\Http\Controllers\StockController::class, 'edit'])->name('stocks.edit');
        Route::put('stocks/{stock}', [App\Http\Controllers\StockController::class, 'update'])->name('stocks.update');
    });
    
    Route::middleware(['permission:stocks.delete'])->group(function () {
        Route::delete('stocks/{stock}', [App\Http\Controllers\StockController::class, 'destroy'])->name('stocks.delete');
    });
    
    Route::middleware(['permission:stocks.view'])->group(function () {
        Route::get('stocks/{stock}', [App\Http\Controllers\StockController::class, 'show'])->name('stocks.show');
    });
    
    // Receipt Management
    Route::middleware(['permission:receipts.view'])->group(function () {
        Route::get('receipts', [App\Http\Controllers\ReceiptController::class, 'index'])->name('receipts.index');
    });
    
    Route::middleware(['permission:receipts.create'])->group(function () {
        Route::get('receipts/create', [App\Http\Controllers\ReceiptController::class, 'create'])->name('receipts.create');
        Route::post('receipts', [App\Http\Controllers\ReceiptController::class, 'store'])->name('receipts.store');
    });
    
    Route::middleware(['permission:receipts.edit'])->group(function () {
        Route::get('receipts/{receipt}/edit', [App\Http\Controllers\ReceiptController::class, 'edit'])->name('receipts.edit');
        Route::put('receipts/{receipt}', [App\Http\Controllers\ReceiptController::class, 'update'])->name('receipts.update');
        Route::post('receipts/{receipt}/status', [App\Http\Controllers\ReceiptController::class, 'updateStatus'])->name('receipts.update-status');
        Route::post('receipts/{receipt}/goods-receive', [App\Http\Controllers\ReceiptController::class, 'goodsReceive'])->name('receipts.goods-receive');
    });
    
    Route::middleware(['permission:receipts.delete'])->group(function () {
        Route::delete('receipts/{receipt}', [App\Http\Controllers\ReceiptController::class, 'destroy'])->name('receipts.delete');
    });
    
    Route::middleware(['permission:receipts.view'])->group(function () {
        Route::get('receipts/{receipt}', [App\Http\Controllers\ReceiptController::class, 'show'])->name('receipts.show');
    });
    
    // Delivery Management
    Route::middleware(['permission:deliveries.view'])->group(function () {
        Route::get('deliveries', [App\Http\Controllers\DeliveryController::class, 'index'])->name('deliveries.index');
    });
    
    Route::middleware(['permission:deliveries.create'])->group(function () {
        Route::get('deliveries/create', [App\Http\Controllers\DeliveryController::class, 'create'])->name('deliveries.create');
        Route::post('deliveries', [App\Http\Controllers\DeliveryController::class, 'store'])->name('deliveries.store');
    });
    
    Route::middleware(['permission:deliveries.edit'])->group(function () {
        Route::get('deliveries/{delivery}/edit', [App\Http\Controllers\DeliveryController::class, 'edit'])->name('deliveries.edit');
        Route::put('deliveries/{delivery}', [App\Http\Controllers\DeliveryController::class, 'update'])->name('deliveries.update');
        Route::post('deliveries/{delivery}/status', [App\Http\Controllers\DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');
        Route::post('deliveries/{delivery}/goods-issue', [App\Http\Controllers\DeliveryController::class, 'goodsIssue'])->name('deliveries.goods-issue');
        Route::post('deliveries/{delivery}/check-stock', [App\Http\Controllers\DeliveryController::class, 'checkStockAvailability'])->name('deliveries.check-stock');
        Route::post('deliveries/{delivery}/cancel', [App\Http\Controllers\DeliveryController::class, 'cancelDelivery'])->name('deliveries.cancel');
    });
    
    Route::middleware(['permission:deliveries.delete'])->group(function () {
        Route::delete('deliveries/{delivery}', [App\Http\Controllers\DeliveryController::class, 'destroy'])->name('deliveries.delete');
    });
    
    Route::middleware(['permission:deliveries.view'])->group(function () {
        Route::get('deliveries/{delivery}', [App\Http\Controllers\DeliveryController::class, 'show'])->name('deliveries.show');
    });
    
    // Purchase Management
    Route::middleware(['permission:purchase-orders.view'])->group(function () {
        Route::get('purchase-orders', [App\Http\Controllers\PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    });
    
    Route::middleware(['permission:purchase-orders.create'])->group(function () {
        Route::get('purchase-orders/create', [App\Http\Controllers\PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('purchase-orders', [App\Http\Controllers\PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    });
    
    Route::middleware(['permission:purchase-orders.edit'])->group(function () {
        Route::get('purchase-orders/{purchaseOrder}/edit', [App\Http\Controllers\PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
        Route::put('purchase-orders/{purchaseOrder}', [App\Http\Controllers\PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
        Route::post('purchase-orders/{purchaseOrder}/status', [App\Http\Controllers\PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
    });
    
    Route::middleware(['permission:purchase-orders.delete'])->group(function () {
        Route::delete('purchase-orders/{purchaseOrder}', [App\Http\Controllers\PurchaseOrderController::class, 'destroy'])->name('purchase-orders.delete');
    });
    
    Route::middleware(['permission:purchase-orders.view'])->group(function () {
        Route::get('purchase-orders/{purchaseOrder}', [App\Http\Controllers\PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    });
    
    // Sales Management
    Route::middleware(['permission:sales-orders.view'])->group(function () {
        Route::get('sales-orders', [App\Http\Controllers\SalesOrderController::class, 'index'])->name('sales-orders.index');
    });
    
    Route::middleware(['permission:sales-orders.create'])->group(function () {
        Route::get('sales-orders/create', [App\Http\Controllers\SalesOrderController::class, 'create'])->name('sales-orders.create');
        Route::post('sales-orders', [App\Http\Controllers\SalesOrderController::class, 'store'])->name('sales-orders.store');
    });
    
    Route::middleware(['permission:sales-orders.edit'])->group(function () {
        Route::get('sales-orders/{salesOrder}/edit', [App\Http\Controllers\SalesOrderController::class, 'edit'])->name('sales-orders.edit');
        Route::put('sales-orders/{salesOrder}', [App\Http\Controllers\SalesOrderController::class, 'update'])->name('sales-orders.update');
        Route::post('sales-orders/{salesOrder}/status', [App\Http\Controllers\SalesOrderController::class, 'updateStatus'])->name('sales-orders.update-status');
        Route::post('sales-orders/{salesOrder}/quotation', [App\Http\Controllers\SalesOrderController::class, 'generateQuotation'])->name('sales-orders.generate-quotation');
        Route::post('sales-orders/{salesOrder}/invoice', [App\Http\Controllers\SalesOrderController::class, 'generateInvoice'])->name('sales-orders.generate-invoice');
    });
    
    Route::middleware(['permission:sales-orders.delete'])->group(function () {
        Route::delete('sales-orders/{salesOrder}', [App\Http\Controllers\SalesOrderController::class, 'destroy'])->name('sales-orders.delete');
    });
    
    Route::middleware(['permission:sales-orders.view'])->group(function () {
        Route::get('sales-orders/{salesOrder}', [App\Http\Controllers\SalesOrderController::class, 'show'])->name('sales-orders.show');
    });
    
    // Finance Management - General Ledger
    Route::middleware(['permission:general-ledger.view'])->group(function () {
        Route::get('general-ledger', [App\Http\Controllers\GeneralLedgerController::class, 'index'])->name('general-ledger.index');
    });
    
    Route::middleware(['permission:general-ledger.create'])->group(function () {
        Route::get('general-ledger/create', [App\Http\Controllers\GeneralLedgerController::class, 'create'])->name('general-ledger.create');
        Route::post('general-ledger', [App\Http\Controllers\GeneralLedgerController::class, 'store'])->name('general-ledger.store');
    });
    
    Route::middleware(['permission:general-ledger.edit'])->group(function () {
        Route::get('general-ledger/{generalLedger}/edit', [App\Http\Controllers\GeneralLedgerController::class, 'edit'])->name('general-ledger.edit');
        Route::put('general-ledger/{generalLedger}', [App\Http\Controllers\GeneralLedgerController::class, 'update'])->name('general-ledger.update');
    });
    
    Route::middleware(['permission:general-ledger.delete'])->group(function () {
        Route::delete('general-ledger/{generalLedger}', [App\Http\Controllers\GeneralLedgerController::class, 'destroy'])->name('general-ledger.delete');
    });
    
    Route::middleware(['permission:general-ledger.view'])->group(function () {
        Route::get('general-ledger/{generalLedger}', [App\Http\Controllers\GeneralLedgerController::class, 'show'])->name('general-ledger.show');
    });
    
    Route::middleware(['permission:general-ledger.view'])->group(function () {
        Route::get('general-ledger/export', [App\Http\Controllers\GeneralLedgerController::class, 'export'])->name('general-ledger.export');
    });
    
    // Finance Management - Chart of Accounts
    Route::middleware(['permission:accounts.view'])->group(function () {
        Route::get('accounts', [App\Http\Controllers\AccountController::class, 'index'])->name('accounts.index');
    });
    
    Route::middleware(['permission:accounts.create'])->group(function () {
        Route::get('accounts/create', [App\Http\Controllers\AccountController::class, 'create'])->name('accounts.create');
        Route::post('accounts', [App\Http\Controllers\AccountController::class, 'store'])->name('accounts.store');
    });
    
    Route::middleware(['permission:accounts.edit'])->group(function () {
        Route::get('accounts/{account}/edit', [App\Http\Controllers\AccountController::class, 'edit'])->name('accounts.edit');
        Route::put('accounts/{account}', [App\Http\Controllers\AccountController::class, 'update'])->name('accounts.update');
    });
    
    Route::middleware(['permission:accounts.delete'])->group(function () {
        Route::delete('accounts/{account}', [App\Http\Controllers\AccountController::class, 'destroy'])->name('accounts.delete');
    });
    
    Route::middleware(['permission:accounts.view'])->group(function () {
        Route::get('accounts/{account}', [App\Http\Controllers\AccountController::class, 'show'])->name('accounts.show');
    });
    
    // Finance Management - Expenses
    Route::middleware(['permission:expenses.view'])->group(function () {
        Route::get('expenses', [App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
    });
    
    Route::middleware(['permission:expenses.create'])->group(function () {
        Route::get('expenses/create', [App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('expenses', [App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
    });
    
    Route::middleware(['permission:expenses.edit'])->group(function () {
        Route::get('expenses/{expense}/edit', [App\Http\Controllers\ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('expenses/{expense}', [App\Http\Controllers\ExpenseController::class, 'update'])->name('expenses.update');
    });
    
    Route::middleware(['permission:expenses.delete'])->group(function () {
        Route::delete('expenses/{expense}', [App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.delete');
    });
    
    Route::middleware(['permission:expenses.view'])->group(function () {
        Route::get('expenses/{expense}', [App\Http\Controllers\ExpenseController::class, 'show'])->name('expenses.show');
    });
    
    // Finance Management - Incomes
    Route::middleware(['permission:incomes.view'])->group(function () {
        Route::get('incomes', [App\Http\Controllers\IncomeController::class, 'index'])->name('incomes.index');
    });
    
    Route::middleware(['permission:incomes.create'])->group(function () {
        Route::get('incomes/create', [App\Http\Controllers\IncomeController::class, 'create'])->name('incomes.create');
        Route::post('incomes', [App\Http\Controllers\IncomeController::class, 'store'])->name('incomes.store');
    });
    
    Route::middleware(['permission:incomes.edit'])->group(function () {
        Route::get('incomes/{income}/edit', [App\Http\Controllers\IncomeController::class, 'edit'])->name('incomes.edit');
        Route::put('incomes/{income}', [App\Http\Controllers\IncomeController::class, 'update'])->name('incomes.update');
    });
    
    Route::middleware(['permission:incomes.delete'])->group(function () {
        Route::delete('incomes/{income}', [App\Http\Controllers\IncomeController::class, 'destroy'])->name('incomes.delete');
    });
    
    Route::middleware(['permission:incomes.view'])->group(function () {
        Route::get('incomes/{income}', [App\Http\Controllers\IncomeController::class, 'show'])->name('incomes.show');
    });
    
    // Finance Management - Internal Transfers
    Route::middleware(['permission:internal-transfers.view'])->group(function () {
        Route::get('internal-transfers', [App\Http\Controllers\InternalTransferController::class, 'index'])->name('internal-transfers.index');
    });
    
    Route::middleware(['permission:internal-transfers.create'])->group(function () {
        Route::get('internal-transfers/create', [App\Http\Controllers\InternalTransferController::class, 'create'])->name('internal-transfers.create');
        Route::post('internal-transfers', [App\Http\Controllers\InternalTransferController::class, 'store'])->name('internal-transfers.store');
    });
    
    Route::middleware(['permission:internal-transfers.edit'])->group(function () {
        Route::get('internal-transfers/{internalTransfer}/edit', [App\Http\Controllers\InternalTransferController::class, 'edit'])->name('internal-transfers.edit');
        Route::put('internal-transfers/{internalTransfer}', [App\Http\Controllers\InternalTransferController::class, 'update'])->name('internal-transfers.update');
    });
    
    Route::middleware(['permission:internal-transfers.delete'])->group(function () {
        Route::delete('internal-transfers/{internalTransfer}', [App\Http\Controllers\InternalTransferController::class, 'destroy'])->name('internal-transfers.destroy');
    });
    
    Route::middleware(['permission:internal-transfers.view'])->group(function () {
        Route::get('internal-transfers/{internalTransfer}', [App\Http\Controllers\InternalTransferController::class, 'show'])->name('internal-transfers.show');
    });
    
    // Finance Management - Assets
    Route::middleware(['permission:assets.view'])->group(function () {
        Route::get('assets', [App\Http\Controllers\AssetController::class, 'index'])->name('assets.index');
    });
    
    Route::middleware(['permission:assets.create'])->group(function () {
        Route::get('assets/create', [App\Http\Controllers\AssetController::class, 'create'])->name('assets.create');
        Route::post('assets', [App\Http\Controllers\AssetController::class, 'store'])->name('assets.store');
    });
    
    Route::middleware(['permission:assets.edit'])->group(function () {
        Route::get('assets/{asset}/edit', [App\Http\Controllers\AssetController::class, 'edit'])->name('assets.edit');
        Route::put('assets/{asset}', [App\Http\Controllers\AssetController::class, 'update'])->name('assets.update');
    });
    
    Route::middleware(['permission:assets.delete'])->group(function () {
        Route::delete('assets/{asset}', [App\Http\Controllers\AssetController::class, 'destroy'])->name('assets.delete');
    });
    
    Route::middleware(['permission:assets.view'])->group(function () {
        Route::get('assets/{asset}', [App\Http\Controllers\AssetController::class, 'show'])->name('assets.show');
    });
    
    // Financial Reports
    Route::middleware(['permission:general-ledger.view'])->group(function () {
        Route::get('financial-reports', [App\Http\Controllers\FinancialReportController::class, 'index'])->name('financial-reports.index');
        Route::get('financial-reports/statement-of-financial-position', [App\Http\Controllers\FinancialReportController::class, 'statementOfFinancialPosition'])->name('financial-reports.statement-of-financial-position');
        Route::get('financial-reports/profit-and-loss', [App\Http\Controllers\FinancialReportController::class, 'profitAndLoss'])->name('financial-reports.profit-and-loss');
        Route::get('financial-reports/general-ledger-history', [App\Http\Controllers\FinancialReportController::class, 'generalLedgerHistory'])->name('financial-reports.general-ledger-history');
    });
    
    // HR Management - Departments
    Route::middleware(['permission:departments.view'])->group(function () {
        Route::get('departments', [App\Http\Controllers\DepartmentController::class, 'index'])->name('departments.index');
    });
    
    Route::middleware(['permission:departments.create'])->group(function () {
        Route::get('departments/create', [App\Http\Controllers\DepartmentController::class, 'create'])->name('departments.create');
        Route::post('departments', [App\Http\Controllers\DepartmentController::class, 'store'])->name('departments.store');
    });
    
    Route::middleware(['permission:departments.edit'])->group(function () {
        Route::get('departments/{department}/edit', [App\Http\Controllers\DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'update'])->name('departments.update');
    });
    
    Route::middleware(['permission:departments.delete'])->group(function () {
        Route::delete('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'destroy'])->name('departments.delete');
    });
    
    // HR Management - Employees
    Route::middleware(['permission:employees.view'])->group(function () {
        Route::get('employees', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    });
    
    Route::middleware(['permission:employees.create'])->group(function () {
        Route::get('employees/create', [App\Http\Controllers\EmployeeController::class, 'create'])->name('employees.create');
        Route::post('employees', [App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
    });
    
    Route::middleware(['permission:employees.edit'])->group(function () {
        Route::get('employees/{employee}/edit', [App\Http\Controllers\EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('employees/{employee}', [App\Http\Controllers\EmployeeController::class, 'update'])->name('employees.update');
    });
    
    Route::middleware(['permission:employees.delete'])->group(function () {
        Route::delete('employees/{employee}', [App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.delete');
    });
    
    // HR Management - Attendances
    Route::middleware(['permission:attendances.view'])->group(function () {
        Route::get('attendances', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendances.index');
    });
    
    // Clock in/out routes (no specific permission required, but user must be employee)
    Route::post('attendances/clock-in', [App\Http\Controllers\AttendanceController::class, 'clockIn'])->name('attendances.clock-in');
    Route::post('attendances/clock-out', [App\Http\Controllers\AttendanceController::class, 'clockOut'])->name('attendances.clock-out');
    
    // HR Management - Time Offs
    Route::middleware(['permission:time-offs.view'])->group(function () {
        Route::get('time-offs', [App\Http\Controllers\TimeOffController::class, 'index'])->name('time-offs.index');
    });
    
    Route::middleware(['permission:time-offs.create'])->group(function () {
        Route::get('time-offs/create', [App\Http\Controllers\TimeOffController::class, 'create'])->name('time-offs.create');
        Route::post('time-offs', [App\Http\Controllers\TimeOffController::class, 'store'])->name('time-offs.store');
    });
    
    Route::middleware(['permission:time-offs.edit'])->group(function () {
        Route::get('time-offs/{timeOff}/edit', [App\Http\Controllers\TimeOffController::class, 'edit'])->name('time-offs.edit');
        Route::put('time-offs/{timeOff}', [App\Http\Controllers\TimeOffController::class, 'update'])->name('time-offs.update');
    });
    
    Route::middleware(['permission:time-offs.delete'])->group(function () {
        Route::delete('time-offs/{timeOff}', [App\Http\Controllers\TimeOffController::class, 'destroy'])->name('time-offs.delete');
    });
    
    // Time off approval routes
    Route::middleware(['permission:time-offs.approve'])->group(function () {
        Route::get('time-offs/approval', [App\Http\Controllers\TimeOffController::class, 'approvalIndex'])->name('time-offs.approval');
        Route::get('time-offs/{timeOff}/approval', [App\Http\Controllers\TimeOffController::class, 'approvalForm'])->name('time-offs.approval-form');
        Route::post('time-offs/{timeOff}/approval', [App\Http\Controllers\TimeOffController::class, 'processApproval'])->name('time-offs.process-approval');
    });
    
    // HR Management - Payrolls
    Route::middleware(['permission:payrolls.view'])->group(function () {
        Route::get('payrolls', [App\Http\Controllers\PayrollController::class, 'index'])->name('payrolls.index');
    });
    
    Route::middleware(['permission:payrolls.create'])->group(function () {
        Route::get('payrolls/create', [App\Http\Controllers\PayrollController::class, 'create'])->name('payrolls.create');
        Route::post('payrolls', [App\Http\Controllers\PayrollController::class, 'store'])->name('payrolls.store');
    });
    
    Route::middleware(['permission:payrolls.edit'])->group(function () {
        Route::get('payrolls/{payroll}/edit', [App\Http\Controllers\PayrollController::class, 'edit'])->name('payrolls.edit');
        Route::put('payrolls/{payroll}', [App\Http\Controllers\PayrollController::class, 'update'])->name('payrolls.update');
    });
    
    Route::middleware(['permission:payrolls.delete'])->group(function () {
        Route::delete('payrolls/{payroll}', [App\Http\Controllers\PayrollController::class, 'destroy'])->name('payrolls.delete');
    });
    
    // Payroll view route
    Route::middleware(['permission:payrolls.view'])->group(function () {
        Route::get('payrolls/{payroll}', [App\Http\Controllers\PayrollController::class, 'show'])->name('payrolls.show');
    });

    // Role Management (for company owners)
    Route::middleware(['permission:company.manage-employee-roles'])->group(function () {
        Route::get('roles', [App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
        Route::get('employee-roles', [App\Http\Controllers\EmployeeRoleController::class, 'index'])->name('employee-roles.index');
        Route::get('employee-roles/create', [App\Http\Controllers\EmployeeRoleController::class, 'create'])->name('employee-roles.create');
        Route::post('employee-roles', [App\Http\Controllers\EmployeeRoleController::class, 'store'])->name('employee-roles.store');
        Route::delete('employee-roles/{employee}', [App\Http\Controllers\EmployeeRoleController::class, 'destroy'])->name('employee-roles.destroy');
    });

    // AI Evaluation Management
    Route::middleware(['permission:ai-evaluation.view'])->group(function () {
        Route::get('ai-evaluation', [App\Http\Controllers\AIEvaluationController::class, 'index'])->name('ai-evaluation.index');
        Route::get('ai-evaluation/create', [App\Http\Controllers\AIEvaluationController::class, 'create'])->name('ai-evaluation.create');
        Route::post('ai-evaluation', [App\Http\Controllers\AIEvaluationController::class, 'store'])->name('ai-evaluation.store');
        Route::get('ai-evaluation/{evaluation}', [App\Http\Controllers\AIEvaluationController::class, 'show'])->name('ai-evaluation.show');
    });

    Route::middleware(['permission:ai-evaluation.edit'])->group(function () {
        Route::get('ai-evaluation/{evaluation}/edit', [App\Http\Controllers\AIEvaluationController::class, 'edit'])->name('ai-evaluation.edit');
        Route::put('ai-evaluation/{evaluation}', [App\Http\Controllers\AIEvaluationController::class, 'update'])->name('ai-evaluation.update');
        Route::post('ai-evaluation/{evaluation}/regenerate', [App\Http\Controllers\AIEvaluationController::class, 'regenerate'])->name('ai-evaluation.regenerate');
    });

    Route::middleware(['permission:ai-evaluation.delete'])->group(function () {
        Route::delete('ai-evaluation/{evaluation}', [App\Http\Controllers\AIEvaluationController::class, 'destroy'])->name('ai-evaluation.destroy');
    });
});
