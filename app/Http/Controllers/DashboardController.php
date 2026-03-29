<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\SyncRun;
use App\Services\DashboardMetricsService;
use App\Services\ReplenishmentService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(
        DashboardMetricsService $dashboardMetricsService,
        ReplenishmentService $replenishmentService
    ): View {
        return view('dashboard', [
            'summary' => $dashboardMetricsService->summary(),
            'revenueTrend' => $dashboardMetricsService->revenueTrend(),
            'orderStatusBreakdown' => $dashboardMetricsService->orderStatusBreakdown(),
            'categoryPerformance' => $dashboardMetricsService->categoryPerformance(),
            'channelHealth' => $dashboardMetricsService->channelHealth(),
            'channels' => $dashboardMetricsService->channelPerformance(),
            'topProducts' => $dashboardMetricsService->topProducts(),
            'lowStockAlerts' => $replenishmentService->recommendations()->take(5),
            'products' => Product::query()->with(['supplier', 'inventoryBatches', 'listings'])->orderBy('name')->take(6)->get(),
            'recentOrders' => Order::query()->with(['product', 'channel'])->latest('ordered_at')->take(8)->get(),
            'recentSyncRuns' => SyncRun::query()->with(['channel', 'user'])->latest()->take(6)->get(),
        ]);
    }
}
