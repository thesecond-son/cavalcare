@extends('adminos.auth.shell')

@section('title', 'Two-factor code')

@section('card')
  @php $useRecovery = $errors->has('recovery_code') || old('recovery_code'); @endphp
  <form method="POST" action="{{ route('os.2fa.verify') }}" novalidate>
    @csrf
    <h2>Enter your code</h2>
    <p class="muted" style="margin:0">Open your authenticator app and type the 6-digit code for Caval OS.</p>

    <div class="field" id="codeField" @if($useRecovery) hidden @endif>
      <label for="code">6-digit code</label>
      <input id="code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9 ]*" maxlength="7" class="code-input" @unless($useRecovery) autofocus @endunless>
    </div>

    <div class="field" id="recoveryField" @unless($useRecovery) hidden @endunless>
      <label for="recovery_code">Recovery code</label>
      <input id="recovery_code" name="recovery_code" value="{{ old('recovery_code') }}" autocomplete="off" placeholder="XXXXX-XXXXX" @if($useRecovery) autofocus @endif>
    </div>

    @error('code') <div class="err" role="alert">{{ $message }}</div> @enderror
    @error('recovery_code') <div class="err" role="alert">{{ $message }}</div> @enderror

    <button class="btn primary btn-block" type="submit">Sign in</button>

    <p style="margin:18px 0 0;text-align:center">
      <button type="button" class="link-btn" id="toggleRecovery">
        {{ $useRecovery ? 'Use my authenticator app instead' : 'Lost your phone? Use a recovery code' }}
      </button>
    </p>
  </form>
  <script>
    document.getElementById('toggleRecovery').addEventListener('click', function () {
      var code = document.getElementById('codeField'), rec = document.getElementById('recoveryField');
      var showRec = rec.hidden;
      rec.hidden = !showRec; code.hidden = showRec;
      (showRec ? rec : code).querySelector('input').focus();
      (showRec ? code : rec).querySelector('input').value = '';
      this.textContent = showRec ? 'Use my authenticator app instead' : 'Lost your phone? Use a recovery code';
    });
  </script>
@endsection
