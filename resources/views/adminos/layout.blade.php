<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') · Caval OS</title>
  <link rel="icon" href="{{ asset('os-assets/favicon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('os-assets/os.css') }}?v={{ filemtime(public_path('os-assets/os.css')) }}">
</head>
<body>
@php
  $me = auth()->user();
  $initials = collect(explode(' ', $me->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->join('');
  $nav = [
    ['os.dashboard', null, 'Dashboard', 'grid', null],
    ['os.bookings.index', null, 'Bookings', 'card', $navAwaiting ?? null],
    ['os.section', 'schedule', 'Schedule', 'calendar', null],
    ['os.section', 'clients', 'Clients', 'users', null],
    ['os.section', 'caregivers', 'Caregivers', 'steth', null],
    ['os.section', 'visits', 'Visits & notes', 'clip', null],
    ['os.section', 'billing', 'Billing & payouts', 'cash', null],
    ['os.section', 'messages', 'Family messages', 'chat', null],
    ['os.section', 'analytics', 'Analytics', 'chart', null],
    ['os.section', 'settings', 'Settings', 'gear', null],
  ];
  $icons = [
    'grid' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
    'card' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h4"/>',
    'calendar' => '<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/>',
    'users' => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M17 4a4 4 0 0 1 0 8M22 21a7 7 0 0 0-4-6.3"/>',
    'steth' => '<path d="M5 3v6a5 5 0 0 0 10 0V3M10 14v2a5 5 0 0 0 10 0v-3"/><circle cx="20" cy="11" r="2"/>',
    'clip' => '<rect x="5" y="4" width="14" height="18" rx="2"/><path d="M9 4V2h6v2M9 13l2 2 4-4"/>',
    'cash' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M15 8h-4.5a2 2 0 0 0 0 4h3a2 2 0 0 1 0 4H8M12 6v12"/>',
    'chat' => '<path d="M21 12a8 8 0 0 1-11.6 7.1L3 21l1.9-6.4A8 8 0 1 1 21 12z"/>',
    'chart' => '<path d="M3 3v18h18M7 15l4-4 3 3 6-7"/>',
    'gear' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
  ];
  $live = ['os.dashboard', 'os.bookings.index'];
@endphp
<div class="os">
  <aside class="os-side" aria-label="Caval OS navigation">
    <a class="os-brand" href="{{ route('os.dashboard') }}">
      <img src="{{ asset('os-assets/logo-light.png') }}" alt="Cavalcare">
      <span class="os-brand-badge">OS</span>
    </a>

    <div class="os-org">
      <div class="os-org-mark">C</div>
      <div><b>CavalCare</b><small>Lagos operations</small></div>
    </div>

    <nav class="os-nav">
      @foreach($nav as [$route, $param, $label, $icon, $count])
        @php
          $href = $param ? route($route, $param) : route($route);
          $active = $param
            ? request()->routeIs($route) && request()->route('section') === $param
            : request()->routeIs($route) || request()->routeIs(str_replace('.index', '.*', $route));
        @endphp
        <a href="{{ $href }}" class="{{ $active ? 'active' : '' }}" @if($active) aria-current="page" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$icon] !!}</svg>
          {{ $label }}
          @if($count)
            <span class="count">{{ $count }}</span>
          @elseif(! in_array($route, $live, true))
            <span class="soon">Soon</span>
          @endif
        </a>
      @endforeach
    </nav>

    <div class="os-side-foot">
      <div class="os-me">
        <div class="avatar">{{ $initials }}</div>
        <div><b>{{ $me->name }}</b><small>Admin</small></div>
        <form method="POST" action="{{ route('os.logout') }}">
          @csrf
          <button class="os-signout" type="submit">Sign out</button>
        </form>
      </div>
    </div>
  </aside>

  <main class="os-main">
    <header class="os-top">
      <div>
        <div class="date">{{ now()->timezone('Africa/Lagos')->format('l, j F Y') }}</div>
        <h1>@yield('heading')</h1>
      </div>
      <form class="os-search" action="{{ route('os.bookings.index') }}" method="GET" role="search">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input name="q" value="{{ request('q') }}" placeholder="Search bookings by name, email or reference" aria-label="Search bookings">
      </form>
    </header>

    <div class="os-content">
      @if(session('os_flash'))
        @php [$flashKind, $flashText] = session('os_flash'); @endphp
        <div class="alert {{ $flashKind === 'stable' ? '' : $flashKind }}" role="status"
             style="{{ $flashKind === 'stable' ? 'background:var(--stable);border-color:#BFE6E2' : '' }}">
          <div class="alert-title" style="{{ $flashKind === 'stable' ? 'color:var(--stable-ink)' : '' }}">{{ $flashText }}</div>
        </div>
      @endif
      @yield('content')
    </div>
  </main>
</div>

<script>
  // Click anywhere on a table row to open it (links stay keyboard-accessible).
  document.querySelectorAll('tr[data-href]').forEach(function (row) {
    row.addEventListener('click', function (e) {
      if (e.target.closest('a, button')) return;
      window.location = row.dataset.href;
    });
  });
</script>
</body>
</html>
