@extends('layouts.storefront', ['title' => '商品目录 | 前台选品站'])

@section('content')
    <section class="catalog-hero">
        <div>
            <p class="hero-kicker">商品目录</p>
            <h1>按类目、价格、毛利和交期，快速筛选候选商品。</h1>
            <p class="hero-text">公开前台展示商品选择逻辑，后台继续承接订单、渠道同步和经营分析。</p>
        </div>

        <div class="catalog-summary">
            <article>
                <span>匹配商品</span>
                <strong>{{ $products->total() }}</strong>
            </article>
            <article>
                <span>已选条目</span>
                <strong>{{ $planSummary['line_count'] }}</strong>
            </article>
            <article>
                <span>预估金额</span>
                <strong>${{ number_format((float) $planSummary['estimated_value'], 2) }}</strong>
            </article>
        </div>
    </section>

    <section class="catalog-layout">
        <aside class="catalog-sidebar">
            <article class="storefront-panel">
                <div class="section-heading compact-heading">
                    <div>
                        <p class="hero-kicker">筛选</p>
                        <h2>过滤商品</h2>
                    </div>
                </div>

                <form method="get" class="form-stack">
                    <label class="field">
                        <span>关键词</span>
                        <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="SKU、商品名、卖点">
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
                        <button type="submit" class="primary-button">应用筛选</button>
                        <a class="secondary-button" href="{{ route('storefront.catalog') }}">重置</a>
                    </div>
                </form>
            </article>

            <article class="storefront-panel">
                <div class="section-heading compact-heading">
                    <div>
                        <p class="hero-kicker">意向清单</p>
                        <h2>当前摘要</h2>
                    </div>
                </div>

                <div class="summary-stack">
                    <article class="summary-card">
                        <span>条目数</span>
                        <strong>{{ $planSummary['line_count'] }}</strong>
                    </article>
                    <article class="summary-card">
                        <span>总数量</span>
                        <strong>{{ $planSummary['total_quantity'] }}</strong>
                    </article>
                    <article class="summary-card">
                        <span>预估金额</span>
                        <strong>${{ number_format((float) $planSummary['estimated_value'], 2) }}</strong>
                    </article>
                    <article class="summary-card">
                        <span>平均毛利</span>
                        <strong>{{ number_format((float) $planSummary['average_margin'], 1) }}%</strong>
                    </article>
                </div>

                <a class="secondary-button full-width" href="{{ route('storefront.plan.index') }}">打开意向清单</a>
            </article>
        </aside>

        <div class="catalog-main">
            <div class="section-heading">
                <div>
                    <p class="hero-kicker">结果列表</p>
                    <h2>共 {{ $products->total() }} 个候选商品</h2>
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
