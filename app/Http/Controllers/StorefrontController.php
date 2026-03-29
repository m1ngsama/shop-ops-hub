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

        return view('storefront.home', [
            'featuredProducts' => $products->take(4),
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
            'channelHighlights' => Channel::query()
                ->withCount(['listings', 'orders'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
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
}
