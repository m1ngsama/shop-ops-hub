<?php

use App\Http\Controllers\Api\ChannelSyncController;
use App\Http\Controllers\Api\DashboardMetricsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['ops.token', 'throttle:60,1'])->group(function (): void {
    Route::get('/dashboard/metrics', DashboardMetricsController::class);
});

Route::middleware(['ops.token', 'throttle:10,1'])->group(function (): void {
    Route::post('/channels/{channel}/sync', ChannelSyncController::class);
});
