<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view articles');
    }

    public function view(User $user, Article $article): bool
    {
        if ($user->can('edit any articles')) {
            return true;
        }
        if ($user->can('edit own articles') && $article->user_id === $user->id) {
            return true;
        }

        return $article->status === 'published' && $user->can('view articles');
    }

    public function create(User $user): bool
    {
        return $user->can('create articles');
    }

    public function update(User $user, Article $article): bool
    {
        return $user->can('edit any articles') ||
            ($user->can('edit own articles') && $article->user_id === $user->id);
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->can('delete articles');
    }
}
