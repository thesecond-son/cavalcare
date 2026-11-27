@extends('adminos.auth.shell')

@section('title', 'Set up two-factor')

@section('card')
  <form method="POST" action="{{ route('os.2fa.confirm') }}" novalidate>
    @csrf
    <h2>Protect your account</h2>
    <p class="muted" style="margin:0 0 16px">Caval OS needs a code from an authenticator app every time you sign in. Google Authenticator, Microsoft Authenticator, 1Password or Authy all work.</p>

    <ol class="steps">
      <li>Open your authenticator app and add a new account.</li>
      <li>Scan this QR code.
        <div class="qr" aria-label="QR code for your authenticator app">{!! $qr !!}</div>
        <details>
          <summary>Can't scan it? Type this key instead</summary>
          <code class="secret">{{ $secret }}</code>
        </details>
      </li>
      <li>Enter the 6-digit code the app shows.</li>
    </ol>

    <div class="field">
      <label for="code">6-digit code</label>
      <input id="code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9 ]*" maxlength="7" class="code-input" required autofocus>
    </div>

    @error('code') <div class="err" role="alert">{{ $message }}</div> @enderror

    <button class="btn primary btn-block" type="submit">Turn on and sign in</button>
  </form>
@endsection
