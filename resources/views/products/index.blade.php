@extends('layouts.app', ['title' => 'Catalog | Shop Ops Hub'])

@section('page_kicker', 'Catalog desk')
@section('page_title', 'Product master')
@section('page_copy', 'Search and filter SKU performance, supplier coverage, inventory availability, and margin profile.')

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="section-kicker">Filter catalog</p>
                <h2>Find the SKU that needs attention</h2>
            </div>
            <p class="section-copy">Filter by SKU, name, status, or assortment category.</p>
        </div>

        <form method="get" class="filter-grid">
            <label class="field">
                <span>Search</span>
                <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="SKU, name, marketplace focus">
            </label>

            <label class="field">
                <span>Status</span>
                <select name="status">
                    <option value="">All statuses</option>
                    <option value="active" @selected($filters['status'] === 'active')>Active</option>
                    <option value="paused" @selected($filters['status'] === 'paused')>Paused</option>
                </select>
            </label>

            <label class="field">
                <span>Category</span>
                <select name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </label>

            <div class="filter-actions">
                <button type="submit" class="primary-button">Apply</button>
                <a class="ghost-button" href="{{ route('admin.products.index') }}">Reset</a>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="section-kicker">Catalog table</p>
                <h2>{{ $products->total() }} SKUs matched</h2>
            </div>
            <p class="section-copy">Each row carries assortment, supply, inventory, and margin context.</p>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Supplier</th>
                        <th>Inventory</th>
                        <th>Focus</th>
                        <th>Margin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><a href="{{ route('admin.products.show', $product) }}">{{ $product->sku }}</a></td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <div class="table-subcopy">{{ $product->category }}</div>
                            </td>
                            <td>
                                {{ $product->supplier?->name ?? 'Unassigned' }}
                                <div class="table-subcopy">{{ $product->lead_time_days }} day lead time</div>
                            </td>
                            <td>
                                {{ $product->availableInventory() }}
                                <div class="table-subcopy">Safety stock {{ $product->safety_stock }}</div>
                            </td>
                            <td>{{ $product->marketplace_focus }}</td>
                            <td>{{ number_format($product->marginRate(), 1) }}%</td>
                            <td><span class="status-pill" data-tone="{{ $product->status === 'active' ? 'success' : 'warning' }}">{{ strtoupper($product->status) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No products match the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $products])
    </section>
@endsection
