<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country_code', 2)->default('CN');
            $table->unsignedSmallInteger('lead_time_days')->default(0);
            $table->string('contact_email')->nullable();
            $table->unsignedTinyInteger('quality_score')->default(80);
            $table->timestamps();
        });

        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('marketplace');
            $table->string('region');
            $table->string('currency', 3)->default('USD');
            $table->string('endpoint_url')->nullable();
            $table->decimal('fee_percentage', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('category');
            $table->string('status')->default('active');
            $table->string('marketplace_focus')->default('marketplace-first');
            $table->decimal('cost_price', 10, 2);
            $table->decimal('target_price', 10, 2);
            $table->decimal('fulfillment_fee', 10, 2)->default(0);
            $table->unsignedInteger('safety_stock')->default(0);
            $table->unsignedSmallInteger('lead_time_days')->default(0);
            $table->text('selling_points')->nullable();
            $table->timestamps();

            $table->index(['category', 'status']);
        });

        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->string('external_sku');
            $table->string('status')->default('active');
            $table->decimal('price', 10, 2);
            $table->decimal('ad_daily_budget', 10, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('performance_score', 5, 2)->default(0);
            $table->timestamp('last_synced_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'channel_id']);
            $table->index(['channel_id', 'status']);
        });

        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('warehouse_code');
            $table->string('batch_code');
            $table->unsignedInteger('quantity_on_hand')->default(0);
            $table->unsignedInteger('quantity_reserved')->default(0);
            $table->unsignedInteger('quantity_inbound')->default(0);
            $table->date('inbound_eta')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'batch_code']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->string('external_order_no')->unique();
            $table->string('status')->default('processing');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('sale_price', 10, 2);
            $table->decimal('ad_spend', 10, 2)->default(0);
            $table->decimal('channel_fee', 10, 2)->default(0);
            $table->timestamp('ordered_at');
            $table->timestamps();

            $table->index(['channel_id', 'ordered_at']);
            $table->index('status');
        });

        Schema::create('sync_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->string('trigger_type')->default('manual');
            $table->string('status')->default('queued');
            $table->unsignedInteger('processed_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['channel_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_runs');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('inventory_batches');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('products');
        Schema::dropIfExists('channels');
        Schema::dropIfExists('suppliers');
    }
};
