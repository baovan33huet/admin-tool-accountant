<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ProductSaleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/stores/create', [StoreController::class, 'create'])->name('stores.create');
    Route::post('/stores', [StoreController::class, 'store'])->name('stores.store');

    Route::get('/store-{store}/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/store-{store}/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/store-{store}/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/store-{store}/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/store-{store}/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/store-{store}/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/store-{store}/revenue', [ProductController::class, 'revenue'])->name('products.revenue');

    Route::get('/store-{store}/imports', [ProductImportController::class, 'index'])->name('product-imports.index');
    Route::get('/store-{store}/imports/create', [ProductImportController::class, 'create'])->name('product-imports.create');
    Route::post('/store-{store}/imports', [ProductImportController::class, 'store'])->name('product-imports.store');

    Route::get('/store-{store}/sales', [ProductSaleController::class, 'index'])->name('product-sales.index');
    Route::get('/store-{store}/sales/create', [ProductSaleController::class, 'create'])->name('product-sales.create');
    Route::post('/store-{store}/sales', [ProductSaleController::class, 'store'])->name('product-sales.store');
});
