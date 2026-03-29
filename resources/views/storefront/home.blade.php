@extends('layouts.storefront', ['title' => '前台选品站'])

@section('content')
    <section class="hero-shell">
        <div class="hero-copy">
            <p class="hero-kicker">公开前台样板</p>
            <h1>把商品浏览、供给判断和意向协同，放到一套真正能用的前台程序里。</h1>
            <p class="hero-text">
                这个前台不再只是一个登录前的空壳。它提供公开商品目录、类目筛选、详情页和会话意向清单，
                让选品、询盘和后台运营之间形成完整闭环。
            </p>

            <div class="pill-row hero-pills">
                <span class="metric-pill">活跃商品 {{ $heroSummary['active_products'] }}</span>
                <span class="metric-pill">可售库存 {{ $heroSummary['available_inventory'] }}</span>
                <span class="metric-pill">平均毛利 {{ number_format($heroSummary['average_margin'], 1) }}%</span>
                <span class="metric-pill">最快交期 {{ $heroSummary['fastest_lead_time'] }} 天</span>
            </div>

            <div class="hero-actions">
                <a class="primary-button" href="{{ route('storefront.catalog') }}">浏览商品目录</a>
                <a class="secondary-button" href="{{ route('storefront.plan.index') }}">查看意向清单</a>
            </div>
        </div>

        <div class="hero-stage">
            <article class="hero-panel">
                <div class="hero-panel-head">
                    <span class="surface-tag">当前意向</span>
                    <strong>{{ $planSummary['line_count'] }} 个条目</strong>
                </div>

                <div class="hero-panel-metrics">
                    <article>
                        <span>总数量</span>
                        <strong>{{ $planSummary['total_quantity'] }}</strong>
                    </article>
                    <article>
                        <span>预估金额</span>
                        <strong>${{ number_format((float) $planSummary['estimated_value'], 2) }}</strong>
                    </article>
                    <article>
                        <span>平均毛利</span>
                        <strong>{{ number_format((float) $planSummary['average_margin'], 1) }}%</strong>
                    </article>
                    <article>
                        <span>最快交期</span>
                        <strong>{{ $planSummary['fastest_lead_time'] ?? '--' }} 天</strong>
                    </article>
                </div>
            </article>

            <div class="hero-mini-grid">
                @foreach ($featuredProducts->take(3) as $product)
                    <article class="hero-mini-card">
                        <span class="surface-tag">{{ $product->category }}</span>
                        <strong>{{ $product->name }}</strong>
                        <p>{{ $product->sku }} · ${{ number_format((float) $product->target_price, 2) }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="feature-grid">
        <article class="feature-card">
            <span class="surface-tag">目录</span>
            <h2>商品目录不是静态展示</h2>
            <p>支持关键词、类目和排序筛选，前台可以直接完成初步选品与比较。</p>
        </article>
        <article class="feature-card">
            <span class="surface-tag">意向</span>
            <h2>会话级意向清单</h2>
            <p>把感兴趣的商品加入清单，保留数量和预估金额，作为后续询盘和运营动作入口。</p>
        </article>
        <article class="feature-card">
            <span class="surface-tag">后台</span>
            <h2>与后台看板联动</h2>
            <p>同一套商品、库存和渠道数据同时驱动前台浏览页与后台可视化驾驶舱。</p>
        </article>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">类目视图</p>
                <h2>用类目看选品节奏</h2>
            </div>
            <a class="text-link" href="{{ route('storefront.catalog') }}">查看完整目录</a>
        </div>

        <div class="category-grid">
            @foreach ($categoryHighlights as $category)
                <article class="category-card">
                    <span class="surface-tag">{{ $category['name'] }}</span>
                    <strong>{{ $category['sku_count'] }} 个 SKU</strong>
                    <p>平均毛利 {{ number_format($category['average_margin'], 1) }}% · 平均交期 {{ $category['average_lead_time'] }} 天</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">推荐陈列</p>
                <h2>前台商品卡片</h2>
            </div>
            <a class="text-link" href="{{ route('storefront.catalog') }}">全部商品</a>
        </div>

        <div class="product-grid">
            @foreach ($featuredProducts as $product)
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">渠道准备度</p>
                <h2>前台也能表达供给和履约能力</h2>
            </div>
        </div>

        <div class="channel-strip">
            @foreach ($channelHighlights as $channel)
                <article class="channel-tile">
                    <div>
                        <span class="surface-tag">{{ $channel->region }}</span>
                        <strong>{{ $channel->name }}</strong>
                    </div>
                    <p>{{ $channel->marketplace }} · {{ $channel->currency }}</p>
                    <div class="pill-row">
                        <span class="metric-pill">刊登 {{ $channel->listings_count }}</span>
                        <span class="metric-pill">订单 {{ $channel->orders_count }}</span>
                        <span class="metric-pill">费率 {{ number_format((float) $channel->fee_percentage, 1) }}%</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
