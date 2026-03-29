<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Product;
use App\Models\SyncRun;
use Carbon\Carbon;
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
                'queued_sync_runs' => SyncRun::query()->whereIn('status', ['queued', 'running'])->count(),
                'healthy_channels' => Channel::query()->where('is_active', true)->count(),
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

    public function revenueTrend(int $days = 7): Collection
    {
        $startDate = now()->subDays($days - 1)->startOfDay();

        $totals = Order::query()
            ->where('ordered_at', '>=', $startDate)
            ->selectRaw('DATE(ordered_at) as day')
            ->selectRaw('COALESCE(SUM(sale_price * quantity), 0) as revenue')
            ->groupBy('day')
            ->pluck('revenue', 'day');

        return collect(range(0, $days - 1))
            ->map(function (int $offset) use ($startDate, $totals): array {
                $day = $startDate->copy()->addDays($offset);
                $revenue = (float) ($totals[$day->toDateString()] ?? 0);

                return [
                    'date' => $day->toDateString(),
                    'label' => $day->format('M j'),
                    'revenue' => round($revenue, 2),
                ];
            });
    }

    public function financialTrend(int $days = 7): Collection
    {
        $startDate = now()->subDays($days - 1)->startOfDay();

        $revenueTotals = Order::query()
            ->where('ordered_at', '>=', $startDate)
            ->selectRaw('DATE(ordered_at) as day')
            ->selectRaw('COALESCE(SUM(sale_price * quantity), 0) as revenue')
            ->groupBy('day')
            ->pluck('revenue', 'day');

        $profitTotals = Order::query()
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->where('ordered_at', '>=', $startDate)
            ->selectRaw('DATE(orders.ordered_at) as day')
            ->selectRaw('COALESCE(SUM((orders.sale_price * orders.quantity) - (products.cost_price * orders.quantity) - orders.ad_spend - orders.channel_fee), 0) as profit')
            ->groupBy('day')
            ->pluck('profit', 'day');

        return collect(range(0, $days - 1))
            ->map(function (int $offset) use ($startDate, $revenueTotals, $profitTotals): array {
                $day = $startDate->copy()->addDays($offset);

                return [
                    'date' => $day->toDateString(),
                    'label' => $day->format('m/d'),
                    'revenue' => round((float) ($revenueTotals[$day->toDateString()] ?? 0), 2),
                    'profit' => round((float) ($profitTotals[$day->toDateString()] ?? 0), 2),
                ];
            });
    }

    public function orderStatusBreakdown(): Collection
    {
        return Order::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();
    }

    public function categoryPerformance(): Collection
    {
        return Product::query()
            ->leftJoin('orders', 'products.id', '=', 'orders.product_id')
            ->select('products.category')
            ->selectRaw('COUNT(DISTINCT products.id) as sku_count')
            ->selectRaw('COALESCE(SUM(orders.sale_price * orders.quantity), 0) as revenue')
            ->groupBy('products.category')
            ->orderByDesc('revenue')
            ->get();
    }

    public function channelHealth(): Collection
    {
        $performance = $this->channelPerformance()->keyBy('id');

        return Channel::query()
            ->with(['syncRuns' => fn ($query) => $query->latest()->take(1)])
            ->withCount('listings')
            ->orderBy('name')
            ->get()
            ->map(function (Channel $channel) use ($performance): array {
                $latestRun = $channel->syncRuns->first();
                $channelPerformance = $performance->get($channel->id);

                return [
                    'channel' => $channel,
                    'status' => $latestRun?->status ?? 'idle',
                    'latest_sync' => $latestRun?->created_at,
                    'orders' => (int) ($channelPerformance?->order_count ?? 0),
                    'revenue' => round((float) ($channelPerformance?->revenue ?? 0), 2),
                    'listing_count' => $channel->listings_count,
                    'is_stale' => $latestRun?->created_at instanceof Carbon
                        ? $latestRun->created_at->lt(now()->subHours(12))
                        : true,
                ];
            });
    }
}
