<?php

namespace App\Services\Channels;

use App\Models\Channel;
use App\Models\Product;

class DirectStoreDriver implements ChannelDriver
{
    public function pull(Channel $channel): array
    {
        $products = Product::query()->latest('id')->take(2)->get();
        $timestamp = now();
        $listings = [];
        $orders = [];

        foreach ($products as $index => $product) {
            $listings[] = [
                'product_id' => $product->id,
                'channel_id' => $channel->id,
                'external_sku' => 'DS-'.$product->sku,
                'status' => 'active',
                'price' => round((float) $product->target_price + 0.5, 2),
                'ad_daily_budget' => 8 + ($index * 2),
                'review_count' => 16 + ($index * 10),
                'conversion_rate' => 6.4 + ($index * 0.9),
                'performance_score' => 83 + ($index * 2.3),
                'last_synced_at' => $timestamp,
                'metadata' => [
                    'program' => '站内直售',
                    'badge' => '活动位',
                ],
            ];

            $orders[] = [
                'product_id' => $product->id,
                'channel_id' => $channel->id,
                'external_order_no' => sprintf('DS-%s-%02d', $timestamp->format('YmdHis'), $index + 1),
                'status' => $index === 0 ? 'delivered' : 'shipped',
                'quantity' => $index + 1,
                'sale_price' => round((float) $product->target_price + 1.1, 2),
                'ad_spend' => 1.5 + ($index * 0.8),
                'channel_fee' => 1.2 + ($index * 0.4),
                'ordered_at' => $timestamp->copy()->subHours(($index + 1) * 6),
            ];
        }

        return [
            'listings' => $listings,
            'orders' => $orders,
            'notes' => '独立站已完成广告归因与订单快照同步。',
        ];
    }
}
