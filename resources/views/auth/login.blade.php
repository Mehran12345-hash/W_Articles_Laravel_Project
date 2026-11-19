@extends('layouts.guest')
@section('title', 'Sign in')
@section('content')
<div class="auth-heading"><span class="eyebrow">WELCOME BACK</span><h2>Sign in to your account</h2><p>Good to see you again. Please enter your details.</p></div>
@if ($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('login.submit') }}" class="auth-form">
    @csrf
    <label class="field-label" for="email">Email address</label>
    <input id="email" class="field" type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email" required autofocus>
    <div class="label-row"><label class="field-label" for="password">Password</label></div>
    <input id="password" class="field" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
    <label class="remember"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
    <button class="btn btn-primary btn-block" type="submit">Sign in <span>→</span></button>
</form>
<p class="auth-footer">New here? <a href="{{ route('register') }}">Create an account</a></p>
@endsection
