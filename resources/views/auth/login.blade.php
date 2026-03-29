<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Sign In | Shop Ops Hub</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
    <div class="auth-shell">
        <section class="auth-panel auth-copy-panel">
            <p class="section-kicker">Private admin access</p>
            <h1>Sign in to the operations workspace.</h1>
            <p class="hero-copy">
                This environment is for catalog operators, channel managers, and finance-aware marketplace teams.
                Admin access is required for dashboard, catalog, channel sync, and order workflows.
            </p>

            <div class="auth-points">
                <article>
                    <strong>Role-gated workspace</strong>
                    <p>Only active admin users can enter the `/admin` surface.</p>
                </article>
                <article>
                    <strong>Queued channel jobs</strong>
                    <p>Sync actions move through background workers instead of blocking browser requests.</p>
                </article>
                <article>
                    <strong>Integration-friendly API</strong>
                    <p>Token authentication is available for machine-driven sync and metrics access.</p>
                </article>
            </div>
        </section>

        <section class="auth-panel auth-form-panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Admin sign in</p>
                    <h2>Enter your credentials</h2>
                </div>
                <a class="ghost-button" href="{{ route('home') }}">Back</a>
            </div>

            <form method="post" action="{{ route('login.store') }}" class="auth-form">
                @csrf

                <label class="field">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                </label>

                <label class="field">
                    <span>Password</span>
                    <input type="password" name="password" required autocomplete="current-password">
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Keep this session active on this device</span>
                </label>

                @if ($errors->any())
                    <div class="error-banner">
                        <strong>Login failed.</strong>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <button type="submit" class="primary-button full-width">Sign in to admin</button>
            </form>
        </section>
    </div>
</body>
</html>
