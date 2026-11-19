<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    private const GROUPS = [
        'dashboard' => [
            'title' => 'Dashboard',
            'description' => 'Access to the private workspace and overview.',
            'permissions' => [
                'view dashboard' => 'Open the dashboard and workspace overview',
            ],
        ],
        'articles' => [
            'title' => 'Articles & publishing',
            'description' => 'Control who can create, review, edit and publish stories.',
            'permissions' => [
                'view articles' => 'View articles in the workspace',
                'create articles' => 'Create new articles',
                'edit own articles' => 'Edit articles created by this user',
                'edit any articles' => 'Edit articles created by any user',
                'delete articles' => 'Delete articles from the system',
                'publish articles' => 'Publish articles to the public blog',
            ],
        ],
        'users' => [
            'title' => 'Team & users',
            'description' => 'View team members and manage their accounts or roles.',
            'permissions' => [
                'view users' => 'View the team members list',
                'manage users' => 'Manage team member accounts',
                'assign roles' => 'Assign roles to team members',
            ],
        ],
        'access' => [
            'title' => 'Roles & permissions',
            'description' => 'Manage role definitions and permission checklists.',
            'permissions' => [
                'manage roles' => 'Create, rename and delete custom roles',
                'manage permissions' => 'Change permissions assigned to roles',
            ],
        ],
    ];

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('manage permissions'), 403);

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderByRaw("CASE name WHEN 'super-admin' THEN 0 WHEN 'supervisor' THEN 1 WHEN 'admin' THEN 2 WHEN 'editor' THEN 3 WHEN 'user' THEN 4 ELSE 5 END")
            ->orderBy('name')
            ->get();

        $selectedRole = null;
        if ($request->filled('role')) {
            $selectedRole = $roles->firstWhere('id', (int) $request->query('role'));
        }
        $selectedRole ??= $roles->firstWhere('name', 'admin') ?? $roles->first();

        $permissionNames = collect(self::GROUPS)
            ->flatMap(fn (array $group) => array_keys($group['permissions']))
            ->values();
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $permissionNames)
            ->get()
            ->keyBy('name');

        $groups = collect(self::GROUPS)->map(function (array $group) use ($permissions) {
            $group['items'] = collect($group['permissions'])->map(function (string $description, string $name) use ($permissions) {
                return [
                    'name' => $name,
                    'label' => ucfirst($name),
                    'description' => $description,
                    'exists' => $permissions->has($name),
                ];
            })->values();
            return $group;
        });

        return view('permissions.index', compact('roles', 'selectedRole', 'groups'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()->can('manage permissions'), 403);
        abort_unless($role->guard_name === 'web', 404);

        if ($role->name === 'super-admin') {
            return back()->withErrors(['permissions' => 'Super Admin always has full access. Its permissions are locked.']);
        }

        $allowed = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', collect(self::GROUPS)->flatMap(fn (array $group) => array_keys($group['permissions'])) )
            ->pluck('name')
            ->all();

        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($allowed)],
        ]);

        $selected = array_values(array_unique($data['permissions'] ?? []));
        $role->syncPermissions($selected);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('permissions.index', ['role' => $role->id])
            ->with('success', 'Permissions updated for '.str($role->name)->headline().'.');
    }
}
