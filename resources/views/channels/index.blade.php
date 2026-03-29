@extends('layouts.app', ['title' => 'Channels | Shop Ops Hub'])

@section('content')
    <section class="hero-panel compact">
        <div>
            <p class="eyebrow">Channel adapters</p>
            <h1>Marketplace connectors designed for Amazon-style integrations.</h1>
            <p class="hero-copy">Adapters are separated from controllers so the sync workflow can graduate from mock payloads to real APIs.</p>
        </div>
    </section>

    <section class="channel-layout">
        @foreach ($channels as $channel)
            @php($stats = $performance[$channel->id] ?? null)

            <article class="panel channel-panel">
                <div class="panel-head">
                    <div>
                        <h2>{{ $channel->name }}</h2>
                        <p>{{ $channel->marketplace }} · {{ $channel->region }}</p>
                    </div>

                    <form method="post" action="{{ route('channels.sync', $channel) }}">
                        @csrf
                        <button type="submit" class="sync-button">Run sync</button>
                    </form>
                </div>

                <div class="channel-metrics">
                    <div>
                        <span>Revenue</span>
                        <strong>${{ number_format($stats?->revenue ?? 0, 2) }}</strong>
                    </div>
                    <div>
                        <span>Orders</span>
                        <strong>{{ $stats?->order_count ?? 0 }}</strong>
                    </div>
                    <div>
                        <span>Listings</span>
                        <strong>{{ $channel->listings->count() }}</strong>
                    </div>
                    <div>
                        <span>Fee rate</span>
                        <strong>{{ number_format($channel->fee_percentage, 2) }}%</strong>
                    </div>
                </div>

                <div class="stack-note">
                    <p>Endpoint</p>
                    <code>{{ $channel->endpoint_url }}</code>
                </div>

                <div class="list-stack">
                    @foreach ($channel->syncRuns->take(3) as $run)
                        <article class="list-row">
                            <div>
                                <strong>{{ ucfirst($run->status) }}</strong>
                                <span>{{ strtoupper($run->trigger_type) }}</span>
                            </div>
                            <div>
                                <strong>{{ $run->processed_count }} updates</strong>
                                <span>{{ $run->created_at?->diffForHumans() }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </article>
        @endforeach
    </section>
@endsection
