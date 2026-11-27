@extends('adminos.auth.shell')

@section('title', 'Recovery codes')

@section('card')
  <h2>Save your recovery codes</h2>
  <p class="muted" style="margin:0 0 16px">If you lose your phone, each code below signs you in once. Store them somewhere safe, like a password manager. <b>This is the only time they are shown.</b></p>

  <ul class="recovery" id="codes">
    @foreach($codes as $code)
      <li><code>{{ $code }}</code></li>
    @endforeach
  </ul>

  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px">
    <button type="button" class="btn" id="copyCodes">Copy codes</button>
    <a class="btn primary" href="{{ route('os.dashboard') }}">I've saved them, continue</a>
  </div>
  <script>
    document.getElementById('copyCodes').addEventListener('click', function () {
      var text = Array.from(document.querySelectorAll('#codes code')).map(function (c) { return c.textContent; }).join('\n');
      navigator.clipboard.writeText(text).then(() => { this.textContent = 'Copied'; });
    });
  </script>
@endsection
