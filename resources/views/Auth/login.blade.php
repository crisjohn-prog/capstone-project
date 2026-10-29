<!doctype html>
<html lang="en">
    <head><meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Sign in · RGL Air Conditioning Service</title><link rel="stylesheet" href="{{ asset('css/aircare.css') }}">
        <style>
            .auth-brand > span:last-child { font-size: 1.6rem; }
            .auth-brand small { font-size: .85rem; }
        </style>
    </head>
    <body class="auth-body">
        <div class="auth-wrap">
            <div class="auth-brand">
                <span class="brand-mark">❄</span>
                <span>RGL<small>AIR CONDITIONING SERVICE</small></span>
            </div>
            <div class="auth-card"><div class="eyebrow">WELCOME BACK</div>
            <h1>Sign in to your<br>service workspace.</h1>
            <p class="muted">Manage requests, schedules, and service records.</p>
            <form method="POST" action="{{ route('login.store') }}">@csrf<div class="field">
                <label>Email address</label><input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
            </div>
            <div class="field"><label>Password</label><input type="password" name="password" required placeholder="Your password"></div>
            <label class="check"><input type="checkbox" name="remember"> Keep me signed in</label><button class="button primary full">Sign in <span>→</span></button></form><div class="auth-switch">New to RGL Air Conditioning Service?
                <a href="{{ route('register') }}">Create a customer account</a>
            </div>
        </div>

    </div>
</body>
</html>
