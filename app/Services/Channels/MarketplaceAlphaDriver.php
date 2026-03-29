<?php

namespace App\Services\Channels;

use App\Models\Channel;
use App\Models\Product;

class MarketplaceAlphaDriver implements ChannelDriver
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
                'external_sku' => 'PA-'.$product->sku,
                'status' => 'active',
                'price' => round((float) $product->target_price + ($index * 1.2), 2),
                'ad_daily_budget' => 18 + ($index * 4),
                'review_count' => 120 + ($index * 27),
                'conversion_rate' => 7.6 + ($index * 0.6),
                'performance_score' => 88 + ($index * 1.5),
                'last_synced_at' => $timestamp,
                'metadata' => [
                    'program' => '平台仓配',
                    'badge' => $index === 0 ? '核心款' : '稳定款',
                ],
            ];

            $orders[] = [
                'product_id' => $product->id,
                'channel_id' => $channel->id,
                'external_order_no' => sprintf('PA-%s-%02d', $timestamp->format('YmdHis'), $index + 1),
                'status' => $index === 0 ? 'processing' : 'shipped',
                'quantity' => $index + 1,
                'sale_price' => round((float) $product->target_price + 2.2, 2),
                'ad_spend' => 4 + ($index * 1.1),
                'channel_fee' => 3.2 + ($index * 0.9),
                'ordered_at' => $timestamp->copy()->subHours($index * 3),
            ];
        }

        return [
            'listings' => $listings,
            'orders' => $orders,
            'notes' => '平台一已完成价格、转化与订单增量同步。',
        ];
    }
}
