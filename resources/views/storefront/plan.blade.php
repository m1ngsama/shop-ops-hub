@extends('layouts.storefront', ['title' => '意向清单 | 前台选品站'])

@section('content')
    <section class="catalog-hero">
        <div>
            <p class="hero-kicker">意向清单</p>
            <h1>把想继续沟通的商品集中到一个清单里。</h1>
            <p class="hero-text">这里保留选择数量、预估金额和供给能力，方便后续询盘、补货和后台处理。</p>
        </div>

        <div class="catalog-summary">
            <article>
                <span>条目数</span>
                <strong>{{ $summary['line_count'] }}</strong>
            </article>
            <article>
                <span>总数量</span>
                <strong>{{ $summary['total_quantity'] }}</strong>
            </article>
            <article>
                <span>预估金额</span>
                <strong>${{ number_format((float) $summary['estimated_value'], 2) }}</strong>
            </article>
        </div>
    </section>

    @if ($items->isEmpty())
        <section class="empty-panel empty-panel-large">
            <strong>意向清单还是空的。</strong>
            <p>先从商品目录里挑一些候选商品，再回到这里统一查看。</p>
            <a class="primary-button" href="{{ route('storefront.catalog') }}">去选商品</a>
        </section>
    @else
        <section class="catalog-layout">
            <div class="catalog-main">
                <article class="storefront-panel">
                    <div class="section-heading">
                        <div>
                            <p class="hero-kicker">条目列表</p>
                            <h2>共 {{ $summary['line_count'] }} 个条目</h2>
                        </div>
                    </div>

                    <div class="table-shell">
                        <table>
                            <thead>
                                <tr>
                                    <th>商品</th>
                                    <th>供应商</th>
                                    <th>可售库存</th>
                                    <th>数量</th>
                                    <th>预估金额</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('storefront.products.show', ['product' => $item['product']->sku]) }}">{{ $item['product']->name }}</a>
                                            <div class="table-subtext">{{ $item['product']->sku }} · {{ $item['product']->category }}</div>
                                        </td>
                                        <td>{{ $item['product']->supplier?->name ?? '未分配' }}</td>
                                        <td>{{ $item['available_inventory'] }}</td>
                                        <td>
                                            <form method="post" action="{{ route('storefront.plan.update', ['product' => $item['product']]) }}" class="inline-form">
                                                @csrf
                                                @method('patch')
                                                <input type="number" min="1" name="quantity" value="{{ $item['quantity'] }}">
                                                <button type="submit" class="secondary-button">更新</button>
                                            </form>
                                        </td>
                                        <td>${{ number_format((float) $item['estimated_value'], 2) }}</td>
                                        <td>
                                            <form method="post" action="{{ route('storefront.plan.destroy', ['product' => $item['product']]) }}">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="secondary-button">移除</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <aside class="catalog-sidebar">
                <article class="storefront-panel">
                    <div class="section-heading compact-heading">
                        <div>
                            <p class="hero-kicker">摘要</p>
                            <h2>当前意向</h2>
                        </div>
                    </div>

                    <div class="summary-stack">
                        <article class="summary-card">
                            <span>条目数</span>
                            <strong>{{ $summary['line_count'] }}</strong>
                        </article>
                        <article class="summary-card">
                            <span>总数量</span>
                            <strong>{{ $summary['total_quantity'] }}</strong>
                        </article>
                        <article class="summary-card">
                            <span>预估金额</span>
                            <strong>${{ number_format((float) $summary['estimated_value'], 2) }}</strong>
                        </article>
                        <article class="summary-card">
                            <span>平均毛利</span>
                            <strong>{{ number_format((float) $summary['average_margin'], 1) }}%</strong>
                        </article>
                        <article class="summary-card">
                            <span>最快交期</span>
                            <strong>{{ $summary['fastest_lead_time'] ?? '--' }} 天</strong>
                        </article>
                    </div>

                    <div class="action-list">
                        <article class="action-item">
                            <div>
                                <strong>后续建议</strong>
                                <p>先优先处理高毛利且库存安全的商品，再推进长交期条目的询盘动作。</p>
                            </div>
                        </article>
                    </div>

                    <form method="post" action="{{ route('storefront.plan.clear') }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="secondary-button full-width">清空意向清单</button>
                    </form>
                </article>
            </aside>
        </section>
    @endif
@endsection
