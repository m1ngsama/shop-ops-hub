@extends('layouts.storefront', ['title' => '商店 | Shop Ops Hub'])

@section('content')
    <section class="catalog-hero">
        <div>
            <p class="hero-kicker">Shop</p>
            <h1>找到适合你的商品。</h1>
            <p class="hero-text">简洁浏览，快速选择。</p>
        </div>

        <div class="catalog-summary">
            <article>
                <span>商品</span>
                <strong>{{ $products->total() }}</strong>
            </article>
            <article>
                <span>购物袋</span>
                <strong>{{ $planSummary['line_count'] }}</strong>
            </article>
            <article>
                <span>金额</span>
                <strong>${{ number_format((float) $planSummary['estimated_value'], 2) }}</strong>
            </article>
        </div>
    </section>

    <section class="catalog-chip-strip">
        <a class="catalog-chip @if($filters['category'] === '') is-active @endif" href="{{ route('storefront.catalog') }}">全部类目</a>
        @foreach ($categories as $category)
            <a
                class="catalog-chip @if($filters['category'] === $category) is-active @endif"
                href="{{ route('storefront.catalog', array_filter(['category' => $category, 'search' => $filters['search'], 'sort' => $filters['sort']])) }}"
            >
                {{ $category }}
            </a>
        @endforeach
    </section>

    <section class="catalog-layout">
        <aside class="catalog-sidebar">
            <article class="storefront-panel">
                <div class="section-heading compact-heading">
                    <div>
                        <p class="hero-kicker">Filter</p>
                        <h2>筛选</h2>
                    </div>
                </div>

                <form method="get" class="form-stack">
                    <label class="field">
                        <span>关键词</span>
                        <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="商品名">
                    </label>

                    <label class="field">
                        <span>类目</span>
                        <select name="category">
                            <option value="">全部</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="field">
                        <span>排序</span>
                        <select name="sort">
                            <option value="recommended" @selected($filters['sort'] === 'recommended')>推荐优先</option>
                            <option value="margin" @selected($filters['sort'] === 'margin')>毛利优先</option>
                            <option value="lead_time" @selected($filters['sort'] === 'lead_time')>交期优先</option>
                            <option value="price_low" @selected($filters['sort'] === 'price_low')>价格从低到高</option>
                            <option value="price_high" @selected($filters['sort'] === 'price_high')>价格从高到低</option>
                        </select>
                    </label>

                    <div class="filter-actions">
                        <button type="submit" class="primary-button">筛选</button>
                        <a class="secondary-button" href="{{ route('storefront.catalog') }}">重置</a>
                    </div>
                </form>
            </article>

            <article class="storefront-panel">
                <div class="section-heading compact-heading">
                    <div>
                        <p class="hero-kicker">Bag</p>
                        <h2>购物袋</h2>
                    </div>
                </div>

                <div class="summary-stack">
                    <article class="summary-card">
                        <span>商品</span>
                        <strong>{{ $planSummary['line_count'] }}</strong>
                    </article>
                    <article class="summary-card">
                        <span>数量</span>
                        <strong>{{ $planSummary['total_quantity'] }}</strong>
                    </article>
                    <article class="summary-card">
                        <span>金额</span>
                        <strong>${{ number_format((float) $planSummary['estimated_value'], 2) }}</strong>
                    </article>
                </div>

                <a class="secondary-button full-width" href="{{ route('storefront.plan.index') }}">查看购物袋</a>
            </article>
        </aside>

        <div class="catalog-main">
            <div class="section-heading">
                <div>
                    <p class="hero-kicker">Products</p>
                    <h2>{{ $products->total() }} 件商品</h2>
                    <p class="page-copy">{{ ['recommended' => '推荐优先', 'margin' => '热门优先', 'lead_time' => '发货更快', 'price_low' => '价格从低到高', 'price_high' => '价格从高到低'][$filters['sort']] ?? '推荐优先' }}</p>
                </div>
            </div>

            <div class="product-grid">
                @forelse ($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @empty
                    <article class="empty-panel">
                        <strong>当前没有符合条件的商品。</strong>
                        <p>可以尝试放宽关键词或切换类目。</p>
                    </article>
                @endforelse
            </div>

            @include('partials.pagination', ['paginator' => $products])
        </div>
    </section>
@endsection
