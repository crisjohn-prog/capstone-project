<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · RGL Air Conditioning Service</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/aircare.css') }}">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="brand"><span class="brand-mark">❄</span><span>RGL<small>AIR CONDITIONING</small></span></a>
        <div class="nav-label">WORKSPACE</div>
        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><span>▦</span> Overview</a>
            <a href="{{ route('requests.index') }}" class="{{ request()->routeIs('requests.*') && !request()->routeIs('requests.create') ? 'active' : '' }}"><span>◷</span> Service requests</a>
            @if(auth()->user()->role === 'customer')<a href="{{ route('requests.create') }}" class="{{ request()->routeIs('requests.create') ? 'active' : '' }}"><span>＋</span> New request</a>@endif
            @if(auth()->user()->isStaff())
                <a href="{{ route('technicians.index') }}" class="{{ request()->routeIs('technicians.*') ? 'active' : '' }}"><span>♙</span> Technicians</a>
                <a href="{{ route('reports') }}" class="{{ request()->routeIs('reports') ? 'active' : '' }}"><span>▤</span> Reports & history</a>
            @endif
        </nav>
        <div class="sidebar-bottom"><div class="user-chip"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="user-meta"><b>{{ auth()->user()->name }}</b><small>{{ ucfirst(auth()->user()->role) }}</small></span></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout">↪ &nbsp; Sign out</button></form><div class="side-foot">RGL Air Conditioning Service<br>Integrated service management</div></div>
    </aside>
    <main class="main">
        <header class="topbar"><div><div class="eyebrow">RGL AIR CONDITIONING SERVICE</div><div class="page-title">@yield('title', 'Overview')</div></div><div class="top-right"><span class="live-dot"></span> Service desk online <span class="top-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span></div></header>
        <section class="page-content">
            @if(session('success'))<div class="alert success"><span>✓</span>{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert error"><span>!</span><div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div></div>@endif
            @yield('content')
        </section>
    </main>
</div>
</body>
</html>
