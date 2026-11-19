@extends('layouts.app')
@section('title', 'Roles')
@section('breadcrumb', 'Access control / Roles')
@section('content')
<div class="access-shell">
    <div class="access-heading">
        <div>
            <span class="eyebrow">ACCESS CONTROL</span>
            <h1>Roles</h1>
            <p>Group permissions into clear job roles, then assign those roles to your team.</p>
        </div>
        <div class="access-heading-actions">
            <a class="btn btn-secondary" href="{{ route('users.index') }}">Team members <span>↗</span></a>
            @can('manage permissions')<a class="btn btn-primary" href="{{ route('permissions.index') }}">Manage permissions <span>→</span></a>@endcan
        </div>
    </div>

    <nav class="access-tabs" aria-label="Access control pages">
        <a class="access-tab active" href="{{ route('roles.index') }}"><span>♙</span> Roles</a>
        @can('manage permissions')<a class="access-tab" href="{{ route('permissions.index') }}"><span>☷</span> Permissions</a>@endcan
        <a class="access-tab" href="{{ route('users.index') }}"><span>♧</span> Team members</a>
    </nav>

    <section class="role-create-panel">
        <div class="role-create-copy">
            <span class="panel-icon">＋</span>
            <div>
                <h2>Create a role</h2>
                <p>Examples: Content Manager, Reviewer, Support Agent.</p>
            </div>
        </div>
        <form class="role-create-form" method="POST" action="{{ route('roles.store') }}">
            @csrf
            <label class="sr-only" for="role-name">Role name</label>
            <input class="field" id="role-name" name="name" value="{{ old('name') }}" placeholder="Enter a role name" maxlength="50" required>
            <button class="btn btn-primary" type="submit">Create role <span>＋</span></button>
        </form>
    </section>

    <div class="access-section-title">
        <div><h2>Role directory</h2><p>{{ $roles->count() }} roles configured in your workspace</p></div>
        <span class="access-count-pill">{{ $roles->whereIn('name', ['super-admin','supervisor','admin','editor','user','subscriber'])->count() }} built-in</span>
    </div>

    <div class="role-card-grid">
        @foreach($roles as $role)
            @php
                $isBuiltIn = in_array($role->name, ['super-admin','supervisor','admin','editor','user','subscriber'], true);
                $roleTone = match($role->name) {
                    'super-admin' => 'violet',
                    'supervisor' => 'blue',
                    'admin' => 'indigo',
                    'editor' => 'teal',
                    'user', 'subscriber' => 'slate',
                    default => 'amber',
                };
                $roleIcon = match($role->name) {
                    'super-admin' => '✦', 'supervisor' => '◈', 'admin' => '▣',
                    'editor' => '✎', 'user', 'subscriber' => '♙', default => '◇',
                };
            @endphp
            <article class="role-card">
                <div class="role-card-top">
                    <span class="role-icon role-icon-{{ $roleTone }}">{{ $roleIcon }}</span>
                    @if($isBuiltIn)
                        <span class="role-type-badge">Built-in</span>
                    @else
                        <span class="role-type-badge role-type-custom">Custom</span>
                    @endif
                </div>
                <h3>{{ str($role->name)->headline() }}</h3>
                <p class="role-description">{{ $descriptions[$role->name] ?? 'A custom role for your team. Choose its access from the Permissions page.' }}</p>
                <div class="role-card-metrics">
                    <div><strong>{{ $role->users_count }}</strong><span>Members</span></div>
                    <div><strong>{{ $role->permissions_count }}</strong><span>Permissions</span></div>
                </div>
                <div class="role-card-actions">
                    @can('manage permissions')<a class="btn btn-primary btn-small" href="{{ route('permissions.index', ['role' => $role->id]) }}">Configure access <span>→</span></a>@else<span class="role-protected-note">Permission editing restricted</span>@endcan
                    @if(!$isBuiltIn)
                        <a class="role-icon-action" href="{{ route('roles.edit', $role) }}" aria-label="Rename {{ $role->name }}" title="Rename role">✎</a>
                        <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Delete this custom role? This action cannot be undone.');">
                            @csrf @method('DELETE')
                            <button class="role-icon-action role-delete-action" type="submit" aria-label="Delete {{ $role->name }}" title="Delete role">⌫</button>
                        </form>
                    @else
                        <span class="role-protected-note">Protected role</span>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    <div class="access-footnote"><span>ⓘ</span> Built-in roles cannot be renamed or deleted. You can customize their permissions from the separate Permissions page. Super Admin always retains full system access.</div>
</div>
@endsection
