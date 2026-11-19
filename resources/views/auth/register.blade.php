@extends('layouts.guest')
@section('title', 'Create account')
@section('content')
<div class="auth-heading"><span class="eyebrow">LET'S GET STARTED</span><h2>Create your account</h2><p>Start writing and organizing your ideas in minutes.</p></div>
@if ($errors->any())<div class="alert alert-error" role="alert"><strong>Please fix:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('register.submit') }}" class="auth-form">
    @csrf
    <label class="field-label" for="name">Full name</label>
    <input id="name" class="field" name="name" value="{{ old('name') }}" placeholder="Your full name" autocomplete="name" maxlength="100" required>
    <label class="field-label" for="email">Email address</label>
    <input id="email" class="field" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
    <label class="field-label" for="password">Password</label>
    <input id="password" class="field" type="password" name="password" placeholder="At least 8 characters" minlength="8" autocomplete="new-password" required>
    <label class="field-label" for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" class="field" type="password" name="password_confirmation" placeholder="Enter password again" autocomplete="new-password" required>
    <button class="btn btn-primary btn-block" type="submit">Create account <span>→</span></button>
</form>
<p class="auth-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
@endsection
