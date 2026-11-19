@extends('layouts.app')
@section('title',$article->title)
@section('breadcrumb','View article')
@section('content')
<div class="article-read-header"><a class="back-link" href="{{ route('articles.index') }}">← Back to articles</a><div class="read-actions">@if($article->status === 'published')<a class="btn btn-secondary" href="{{ route('blog.show',$article) }}" target="_blank" rel="noopener">View on blog ↗</a>@endif @can('update',$article)<a class="btn btn-secondary" href="{{ route('articles.edit',$article) }}">✎ Edit article</a>@endcan @can('delete',$article)<form method="POST" action="{{ route('articles.destroy',$article) }}" onsubmit="return confirm('Delete this article permanently?');">@csrf @method('DELETE')<button class="btn btn-danger-ghost" type="submit">Delete</button></form>@endcan</div></div>
<article class="surface-card read-card"><div class="article-read-meta"><span class="small-chip">{{ $article->category }}</span><span class="status-pill status-{{ $article->status }}">● {{ ucfirst($article->status) }}</span><span>{{ $article->created_at->format('M d, Y') }}</span></div><h1>{{ $article->title }}</h1>@if($article->excerpt)<p class="read-excerpt">{{ $article->excerpt }}</p>@endif
@if($article->cover_image)<img class="read-cover" src="{{ asset('storage/'.$article->cover_image) }}" alt="Cover for {{ $article->title }}">@endif
<div class="read-divider"></div><div class="read-body">{!! nl2br(e($article->body)) !!}</div><div class="read-author"><div class="avatar">{{ strtoupper(substr($article->user?->name ?? 'W',0,1)) }}</div><span>Written by <strong>{{ $article->user?->name ?? 'W Stories' }}</strong></span></div></article>
@endsection
