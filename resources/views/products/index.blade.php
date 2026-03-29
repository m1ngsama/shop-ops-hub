@extends('layouts.app', ['title' => '商品中心 | 商运后台'])

@section('page_kicker', '商品模块')
@section('page_title', '商品中心')
@section('page_copy', '按 SKU、类目、状态和关键词筛选商品，查看库存、利润与供货关系。')
@section('page_actions')
    <a class="secondary-button" href="{{ route('admin.channels.index') }}">渠道同步</a>
@endsection

@section('content')
    @php
        $visibleProducts = $products->getCollection();
        $activeProducts = $visibleProducts->where('status', 'active')->count();
        $inventoryTotal = $visibleProducts->sum(fn ($product) => $product->availableInventory());
    @endphp

    <section class="admin-hero-grid admin-hero-grid-compact">
        <article class="admin-callout">
            <p class="page-kicker">商品视图</p>
            <h2>把 SKU、类目、供给与利润判断放进同一筛选工作流。</h2>
            <p>
                商品中心不只是查表，而是帮助你快速判断哪些货盘适合继续放量、哪些需要补货、哪些应该暂停观察。
            </p>
        </article>

        <article class="admin-stat-ribbon">
            <div>
                <span>当前页商品</span>
                <strong>{{ $visibleProducts->count() }}</strong>
            </div>
            <div>
                <span>上架中</span>
                <strong>{{ $activeProducts }}</strong>
            </div>
            <div>
                <span>库存总量</span>
                <strong>{{ $inventoryTotal }}</strong>
            </div>
        </article>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="page-kicker">筛选条件</p>
                <h2>快速定位商品</h2>
                <p class="page-copy">根据关键词、状态和类目缩小范围，优先找出当前最值得处理的商品集合。</p>
            </div>
        </div>

        <form method="get" class="filter-grid">
            <label class="field">
                <span>关键词</span>
                <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="SKU、商品名、运营策略">
            </label>

            <label class="field">
                <span>状态</span>
                <select name="status">
                    <option value="">全部</option>
                    <option value="active" @selected($filters['status'] === 'active')>上架中</option>
                    <option value="paused" @selected($filters['status'] === 'paused')>已暂停</option>
                </select>
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

            <div class="filter-actions">
                <button type="submit" class="primary-button">筛选</button>
                <a class="secondary-button" href="{{ route('admin.products.index') }}">重置</a>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="page-kicker">商品列表</p>
                <h2>共 {{ $products->total() }} 个商品</h2>
                <p class="page-copy">每行同时给出供给、价格、毛利和状态，减少在详情页之间反复跳转。</p>
            </div>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>商品</th>
                        <th>类目</th>
                        <th>供应商</th>
                        <th>可售库存</th>
                        <th>目标售价</th>
                        <th>毛利率</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><a href="{{ route('admin.products.show', $product) }}">{{ $product->sku }}</a></td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <div class="table-subtext">{{ $product->marketplace_focus }}</div>
                            </td>
                            <td>{{ $product->category }}</td>
                            <td>
                                {{ $product->supplier?->name ?? '未分配' }}
                                <div class="table-subtext">交期 {{ $product->lead_time_days }} 天</div>
                            </td>
                            <td>{{ $product->availableInventory() }}</td>
                            <td>${{ number_format((float) $product->target_price, 2) }}</td>
                            <td>{{ number_format($product->marginRate(), 1) }}%</td>
                            <td><span class="status-chip tone-{{ $product->status === 'active' ? 'success' : 'warning' }}">{{ $product->status === 'active' ? '上架中' : '已暂停' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">当前没有符合条件的商品。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $products])
    </section>
@endsection
