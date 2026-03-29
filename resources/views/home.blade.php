<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop Ops Hub</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="public-body">
    <div class="marketing-shell">
        <header class="marketing-header">
            <a class="brand-lockup" href="{{ route('home') }}">
                <span class="brand-kicker">Cross-border commerce</span>
                <strong>Shop Ops Hub</strong>
            </a>

            <div class="marketing-actions">
                <a class="ghost-button" href="#modules">Modules</a>
                <a class="primary-button" href="{{ route('login') }}">Admin sign in</a>
            </div>
        </header>

        <main class="marketing-content">
            <section class="hero-grid">
                <div class="hero-copy-block">
                    <p class="section-kicker">Private operations workspace</p>
                    <h1>Run catalog, inventory, marketplace sync, and order margin from one Laravel control room.</h1>
                    <p class="hero-copy">
                        Built for fast-moving Amazon and Walmart teams, with product master data, replenishment signals,
                        connector jobs, and finance-aware order visibility behind authenticated admin access.
                    </p>

                    <div class="cta-row">
                        <a class="primary-button" href="{{ route('login') }}">Enter the workspace</a>
                        <a class="ghost-button" href="#security">Security posture</a>
                    </div>
                </div>

                <div class="marketing-card-stack">
                    <article class="floating-card">
                        <span>Catalog command</span>
                        <strong>SKU, supplier, listings, inventory batches</strong>
                        <p>Built for multi-channel product operations rather than a public storefront.</p>
                    </article>
                    <article class="floating-card">
                        <span>Connector control</span>
                        <strong>Queued sync jobs for marketplace adapters</strong>
                        <p>Amazon and Walmart style jobs are queued and tracked instead of blocking requests.</p>
                    </article>
                    <article class="floating-card">
                        <span>Access model</span>
                        <strong>Session login for admins, token auth for API calls</strong>
                        <p>Operational screens are private, and integration routes require credentials.</p>
                    </article>
                </div>
            </section>

            <section class="feature-strip">
                <article>
                    <span>01</span>
                    <strong>Catalog operations</strong>
                    <p>Filterable SKU table with margin, inventory, supplier, and marketplace focus context.</p>
                </article>
                <article>
                    <span>02</span>
                    <strong>Channel oversight</strong>
                    <p>Channel health, sync history, queue status, and connector entry points in one view.</p>
                </article>
                <article>
                    <span>03</span>
                    <strong>Order intelligence</strong>
                    <p>Searchable order ledger with channel mapping, status, revenue, and gross profit.</p>
                </article>
            </section>

            <section class="marketing-panel" id="modules">
                <div class="panel-header">
                    <div>
                        <p class="section-kicker">Core modules</p>
                        <h2>What the system actually covers</h2>
                    </div>
                    <p class="section-copy">This is an internal commerce ERP surface, not a consumer storefront.</p>
                </div>

                <div class="module-grid">
                    <article class="module-card">
                        <h3>Product master</h3>
                        <p>SKU identity, category, supplier, target price, safety stock, and marketplace strategy.</p>
                    </article>
                    <article class="module-card">
                        <h3>Inventory planning</h3>
                        <p>Warehouse batches, inbound ETA, reserved quantity, and replenishment gap watchlists.</p>
                    </article>
                    <article class="module-card">
                        <h3>Marketplace adapters</h3>
                        <p>Amazon US, Walmart US, and TikTok Shop style connector definitions with sync history.</p>
                    </article>
                    <article class="module-card">
                        <h3>Margin ledger</h3>
                        <p>Orders tracked with sale price, ad spend, channel fee, and gross profit visibility.</p>
                    </article>
                </div>
            </section>

            <section class="marketing-panel security-panel" id="security">
                <div class="panel-header">
                    <div>
                        <p class="section-kicker">Security posture</p>
                        <h2>What changed from the previous demo</h2>
                    </div>
                    <p class="section-copy">The public site is now a landing page. Operations and integration surfaces are no longer exposed by default.</p>
                </div>

                <div class="security-grid">
                    <article>
                        <strong>Admin authentication</strong>
                        <p>All operational screens now sit behind session-based login and admin role checks.</p>
                    </article>
                    <article>
                        <strong>Protected API</strong>
                        <p>Dashboard metrics and sync triggers now require either an authenticated admin session or API token.</p>
                    </article>
                    <article>
                        <strong>Proxy-aware HTTPS</strong>
                        <p>The app now trusts reverse proxy headers and can force HTTPS URL generation in production.</p>
                    </article>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
