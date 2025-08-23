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
Route::get('about', [AdditionalPageController::class, 'About'])->name('additional-page.about');
Route::get('terms', [AdditionalPageController::class, 'Terms'])->name('additional-page.terms');
Route::get('privacy', [AdditionalPageController::class, 'Privacy'])->name('additional-page.privacy');

Route::middleware(['auth'])->group(function () {
    Route::get('home', [HomeController::class, 'Home'])->name('home');
    
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
        Route::get('sales-orders', function () { return 'Sales Orders List - View Permission Required'; })->name('sales-orders.index');
    });
    
    Route::middleware(['permission:sales-orders.create'])->group(function () {
        Route::get('sales-orders/create', function () { return 'Create Sales Order - Create Permission Required'; })->name('sales-orders.create');
    });
    
    // Finance Management
    Route::middleware(['permission:general-ledger.view'])->group(function () {
        Route::get('general-ledger', function () { return 'General Ledger - View Permission Required'; })->name('general-ledger.index');
    });
    
    Route::middleware(['permission:expenses.view'])->group(function () {
        Route::get('expenses', function () { return 'Expenses List - View Permission Required'; })->name('expenses.index');
    });
    
    // HR Management
    Route::middleware(['permission:employees.view'])->group(function () {
        Route::get('employees', function () { return 'Employees List - View Permission Required'; })->name('employees.index');
    });
    
    Route::middleware(['permission:attendances.view'])->group(function () {
        Route::get('attendances', function () { return 'Attendances List - View Permission Required'; })->name('attendances.index');
    });
});
