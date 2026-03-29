<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Shop Ops Hub' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="sidebar-block">
                <a class="brand-lockup" href="{{ route('admin.dashboard') }}">
                    <span class="brand-kicker">Cross-border ops suite</span>
                    <strong>Shop Ops Hub</strong>
                </a>
                <p class="sidebar-copy">Marketplace control room for catalog, inventory, channel sync, and margin-aware orders.</p>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>Overview</a>
                <a href="{{ route('admin.products.index') }}" @class(['is-active' => request()->routeIs('admin.products.*')])>Catalog</a>
                <a href="{{ route('admin.channels.index') }}" @class(['is-active' => request()->routeIs('admin.channels.*')])>Channels</a>
                <a href="{{ route('admin.orders.index') }}" @class(['is-active' => request()->routeIs('admin.orders.*')])>Orders</a>
            </nav>

            <div class="sidebar-block sidebar-user">
                <div>
                    <span class="sidebar-label">Signed in as</span>
                    <strong>{{ auth()->user()?->name }}</strong>
                </div>
                <span class="status-pill" data-tone="neutral">{{ strtoupper(auth()->user()?->role ?? 'guest') }}</span>
            </div>

            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ghost-button full-width">Sign out</button>
            </form>
        </aside>

        <div class="admin-main">
            <header class="admin-header">
                <div>
                    <p class="section-kicker">@yield('page_kicker', 'Operations desk')</p>
                    <h1>@yield('page_title', 'Shop Ops Hub')</h1>
                    <p class="section-copy">@yield('page_copy', 'Operational visibility across marketplace teams, channels, and supply chain pressure.') </p>
                </div>

                <div class="header-meta">
                    <div class="metric-badge">
                        <span>Environment</span>
                        <strong>{{ strtoupper(app()->environment()) }}</strong>
                    </div>
                    <div class="metric-badge">
                        <span>Clock</span>
                        <strong>{{ now()->format('M j, H:i') }}</strong>
                    </div>
                </div>
            </header>

            @if (session('status'))
                <div class="flash-banner">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-banner">
                    <strong>Action blocked.</strong>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
