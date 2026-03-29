<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = $request->string('status')->toString();
        $category = $request->string('category')->toString();

        return view('products.index', [
            'products' => Product::query()
                ->with(['supplier', 'inventoryBatches', 'listings.channel'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('sku', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('marketplace_focus', 'like', "%{$search}%");
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($category !== '', fn ($query) => $query->where('category', $category))
                ->orderBy('name')
                ->paginate(12)
                ->withQueryString(),
            'categories' => Product::query()->select('category')->distinct()->orderBy('category')->pluck('category'),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'category' => $category,
            ],
        ]);
    }

    public function show(Product $product): View
    {
        return view('products.show', [
            'product' => $product->load([
                'supplier',
                'inventoryBatches',
                'listings.channel',
                'orders.channel',
            ]),
        ]);
    }
}
