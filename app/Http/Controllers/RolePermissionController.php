<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    private const BUILT_IN_ROLES = [
        'super-admin', 'supervisor', 'admin', 'editor', 'user', 'subscriber',
    ];

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('manage roles'), 403);

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->withCount(['users', 'permissions'])
            ->orderByRaw("CASE name WHEN 'super-admin' THEN 0 WHEN 'supervisor' THEN 1 WHEN 'admin' THEN 2 WHEN 'editor' THEN 3 WHEN 'user' THEN 4 ELSE 5 END")
            ->orderBy('name')
            ->get();

        $descriptions = [
            'super-admin' => 'Full control of the application, team, roles and permissions.',
            'supervisor' => 'Oversees content and team activity without changing access rules.',
            'admin' => 'Manages daily content operations and team accounts.',
            'editor' => 'Creates and manages their own articles.',
            'user' => 'Can read published stories and use their personal dashboard.',
            'subscriber' => 'Read-only access to published stories.',
        ];

        return view('roles.index', compact('roles', 'descriptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('manage roles'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        $slug = Str::slug($data['name']);
        if ($slug === '' || strlen($slug) < 2) {
            return back()->withErrors(['name' => 'Enter a role name with at least two letters.'])->withInput();
        }

        if (in_array($slug, self::BUILT_IN_ROLES, true)) {
            return back()->withErrors(['name' => 'That name is reserved for a built-in role.'])->withInput();
        }

        if (Role::where('name', $slug)->where('guard_name', 'web')->exists()) {
            return back()->withErrors(['name' => 'A role with this name already exists.'])->withInput();
        }

        Role::create(['name' => $slug, 'guard_name' => 'web']);

        return redirect()->route('roles.index')->with('success', Str::headline($slug).' role created. Open Permissions to choose its access.');
    }

    public function edit(Request $request, Role $role): View
    {
        abort_unless($request->user()->can('manage roles'), 403);
        abort_unless($role->guard_name === 'web', 404);
        abort_if(in_array($role->name, self::BUILT_IN_ROLES, true), 403, 'Built-in roles cannot be renamed.');

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()->can('manage roles'), 403);
        abort_unless($role->guard_name === 'web', 404);
        abort_if(in_array($role->name, self::BUILT_IN_ROLES, true), 403, 'Built-in roles cannot be renamed.');

        $data = $request->validate(['name' => ['required', 'string', 'max:50']]);
        $slug = Str::slug($data['name']);
        if ($slug === '' || strlen($slug) < 2) {
            return back()->withErrors(['name' => 'Enter a role name with at least two letters.'])->withInput();
        }
        if (in_array($slug, self::BUILT_IN_ROLES, true)) {
            return back()->withErrors(['name' => 'That name is reserved for a built-in role.'])->withInput();
        }

        if (Role::where('name', $slug)->where('guard_name', 'web')->where('id', '!=', $role->id)->exists()) {
            return back()->withErrors(['name' => 'A role with this name already exists.'])->withInput();
        }

        $role->name = $slug;
        $role->save();

        return redirect()->route('roles.index')->with('success', 'Role name updated. Its users and permissions were kept.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()->can('manage roles'), 403);
        abort_unless($role->guard_name === 'web', 404);

        if (in_array($role->name, self::BUILT_IN_ROLES, true)) {
            return back()->withErrors(['role' => 'Built-in roles cannot be deleted.']);
        }
        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'This role is assigned to users. Move those users to another role first.']);
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Custom role deleted.');
    }
}
