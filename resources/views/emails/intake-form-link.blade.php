{{-- resources/views/emails/intake-form-link.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complete Your Care Details — CavalCare</title>
  <style>
    body { margin:0; padding:0; background:#f4f4f4; font-family:'Cabin','Helvetica Neue',Helvetica,Arial,sans-serif; }
    .wrapper { max-width:580px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
    .header { background:#1A3A2F; padding:36px 40px; text-align:center; }
    .header img { height:48px; width:auto; }
    .body { padding:40px; color:#1A3A2F; }
    h1 { font-size:1.5rem; font-weight:400; line-height:1.25; margin:0 0 16px; }
    p { font-size:.95rem; line-height:1.72; color:#3d6b57; margin:0 0 18px; }
    .highlight { color:#1A3A2F; font-weight:600; }
    .plan-box { background:#F0FFDB; border-radius:10px; padding:16px 20px; margin:24px 0; }
    .plan-box p { margin:0; font-size:.88rem; }
    .cta-wrap { text-align:center; margin:32px 0 24px; }
    .cta-btn {
      display:inline-block;
      background:#04A9A7; color:#fff !important;
      text-decoration:none; font-weight:700; font-size:1rem;
      padding:16px 36px; border-radius:10px;
      letter-spacing:.01em;
    }
    .cta-hint { font-size:.78rem; color:#3d6b57; margin-top:10px; }
    .link-fallback { font-size:.76rem; color:#3d6b57; word-break:break-all; }
    .link-fallback a { color:#04A9A7; }
    .divider { height:1px; background:#e8f4e8; margin:28px 0; }
    .footer { background:#F0FFDB; padding:24px 40px; font-size:.75rem; color:#3d6b57; text-align:center; line-height:1.65; }
    .footer a { color:#1A3A2F; font-weight:600; }
    .warning { background:#fff8f0; border-left:3px solid #FFCDAD; border-radius:0 8px 8px 0; padding:12px 16px; margin:20px 0; font-size:.84rem; color:#7a4016; }
  </style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <img src="{{ asset('images/caval-care-logo.png') }}" alt="CavalCare">
  </div>

  <div class="body">
    <h1>Hi {{ $firstName }},<br>your payment is confirmed ✓</h1>

    <p>
      Thank you for booking with CavalCare. We've received your payment and your care journey
      is officially underway.
    </p>

    <div class="plan-box">
      <p><span class="highlight">Plan:</span> {{ $planName }}</p>
      <p style="margin-top:6px;"><span class="highlight">Care Type:</span> {{ $careType }}</p>
    </div>

    <p>
      <strong style="color:#1A3A2F;">One last step</strong> — to assign you the right nurse,
      we need a few more details about you and your loved one.
      This takes about <strong style="color:#1A3A2F;">5 minutes</strong>.
    </p>

    <div class="cta-wrap">
      <a href="{{ $intakeUrl }}" class="cta-btn">Complete My Care Details →</a>
      <p class="cta-hint">Button not working? Copy the link below into your browser.</p>
    </div>

    <p class="link-fallback"><a href="{{ $intakeUrl }}">{{ $intakeUrl }}</a></p>

    <div class="warning">
      <strong>This link is private and unique to you.</strong>
      Please do not share it. It expires after your details have been submitted.
    </div>

    <div class="divider"></div>

    <p style="font-size:.88rem;">
      If you have any questions before filling in the form, reach us on WhatsApp at
      <a href="https://wa.me/{{ config('app.whatsapp_number') }}" style="color:#1A3A2F;font-weight:600;">
        +234 706 311 2134
      </a>.
    </p>
  </div>

  <div class="footer">
    © {{ date('Y') }} CavalCare. All rights reserved.<br>
    <a href="{{ url('/') }}">cavalcare.com</a> ·
    <a href="https://wa.me/{{ config('app.whatsapp_number') }}">WhatsApp Support</a>
  </div>

</div>
</body>
</html>