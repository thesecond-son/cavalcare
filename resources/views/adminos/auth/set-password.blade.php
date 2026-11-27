@extends('adminos.auth.shell')

@section('title', 'Choose a password')

@section('card')
  <form method="POST" action="{{ $action }}" novalidate>
    @csrf
    <h2>Welcome, {{ explode(' ', $user->name)[0] }}</h2>
    <p class="muted" style="margin:0">Choose a password for <b>{{ $user->email }}</b>. Use at least 12 characters with upper and lower case letters and a number.</p>

    <div class="field">
      <label for="password">New password</label>
      <input id="password" name="password" type="password" autocomplete="new-password" required autofocus>
    </div>
    <div class="field">
      <label for="password_confirmation">Type it again</label>
      <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
    </div>

    @error('password') <div class="err" role="alert">{{ $message }}</div> @enderror

    <button class="btn primary btn-block" type="submit">Save password</button>
  </form>
@endsection
