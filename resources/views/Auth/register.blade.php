<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Create account · RGL Air Conditioning Service</title>
        <link rel="stylesheet" href="{{ asset('css/aircare.css') }}">
        <style>
            .auth-brand > span:last-child { font-size: 1.6rem; }
            .auth-brand small { font-size: .85rem; }
        </style>
    </head>
    <body class="auth-body">
        <div class="auth-wrap"><div class="auth-brand">
            <span class="brand-mark">❄</span>
            <span>RGL<small>AIR CONDITIONING SERVICE</small></span>
        </div>
<div class="auth-card">
    <div class="eyebrow">CUSTOMER PORTAL</div>
    <h1>Set up your account.</h1>
    <p class="muted">Submit a service request and follow its progress.</p>
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="field"><label>Full name</label><input name="name" value="{{ old('name') }}" required autocomplete="name"></div><div class="field"><label>Email address</label><input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></div><div class="field"><label>Phone number <small>Optional</small></label><input name="phone" value="{{ old('phone') }}"></div><div class="field"><label>Password</label><input type="password" name="password" required minlength="8"></div><div class="field"><label>Confirm password</label><input type="password" name="password_confirmation" required minlength="8"></div><button class="button primary full">Create account <span>→</span></button></form><div class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></div></div></div></body></html>
