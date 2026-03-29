<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Shop Ops Hub' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-shell">
        <header class="site-header">
            <div>
                <a class="brand-mark" href="{{ route('dashboard') }}">Shop Ops Hub</a>
                <p class="brand-copy">Laravel cockpit for cross-border catalog, channel, inventory, and order operations.</p>
            </div>

            <nav class="site-nav">
                <a href="{{ route('dashboard') }}" @class(['is-active' => request()->routeIs('dashboard')])>Dashboard</a>
                <a href="{{ route('products.index') }}" @class(['is-active' => request()->routeIs('products.*')])>Products</a>
                <a href="{{ route('channels.index') }}" @class(['is-active' => request()->routeIs('channels.*')])>Channels</a>
                <a href="{{ route('orders.index') }}" @class(['is-active' => request()->routeIs('orders.*')])>Orders</a>
            </nav>
        </header>

        @if (session('status'))
            <div class="flash-banner">{{ session('status') }}</div>
        @endif

        <main class="content-grid">
            @yield('content')
        </main>

        <footer class="site-footer">
            <p>Built from scratch with Laravel, MySQL-ready schema, Redis caching support, and mock marketplace sync adapters.</p>
            <p>This demo is independent and not affiliated with any employer.</p>
        </footer>
    </div>
</body>
</html>
