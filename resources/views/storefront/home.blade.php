@extends('layouts.storefront', ['title' => '零售样板站'])

@section('content')
    <section class="hero-shell">
        <div class="hero-copy">
            <p class="hero-kicker">Merchandising Frontend</p>
            <h1>更干净的商品前台。更直接的购买判断。</h1>
            <p class="hero-text">围绕商品本身组织浏览、比较和选择，克制、清晰、足够高级。</p>

            <div class="pill-row hero-pills">
                <span class="metric-pill">活跃商品 {{ $heroSummary['active_products'] }}</span>
                <span class="metric-pill">可售库存 {{ $heroSummary['available_inventory'] }}</span>
                <span class="metric-pill">平均毛利 {{ number_format($heroSummary['average_margin'], 1) }}%</span>
                <span class="metric-pill">最快交期 {{ $heroSummary['fastest_lead_time'] }} 天</span>
            </div>

            <div class="hero-actions">
                <a class="primary-button" href="{{ route('storefront.catalog') }}">浏览商品</a>
                <a class="secondary-button" href="{{ route('storefront.plan.index') }}">意向清单</a>
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

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">Products</p>
                <h2>现在值得看的商品</h2>
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
                <p class="hero-kicker">Category</p>
                <h2>从类目开始</h2>
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
                <p class="hero-kicker">Featured</p>
                <h2>精选组合</h2>
            </div>
        </div>

        <div class="collection-grid">
            @foreach ($curatedCollections as $collection)
                <article class="collection-card">
                    <div class="collection-head">
                        <span class="surface-tag">{{ $collection['tag'] }}</span>
                        <strong>{{ $collection['title'] }}</strong>
                    </div>
                    <p>{{ $collection['copy'] }}</p>

                    <div class="pill-row">
                        <span class="metric-pill">组合客单 ${{ number_format((float) $collection['estimated_ticket'], 2) }}</span>
                        <span class="metric-pill">平均毛利 {{ number_format($collection['average_margin'], 1) }}%</span>
                        <span class="metric-pill">平均交期 {{ $collection['average_lead_time'] }} 天</span>
                    </div>

                    <div class="collection-products">
                        @foreach ($collection['products'] as $product)
                            <a class="collection-product" href="{{ route('storefront.products.show', ['product' => $product->sku]) }}">
                                <span>{{ $product->category }}</span>
                                <strong>{{ $product->name }}</strong>
                                <p>{{ $product->sku }} · ${{ number_format((float) $product->target_price, 2) }}</p>
                            </a>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">Compare</p>
                <h2>快速比较</h2>
            </div>
            <a class="text-link" href="{{ route('storefront.catalog') }}">进入完整目录</a>
        </div>

        <div class="comparison-shell">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>比较项</th>
                        @foreach ($comparisonProducts as $product)
                            <th>{{ $product->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>目标售价</td>
                        @foreach ($comparisonProducts as $product)
                            <td>${{ number_format((float) $product->target_price, 2) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>毛利率</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ number_format($product->marginRate(), 1) }}%</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>可售库存</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ $product->availableInventory() }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>交期</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ $product->lead_time_days }} 天</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>渠道覆盖</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ $product->listings->pluck('channel.name')->implode(' / ') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>评价规模</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ $product->listings->sum('review_count') }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
