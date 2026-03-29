@extends('layouts.app', ['title' => '商品中心 | 商运后台'])

@section('page_kicker', '商品模块')
@section('page_title', '商品中心')
@section('page_copy', '按 SKU、类目、状态和关键词筛选商品，查看库存、利润与供货关系。')
@section('page_actions')
    <a class="secondary-button" href="{{ route('admin.channels.index') }}">渠道同步</a>
@endsection

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="page-kicker">筛选条件</p>
                <h2>快速定位商品</h2>
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
