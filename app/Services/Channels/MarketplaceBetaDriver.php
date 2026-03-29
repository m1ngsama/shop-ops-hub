<?php

namespace App\Services\Channels;

use App\Models\Channel;
use App\Models\Product;

class MarketplaceBetaDriver implements ChannelDriver
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
                'external_sku' => 'PB-'.$product->sku,
                'status' => 'active',
                'price' => round((float) $product->target_price - 0.8 + ($index * 0.6), 2),
                'ad_daily_budget' => 10 + ($index * 2.8),
                'review_count' => 46 + ($index * 18),
                'conversion_rate' => 5.8 + ($index * 0.5),
                'performance_score' => 80 + ($index * 2.2),
                'last_synced_at' => $timestamp,
                'metadata' => [
                    'program' => '平台履约',
                    'badge' => '利润观察',
                ],
            ];

            $orders[] = [
                'product_id' => $product->id,
                'channel_id' => $channel->id,
                'external_order_no' => sprintf('PB-%s-%02d', $timestamp->format('YmdHis'), $index + 1),
                'status' => 'processing',
                'quantity' => 1,
                'sale_price' => round((float) $product->target_price - 0.65, 2),
                'ad_spend' => 2.8 + ($index * 0.7),
                'channel_fee' => 2.3 + ($index * 0.4),
                'ordered_at' => $timestamp->copy()->subHours(($index + 1) * 5),
            ];
        }

        return [
            'listings' => $listings,
            'orders' => $orders,
            'notes' => '平台二已完成价格带与库存压力同步。',
        ];
    }
}
