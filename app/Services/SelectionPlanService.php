<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class SelectionPlanService
{
    private const SESSION_KEY = 'storefront.selection_plan';

    public function snapshot(): array
    {
        $items = $this->items();

        return [
            'items' => $items,
            'summary' => [
                'line_count' => $items->count(),
                'total_quantity' => (int) $items->sum('quantity'),
                'estimated_value' => round((float) $items->sum('estimated_value'), 2),
                'average_margin' => round((float) $items->avg(fn (array $item): float => $item['product']->marginRate()), 1),
                'fastest_lead_time' => $items->isNotEmpty()
                    ? (int) $items->min(fn (array $item): int => $item['product']->lead_time_days)
                    : null,
            ],
        ];
    }

    public function summary(): array
    {
        return $this->snapshot()['summary'];
    }

    public function count(): int
    {
        return (int) collect($this->raw())->sum();
    }

    public function quantityFor(Product $product): int
    {
        return (int) ($this->raw()[$product->getKey()] ?? 0);
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $items = $this->raw();
        $items[$product->getKey()] = max(1, (int) ($items[$product->getKey()] ?? 0) + $quantity);

        session([self::SESSION_KEY => $items]);
    }

    public function update(Product $product, int $quantity): void
    {
        $items = $this->raw();

        if ($quantity <= 0) {
            unset($items[$product->getKey()]);
        } else {
            $items[$product->getKey()] = $quantity;
        }

        session([self::SESSION_KEY => $items]);
    }

    public function remove(Product $product): void
    {
        $items = $this->raw();
        unset($items[$product->getKey()]);

        session([self::SESSION_KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function items(): Collection
    {
        $raw = collect($this->raw())
            ->map(fn (mixed $quantity): int => max(1, (int) $quantity))
            ->filter(fn (int $quantity): bool => $quantity > 0);

        if ($raw->isEmpty()) {
            return collect();
        }

        $products = Product::query()
            ->with(['supplier', 'inventoryBatches', 'listings.channel'])
            ->whereKey($raw->keys()->all())
            ->get()
            ->keyBy(fn (Product $product): int => $product->getKey());

        return $raw
            ->map(function (int $quantity, int|string $productId) use ($products): ?array {
                $product = $products->get((int) $productId);

                if (! $product) {
                    return null;
                }

                $estimatedValue = round((float) $product->target_price * $quantity, 2);

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'available_inventory' => $product->availableInventory(),
                    'estimated_value' => $estimatedValue,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @return array<int, int>
     */
    private function raw(): array
    {
        return collect(session(self::SESSION_KEY, []))
            ->mapWithKeys(fn (mixed $quantity, mixed $productId): array => [(int) $productId => max(1, (int) $quantity)])
            ->all();
    }
}
