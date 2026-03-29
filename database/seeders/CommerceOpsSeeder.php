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

        Schema::enableForeignKeyConstraints();

        User::query()->updateOrCreate(
            ['email' => config('shop_ops.admin_email')],
            [
                'name' => 'Ops Analyst',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make(config('shop_ops.admin_password')),
            ]
        );

        $suppliers = collect([
            'Pearl Beauty Lab' => Supplier::query()->create([
                'name' => 'Pearl Beauty Lab',
                'country_code' => 'CN',
                'lead_time_days' => 18,
                'contact_email' => 'beauty@suppliers.local',
                'quality_score' => 93,
            ]),
            'North Harbor Home' => Supplier::query()->create([
                'name' => 'North Harbor Home',
                'country_code' => 'CN',
                'lead_time_days' => 24,
                'contact_email' => 'home@suppliers.local',
                'quality_score' => 88,
            ]),
            'Nova Threads Studio' => Supplier::query()->create([
                'name' => 'Nova Threads Studio',
                'country_code' => 'CN',
                'lead_time_days' => 15,
                'contact_email' => 'apparel@suppliers.local',
                'quality_score' => 90,
            ]),
        ]);

        $channels = collect([
            'amazon_us' => Channel::query()->create([
                'code' => 'amazon_us',
                'name' => 'Amazon US',
                'marketplace' => 'Amazon',
                'region' => 'United States',
                'currency' => 'USD',
                'endpoint_url' => 'https://sellingpartnerapi-na.amazon.com',
                'fee_percentage' => 15,
                'is_active' => true,
            ]),
            'walmart_us' => Channel::query()->create([
                'code' => 'walmart_us',
                'name' => 'Walmart US',
                'marketplace' => 'Walmart',
                'region' => 'United States',
                'currency' => 'USD',
                'endpoint_url' => 'https://marketplace.walmartapis.com',
                'fee_percentage' => 12,
                'is_active' => true,
            ]),
            'tiktok_shop_us' => Channel::query()->create([
                'code' => 'tiktok_shop_us',
                'name' => 'TikTok Shop US',
                'marketplace' => 'TikTok Shop',
                'region' => 'United States',
                'currency' => 'USD',
                'endpoint_url' => 'https://open-api.tiktokglobalshop.com',
                'fee_percentage' => 8,
                'is_active' => true,
            ]),
        ]);

        $products = collect([
            Product::query()->create([
                'supplier_id' => $suppliers['Pearl Beauty Lab']->id,
                'sku' => 'BEA-1001',
                'name' => 'Velvet Repair Hair Mask',
                'category' => 'Beauty',
                'status' => 'active',
                'marketplace_focus' => 'amazon-first',
                'cost_price' => 6.40,
                'target_price' => 18.99,
                'fulfillment_fee' => 4.15,
                'safety_stock' => 140,
                'lead_time_days' => 18,
                'selling_points' => 'High-repeat beauty item with strong review velocity and replenishment demand.',
            ]),
            Product::query()->create([
                'supplier_id' => $suppliers['North Harbor Home']->id,
                'sku' => 'HOM-2201',
                'name' => 'Ceramic Aroma Diffuser',
                'category' => 'Home',
                'status' => 'active',
                'marketplace_focus' => 'walmart-expansion',
                'cost_price' => 12.80,
                'target_price' => 34.50,
                'fulfillment_fee' => 7.10,
                'safety_stock' => 90,
                'lead_time_days' => 24,
                'selling_points' => 'Lifestyle home catalog item suited for bundle and seasonal ad campaigns.',
            ]),
            Product::query()->create([
                'supplier_id' => $suppliers['Nova Threads Studio']->id,
                'sku' => 'APP-3104',
                'name' => 'Sculpting Waist Leggings',
                'category' => 'Apparel',
                'status' => 'active',
                'marketplace_focus' => 'amazon-and-tiktok',
                'cost_price' => 9.20,
                'target_price' => 28.90,
                'fulfillment_fee' => 5.30,
                'safety_stock' => 120,
                'lead_time_days' => 15,
                'selling_points' => 'Fast-moving apparel SKU with influencer and social commerce potential.',
            ]),
            Product::query()->create([
                'supplier_id' => $suppliers['North Harbor Home']->id,
                'sku' => 'ACC-4107',
                'name' => 'Compression Travel Cubes',
                'category' => 'Accessories',
                'status' => 'active',
                'marketplace_focus' => 'amazon-core',
                'cost_price' => 7.50,
                'target_price' => 22.40,
                'fulfillment_fee' => 4.80,
                'safety_stock' => 160,
                'lead_time_days' => 20,
                'selling_points' => 'Accessory line with gifting potential and healthy margin profile.',
            ]),
            Product::query()->create([
                'supplier_id' => $suppliers['Pearl Beauty Lab']->id,
                'sku' => 'JEW-5202',
                'name' => 'Mini Jewelry Organizer',
                'category' => 'Jewelry',
                'status' => 'active',
                'marketplace_focus' => 'walmart-and-amazon',
                'cost_price' => 5.90,
                'target_price' => 19.60,
                'fulfillment_fee' => 4.05,
                'safety_stock' => 110,
                'lead_time_days' => 16,
                'selling_points' => 'Compact organizer with strong attach rate to beauty and travel bundles.',
            ]),
        ])->keyBy('sku');

        $listingSeed = [
            ['sku' => 'BEA-1001', 'channel' => 'amazon_us', 'price' => 18.99, 'budget' => 22.00, 'reviews' => 176, 'conversion' => 8.7, 'score' => 91.0],
            ['sku' => 'HOM-2201', 'channel' => 'walmart_us', 'price' => 34.50, 'budget' => 16.00, 'reviews' => 54, 'conversion' => 5.4, 'score' => 82.4],
            ['sku' => 'APP-3104', 'channel' => 'amazon_us', 'price' => 28.90, 'budget' => 19.50, 'reviews' => 88, 'conversion' => 6.9, 'score' => 86.8],
            ['sku' => 'APP-3104', 'channel' => 'tiktok_shop_us', 'price' => 26.90, 'budget' => 14.00, 'reviews' => 22, 'conversion' => 7.8, 'score' => 84.0],
            ['sku' => 'ACC-4107', 'channel' => 'amazon_us', 'price' => 22.40, 'budget' => 18.00, 'reviews' => 129, 'conversion' => 8.1, 'score' => 89.2],
            ['sku' => 'JEW-5202', 'channel' => 'walmart_us', 'price' => 19.60, 'budget' => 9.00, 'reviews' => 38, 'conversion' => 4.8, 'score' => 78.5],
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
            ['sku' => 'BEA-1001', 'warehouse' => 'SZX-FBA', 'batch' => 'HB-2401', 'on_hand' => 52, 'reserved' => 11, 'inbound' => 48, 'eta' => now()->addDays(6)],
            ['sku' => 'HOM-2201', 'warehouse' => 'SZX-WFS', 'batch' => 'HD-2403', 'on_hand' => 38, 'reserved' => 4, 'inbound' => 20, 'eta' => now()->addDays(10)],
            ['sku' => 'APP-3104', 'warehouse' => 'GZ-TTS', 'batch' => 'AP-2402', 'on_hand' => 148, 'reserved' => 19, 'inbound' => 0, 'eta' => null],
            ['sku' => 'ACC-4107', 'warehouse' => 'SZX-FBA', 'batch' => 'AC-2405', 'on_hand' => 210, 'reserved' => 28, 'inbound' => 40, 'eta' => now()->addDays(8)],
            ['sku' => 'JEW-5202', 'warehouse' => 'DG-WFS', 'batch' => 'JW-2401', 'on_hand' => 62, 'reserved' => 6, 'inbound' => 12, 'eta' => now()->addDays(5)],
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
            ['sku' => 'BEA-1001', 'channel' => 'amazon_us', 'order_no' => 'AMZ-20260328-001', 'status' => 'shipped', 'quantity' => 2, 'sale_price' => 18.99, 'ad_spend' => 5.10, 'channel_fee' => 4.30, 'hours_ago' => 8],
            ['sku' => 'APP-3104', 'channel' => 'amazon_us', 'order_no' => 'AMZ-20260328-002', 'status' => 'processing', 'quantity' => 1, 'sale_price' => 28.90, 'ad_spend' => 4.40, 'channel_fee' => 4.85, 'hours_ago' => 12],
            ['sku' => 'ACC-4107', 'channel' => 'amazon_us', 'order_no' => 'AMZ-20260327-003', 'status' => 'shipped', 'quantity' => 3, 'sale_price' => 22.40, 'ad_spend' => 6.20, 'channel_fee' => 6.45, 'hours_ago' => 27],
            ['sku' => 'HOM-2201', 'channel' => 'walmart_us', 'order_no' => 'WMT-20260327-001', 'status' => 'processing', 'quantity' => 1, 'sale_price' => 34.50, 'ad_spend' => 3.60, 'channel_fee' => 4.10, 'hours_ago' => 31],
            ['sku' => 'JEW-5202', 'channel' => 'walmart_us', 'order_no' => 'WMT-20260326-002', 'status' => 'shipped', 'quantity' => 2, 'sale_price' => 19.60, 'ad_spend' => 2.90, 'channel_fee' => 3.20, 'hours_ago' => 49],
            ['sku' => 'APP-3104', 'channel' => 'tiktok_shop_us', 'order_no' => 'TTS-20260326-004', 'status' => 'delivered', 'quantity' => 1, 'sale_price' => 26.90, 'ad_spend' => 3.20, 'channel_fee' => 2.15, 'hours_ago' => 56],
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
            'channel_id' => $channels['amazon_us']->id,
            'user_id' => User::query()->where('email', config('shop_ops.admin_email'))->value('id'),
            'trigger_type' => 'manual',
            'status' => 'completed',
            'processed_count' => 7,
            'notes' => 'Seeded Amazon sync snapshot.',
            'created_at' => now()->subHours(4),
            'updated_at' => now()->subHours(4),
            'finished_at' => now()->subHours(4)->addMinutes(2),
        ]);

        SyncRun::query()->create([
            'channel_id' => $channels['walmart_us']->id,
            'user_id' => User::query()->where('email', config('shop_ops.admin_email'))->value('id'),
            'trigger_type' => 'scheduler',
            'status' => 'completed',
            'processed_count' => 4,
            'notes' => 'Seeded Walmart sync snapshot.',
            'created_at' => now()->subHours(10),
            'updated_at' => now()->subHours(10),
            'finished_at' => now()->subHours(10)->addMinutes(1),
        ]);
    }
}
