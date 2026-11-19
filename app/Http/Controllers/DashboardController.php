<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view dashboard');
        Gate::authorize('viewAny', Article::class);

        $user = $request->user();
        $query = Article::query();

        if ($user->can('edit any articles')) {
            // Supervisors and administrators can review the whole content library.
        } elseif ($user->can('create articles')) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('status', 'published');
        }

        $total = (clone $query)->count();
        $published = (clone $query)->where('status', 'published')->count();
        $drafts = (clone $query)->where('status', 'draft')->count();
        $latest = (clone $query)->with('user')->latest()->limit(6)->get();

        $start = now()->startOfDay()->subDays(6);
        $counts = (clone $query)->where('created_at', '>=', $start)
            ->get(['created_at'])
            ->groupBy(fn ($article) => $article->created_at->format('Y-m-d'))
            ->map->count();

        $activity = collect(range(0, 6))->map(function ($offset) use ($start, $counts) {
            $date = $start->copy()->addDays($offset);
            return [
                'day' => $date->format('D'),
                'date' => $date->format('M d'),
                'count' => $counts->get($date->format('Y-m-d'), 0),
            ];
        });

        $teamCount = $user->can('view users') ? User::count() : null;
        $roleCount = $user->can('manage roles') ? Role::where('guard_name', 'web')->count() : null;
        $roleLabel = $user->getRoleNames()->map(fn ($role) => str($role)->headline())->join(', ');

        return view('dashboard', compact(
            'total', 'published', 'drafts', 'latest', 'activity',
            'teamCount', 'roleCount', 'roleLabel'
        ));
    }
}
