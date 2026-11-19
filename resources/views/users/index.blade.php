@extends('layouts.app')
@section('title', 'Team members')
@section('breadcrumb', 'Team members')
@section('content')
<div class="access-shell">
    <div class="access-heading">
        <div>
            <span class="eyebrow">PEOPLE & ACCESS</span>
            <h1>Team members</h1>
            <p>See who has access to your workspace and manage their assigned role.</p>
        </div>
        @can('manage roles')<a class="btn btn-secondary" href="{{ route('roles.index') }}">Manage roles <span>→</span></a>@endcan
    </div>

    <div class="team-summary-grid">
        <div class="team-summary-card"><span class="team-summary-icon team-icon-blue">♧</span><div><strong>{{ $users->total() }}</strong><small>Registered members</small></div></div>
        <div class="team-summary-card"><span class="team-summary-icon team-icon-green">✓</span><div><strong>{{ $users->getCollection()->filter(fn($member) => $member->hasRole('admin'))->count() }}</strong><small>Admins on this page</small></div></div>
        <div class="team-summary-card"><span class="team-summary-icon team-icon-purple">✎</span><div><strong>{{ $users->getCollection()->filter(fn($member) => $member->hasRole('editor'))->count() }}</strong><small>Editors on this page</small></div></div>
    </div>

    <section class="surface-card team-table-card">
        <div class="team-table-heading"><div><h2>Workspace members</h2><p>Roles decide which pages and actions each person can access.</p></div><span class="access-count-pill">{{ $users->total() }} total</span></div>
        <div class="table-scroll">
            <table class="data-table team-table">
                <thead><tr><th>MEMBER</th><th>ASSIGNED ROLE</th><th>ROLE ACCESS</th><th>UPDATE</th></tr></thead>
                <tbody>
                @forelse($users as $member)
                    <tr>
                        <td><div class="table-title"><span class="access-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</span><div><strong>{{ $member->name }} @if(auth()->id() === $member->id)<span class="you-pill">YOU</span>@endif</strong><small>{{ $member->email }}</small></div></div></td>
                        <td><div class="team-role-tags">@forelse($member->getRoleNames() as $roleName)<span class="role-chip role-chip-{{ str_replace('-', '', $roleName) }}">{{ str($roleName)->headline() }}</span>@empty<span class="role-chip">No role</span>@endforelse</div></td>
                        <td><span class="team-access-summary">{{ $member->getAllPermissions()->count() }} permissions</span></td>
                        <td>
                                @if($canChangeMemberRole && $roles->isNotEmpty() && auth()->id() !== $member->id)
                                <form class="team-role-form" method="POST" action="{{ route('users.role.update', $member) }}">
                                    @csrf @method('PATCH')
                                    <label class="sr-only" for="member-role-{{ $member->id }}">Role for {{ $member->name }}</label>
                                    <select class="field team-role-select" id="member-role-{{ $member->id }}" name="role">
                                        @foreach($roles as $role)<option value="{{ $role->name }}" @selected($member->hasRole($role))>{{ str($role->name)->headline() }}</option>@endforeach
                                    </select>
                                    <button class="btn btn-primary btn-small" type="submit">Save</button>
                                </form>
                            @else
                                <span class="read-only-label">{{ auth()->id() === $member->id ? 'Your account' : 'View only' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="empty-state"><h3>No team members found</h3><p>Registered accounts will appear here.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $users->links() }}</div>
    </section>
    <div class="access-footnote"><span>ⓘ</span> Admins can assign day-to-day roles. Only Super Admin can promote or change Supervisor, Admin and Super Admin accounts.</div>
</div>
@endsection
