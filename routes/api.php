<?php

use App\Http\Controllers\Api\ChannelSyncController;
use App\Http\Controllers\Api\DashboardMetricsController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard/metrics', DashboardMetricsController::class);
Route::post('/channels/{channel}/sync', ChannelSyncController::class);
