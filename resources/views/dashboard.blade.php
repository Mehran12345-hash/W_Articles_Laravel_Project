@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Overview')
@section('content')
<div class="modern-dashboard">
    <section class="dashboard-welcome">
        <div class="dashboard-welcome-copy">
            <span class="dashboard-kicker">W STORIES WORKSPACE</span>
            <h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ explode(' ', trim(auth()->user()->name))[0] }}.</h1>
            <p>Your content, team and publishing activity — all in one place.</p>
            <div class="dashboard-welcome-actions">
                @can('create articles')<a class="btn btn-light" href="{{ route('articles.create') }}">＋ Write an article</a>@endcan
                <a class="dashboard-quiet-link" href="{{ route('blog.index') }}" target="_blank" rel="noopener">View public blog ↗</a>
            </div>
        </div>
        <div class="dashboard-welcome-art" aria-hidden="true"><span class="welcome-orbit orbit-one"></span><span class="welcome-orbit orbit-two"></span><span class="welcome-spark">✦</span><div class="welcome-document"><span></span><span></span><span></span><b>W</b></div></div>
        <div class="dashboard-role-label"><span class="dashboard-role-dot"></span>{{ $roleLabel ?: 'Workspace member' }}</div>
    </section>

    <div class="dashboard-stat-grid">
        <article class="dashboard-stat-card"><div class="dashboard-stat-head"><span>Articles in workspace</span><span class="dashboard-stat-icon stat-blue">▤</span></div><strong>{{ number_format($total) }}</strong><small>Content you can access</small></article>
        <article class="dashboard-stat-card"><div class="dashboard-stat-head"><span>Published stories</span><span class="dashboard-stat-icon stat-green">✓</span></div><strong>{{ number_format($published) }}</strong><small>Visible on your public blog</small></article>
        <article class="dashboard-stat-card"><div class="dashboard-stat-head"><span>Draft articles</span><span class="dashboard-stat-icon stat-orange">◷</span></div><strong>{{ number_format($drafts) }}</strong><small>Still being prepared</small></article>
        @if(!is_null($teamCount))
            <article class="dashboard-stat-card"><div class="dashboard-stat-head"><span>Team members</span><span class="dashboard-stat-icon stat-purple">♧</span></div><strong>{{ number_format($teamCount) }}</strong><small>People in your workspace</small></article>
        @else
            <article class="dashboard-stat-card"><div class="dashboard-stat-head"><span>Publish rate</span><span class="dashboard-stat-icon stat-purple">↗</span></div><strong>{{ $total ? round($published / $total * 100) : 0 }}<small class="stat-percent">%</small></strong><small>Of accessible articles</small></article>
        @endif
    </div>

    <div class="dashboard-main-grid">
        <section class="surface-card dashboard-activity-card">
            <div class="dashboard-card-heading"><div><span class="dashboard-section-kicker">YOUR PROGRESS</span><h2>Publishing activity</h2><p>New articles created over the last seven days.</p></div><span class="dashboard-period">Last 7 days</span></div>
            @php($maxCount = max(1, $activity->max('count')))
            <div class="dashboard-chart" role="img" aria-label="Articles created each day over the last seven days">
                @foreach($activity as $point)
                    <div class="dashboard-chart-column"><div class="dashboard-chart-value">{{ $point['count'] ?: '' }}</div><div class="dashboard-chart-track"><div class="dashboard-chart-bar" style="height: {{ $point['count'] ? max(8, round($point['count'] / $maxCount * 100)) : 3 }}%"></div></div><span>{{ $point['day'] }}</span></div>
                @endforeach
            </div>
        </section>

        <section class="surface-card dashboard-quick-card">
            <div class="dashboard-card-heading"><div><span class="dashboard-section-kicker">SHORTCUTS</span><h2>Quick actions</h2><p>Jump into your daily work.</p></div></div>
            <div class="dashboard-shortcut-list">
                @can('create articles')<a class="dashboard-shortcut" href="{{ route('articles.create') }}"><span class="shortcut-icon shortcut-blue">✎</span><span><strong>Create article</strong><small>Write a new story</small></span><b>↗</b></a>@endcan
                <a class="dashboard-shortcut" href="{{ route('articles.index') }}"><span class="shortcut-icon shortcut-green">▤</span><span><strong>Manage articles</strong><small>Review content and drafts</small></span><b>↗</b></a>
                @can('view users')<a class="dashboard-shortcut" href="{{ route('users.index') }}"><span class="shortcut-icon shortcut-purple">♧</span><span><strong>Team members</strong><small>View team access</small></span><b>↗</b></a>@endcan
                @can('manage roles')<a class="dashboard-shortcut" href="{{ route('roles.index') }}"><span class="shortcut-icon shortcut-orange">⚙</span><span><strong>Roles & access</strong><small>Configure team roles</small></span><b>↗</b></a>@endcan
            </div>
        </section>
    </div>

    <section class="surface-card dashboard-recent-card">
        <div class="dashboard-card-heading"><div><span class="dashboard-section-kicker">CONTENT LIBRARY</span><h2>Recent articles</h2><p>The latest stories and drafts available to your account.</p></div><a class="dashboard-view-all" href="{{ route('articles.index') }}">View all articles →</a></div>
        @if($latest->isEmpty())
            <div class="dashboard-empty"><span>✧</span><h3>Your first story starts here</h3><p>Create an article and it will appear in this list.</p>@can('create articles')<a class="btn btn-primary" href="{{ route('articles.create') }}">Create your first article</a>@endcan</div>
        @else
            <div class="table-scroll"><table class="data-table dashboard-table"><thead><tr><th>ARTICLE</th><th>AUTHOR</th><th>STATUS</th><th>UPDATED</th><th></th></tr></thead><tbody>
                @foreach($latest as $article)
                    <tr><td><div class="table-title"><span class="article-thumb article-thumb-photo">@if($article->cover_image)<img src="{{ asset('storage/'.$article->cover_image) }}" alt="{{ $article->title }}">@else{{ strtoupper(substr($article->title, 0, 1)) }}@endif</span><div><a class="table-title-link" href="{{ route('articles.show', $article) }}">{{ $article->title }}</a><small>{{ \Illuminate\Support\Str::limit($article->excerpt ?: $article->body, 58) }}</small></div></div></td><td>{{ $article->user?->name ?? 'Unknown' }}</td><td><span class="status-pill status-{{ $article->status }}">● {{ ucfirst($article->status) }}</span></td><td>{{ $article->updated_at->format('M d, Y') }}</td><td><a class="dashboard-row-link" href="{{ route('articles.show', $article) }}">Open ↗</a></td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </section>
</div>
@endsection
