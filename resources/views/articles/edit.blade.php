@extends('layouts.app')
@section('title', 'Edit article')
@section('breadcrumb', 'Edit article')
@section('content')
<div class="page-heading"><div><span class="eyebrow">EDIT CONTENT</span><h1>Edit your article</h1><p>Make your writing even better.</p></div><a class="btn btn-secondary" href="{{ route('articles.show', $article) }}">← Back to article</a></div>
@include('articles._form', ['article' => $article])
@endsection
