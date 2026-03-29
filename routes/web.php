<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SelectionPlanController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\VisualizationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('storefront.home');
Route::get('/catalog', [StorefrontController::class, 'catalog'])->name('storefront.catalog');
Route::get('/catalog/{product:sku}', [StorefrontController::class, 'show'])->name('storefront.products.show');
Route::get('/selection-plan', [SelectionPlanController::class, 'index'])->name('storefront.plan.index');
Route::post('/selection-plan/items/{product}', [SelectionPlanController::class, 'store'])->name('storefront.plan.store');
Route::patch('/selection-plan/items/{product}', [SelectionPlanController::class, 'update'])->name('storefront.plan.update');
Route::delete('/selection-plan/items/{product}', [SelectionPlanController::class, 'destroy'])->name('storefront.plan.destroy');
Route::delete('/selection-plan', [SelectionPlanController::class, 'clear'])->name('storefront.plan.clear');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::redirect('/dashboard', '/admin');
Route::redirect('/products', '/admin/products');
Route::redirect('/channels', '/admin/channels');
Route::redirect('/orders', '/admin/orders');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/insights', VisualizationController::class)->name('insights');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/channels', [ChannelController::class, 'index'])->name('channels.index');
        Route::post('/channels/{channel}/sync', [ChannelController::class, 'sync'])->name('channels.sync');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    });
