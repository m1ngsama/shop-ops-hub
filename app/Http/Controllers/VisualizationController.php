<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
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
            'recentAuditLogs' => AuditLog::query()
                ->with('user')
                ->latest()
                ->take(5)
                ->get(),
            'supplierPulse' => Supplier::query()
                ->withCount('products')
                ->orderByDesc('quality_score')
                ->take(4)
                ->get(),
            'assortmentReadiness' => Product::query()
                ->with(['inventoryBatches', 'listings'])
                ->where('status', 'active')
                ->orderBy('name')
                ->get()
                ->map(function (Product $product): array {
                    $availableInventory = $product->availableInventory();
                    $reviewCount = (int) $product->listings->sum('review_count');

                    return [
                        'product' => $product,
                        'inventory' => $availableInventory,
                        'review_count' => $reviewCount,
                        'margin_rate' => $product->marginRate(),
                        'readiness_score' => round(($availableInventory * 0.2) + ($product->marginRate() * 1.2) + ($reviewCount * 0.05), 1),
                    ];
                })
                ->sortByDesc('readiness_score')
                ->take(5)
                ->values(),
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
