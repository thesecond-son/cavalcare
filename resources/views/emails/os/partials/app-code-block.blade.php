{{-- Shared app code block: used in the payment email and the resend email. --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F0FFDB;border-radius:16px;margin:8px 0 20px">
  <tr><td style="padding:20px 22px">
    <p style="margin:0 0 6px;font-weight:700;color:#1A3A2F">Follow your care in the Cavalcare app</p>
    <p style="margin:0 0 12px;color:#4E6359;font-size:14px">Get the app, tap <b>"I already have an account"</b>, enter {{ $email ?? 'the email you booked with' }}, then this code. Your booking and care details will already be there.</p>
    <p style="margin:0 0 12px;font-family:Georgia,serif;font-size:30px;letter-spacing:6px;color:#1A3A2F"><b>{{ $code }}</b></p>
    <p style="margin:0;color:#4E6359;font-size:13px">Works once, for 30 days, and only with your email or phone number. Never share it.</p>
    @if(! empty($apps['ios']) || ! empty($apps['android']))
      <p style="margin:14px 0 0">
        @if(! empty($apps['ios']))<a href="{{ $apps['ios'] }}" style="color:#0A7C7A;font-weight:700;margin-right:16px">Download for iPhone</a>@endif
        @if(! empty($apps['android']))<a href="{{ $apps['android'] }}" style="color:#0A7C7A;font-weight:700">Download for Android</a>@endif
      </p>
    @endif
  </td></tr>
</table>
