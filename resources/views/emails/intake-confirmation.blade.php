{{-- resources/views/emails/intake-confirmation.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Care Details Have Been Received — CavalCare</title>

  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f4f4f4;
      font-family: 'Cabin', 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }

    .wrapper {
      max-width: 580px;
      margin: 32px auto;
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,.08);
    }

    .header {
      background: #1A3A2F;
      padding: 36px 40px;
      text-align: center;
    }

    .header img {
      height: 48px;
      width: auto;
    }

    .body {
      padding: 40px;
      color: #1A3A2F;
    }

    h1 {
      font-size: 1.5rem;
      font-weight: 400;
      line-height: 1.3;
      margin: 0 0 16px;
      color: #1A3A2F;
    }

    p {
      font-size: .95rem;
      line-height: 1.72;
      color: #3d6b57;
      margin: 0 0 18px;
    }

    .highlight {
      color: #1A3A2F;
      font-weight: 600;
    }

    /* Success banner */
    .success-box {
      background: #F0FFDB;
      border-radius: 12px;
      padding: 20px;
      margin: 24px 0;
      text-align: center;
    }

    .success-icon {
      width: 52px;
      height: 52px;
      line-height: 52px;
      margin: 0 auto 12px;
      border-radius: 50%;
      background: #04A9A7;
      color: #fff;
      font-size: 24px;
      font-weight: 700;
    }

    .success-title {
      color: #1A3A2F;
      font-size: 1rem;
      font-weight: 700;
      margin: 0 0 5px;
    }

    .success-text {
      color: #3d6b57;
      font-size: .82rem;
      line-height: 1.55;
      margin: 0;
    }

    /* Booking summary */
    .details-box {
      background: #f8fbf6;
      border: 1px solid #e5eee2;
      border-radius: 12px;
      padding: 20px;
      margin: 24px 0;
    }

    .details-title {
      color: #1A3A2F;
      font-size: .85rem;
      font-weight: 700;
      margin: 0 0 14px;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .detail-row {
      padding: 9px 0;
      border-bottom: 1px solid #e8f0e6;
      font-size: .88rem;
      line-height: 1.5;
    }

    .detail-row:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .detail-label {
      color: #6a8978;
    }

    .detail-value {
      color: #1A3A2F;
      font-weight: 600;
    }

    /* Next steps */
    .next-steps {
      margin: 28px 0;
    }

    .next-step {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 14px;
    }

    .step-number {
      width: 26px;
      height: 26px;
      min-width: 26px;
      border-radius: 50%;
      background: #1A3A2F;
      color: #fff;
      text-align: center;
      line-height: 26px;
      font-size: .72rem;
      font-weight: 700;
    }

    .step-text {
      color: #3d6b57;
      font-size: .88rem;
      line-height: 1.6;
      margin: 2px 0 0;
    }

    /* Notice */
    .notice {
      background: #fff8f0;
      border-left: 3px solid #FFCDAD;
      border-radius: 0 8px 8px 0;
      padding: 12px 16px;
      margin: 24px 0;
      font-size: .84rem;
      line-height: 1.6;
      color: #7a4016;
    }

    .divider {
      height: 1px;
      background: #e8f4e8;
      margin: 28px 0;
    }

    .footer {
      background: #F0FFDB;
      padding: 24px 40px;
      font-size: .75rem;
      color: #3d6b57;
      text-align: center;
      line-height: 1.65;
    }

    .footer a {
      color: #1A3A2F;
      font-weight: 600;
    }

    a {
      color: #04A9A7;
    }

    @media only screen and (max-width: 620px) {
      .wrapper {
        margin: 0;
        border-radius: 0;
      }

      .header {
        padding: 30px 24px;
      }

      .body {
        padding: 32px 24px;
      }

      .footer {
        padding: 22px 24px;
      }
    }
  </style>
</head>

<body>

<div class="wrapper">

  {{-- HEADER --}}
  <div class="header">
    <img
      src="{{ asset('images/caval-care-logo.png') }}"
      alt="CavalCare"
    >
  </div>

  {{-- BODY --}}
  <div class="body">

    <h1>
      Hi {{ $firstName }},<br>
      we've received your care details ✓
    </h1>

    <p>
      Thank you for completing your care information with CavalCare.
      We've successfully received the details you provided about yourself
      and your loved one.
    </p>

    {{-- SUCCESS MESSAGE --}}
    <div class="success-box">
      <div class="success-icon">✓</div>

      <p class="success-title">
        Your intake form has been submitted
      </p>

      <p class="success-text">
        Your information is now with our care team for review.
      </p>
    </div>

    {{-- BOOKING DETAILS --}}
    <div class="details-box">

      <p class="details-title">
        Your Care Booking
      </p>

      <div class="detail-row">
        <span class="detail-label">Care Plan:</span><br>
        <span class="detail-value">
          {{ $booking->carePlan->name ?? '—' }}
        </span>
      </div>

      <div class="detail-row">
        <span class="detail-label">Care Type:</span><br>
        <span class="detail-value">
          {{ $booking->careType->name ?? '—' }}
        </span>
      </div>

      @if($startDate)
      <div class="detail-row">
        <span class="detail-label">Requested Start Date:</span><br>
        <span class="detail-value">
          {{ $startDate }}
        </span>
      </div>
      @endif

      @if(!empty($booking->patient_details['full_name']))
      <div class="detail-row">
        <span class="detail-label">Person Receiving Care:</span><br>
        <span class="detail-value">
          {{ $booking->patient_details['full_name'] }}
        </span>
      </div>
      @endif

    </div>

    <p>
      <strong style="color:#1A3A2F;">What happens next?</strong>
    </p>

    {{-- NEXT STEPS --}}
    <div class="next-steps">

      <div class="next-step">
        <div class="step-number">1</div>
        <p class="step-text">
          Our care team will review the information you've submitted.
        </p>
      </div>

      <div class="next-step">
        <div class="step-number">2</div>
        <p class="step-text">
          We'll assess your loved one's needs and identify a suitable
          nurse for the care required.
        </p>
      </div>

      <div class="next-step">
        <div class="step-number">3</div>
        <p class="step-text">
          We'll contact you within <strong style="color:#1A3A2F;">24–48 hours</strong>
          to discuss the nurse match and confirm the next steps.
        </p>
      </div>

    </div>

    {{-- IMPORTANT NOTICE --}}
    <div class="notice">
      <strong>Your information is private.</strong>
      The care details you've provided are securely stored and are only
      accessible to the CavalCare team and the nurse assigned to your loved one.
    </div>

    <div class="divider"></div>

    <p style="font-size:.88rem;">
      If you need to make a correction or have any questions about your
      booking, please contact us on WhatsApp at
      <a
        href="https://wa.me/{{ config('app.whatsapp_number') }}"
        style="color:#1A3A2F;font-weight:600;"
      >
        +234 706 311 2134
      </a>.
    </p>

    <p style="font-size:.88rem;margin-bottom:0;">
      Thank you for trusting CavalCare with the care of your loved one.
    </p>

  </div>

  {{-- FOOTER --}}
  <div class="footer">
    © {{ date('Y') }} CavalCare. All rights reserved.<br>

    <a href="{{ url('/') }}">cavalcare.com</a>
    ·
    <a href="https://wa.me/{{ config('app.whatsapp_number') }}">
      WhatsApp Support
    </a>
  </div>

</div>

</body>
</html>