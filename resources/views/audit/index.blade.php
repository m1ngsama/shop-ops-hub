@extends('layouts.app', ['title' => '操作审计 | 商运后台'])

@section('page_kicker', '安全边界')
@section('page_title', '操作审计')
@section('page_copy', '记录登录、同步、前台意向动作和后台关键操作，便于回溯谁在什么时间做了什么。')
@section('page_actions')
    <a class="secondary-button" href="{{ route('admin.channels.index') }}">返回渠道</a>
    <a class="secondary-button" href="{{ route('admin.insights') }}">查看可视化</a>
@endsection

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="page-kicker">筛选</p>
                <h2>按事件和操作者检索</h2>
            </div>
        </div>

        <form method="get" class="audit-filter-grid">
            <label class="field">
                <span>事件类型</span>
                <select name="event">
                    <option value="">全部事件</option>
                    @foreach ($events as $event)
                        <option value="{{ $event }}" @selected($filters['event'] === $event)>{{ $event }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field">
                <span>操作者</span>
                <input type="search" name="actor" value="{{ $filters['actor'] }}" placeholder="姓名、邮箱或 actor">
            </label>

            <div class="filter-actions audit-filter-actions">
                <button type="submit" class="primary-button">应用筛选</button>
                <a class="secondary-button" href="{{ route('admin.audit.index') }}">重置</a>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="page-kicker">时间线</p>
                <h2>最近 {{ $logs->total() }} 条审计记录</h2>
            </div>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>时间</th>
                        <th>事件</th>
                        <th>操作者</th>
                        <th>对象</th>
                        <th>来源</th>
                        <th>元数据</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('m-d H:i:s') }}</td>
                            <td><span class="status-chip tone-neutral">{{ $log->event }}</span></td>
                            <td>
                                <strong>{{ $log->user?->name ?? $log->actor_label ?? '未知' }}</strong>
                                <p class="table-subtext">{{ $log->user?->email ?? $log->actor_label }}</p>
                            </td>
                            <td>
                                @if ($log->subject_type)
                                    <strong>{{ $log->subject_type }}</strong>
                                    <p class="table-subtext">#{{ $log->subject_id }}</p>
                                @else
                                    <span class="table-subtext">无</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $log->ip_address ?? '内部任务' }}</strong>
                                <p class="table-subtext">{{ $log->user_agent ? \Illuminate\Support\Str::limit($log->user_agent, 48) : 'worker/system' }}</p>
                            </td>
                            <td>
                                @if (! empty($log->meta))
                                    <div class="audit-meta-list">
                                        @foreach ($log->meta as $key => $value)
                                            <span>{{ $key }}: {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="table-subtext">无</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">当前没有符合条件的审计记录。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $logs])
    </section>
@endsection
