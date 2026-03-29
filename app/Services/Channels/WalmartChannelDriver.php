<?php

namespace App\Services\Channels;

use App\Models\Channel;
use App\Models\Product;

class WalmartChannelDriver implements ChannelDriver
{
    public function pull(Channel $channel): array
    {
        $products = Product::query()->inRandomOrder()->take(2)->get();
        $timestamp = now();
        $listings = [];
        $orders = [];

        foreach ($products as $index => $product) {
            $listings[] = [
                'product_id' => $product->id,
                'channel_id' => $channel->id,
                'external_sku' => 'WMT-'.$product->sku,
                'status' => 'active',
                'price' => round((float) $product->target_price - 1.35 + ($index * 0.8), 2),
                'ad_daily_budget' => 9 + ($index * 2.5),
                'review_count' => 42 + ($index * 18),
                'conversion_rate' => 5.9 + ($index * 0.6),
                'performance_score' => 79 + ($index * 2.2),
                'last_synced_at' => $timestamp,
                'metadata' => [
                    'program' => 'WFS',
                    'badge' => 'margin-watch',
                ],
            ];

            $orders[] = [
                'product_id' => $product->id,
                'channel_id' => $channel->id,
                'external_order_no' => sprintf('WMT-%s-%02d', $timestamp->format('YmdHis'), $index + 1),
                'status' => 'processing',
                'quantity' => 1,
                'sale_price' => round((float) $product->target_price - 0.75, 2),
                'ad_spend' => 2.5 + ($index * 0.7),
                'channel_fee' => 2.1 + ($index * 0.5),
                'ordered_at' => $timestamp->copy()->subHours(($index + 1) * 5),
            ];
        }

        return [
            'listings' => $listings,
            'orders' => $orders,
            'notes' => 'Simulated Walmart sync focused on pricing competitiveness and replenishment lead time.',
        ];
    }
}
