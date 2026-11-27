@include('emails.os.partials.shell-top', ['title' => 'Your Caval OS access'])
  <h1 style="font-family:Georgia,serif;font-weight:500;font-size:26px;margin:0 0 12px;color:#1A3A2F">Hi {{ $firstName }},</h1>
  <p style="margin:0 0 16px">You now have access to Caval OS, the CavalCare operations dashboard.</p>
  <p style="margin:0 0 24px">Choose your password to get started. The link works once and expires in 24 hours.</p>
  <p style="margin:0 0 24px">
    <a href="{{ $link }}" style="display:inline-block;background:#1A3A2F;color:#ffffff;text-decoration:none;font-weight:700;padding:14px 22px;border-radius:12px">Choose my password</a>
  </p>
  <p style="margin:0 0 8px;color:#4E6359;font-size:14px">After that you'll set up an authenticator app (such as Google Authenticator). Caval OS asks for its code each time you sign in.</p>
  <p style="margin:0;color:#4E6359;font-size:14px">Didn't expect this email? You can ignore it, nothing changes unless the link is used.</p>
@include('emails.os.partials.shell-bottom')
