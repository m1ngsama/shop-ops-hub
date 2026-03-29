<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products.index', [
            'products' => Product::query()
                ->with(['supplier', 'inventoryBatches', 'listings.channel'])
                ->orderBy('name')
                ->get(),
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
