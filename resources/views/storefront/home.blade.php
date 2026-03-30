@extends('layouts.storefront', ['title' => 'Shop Ops Hub'])

@section('content')
    <section class="hero-shell">
        <div class="hero-copy">
            <p class="hero-kicker">New arrivals</p>
            <h1>为日常购买设计的简洁商店。</h1>
            <p class="hero-text">精选商品、干净的浏览体验，以及更直接的下单路径。</p>

            <div class="pill-row hero-pills">
                <span class="metric-pill">在售 {{ $heroSummary['active_products'] }}</span>
                <span class="metric-pill">最快 {{ $heroSummary['fastest_lead_time'] }} 天发货</span>
            </div>

            <div class="hero-actions">
                <a class="primary-button" href="{{ route('storefront.catalog') }}">立即选购</a>
                <a class="secondary-button" href="{{ route('storefront.plan.index') }}">查看购物袋</a>
            </div>
        </div>

        <div class="hero-stage">
            <div class="hero-mini-grid">
                @foreach ($featuredProducts->take(3) as $product)
                    <article class="hero-mini-card">
                        <span class="surface-tag">{{ $product->category }}</span>
                        <strong>{{ $product->name }}</strong>
                        <p>${{ number_format((float) $product->target_price, 2) }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">Shop</p>
                <h2>本周推荐</h2>
            </div>
            <a class="text-link" href="{{ route('storefront.catalog') }}">查看全部</a>
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
                <p class="hero-kicker">Categories</p>
                <h2>按类目浏览</h2>
            </div>
            <a class="text-link" href="{{ route('storefront.catalog') }}">查看全部</a>
        </div>

        <div class="category-grid">
            @foreach ($categoryHighlights as $category)
                <article class="category-card">
                    <span class="surface-tag">{{ $category['name'] }}</span>
                    <strong>{{ $category['sku_count'] }} 款商品</strong>
                    <p>预计 {{ $category['average_lead_time'] }} 天发货</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">Curated sets</p>
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
                        <span class="metric-pill">组合价 ${{ number_format((float) $collection['estimated_ticket'], 2) }}</span>
                        <span class="metric-pill">预计 {{ $collection['average_lead_time'] }} 天发货</span>
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

@endsection
