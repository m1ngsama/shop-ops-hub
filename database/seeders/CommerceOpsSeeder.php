<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\InventoryBatch;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SyncRun;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CommerceOpsSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        SyncRun::query()->delete();
        Order::query()->delete();
        InventoryBatch::query()->delete();
        Listing::query()->delete();
        Product::query()->delete();
        Channel::query()->delete();
        Supplier::query()->delete();
        User::query()->delete();

        Schema::enableForeignKeyConstraints();

        User::query()->updateOrCreate(
            ['email' => config('shop_ops.admin_email')],
            [
                'name' => '系统管理员',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make(config('shop_ops.admin_password')),
            ]
        );

        $suppliers = collect([
            '华南工厂A' => Supplier::query()->create([
                'name' => '华南工厂A',
                'country_code' => 'CN',
                'lead_time_days' => 18,
                'contact_email' => 'vendor-a@example.local',
                'quality_score' => 93,
            ]),
            '宁波供应组B' => Supplier::query()->create([
                'name' => '宁波供应组B',
                'country_code' => 'CN',
                'lead_time_days' => 24,
                'contact_email' => 'vendor-b@example.local',
                'quality_score' => 88,
            ]),
            '杭州工厂C' => Supplier::query()->create([
                'name' => '杭州工厂C',
                'country_code' => 'CN',
                'lead_time_days' => 15,
                'contact_email' => 'vendor-c@example.local',
                'quality_score' => 90,
            ]),
        ]);

        $channels = collect([
            'marketplace_a' => Channel::query()->create([
                'code' => 'marketplace_a',
                'name' => '平台一-北美',
                'marketplace' => '平台一',
                'region' => '北美',
                'currency' => 'USD',
                'endpoint_url' => 'https://api.marketplace-a.example',
                'fee_percentage' => 15,
                'is_active' => true,
            ]),
            'marketplace_b' => Channel::query()->create([
                'code' => 'marketplace_b',
                'name' => '平台二-北美',
                'marketplace' => '平台二',
                'region' => '北美',
                'currency' => 'USD',
                'endpoint_url' => 'https://api.marketplace-b.example',
                'fee_percentage' => 12,
                'is_active' => true,
            ]),
            'direct_store' => Channel::query()->create([
                'code' => 'direct_store',
                'name' => '独立站-北美',
                'marketplace' => '独立站',
                'region' => '北美',
                'currency' => 'USD',
                'endpoint_url' => 'https://api.direct-store.example',
                'fee_percentage' => 6,
                'is_active' => true,
            ]),
        ]);

        $products = collect([
            Product::query()->create([
                'supplier_id' => $suppliers['华南工厂A']->id,
                'sku' => 'CARE-1001',
                'name' => '修护发膜',
                'category' => '个护',
                'status' => 'active',
                'marketplace_focus' => '主推平台一',
                'cost_price' => 6.40,
                'target_price' => 18.99,
                'fulfillment_fee' => 4.15,
                'safety_stock' => 140,
                'lead_time_days' => 18,
                'selling_points' => '复购率高，适合稳定拉新与老客补单。',
            ]),
            Product::query()->create([
                'supplier_id' => $suppliers['宁波供应组B']->id,
                'sku' => 'HOME-2201',
                'name' => '陶瓷香薰机',
                'category' => '家居',
                'status' => 'active',
                'marketplace_focus' => '平台二扩量',
                'cost_price' => 12.80,
                'target_price' => 34.50,
                'fulfillment_fee' => 7.10,
                'safety_stock' => 90,
                'lead_time_days' => 24,
                'selling_points' => '适合节庆活动与套装售卖，客单价稳定。',
            ]),
            Product::query()->create([
                'supplier_id' => $suppliers['杭州工厂C']->id,
                'sku' => 'WEAR-3104',
                'name' => '高腰运动紧身裤',
                'category' => '服饰',
                'status' => 'active',
                'marketplace_focus' => '独立站试投',
                'cost_price' => 9.20,
                'target_price' => 28.90,
                'fulfillment_fee' => 5.30,
                'safety_stock' => 120,
                'lead_time_days' => 15,
                'selling_points' => '适合内容投放与套装搭售，退货率需持续观察。',
            ]),
            Product::query()->create([
                'supplier_id' => $suppliers['宁波供应组B']->id,
                'sku' => 'TRIP-4107',
                'name' => '压缩旅行收纳袋',
                'category' => '出行',
                'status' => 'active',
                'marketplace_focus' => '利润款',
                'cost_price' => 7.50,
                'target_price' => 22.40,
                'fulfillment_fee' => 4.80,
                'safety_stock' => 160,
                'lead_time_days' => 20,
                'selling_points' => '利润空间健康，适合稳定投放与节假日放量。',
            ]),
            Product::query()->create([
                'supplier_id' => $suppliers['华南工厂A']->id,
                'sku' => 'DESK-5202',
                'name' => '桌面首饰收纳盒',
                'category' => '收纳',
                'status' => 'active',
                'marketplace_focus' => '平台双投',
                'cost_price' => 5.90,
                'target_price' => 19.60,
                'fulfillment_fee' => 4.05,
                'safety_stock' => 110,
                'lead_time_days' => 16,
                'selling_points' => '轻小件，适合组合推荐与加购转化。',
            ]),
        ])->keyBy('sku');

        $listingSeed = [
            ['sku' => 'CARE-1001', 'channel' => 'marketplace_a', 'price' => 18.99, 'budget' => 22.00, 'reviews' => 176, 'conversion' => 8.7, 'score' => 91.0],
            ['sku' => 'HOME-2201', 'channel' => 'marketplace_b', 'price' => 34.50, 'budget' => 16.00, 'reviews' => 54, 'conversion' => 5.4, 'score' => 82.4],
            ['sku' => 'WEAR-3104', 'channel' => 'marketplace_a', 'price' => 28.90, 'budget' => 19.50, 'reviews' => 88, 'conversion' => 6.9, 'score' => 86.8],
            ['sku' => 'WEAR-3104', 'channel' => 'direct_store', 'price' => 26.90, 'budget' => 14.00, 'reviews' => 22, 'conversion' => 7.8, 'score' => 84.0],
            ['sku' => 'TRIP-4107', 'channel' => 'marketplace_a', 'price' => 22.40, 'budget' => 18.00, 'reviews' => 129, 'conversion' => 8.1, 'score' => 89.2],
            ['sku' => 'DESK-5202', 'channel' => 'marketplace_b', 'price' => 19.60, 'budget' => 9.00, 'reviews' => 38, 'conversion' => 4.8, 'score' => 78.5],
        ];

        foreach ($listingSeed as $listing) {
            Listing::query()->create([
                'product_id' => $products[$listing['sku']]->id,
                'channel_id' => $channels[$listing['channel']]->id,
                'external_sku' => strtoupper($listing['channel']).'-'.$listing['sku'],
                'status' => 'active',
                'price' => $listing['price'],
                'ad_daily_budget' => $listing['budget'],
                'review_count' => $listing['reviews'],
                'conversion_rate' => $listing['conversion'],
                'performance_score' => $listing['score'],
                'last_synced_at' => now()->subHours(random_int(1, 12)),
                'metadata' => ['seeded' => true],
            ]);
        }

        $inventorySeed = [
            ['sku' => 'CARE-1001', 'warehouse' => 'CN-SZ-01', 'batch' => 'PC-2401', 'on_hand' => 52, 'reserved' => 11, 'inbound' => 48, 'eta' => now()->addDays(6)],
            ['sku' => 'HOME-2201', 'warehouse' => 'CN-NB-02', 'batch' => 'HM-2403', 'on_hand' => 38, 'reserved' => 4, 'inbound' => 20, 'eta' => now()->addDays(10)],
            ['sku' => 'WEAR-3104', 'warehouse' => 'CN-HZ-03', 'batch' => 'WR-2402', 'on_hand' => 148, 'reserved' => 19, 'inbound' => 0, 'eta' => null],
            ['sku' => 'TRIP-4107', 'warehouse' => 'CN-SZ-01', 'batch' => 'TP-2405', 'on_hand' => 210, 'reserved' => 28, 'inbound' => 40, 'eta' => now()->addDays(8)],
            ['sku' => 'DESK-5202', 'warehouse' => 'CN-DG-04', 'batch' => 'DS-2401', 'on_hand' => 62, 'reserved' => 6, 'inbound' => 12, 'eta' => now()->addDays(5)],
        ];

        foreach ($inventorySeed as $batch) {
            InventoryBatch::query()->create([
                'product_id' => $products[$batch['sku']]->id,
                'warehouse_code' => $batch['warehouse'],
                'batch_code' => $batch['batch'],
                'quantity_on_hand' => $batch['on_hand'],
                'quantity_reserved' => $batch['reserved'],
                'quantity_inbound' => $batch['inbound'],
                'inbound_eta' => $batch['eta'],
            ]);
        }

        $orderSeed = [
            ['sku' => 'CARE-1001', 'channel' => 'marketplace_a', 'order_no' => 'PA-20260328-001', 'status' => 'shipped', 'quantity' => 2, 'sale_price' => 18.99, 'ad_spend' => 5.10, 'channel_fee' => 4.30, 'hours_ago' => 8],
            ['sku' => 'WEAR-3104', 'channel' => 'marketplace_a', 'order_no' => 'PA-20260328-002', 'status' => 'processing', 'quantity' => 1, 'sale_price' => 28.90, 'ad_spend' => 4.40, 'channel_fee' => 4.85, 'hours_ago' => 12],
            ['sku' => 'TRIP-4107', 'channel' => 'marketplace_a', 'order_no' => 'PA-20260327-003', 'status' => 'shipped', 'quantity' => 3, 'sale_price' => 22.40, 'ad_spend' => 6.20, 'channel_fee' => 6.45, 'hours_ago' => 27],
            ['sku' => 'HOME-2201', 'channel' => 'marketplace_b', 'order_no' => 'PB-20260327-001', 'status' => 'processing', 'quantity' => 1, 'sale_price' => 34.50, 'ad_spend' => 3.60, 'channel_fee' => 4.10, 'hours_ago' => 31],
            ['sku' => 'DESK-5202', 'channel' => 'marketplace_b', 'order_no' => 'PB-20260326-002', 'status' => 'shipped', 'quantity' => 2, 'sale_price' => 19.60, 'ad_spend' => 2.90, 'channel_fee' => 3.20, 'hours_ago' => 49],
            ['sku' => 'WEAR-3104', 'channel' => 'direct_store', 'order_no' => 'DS-20260326-004', 'status' => 'delivered', 'quantity' => 1, 'sale_price' => 26.90, 'ad_spend' => 3.20, 'channel_fee' => 2.15, 'hours_ago' => 56],
        ];

        foreach ($orderSeed as $order) {
            Order::query()->create([
                'product_id' => $products[$order['sku']]->id,
                'channel_id' => $channels[$order['channel']]->id,
                'external_order_no' => $order['order_no'],
                'status' => $order['status'],
                'quantity' => $order['quantity'],
                'sale_price' => $order['sale_price'],
                'ad_spend' => $order['ad_spend'],
                'channel_fee' => $order['channel_fee'],
                'ordered_at' => now()->subHours($order['hours_ago']),
            ]);
        }

        SyncRun::query()->create([
            'channel_id' => $channels['marketplace_a']->id,
            'user_id' => User::query()->where('email', config('shop_ops.admin_email'))->value('id'),
            'trigger_type' => 'manual',
            'status' => 'completed',
            'processed_count' => 7,
            'notes' => '平台一样例同步已写入。',
            'created_at' => now()->subHours(4),
            'updated_at' => now()->subHours(4),
            'finished_at' => now()->subHours(4)->addMinutes(2),
        ]);

        SyncRun::query()->create([
            'channel_id' => $channels['marketplace_b']->id,
            'user_id' => User::query()->where('email', config('shop_ops.admin_email'))->value('id'),
            'trigger_type' => 'scheduler',
            'status' => 'completed',
            'processed_count' => 4,
            'notes' => '平台二样例同步已写入。',
            'created_at' => now()->subHours(10),
            'updated_at' => now()->subHours(10),
            'finished_at' => now()->subHours(10)->addMinutes(1),
        ]);
    }
}
