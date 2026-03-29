<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Product;
use App\Models\SyncRun;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardMetricsService
{
    public function summary(): array
    {
        return Cache::remember('dashboard:summary', now()->addMinutes(5), function (): array {
            $weeklyRevenue = (float) Order::query()
                ->where('ordered_at', '>=', now()->subDays(7))
                ->selectRaw('COALESCE(SUM(sale_price * quantity), 0) as total')
                ->value('total');

            $grossProfit = (float) Order::query()
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->selectRaw('COALESCE(SUM((orders.sale_price * orders.quantity) - (products.cost_price * orders.quantity) - orders.ad_spend - orders.channel_fee), 0) as total')
                ->value('total');

            $grossRevenue = (float) Order::query()
                ->selectRaw('COALESCE(SUM(sale_price * quantity), 0) as total')
                ->value('total');

            return [
                'active_products' => Product::query()->where('status', 'active')->count(),
                'active_listings' => Listing::query()->where('status', 'active')->count(),
                'low_stock_count' => app(ReplenishmentService::class)->recommendations()->count(),
                'weekly_revenue' => round($weeklyRevenue, 2),
                'gross_margin_rate' => $grossRevenue > 0 ? round(($grossProfit / $grossRevenue) * 100, 1) : 0.0,
                'recent_orders' => Order::query()->where('ordered_at', '>=', now()->subDays(7))->count(),
                'latest_sync_at' => optional(SyncRun::query()->latest()->first()?->created_at)->toDateTimeString(),
            ];
        });
    }

    public function channelPerformance(): Collection
    {
        return Channel::query()
            ->leftJoin('orders', 'channels.id', '=', 'orders.channel_id')
            ->select(
                'channels.id',
                'channels.code',
                'channels.name',
                'channels.marketplace',
                'channels.region'
            )
            ->selectRaw('COUNT(orders.id) as order_count')
            ->selectRaw('COALESCE(SUM(orders.sale_price * orders.quantity), 0) as revenue')
            ->groupBy('channels.id', 'channels.code', 'channels.name', 'channels.marketplace', 'channels.region')
            ->orderByDesc('revenue')
            ->get();
    }

    public function topProducts(): Collection
    {
        return Product::query()
            ->leftJoin('orders', 'products.id', '=', 'orders.product_id')
            ->select('products.id', 'products.sku', 'products.name', 'products.category')
            ->selectRaw('COALESCE(SUM(orders.sale_price * orders.quantity), 0) as revenue')
            ->selectRaw('COALESCE(SUM(orders.quantity), 0) as units_sold')
            ->groupBy('products.id', 'products.sku', 'products.name', 'products.category')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();
    }
}
