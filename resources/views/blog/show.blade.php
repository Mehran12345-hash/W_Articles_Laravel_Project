@extends('layouts.blog')
@section('title', $article->title)
@section('meta_description', $article->excerpt ?: \Illuminate\Support\Str::limit($article->body, 155))
@section('content')
<main class="reading-page">
    <a class="reading-back" href="{{ route('blog.index') }}">← Back to all stories</a>
    <div class="reading-head"><span class="reading-category">{{ $article->category }}</span><h1>{{ $article->title }}</h1>@if($article->excerpt)<p class="reading-intro">{{ $article->excerpt }}</p>@endif
        <div class="reading-author"><span class="writer-avatar">{{ strtoupper(substr($article->user?->name ?? 'W', 0, 1)) }}</span><span><strong>{{ $article->user?->name ?? 'W Stories' }}</strong><small>{{ $article->created_at->format('F d, Y') }} · Published story</small></span></div>
    </div>
    @if($article->cover_image)<div class="reading-image"><img src="{{ asset('storage/'.$article->cover_image) }}" alt="Cover for {{ $article->title }}"></div>@else<div class="reading-image reading-gradient"><span>W<span>.</span></span></div>@endif
    <article class="reading-body">{!! nl2br(e($article->body)) !!}</article>
    <div class="reading-end"><span>Thanks for reading.</span><a href="{{ route('blog.index') }}">Discover more stories ↗</a></div>
    @if($related->isNotEmpty())<section class="related-stories"><span class="eyebrow-blog">KEEP READING</span><h2>More from {{ $article->category }}</h2><div class="related-grid">@foreach($related as $item)<a href="{{ route('blog.show', $item) }}" class="related-link"><span class="category-label">{{ $item->category }}</span><strong>{{ $item->title }}</strong><small>Read story ↗</small></a>@endforeach</div></section>@endif
</main>
@endsection
