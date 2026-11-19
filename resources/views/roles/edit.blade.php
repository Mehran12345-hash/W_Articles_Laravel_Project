@extends('layouts.app')
@section('title', 'Rename role')
@section('breadcrumb', 'Access control / Rename role')
@section('content')
<div class="access-shell access-narrow">
    <div class="access-heading">
        <div>
            <span class="eyebrow">ROLE SETTINGS</span>
            <h1>Rename role</h1>
            <p>Change the role name without removing its users or permissions.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('roles.index') }}">← Back to roles</a>
    </div>
    <section class="surface-card role-edit-card">
        <div class="role-edit-summary">
            <span class="role-icon role-icon-amber">◇</span>
            <div><strong>{{ str($role->name)->headline() }}</strong><small>Custom role · {{ $role->users()->count() }} assigned members</small></div>
        </div>
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf @method('PATCH')
            <label class="field-label" for="role-name">New role name</label>
            <input class="field" id="role-name" name="name" value="{{ old('name', str($role->name)->headline()) }}" maxlength="50" required>
            <p class="input-hint">Use a clear name such as Content Manager or Article Reviewer. Users and permissions stay connected.</p>
            <div class="role-edit-actions">
                <a class="btn btn-secondary" href="{{ route('roles.index') }}">Cancel</a>
                <button class="btn btn-primary" type="submit">Save role name</button>
            </div>
        </form>
    </section>
</div>
@endsection
