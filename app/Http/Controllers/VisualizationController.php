<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Order;
use App\Models\SyncRun;
use App\Services\DashboardMetricsService;
use App\Services\ReplenishmentService;
use Illuminate\Contracts\View\View;

class VisualizationController extends Controller
{
    public function __invoke(
        DashboardMetricsService $dashboardMetricsService,
        ReplenishmentService $replenishmentService
    ): View {
        return view('insights.index', [
            'summary' => $dashboardMetricsService->summary(),
            'financialTrend' => $dashboardMetricsService->financialTrend(),
            'profitBreakdown' => $dashboardMetricsService->profitBreakdown(),
            'orderStatusBreakdown' => $dashboardMetricsService->orderStatusBreakdown(),
            'channelPerformance' => $dashboardMetricsService->channelPerformance(),
            'channelProfitability' => $dashboardMetricsService->channelProfitability(),
            'inventoryCoverage' => $dashboardMetricsService->inventoryCoverage(),
            'topProducts' => $dashboardMetricsService->topProducts(),
            'listingPerformance' => Listing::query()
                ->with(['product', 'channel'])
                ->orderByDesc('performance_score')
                ->orderByDesc('conversion_rate')
                ->take(6)
                ->get(),
            'recentSyncRuns' => SyncRun::query()
                ->with(['channel', 'user'])
                ->latest()
                ->take(6)
                ->get(),
            'inventoryRisks' => $replenishmentService->recommendations()->take(5),
            'visualSummary' => [
                'average_order_value' => round((float) Order::query()->selectRaw('COALESCE(AVG(sale_price * quantity), 0) as value')->value('value'), 2),
                'fulfilled_ratio' => $this->fulfilledRatio(),
            ],
        ]);
    }

    private function fulfilledRatio(): float
    {
        $total = Order::query()->count();

        if ($total === 0) {
            return 0.0;
        }

        $fulfilled = Order::query()
            ->whereIn('status', ['shipped', 'delivered'])
            ->count();

        return round(($fulfilled / $total) * 100, 1);
    }
}
