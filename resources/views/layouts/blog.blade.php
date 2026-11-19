<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="@yield('meta_description', 'Discover ideas, perspectives, and useful stories from our writers. Read the latest articles at W Stories.')">
<title>@yield('title', 'Stories for curious minds') · W Stories</title>
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/blog.css') }}">
</head>
<body class="blog-body">
<div class="site-shell">
    <header class="site-header">
        <a href="{{ route('blog.index') }}" class="site-brand" aria-label="W Stories home">
            <img src="{{ asset('images/brand-logo.png') }}" alt="W Stories logo">
            <span><strong>W Stories</strong><small>Ideas worth sharing</small></span>
        </a>
        <nav class="site-nav" aria-label="Site navigation">
            <a class="{{ request()->routeIs('blog.*') ? 'is-active' : '' }}" href="{{ route('blog.index') }}">Explore articles</a>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                @can('create', App\Models\Article::class)<a class="site-nav-create" href="{{ route('articles.create') }}">Write an article ↗</a>@endcan
            @else
                <a href="{{ route('login') }}">Sign in</a>
                <a class="site-nav-create" href="{{ route('register') }}">Join for free ↗</a>
            @endauth
        </nav>
    </header>
    @yield('content')
    <footer class="site-footer">
        <div class="footer-inner">
            <div><a href="{{ route('blog.index') }}" class="footer-brand">W<span>.</span> Stories</a><p>A home for meaningful ideas and honest stories.</p></div>
            <div class="footer-links"><a href="{{ route('blog.index') }}">Explore stories</a>@auth<a href="{{ route('dashboard') }}">Your workspace</a>@else<a href="{{ route('register') }}">Create account</a>@endauth</div>
        </div>
        <p class="footer-small">© {{ date('Y') }} W Stories. Made for people who love to read and write.</p>
    </footer>
</div>
</body>
</html>
