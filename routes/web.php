<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdditionalPageController;
use App\Http\Controllers\ProductController;

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
        Route::get('customers', function () { return 'Customers List - View Permission Required'; })->name('customers.index');
    });
    
    // Inventory Management
    Route::middleware(['permission:warehouses.view'])->group(function () {
        Route::get('warehouses', function () { return 'Warehouses List - View Permission Required'; })->name('warehouses.index');
    });
    
    Route::middleware(['permission:stocks.view'])->group(function () {
        Route::get('stocks', function () { return 'Stocks List - View Permission Required'; })->name('stocks.index');
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
