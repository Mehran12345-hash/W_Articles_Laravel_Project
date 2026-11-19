@extends('layouts.app')
@section('title', 'Permissions')
@section('breadcrumb', 'Access control / Permissions')
@section('content')
<div class="access-shell">
    <div class="access-heading">
        <div>
            <span class="eyebrow">ACCESS CONTROL</span>
            <h1>Permissions</h1>
            <p>Choose exactly what each role can see and do. Changes apply to every member with that role.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('roles.index') }}">View roles <span>↗</span></a>
    </div>

    <nav class="access-tabs" aria-label="Access control pages">
        <a class="access-tab" href="{{ route('roles.index') }}"><span>♙</span> Roles</a>
        <a class="access-tab active" href="{{ route('permissions.index') }}"><span>☷</span> Permissions</a>
        <a class="access-tab" href="{{ route('users.index') }}"><span>♧</span> Team members</a>
    </nav>

    @if($selectedRole)
        <section class="permission-target-panel">
            <div class="permission-target-copy">
                <span class="permission-target-icon">☷</span>
                <div><span class="target-kicker">EDITING ACCESS FOR</span><h2>{{ str($selectedRole->name)->headline() }}</h2><p>Select the permissions this role should have.</p></div>
            </div>
            <form method="GET" action="{{ route('permissions.index') }}" class="permission-role-picker">
                <label for="permission-role">Choose role</label>
                <select class="field" id="permission-role" name="role" onchange="this.form.submit()">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected($selectedRole->id === $role->id)>{{ str($role->name)->headline() }}</option>
                    @endforeach
                </select>
            </form>
        </section>

        @if($selectedRole->name === 'super-admin')
            <div class="superadmin-lock-note"><span>✦</span><div><strong>Full system access is protected</strong><p>Super Admin always has every permission. This role cannot be restricted from this screen.</p></div></div>
        @endif

        <form method="POST" action="{{ route('permissions.update', $selectedRole) }}" id="permission-form">
            @csrf @method('PUT')
            <div class="permission-toolbar">
                <div><strong>Permission checklist</strong><span>Tick a box to allow an action. Untick it to block that action.</span></div>
                @unless($selectedRole->name === 'super-admin')
                    <button class="text-button" type="button" id="select-all-permissions">Select all available</button>
                @endunless
            </div>

            <div class="permission-module-grid">
                @foreach($groups as $groupKey => $group)
                    <section class="permission-module-card">
                        <div class="permission-module-head">
                            <div><span class="permission-module-icon">{{ match($groupKey) {'dashboard' => '▦', 'articles' => '▤', 'users' => '♧', default => '⚙'} }}</span><h3>{{ $group['title'] }}</h3><p>{{ $group['description'] }}</p></div>
                            @unless($selectedRole->name === 'super-admin')
                                <label class="module-select-all"><input type="checkbox" class="module-toggle" data-module="{{ $groupKey }}"> Select all</label>
                            @endunless
                        </div>
                        <div class="permission-check-list">
                            @foreach($group['items'] as $item)
                                <label class="permission-check-row {{ !$item['exists'] ? 'permission-unavailable' : '' }}">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $item['name'] }}"
                                        class="permission-check"
                                        data-module="{{ $groupKey }}"
                                        @checked($item['exists'] && $selectedRole->hasPermissionTo($item['name']))
                                        @disabled($selectedRole->name === 'super-admin' || !$item['exists'])
                                    >
                                    <span class="permission-check-copy"><strong>{{ $item['label'] }}</strong><small>{{ $item['description'] }}</small></span>
                                    <span class="permission-check-state">{{ $item['exists'] ? 'Access' : 'Run migration' }}</span>
                                </label>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="permission-save-bar">
                <div><strong>{{ str($selectedRole->name)->headline() }} access</strong><span>Changes are saved immediately after you press Save permissions.</span></div>
                @unless($selectedRole->name === 'super-admin')
                    <button class="btn btn-primary" type="submit">Save permissions <span>→</span></button>
                @endunless
            </div>
        </form>
    @else
        <div class="surface-card empty-state"><h3>No roles found</h3><p>Create a role first, then return here to assign its permissions.</p><a class="btn btn-primary" href="{{ route('roles.index') }}">Go to Roles</a></div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.module-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            document.querySelectorAll('.permission-check[data-module="' + this.dataset.module + '"]').forEach(function (box) {
                if (!box.disabled) box.checked = toggle.checked;
            });
        });
    });

    document.querySelectorAll('.permission-check').forEach(function (box) {
        box.addEventListener('change', function () {
            const group = document.querySelector('.module-toggle[data-module="' + this.dataset.module + '"]');
            if (!group) return;
            const items = Array.from(document.querySelectorAll('.permission-check[data-module="' + this.dataset.module + '"]:not(:disabled)'));
            group.checked = items.length > 0 && items.every(item => item.checked);
            group.indeterminate = items.some(item => item.checked) && !group.checked;
        });
    });

    const selectAll = document.getElementById('select-all-permissions');
    if (selectAll) {
        selectAll.addEventListener('click', function () {
            const boxes = Array.from(document.querySelectorAll('.permission-check:not(:disabled)'));
            const shouldCheck = !boxes.every(box => box.checked);
            boxes.forEach(box => box.checked = shouldCheck);
            document.querySelectorAll('.module-toggle').forEach(toggle => {
                toggle.checked = shouldCheck;
                toggle.indeterminate = false;
            });
        });
    }
});
</script>
@endsection
