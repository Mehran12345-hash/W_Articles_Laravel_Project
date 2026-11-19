@extends('layouts.blog')
@section('title', 'Explore articles')
@section('content')
<section class="blog-hero">
    <div class="hero-decoration hero-decoration-one"></div><div class="hero-decoration hero-decoration-two"></div>
    <div class="blog-hero-inner">
        <span class="hero-kicker"><span class="kicker-dot"></span> THE EDITORIAL · W STORIES</span>
        <h1>Good stories.<br><em>Fresh perspectives.</em></h1>
        <p>Ideas that inspire you to think, create, and see the world a little differently. Read something worth your time.</p>
        <a class="hero-cta" href="#latest-stories">Explore stories <span>↗</span></a>
        <div class="hero-side-note">READ · DISCOVER · BE INSPIRED</div>
    </div>
</section>
<main class="blog-main" id="latest-stories">
    <div class="section-heading-blog">
        <div><span class="eyebrow-blog">HANDPICKED STORIES</span><h2>Latest articles<span class="accent-dot">.</span></h2><p>Explore something new from our community of writers.</p></div>
        <span class="article-counter">{{ $articles->total() }} {{ \Illuminate\Support\Str::plural('story', $articles->total()) }}</span>
    </div>
    <form method="GET" action="{{ route('blog.index') }}" class="blog-search" role="search">
        <label class="search-input"><span>⌕</span><input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search stories or categories" aria-label="Search articles"></label>
        <select name="category" aria-label="Filter category">
            <option value="">All categories</option>
            @foreach($categories as $category)<option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>@endforeach
        </select>
        <button type="submit">Find stories ↗</button>
        @if(!empty($filters['search']) || !empty($filters['category']))<a href="{{ route('blog.index') }}" class="clear-search">Clear filters</a>@endif
    </form>
    @if($articles->isEmpty())
    <div class="blog-empty"><span>✎</span><h3>No stories found yet.</h3><p>Try another search, or visit again when new articles are published.</p><a href="{{ route('blog.index') }}">Show all articles →</a></div>
    @else
    <div class="blog-grid">
        @foreach($articles as $article)
        <article class="story-card">
            <a href="{{ route('blog.show', $article) }}" class="story-visual" aria-label="Read {{ $article->title }}">
                @if($article->cover_image)<img src="{{ asset('storage/'.$article->cover_image) }}" alt="{{ $article->title }}" loading="lazy">@else<div class="story-placeholder"><span>W<span class="placeholder-point">.</span></span></div>@endif
                <span class="story-visual-arrow">↗</span>
            </a>
            <div class="story-details"><div class="story-meta"><span class="category-label">{{ $article->category }}</span><span>{{ $article->created_at->format('M d, Y') }}</span></div>
                <h3><a href="{{ route('blog.show', $article) }}">{{ $article->title }}</a></h3>
                <p>{{ \Illuminate\Support\Str::limit($article->excerpt ?: $article->body, 125) }}</p>
                <div class="story-byline"><span class="writer-avatar">{{ strtoupper(substr($article->user?->name ?? 'W',0,1)) }}</span><span>{{ $article->user?->name ?? 'W Stories' }}</span><a href="{{ route('blog.show', $article) }}" class="read-more">Read article →</a></div>
            </div>
        </article>
        @endforeach
    </div>
    <div class="blog-pagination">{{ $articles->links() }}</div>
    @endif
</main>
<section class="join-banner"><div><span>YOUR VOICE MATTERS</span><h2>Every story starts with an idea.</h2><p>Bring your thoughts to life in a beautiful writing space.</p></div>@auth<a href="{{ route('dashboard') }}">Open your workspace ↗</a>@else<a href="{{ route('register') }}">Create an account ↗</a>@endauth</section>
@endsection
