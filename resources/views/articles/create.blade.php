@extends('layouts.app')
@section('title', 'New article')
@section('breadcrumb', 'New article')
@section('content')
<div class="page-heading"><div><span class="eyebrow">CREATE CONTENT</span><h1>Write a new article</h1><p>Turn your next big idea into something worth sharing.</p></div><a class="btn btn-secondary" href="{{ route('articles.index') }}">← Back to articles</a></div>
@include('articles._form', ['article' => null])
@endsection
