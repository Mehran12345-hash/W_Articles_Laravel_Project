<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | W Stories</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/inkflow.css') }}">
    <link rel="stylesheet" href="{{ asset('css/access-control.css') }}">
</head>
<body class="app-body">
    <div class="app-layout">
        <aside class="sidebar" id="app-sidebar">
            <a href="{{ route('dashboard') }}" class="brand brand--sidebar">@include('partials.brand')</a>
            <span class="sidebar-label">WORKSPACE</span>
            <nav class="sidebar-nav" aria-label="Main navigation">
                <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="nav-icon">▦</span> Overview</a>
                <a class="sidebar-link {{ request()->routeIs('articles.index', 'articles.show', 'articles.edit') ? 'active' : '' }}" href="{{ route('articles.index') }}"><span class="nav-icon">▤</span> All articles</a>
                @can('create articles')<a class="sidebar-link {{ request()->routeIs('articles.create') ? 'active' : '' }}" href="{{ route('articles.create') }}"><span class="nav-icon">＋</span> Write article</a>@endcan
                <a class="sidebar-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index') }}" target="_blank" rel="noopener"><span class="nav-icon">↗</span> Public blog</a>
            </nav>

            @if(auth()->user()->can('view users') || auth()->user()->can('manage roles') || auth()->user()->can('manage permissions'))
                <span class="sidebar-label sidebar-label-spaced">PEOPLE & ACCESS</span>
                <nav class="sidebar-nav" aria-label="People and access">
                    @can('view users')<a class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><span class="nav-icon">♧</span> Team members</a>@endcan
                    @can('manage roles')<a class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}"><span class="nav-icon">◈</span> Roles</a>@endcan
                    @can('manage permissions')<a class="sidebar-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}" href="{{ route('permissions.index') }}"><span class="nav-icon">☷</span> Permissions</a>@endcan
                </nav>
            @endif

            <div class="sidebar-bottom">
                <div class="sidebar-help"><span class="help-icon">✧</span><strong>Your ideas matter</strong><p>Organize your writing and share with confidence.</p>@can('create articles')<a href="{{ route('articles.create') }}">Start writing →</a>@else<a href="{{ route('blog.index') }}">Read stories →</a>@endcan</div>
                <div class="sidebar-profile"><div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->getRoleNames()->map(fn($r) => str($r)->headline())->join(', ') ?: 'Workspace member' }}</small></div></div>
            </div>
        </aside>

        <div class="app-main">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" aria-label="Open navigation" aria-controls="app-sidebar" aria-expanded="false" onclick="let s=document.getElementById('app-sidebar');let open=s.classList.toggle('sidebar-open');this.setAttribute('aria-expanded', String(open));">☰</button>
                    <span class="breadcrumbs">Workspace <span>/</span> @yield('breadcrumb', 'Overview')</span>
                </div>
                <div class="topbar-right">
                    <span class="topbar-greeting">Hello, {{ explode(' ', trim(auth()->user()->name))[0] }}</span>
                    <span class="topbar-role-chip">{{ auth()->user()->getRoleNames()->map(fn($r) => str($r)->headline())->join(', ') ?: 'Member' }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button" type="submit">Log out <span>↗</span></button></form>
                </div>
            </header>
            <main class="page-content">
                @if (session('success'))<div class="alert alert-success" role="status">✓ {{ session('success') }}</div>@endif
                @if ($errors->any())<div class="alert alert-error" role="alert"><strong>Please check the form.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
