@extends('adminos.auth.shell')

@section('title', 'Sign in')

@section('card')
  <form method="POST" action="{{ route('os.login.attempt') }}" novalidate>
    @csrf
    <h2>Sign in to Caval OS</h2>
    <p class="muted" style="margin:0">Use your CavalCare admin email and password. You'll confirm with your authenticator app next.</p>

    <div class="field">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required>
    </div>

    @error('email')
      <div class="err" role="alert">{{ $message }}</div>
    @enderror

    <button class="btn primary btn-block" type="submit">Continue</button>
  </form>
@endsection
