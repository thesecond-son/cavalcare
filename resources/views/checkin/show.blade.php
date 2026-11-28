<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#1A3A2F">
  <title>CavalCare check-in</title>
  <style>
    :root { --forest:#1A3A2F; --mint:#F0FFDB; --teal:#0A7C7A; --ink:#1A3A2F; --muted:#4E6359; --canvas:#F6F7F2; }
    * { box-sizing: border-box; }
    body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", sans-serif; background: var(--canvas); color: var(--ink);
           min-height: 100vh; display: grid; place-items: center; padding: 24px 16px calc(24px + env(safe-area-inset-bottom)); }
    .card { width: min(420px, 100%); background: #fff; border-radius: 24px; padding: 28px 22px; text-align: center; box-shadow: 0 10px 40px rgba(26,58,47,.08); }
    .brand { font-family: Georgia, serif; font-size: 22px; margin-bottom: 18px; }
    h1 { font-size: 22px; margin: 0 0 6px; }
    p { font-size: 18px; line-height: 1.45; color: var(--muted); margin: 0 0 12px; }
    .who { font-size: 24px; font-weight: 700; color: var(--ink); }
    .code { font-size: 56px; font-weight: 800; letter-spacing: .12em; font-variant-numeric: tabular-nums; margin: 14px 0 6px; }
    .qr { width: 240px; max-width: 100%; margin: 8px auto 0; }
    .qr svg { width: 100%; height: auto; display: block; }
    .bar { height: 8px; background: #EEF0EA; border-radius: 99px; overflow: hidden; margin: 14px 0 4px; }
    .bar span { display: block; height: 100%; background: var(--teal); transition: width 1s linear; }
    .ok { background: var(--mint); border-radius: 16px; padding: 14px; font-size: 18px; color: var(--forest); font-weight: 700; }
    .help { margin-top: 18px; font-size: 16px; }
    .help a { color: var(--teal); font-weight: 700; }
  </style>
</head>
<body>
  <main class="card" id="app">
    <div class="brand">CavalCare</div>
    @if(! $active)
      <h1>No visit right now</h1>
      <p>When your nurse or caregiver arrives, open this page again. Their code will be here.</p>
    @else
      @if($checked_in)
        <div class="ok">{{ $caregiver }} has checked in. Thank you.</div>
      @else
        <p>Your visit from</p>
        <div class="who">{{ $caregiver }}</div>
        <p>{{ $caregiver_role }} · from {{ $starts }}</p>
        <p style="margin-top:14px">Show this to them to scan:</p>
        <div class="code" id="code" aria-live="polite">{{ $code }}</div>
        <div class="qr" id="qr" aria-hidden="true">{!! $qr_svg !!}</div>
        <div class="bar" aria-hidden="true"><span id="bar" style="width: {{ round($seconds_left / 30 * 100) }}%"></span></div>
        <p style="font-size:14px">The code changes every 30 seconds.</p>
      @endif
    @endif
    <p class="help">Not who you expected? Don't open the door.
      @if($support_phone) Call CavalCare: <a href="tel:{{ preg_replace('/[^\d+]/', '', $support_phone) }}">{{ $support_phone }}</a>@endif
    </p>
  </main>
  <script>
    (function () {
      var url = {!! json_encode(url('/c/' . $linkCode . '/now')) !!}, left = {{ (int) ($seconds_left ?? 30) }}, active = {{ $active ? 'true' : 'false' }};
      function refresh() {
        fetch(url, { headers: { 'Accept': 'application/json' }, cache: 'no-store' }).then(function (r) { return r.json(); }).then(function (s) {
          if (s.active !== active || s.checked_in) { location.reload(); return; }
          if (!s.active) return;
          document.getElementById('code').textContent = s.code;
          document.getElementById('qr').innerHTML = s.qr_svg;
          left = s.seconds_left;
        }).catch(function () {});
      }
      setInterval(function () {
        left = left - 1;
        var bar = document.getElementById('bar');
        if (bar) bar.style.width = Math.max(0, left / 30 * 100) + '%';
        if (left <= 0) refresh();
      }, 1000);
      setInterval(refresh, 15000);  // also notices a visit starting or a check-in
    })();
  </script>
</body>
</html>
