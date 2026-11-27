<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>@yield('title') · Caval OS</title>
  <link rel="icon" href="{{ asset('os-assets/favicon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;1,9..144,500&family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('os-assets/os.css') }}?v={{ filemtime(public_path('os-assets/os.css')) }}">
</head>
<body>
<div class="login-wrap">
  <section class="login-art">
    <div class="os-brand">
      <img src="{{ asset('os-assets/logo-light.png') }}" alt="Cavalcare" style="height:38px">
      <span class="os-brand-badge">OS</span>
    </div>
    <h1>Every family, visit and caregiver, <em>in one place.</em></h1>
    <p style="opacity:.7;margin:0">Authorised CavalCare staff only. Sign-ins are recorded.</p>
  </section>

  <section class="login-form">
    <div class="login-card">
      @if(session('os_status'))
        <div class="flash" role="status" style="margin:0 0 18px">{{ session('os_status') }}</div>
      @endif
      @yield('card')
    </div>
  </section>
</div>
</body>
</html>
