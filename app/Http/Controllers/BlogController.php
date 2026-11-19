<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:60'],
        ]);
        $query = Article::with('user')->where('status', 'published');
        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                  ->orWhere('excerpt', 'like', '%'.$search.'%')
                  ->orWhere('category', 'like', '%'.$search.'%');
            });
        }
        if ($category = $filters['category'] ?? null) {
            $query->where('category', $category);
        }
        $articles = $query->latest()->paginate(9)->withQueryString();
        $categories = Article::where('status', 'published')->distinct()->orderBy('category')->pluck('category');
        $featured = Article::with('user')->where('status', 'published')->latest()->first();
        return view('blog.index', compact('articles', 'categories', 'filters', 'featured'));
    }

    public function show(Article $article): View
    {
        abort_unless($article->status === 'published', 404);
        $article->load('user');
        $related = Article::where('status', 'published')->where('id', '!=', $article->id)
            ->where('category', $article->category)->latest()->limit(3)->get();
        return view('blog.show', compact('article', 'related'));
    }
}
