<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
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
        $lowStockAlerts = $replenishmentService->recommendations()->take(5);
        $products = Product::query()
            ->with(['supplier', 'inventoryBatches', 'listings'])
            ->orderBy('name')
            ->take(6)
            ->get();

        return view('dashboard', [
            'summary' => $dashboardMetricsService->summary(),
            'revenueTrend' => $dashboardMetricsService->revenueTrend(),
            'orderStatusBreakdown' => $dashboardMetricsService->orderStatusBreakdown(),
            'categoryPerformance' => $dashboardMetricsService->categoryPerformance(),
            'channelHealth' => $dashboardMetricsService->channelHealth(),
            'channels' => $dashboardMetricsService->channelPerformance(),
            'topProducts' => $dashboardMetricsService->topProducts(),
            'lowStockAlerts' => $lowStockAlerts,
            'products' => $products,
            'recentOrders' => Order::query()->with(['product', 'channel'])->latest('ordered_at')->take(8)->get(),
            'recentSyncRuns' => SyncRun::query()->with(['channel', 'user'])->latest()->take(6)->get(),
            'orderPipeline' => collect([
                [
                    'label' => '待处理',
                    'tone' => 'warning',
                    'count' => Order::query()->where('status', 'processing')->count(),
                    'revenue' => (float) Order::query()->where('status', 'processing')->selectRaw('COALESCE(SUM(sale_price * quantity), 0) as total')->value('total'),
                ],
                [
                    'label' => '已发货',
                    'tone' => 'info',
                    'count' => Order::query()->where('status', 'shipped')->count(),
                    'revenue' => (float) Order::query()->where('status', 'shipped')->selectRaw('COALESCE(SUM(sale_price * quantity), 0) as total')->value('total'),
                ],
                [
                    'label' => '已签收',
                    'tone' => 'success',
                    'count' => Order::query()->where('status', 'delivered')->count(),
                    'revenue' => (float) Order::query()->where('status', 'delivered')->selectRaw('COALESCE(SUM(sale_price * quantity), 0) as total')->value('total'),
                ],
            ]),
            'supplierPulse' => Supplier::query()
                ->withCount('products')
                ->orderByDesc('quality_score')
                ->take(4)
                ->get()
                ->map(function (Supplier $supplier) use ($products): array {
                    $supplierProducts = $products->where('supplier_id', $supplier->id);

                    return [
                        'supplier' => $supplier,
                        'product_count' => $supplier->products_count,
                        'average_margin' => round((float) $supplierProducts->avg(fn (Product $product): float => $product->marginRate()), 1),
                        'available_inventory' => $supplierProducts->sum(fn (Product $product): int => $product->availableInventory()),
                    ];
                }),
            'riskBoard' => collect([
                [
                    'title' => '低库存缺口',
                    'value' => $lowStockAlerts->count().' 个',
                    'copy' => $lowStockAlerts->isNotEmpty()
                        ? $lowStockAlerts->first()['product']->name.' 缺口 '.$lowStockAlerts->first()['gap'].' 件'
                        : '当前没有低库存风险',
                    'tone' => $lowStockAlerts->isNotEmpty() ? 'warning' : 'success',
                ],
                [
                    'title' => '同步队列',
                    'value' => SyncRun::query()->whereIn('status', ['queued', 'running'])->count().' 条',
                    'copy' => '把手动发起、接口触发和调度任务放到一个执行面板里。',
                    'tone' => 'info',
                ],
                [
                    'title' => '操作审计',
                    'value' => AuditLog::query()->count().' 条',
                    'copy' => '登录、同步和前台意向动作都能追溯到最近记录。',
                    'tone' => 'brand',
                ],
            ]),
        ]);
    }
}
