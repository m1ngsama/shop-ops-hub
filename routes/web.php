<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/channels', [ChannelController::class, 'index'])->name('channels.index');
Route::post('/channels/{channel}/sync', [ChannelController::class, 'sync'])->name('channels.sync');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
