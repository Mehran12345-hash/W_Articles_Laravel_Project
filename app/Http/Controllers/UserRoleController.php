<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    // Display all team members
    public function index(Request $request): View
    {
        abort_unless(
            $request->user()->can('view users'),
            403
        );

        $actor = $request->user();

        // Check whether current user can change member roles
        $canChangeMemberRole =
            $actor->can('manage users') &&
            $actor->can('assign roles');

        // Get available roles
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        // Only Super Admin can assign protected roles
        if (! $actor->hasRole('super-admin')) {
            $roles = $roles
                ->whereIn('name', [
                    'editor',
                    'user',
                    'subscriber'
                ])
                ->values();
        }

        return view('users.index', [
            'users' => User::with('roles')
                ->orderBy('name')
                ->paginate(12),

            'roles' => $roles,

            // FIX: Pass the missing variable to Blade
            'canChangeMemberRole' => $canChangeMemberRole,
        ]);
    }

    // Update a member's role
    public function update(
        Request $request,
        User $user
    ): RedirectResponse {

        abort_unless(
            $request->user()->can('manage users') &&
            $request->user()->can('assign roles'),
            403
        );

        $actor = $request->user();

        $data = $request->validate([
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web'),
            ],
        ]);

        // Prevent changing your own role
        if ($actor->is($user)) {
            return back()->withErrors([
                'role' => 'You cannot change your own role.'
            ]);
        }

        $isSuperAdmin = $actor->hasRole('super-admin');

        $protectedRoles = [
            'super-admin',
            'supervisor',
            'admin'
        ];

        if (! $isSuperAdmin) {

            if ($user->hasAnyRole($protectedRoles)) {
                return back()->withErrors([
                    'role' => 'Only Super Admin can change protected accounts.'
                ]);
            }

            if (
                $user->getRoleNames()
                    ->diff(['editor', 'user', 'subscriber'])
                    ->isNotEmpty()
            ) {
                return back()->withErrors([
                    'role' => 'Only Super Admin can change custom-role accounts.'
                ]);
            }

            if (! in_array(
                $data['role'],
                ['editor', 'user', 'subscriber'],
                true
            )) {
                return back()->withErrors([
                    'role' => 'You can assign Editor, User or Subscriber only.'
                ]);
            }
        }

        // Never remove the last Super Admin
        if (
            $user->hasRole('super-admin') &&
            $data['role'] !== 'super-admin' &&
            User::role('super-admin')->count() <= 1
        ) {
            return back()->withErrors([
                'role' => 'You cannot remove the last Super Admin.'
            ]);
        }

        $user->syncRoles([$data['role']]);

        return back()->with(
            'success',
            'Role updated for ' . $user->name . '.'
        );
    }
}

