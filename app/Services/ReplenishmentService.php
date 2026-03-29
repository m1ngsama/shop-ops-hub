<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ReplenishmentService
{
    public function recommendations(): Collection
    {
        return Product::query()
            ->with(['supplier', 'inventoryBatches'])
            ->get()
            ->map(function (Product $product): array {
                $availableUnits = $product->availableInventory();

                return [
                    'product' => $product,
                    'available_units' => $availableUnits,
                    'gap' => max($product->safety_stock - $availableUnits, 0),
                ];
            })
            ->filter(fn (array $alert): bool => $alert['available_units'] <= $alert['product']->safety_stock)
            ->sortByDesc('gap')
            ->values();
    }
}
