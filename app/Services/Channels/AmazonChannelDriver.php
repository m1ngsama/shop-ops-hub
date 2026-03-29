<?php

namespace App\Services\Channels;

use App\Models\Channel;
use App\Models\Product;

class AmazonChannelDriver implements ChannelDriver
{
    public function pull(Channel $channel): array
    {
        $products = Product::query()->orderBy('sku')->take(3)->get();
        $timestamp = now();
        $listings = [];
        $orders = [];

        foreach ($products as $index => $product) {
            $listings[] = [
                'product_id' => $product->id,
                'channel_id' => $channel->id,
                'external_sku' => 'AMZ-'.$product->sku,
                'status' => 'active',
                'price' => round((float) $product->target_price + ($index * 1.25), 2),
                'ad_daily_budget' => 18 + ($index * 4),
                'review_count' => 120 + ($index * 27),
                'conversion_rate' => 7.8 + ($index * 0.7),
                'performance_score' => 88 + ($index * 1.4),
                'last_synced_at' => $timestamp,
                'metadata' => [
                    'program' => 'FBA',
                    'badge' => $index === 0 ? 'hero-sku' : 'steady-grower',
                ],
            ];

            $orders[] = [
                'product_id' => $product->id,
                'channel_id' => $channel->id,
                'external_order_no' => sprintf('AMZ-%s-%02d', $timestamp->format('YmdHis'), $index + 1),
                'status' => $index === 0 ? 'processing' : 'shipped',
                'quantity' => $index + 1,
                'sale_price' => round((float) $product->target_price + 2.5, 2),
                'ad_spend' => 4 + ($index * 1.1),
                'channel_fee' => 3.2 + ($index * 0.9),
                'ordered_at' => $timestamp->copy()->subHours($index * 3),
            ];
        }

        return [
            'listings' => $listings,
            'orders' => $orders,
            'notes' => 'Simulated Amazon delta sync for catalog health, pricing, and order flow.',
        ];
    }
}
