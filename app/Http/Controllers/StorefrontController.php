<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Product;
use App\Services\SelectionPlanService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StorefrontController extends Controller
{
    public function home(SelectionPlanService $selectionPlanService): View
    {
        $products = Product::query()
            ->with(['supplier', 'inventoryBatches', 'listings.channel'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $recommendedProducts = $this->applySort($products, 'recommended')->values();
        $productsBySku = $products->keyBy('sku');

        return view('storefront.home', [
            'featuredProducts' => $recommendedProducts->take(4),
            'comparisonProducts' => $recommendedProducts->take(4),
            'categoryHighlights' => $products
                ->groupBy('category')
                ->map(function (Collection $group, string $category): array {
                    return [
                        'name' => $category,
                        'sku_count' => $group->count(),
                        'average_margin' => round((float) $group->avg(fn (Product $product): float => $product->marginRate()), 1),
                        'average_lead_time' => round((float) $group->avg('lead_time_days')),
                    ];
                })
                ->sortByDesc('sku_count')
                ->take(4)
                ->values(),
            'curatedCollections' => $this->buildCuratedCollections($productsBySku),
            'channelHighlights' => Channel::query()
                ->withCount(['listings', 'orders'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'supplierHighlights' => $products
                ->groupBy(fn (Product $product): string => $product->supplier?->name ?? '未分配')
                ->map(function (Collection $group, string $name): array {
                    $supplier = $group->first()?->supplier;

                    return [
                        'name' => $name,
                        'quality_score' => $supplier?->quality_score,
                        'sku_count' => $group->count(),
                        'average_lead_time' => round((float) $group->avg('lead_time_days')),
                        'categories' => $group->pluck('category')->unique()->values()->implode(' / '),
                    ];
                })
                ->sortByDesc('quality_score')
                ->take(3)
                ->values(),
            'buyerVoices' => $this->buildBuyerVoices($recommendedProducts),
            'servicePromises' => $this->buildServicePromises($products),
            'heroSummary' => [
                'active_products' => $products->count(),
                'available_inventory' => $products->sum(fn (Product $product): int => $product->availableInventory()),
                'average_margin' => round((float) $products->avg(fn (Product $product): float => $product->marginRate()), 1),
                'fastest_lead_time' => (int) $products->min('lead_time_days'),
            ],
            'planSummary' => $selectionPlanService->summary(),
        ]);
    }

    public function catalog(Request $request, SelectionPlanService $selectionPlanService): View
    {
        $search = trim((string) $request->string('search'));
        $category = $request->string('category')->toString();
        $sort = $request->string('sort')->toString() ?: 'recommended';
        $page = max(1, (int) $request->integer('page', 1));

        $products = Product::query()
            ->with(['supplier', 'inventoryBatches', 'listings.channel'])
            ->where('status', 'active')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('sku', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('selling_points', 'like', "%{$search}%")
                        ->orWhere('marketplace_focus', 'like', "%{$search}%");
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->get();

        $products = $this->paginateSortedCollection(
            $this->applySort($products, $sort),
            9,
            $page,
            $request->url(),
            $request->query()
        );

        return view('storefront.catalog', [
            'products' => $products,
            'categories' => Product::query()->select('category')->distinct()->orderBy('category')->pluck('category'),
            'filters' => [
                'search' => $search,
                'category' => $category,
                'sort' => $sort,
            ],
            'planSummary' => $selectionPlanService->summary(),
        ]);
    }

    public function show(Product $product, SelectionPlanService $selectionPlanService): View
    {
        $product->load([
            'supplier',
            'inventoryBatches',
            'listings.channel',
            'orders.channel',
        ]);

        return view('storefront.show', [
            'product' => $product,
            'relatedProducts' => Product::query()
                ->with(['supplier', 'inventoryBatches', 'listings.channel'])
                ->where('status', 'active')
                ->where('category', $product->category)
                ->whereKeyNot($product->getKey())
                ->orderBy('name')
                ->take(3)
                ->get(),
            'companionProducts' => Product::query()
                ->with(['supplier', 'inventoryBatches', 'listings.channel'])
                ->where('status', 'active')
                ->where('category', '!=', $product->category)
                ->whereKeyNot($product->getKey())
                ->orderByDesc('target_price')
                ->take(2)
                ->get(),
            'faqItems' => $this->buildFaqItems($product),
            'planSummary' => $selectionPlanService->summary(),
            'selectedQuantity' => $selectionPlanService->quantityFor($product),
        ]);
    }

    private function applySort(Collection $items, string $sort): Collection
    {
        return match ($sort) {
            'price_low' => $items->sortBy(fn (Product $product): float => (float) $product->target_price),
            'price_high' => $items->sortByDesc(fn (Product $product): float => (float) $product->target_price),
            'margin' => $items->sortByDesc(fn (Product $product): float => $product->marginRate()),
            'lead_time' => $items->sortBy(fn (Product $product): int => $product->lead_time_days),
            default => $items->sortByDesc(function (Product $product): float {
                $listingScore = $product->listings->avg('performance_score') ?? 0;

                return ($product->availableInventory() * 0.3) + ($product->marginRate() * 1.5) + ((float) $listingScore);
            }),
        };
    }

    private function paginateSortedCollection(
        Collection $items,
        int $perPage,
        int $page,
        string $path,
        array $query
    ): LengthAwarePaginator {
        $items = $items->values();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $path,
                'query' => $query,
            ]
        );
    }

    private function buildCuratedCollections(Collection $productsBySku): Collection
    {
        return collect([
            [
                'tag' => '内容投放',
                'title' => '轻健身与出行组合',
                'copy' => '适合用短视频与内容种草带动转化，兼顾客单价和搭配购买。',
                'skus' => ['WEAR-3104', 'TRIP-4107'],
            ],
            [
                'tag' => '居家生活',
                'title' => '居家香氛与桌面收纳',
                'copy' => '偏礼品场景，适合节庆、换季和组合陈列。',
                'skus' => ['HOME-2201', 'DESK-5202'],
            ],
            [
                'tag' => '复购路线',
                'title' => '个护回购与加购补单',
                'copy' => '以稳定复购和老客补单为目标，适合建立连续经营节奏。',
                'skus' => ['CARE-1001', 'DESK-5202'],
            ],
        ])->map(function (array $collection) use ($productsBySku): ?array {
            $products = collect($collection['skus'])
                ->map(fn (string $sku): ?Product => $productsBySku->get($sku))
                ->filter();

            if ($products->isEmpty()) {
                return null;
            }

            return [
                'tag' => $collection['tag'],
                'title' => $collection['title'],
                'copy' => $collection['copy'],
                'products' => $products->values(),
                'average_margin' => round((float) $products->avg(fn (Product $product): float => $product->marginRate()), 1),
                'average_lead_time' => round((float) $products->avg('lead_time_days')),
                'estimated_ticket' => round((float) $products->sum(fn (Product $product): float => (float) $product->target_price), 2),
            ];
        })->filter()->values();
    }

    private function buildBuyerVoices(Collection $products): Collection
    {
        return $products
            ->sortByDesc(fn (Product $product): int => (int) $product->listings->sum('review_count'))
            ->take(3)
            ->values()
            ->map(function (Product $product): array {
                $reviewCount = (int) $product->listings->sum('review_count');
                $score = round((float) ($product->listings->avg('performance_score') ?? 0), 1);
                $conversion = round((float) ($product->listings->avg('conversion_rate') ?? 0), 1);

                return [
                    'product' => $product,
                    'score' => $score,
                    'conversion' => $conversion,
                    'review_count' => $reviewCount,
                    'quote' => match ($product->category) {
                        '个护' => '复购反馈稳定，适合做持续经营，不用靠极端促销拉动。',
                        '服饰' => '展示效果和内容带货更重要，颜色与尺码组合会直接影响转化。',
                        default => '组合购买表现更好，详情页里把使用场景说清楚后更容易成交。',
                    },
                ];
            });
    }

    private function buildServicePromises(Collection $products): Collection
    {
        $averageLeadTime = round((float) $products->avg('lead_time_days'));
        $averageQuality = round((float) $products
            ->map(fn (Product $product): ?int => $product->supplier?->quality_score)
            ->filter()
            ->avg());

        return collect([
            [
                'title' => '商品信息集中透明',
                'copy' => '前台页面直接展示价格区间、交期、库存和渠道覆盖，不把经营判断藏在后台。',
                'value' => $products->count().' 个 SKU',
            ],
            [
                'title' => '履约能力可视化',
                'copy' => '公开页面就能看到库存和交期，让前端浏览与后台履约保持同一套事实来源。',
                'value' => '平均 '.$averageLeadTime.' 天交期',
            ],
            [
                'title' => '供给质量有依据',
                'copy' => '供应商质量分和类目覆盖同时展示，降低“看起来不错但执行不稳”的风险。',
                'value' => '平均质量 '.$averageQuality.' 分',
            ],
        ]);
    }

    private function buildFaqItems(Product $product): Collection
    {
        $availableInventory = $product->availableInventory();
        $averageConversion = round((float) ($product->listings->avg('conversion_rate') ?? 0), 1);

        return collect([
            [
                'question' => '多久可以进入下一轮补货或试单？',
                'answer' => $availableInventory > $product->safety_stock
                    ? '当前库存高于安全库存，可以先做小批量试单，再根据渠道反馈扩量。'
                    : '当前库存低于安全库存，建议先确认在途批次和供应商交期，再决定是否放量。',
            ],
            [
                'question' => '适合什么样的成交方式？',
                'answer' => $averageConversion >= 7
                    ? '当前转化率表现较好，适合做内容承接、组合加购和直接成交。'
                    : '更适合通过场景教育、套装组合或更明确的利益点来完成转化。',
            ],
            [
                'question' => '后台会继续跟哪些数据？',
                'answer' => '后台会继续跟踪库存覆盖、渠道刊登表现、订单利润和同步任务执行状态，避免前台展示与执行脱节。',
            ],
        ]);
    }
}
