<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdditionalPageController;

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
});
