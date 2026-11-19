<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Article::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
        ]);

        $user = $request->user();
        $query = Article::with('user');
        if ($user->can('edit any articles')) {
            // Admin can see all articles.
        } elseif ($user->can('create articles')) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('status', 'published');
        }

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                  ->orWhere('category', 'like', '%'.$search.'%');
            });
        }
        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        return view('articles.index', [
            'articles' => $query->latest()->paginate(8)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Article::class);
        return view('articles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Article::class);
        $data = $this->validated($request);
        $this->ensureCanPublish($request, $data);
        unset($data['remove_cover']);
        $data['user_id'] = $request->user()->id;
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('article-covers', 'public');
        }
        Article::create($data);
        return redirect()->route('articles.index')->with('success', 'Article created successfully.');
    }

    public function show(Article $article): View
    {
        Gate::authorize('view', $article);
        $article->load('user');
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        Gate::authorize('update', $article);
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        Gate::authorize('update', $article);
        $data = $this->validated($request);
        $this->ensureCanPublish($request, $data);
        $oldCover = $article->cover_image;
        $removeCover = $request->boolean('remove_cover');
        unset($data['remove_cover']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('article-covers', 'public');
        } elseif ($removeCover) {
            $data['cover_image'] = null;
        }

        $article->update($data);
        if ($oldCover && array_key_exists('cover_image', $data) && $oldCover !== $data['cover_image']) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()->route('articles.index')->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);
        $cover = $article->cover_image;
        $article->delete();
        if ($cover) {
            Storage::disk('public')->delete($cover);
        }
        return redirect()->route('articles.index')->with('success', 'Article deleted successfully.');
    }

    private function ensureCanPublish(Request $request, array $data): void
    {
        if (($data['status'] ?? 'draft') === 'published') {
            abort_unless($request->user()->can('publish articles'), 403, 'You do not have permission to publish articles.');
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:60'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'body' => ['required', 'string', 'min:10'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_cover' => ['nullable', 'boolean'],
        ]);
    }
}
