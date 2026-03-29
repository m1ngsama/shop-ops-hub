@extends('layouts.app', ['title' => 'Channels | Shop Ops Hub'])

@section('page_kicker', 'Connector desk')
@section('page_title', 'Channels and sync control')
@section('page_copy', 'Manage marketplace adapters, inspect recent runs, and trigger queued sync jobs from a protected workspace.')

@section('content')
    @php
        $toneMap = [
            'completed' => 'success',
            'queued' => 'warning',
            'running' => 'info',
            'failed' => 'danger',
        ];
    @endphp

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="section-kicker">Integration access</p>
                <h2>API routes are now protected</h2>
            </div>
            <p class="section-copy">Use an authenticated admin session or pass a bearer token for machine-driven calls.</p>
        </div>

        <div class="api-note-grid">
            <article class="detail-card">
                <span>Metrics endpoint</span>
                <strong>GET /api/dashboard/metrics</strong>
            </article>
            <article class="detail-card">
                <span>Sync trigger</span>
                <strong>POST /api/channels/{channel}/sync</strong>
            </article>
            <article class="detail-card">
                <span>Auth model</span>
                <strong>Bearer token or admin session</strong>
            </article>
        </div>
    </section>

    <section class="channel-grid">
        @foreach ($channels as $channel)
            @php
                $latestRun = $channel->syncRuns->first();
                $channelPerformance = $performance->get($channel->id);
            @endphp

            <article class="channel-panel">
                <div class="channel-panel-head">
                    <div>
                        <p class="section-kicker">{{ $channel->marketplace }}</p>
                        <h2>{{ $channel->name }}</h2>
                        <p class="section-copy">{{ $channel->region }} · {{ $channel->currency }} · fee {{ number_format((float) $channel->fee_percentage, 1) }}%</p>
                    </div>
                    <span class="status-pill" data-tone="{{ $channel->is_active ? 'success' : 'warning' }}">{{ $channel->is_active ? 'ACTIVE' : 'PAUSED' }}</span>
                </div>

                <div class="micro-stats">
                    <div>
                        <span>Revenue</span>
                        <strong>${{ number_format((float) ($channelPerformance?->revenue ?? 0), 2) }}</strong>
                    </div>
                    <div>
                        <span>Orders</span>
                        <strong>{{ $channel->orders_count }}</strong>
                    </div>
                    <div>
                        <span>Listings</span>
                        <strong>{{ $channel->listings_count }}</strong>
                    </div>
                    <div>
                        <span>Latest run</span>
                        <strong>{{ $latestRun?->created_at?->diffForHumans() ?? 'No run yet' }}</strong>
                    </div>
                </div>

                <div class="channel-actions">
                    <form method="post" action="{{ route('admin.channels.sync', $channel) }}">
                        @csrf
                        <button type="submit" class="primary-button">Queue sync</button>
                    </form>
                    <a class="ghost-button" href="{{ $channel->endpoint_url }}" target="_blank" rel="noreferrer">Endpoint</a>
                </div>

                <div class="stack-list compact">
                    @forelse ($channel->syncRuns->take(3) as $run)
                        <article class="list-row">
                            <div>
                                <strong>Run #{{ $run->id }}</strong>
                                <span>
                                    {{ strtoupper($run->trigger_type) }}
                                    @if ($run->user)
                                        · {{ $run->user->name }}
                                    @endif
                                </span>
                            </div>
                            <div class="list-row-meta">
                                <span class="status-pill" data-tone="{{ $toneMap[$run->status] ?? 'neutral' }}">{{ strtoupper($run->status) }}</span>
                                <span>{{ $run->processed_count }} updates</span>
                            </div>
                        </article>
                    @empty
                        <p class="empty-note">No sync history yet.</p>
                    @endforelse
                </div>
            </article>
        @endforeach
    </section>
@endsection
