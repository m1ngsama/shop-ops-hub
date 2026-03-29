@extends('layouts.app', ['title' => 'Products | Shop Ops Hub'])

@section('content')
    <section class="hero-panel compact">
        <div>
            <p class="eyebrow">Product master</p>
            <h1>Supplier-linked catalog built for marketplace operations.</h1>
            <p class="hero-copy">Each SKU carries sourcing, pricing, fulfillment, and inventory signals required by an ERP-minded PHP team.</p>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Catalog overview</h2>
            <p>Margin and inventory are derived inside the domain model.</p>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Target price</th>
                        <th>Margin</th>
                        <th>Inventory</th>
                        <th>Listings</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td><a href="{{ route('products.show', $product) }}">{{ $product->sku }}</a></td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category }}</td>
                            <td>{{ $product->supplier?->name }}</td>
                            <td>${{ number_format($product->target_price, 2) }}</td>
                            <td>{{ number_format($product->marginRate(), 1) }}%</td>
                            <td>{{ $product->availableInventory() }}</td>
                            <td>{{ $product->listings->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
