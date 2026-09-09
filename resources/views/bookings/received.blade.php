{{-- resources/views/bookings/received.blade.php --}}
@extends('layouts.app')

@section('title', 'Booking Received — CavalCare')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700;800&family=Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<meta name="robots" content="noindex, nofollow">

{{--
  This page needs its own fbq init: the base pixel snippet only loads on
  bookings.create.blade.php now, and a user can land here directly (email
  link, page refresh, new tab) without that page having run in this
  session. No PageView fires here on init — only Purchase below — since
  PageView is scoped to the booking-start page by design.

  Purchase only fires here because this page only ever renders after
  BookingController@received confirms status PAID -- i.e. only after
  Paystack verification, never on button click or modal open.

  eventID matches the server-side CAPI call from BookingController@store,
  so Meta deduplicates the two into a single conversion.
--}}
@php($fbPixelId = config('services.meta.pixel_id'))
@if($fbPixelId)
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');

fbq('init', '{{ $fbPixelId }}');

fbq('track', 'Purchase', {
  content_name: @json($preBooking->carePlan->name ?? 'CavalCare Booking'),
  content_type: 'product',
  currency:     'NGN',
  value:        {{ $preBooking->amount_paid ? $preBooking->amount_paid / 100 : 0 }}
}, {
  eventID: '{{ $preBooking->reference }}'
});
</script>
@endif

<style>
:root{
  --cream:#F0FFDB; --yellow:#04A9A7; --ocean:#1A3A2F;
  --indigo:#04A9A7; --peach:#FFCDAD;
  --muted:#3d6b57;
  --r-md:16px; --r-lg:24px; --r-xl:36px;
  --spring:cubic-bezier(.34,1.56,.64,1);
  --out:cubic-bezier(0,0,.2,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{
  font-family:'Cabin',system-ui,sans-serif;
  background:var(--cream);color:var(--ocean);
  min-height:100svh;
  -webkit-font-smoothing:antialiased;
}

@keyframes fadeUp    {from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:none}}
@keyframes checkPop  {0%{transform:scale(0) rotate(-10deg)}70%{transform:scale(1.15) rotate(3deg)}100%{transform:scale(1) rotate(0)}}
@keyframes shimmerPill{0%{background-position:-300px 0}100%{background-position:300px 0}}

.received-page{
  min-height:100svh;
  display:flex; align-items:center; justify-content:center;
  padding:80px 20px;
  position:relative; overflow:hidden;
}

/* background glows */
.received-page::before{
  content:''; position:absolute;
  top:-15%; right:-10%;
  width:min(500px,110vw); height:min(500px,110vw);
  border-radius:50%;
  background:radial-gradient(circle,rgba(4,169,167,.18) 0%,transparent 65%);
  pointer-events:none;
}
.received-page::after{
  content:''; position:absolute;
  bottom:-12%; left:-8%;
  width:min(360px,80vw); height:min(360px,80vw);
  border-radius:50%;
  background:radial-gradient(circle,rgba(255,205,173,.22) 0%,transparent 65%);
  pointer-events:none;
}

.received-card{
  background:#fff;
  border-radius:var(--r-xl);
  border:1px solid rgba(26,58,47,.08);
  box-shadow:0 32px 80px rgba(26,58,47,.12);
  padding:52px 44px;
  max-width:600px; width:100%;
  position:relative; z-index:1;
  animation:fadeUp .7s .1s var(--out) both;
}
@media(max-width:479px){.received-card{padding:36px 24px;}}

/* ── check icon ── */
.check-wrap{
  width:72px; height:72px; border-radius:50%;
  background:rgba(4,169,167,.12);
  display:flex; align-items:center; justify-content:center;
  margin:0 auto 24px;
  animation:checkPop .6s .3s var(--spring) both;
}
.check-wrap i{font-size:32px;color:var(--yellow);}

/* ── heading ── */
.received-pill{
  display:inline-flex; align-items:center; gap:6px;
  font-size:.68rem; font-weight:700;
  letter-spacing:.08em; text-transform:uppercase;
  padding:4px 12px; border-radius:100px;
  background:rgba(26,58,47,.08); color:var(--ocean);
  margin-bottom:14px;
  position:relative; overflow:hidden;
}
.received-pill::after{
  content:''; position:absolute; inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.3) 50%,transparent);
  background-size:300px 100%;
  animation:shimmerPill 3.5s ease-in-out infinite;
  border-radius:inherit; pointer-events:none;
}
.received-pill-dot{width:5px;height:5px;border-radius:50%;background:currentColor;}

.received-title{
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.7rem,5vw,2.4rem);
  font-weight:400; line-height:1.1;
  letter-spacing:-.03em; color:var(--ocean);
  margin-bottom:10px;
}
.received-sub{
  font-size:.95rem; color:var(--muted);
  line-height:1.72; max-width:440px; margin:0 auto 28px;
}

/* ── booking details box ── */
.booking-details{
  background:var(--cream);
  border-radius:var(--r-lg);
  border:1px solid rgba(26,58,47,.08);
  padding:22px 24px;
  margin-bottom:28px;
  display:flex; flex-direction:column; gap:12px;
}
.bd-row{
  display:flex; align-items:center; gap:12px;
  font-size:.88rem;
}
.bd-ico{
  width:32px; height:32px; flex-shrink:0;
  border-radius:9px; background:rgba(26,58,47,.07);
  display:flex; align-items:center; justify-content:center;
}
.bd-ico i{font-size:14px; color:var(--ocean);}
.bd-label{font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); margin-bottom:1px;}
.bd-value{font-size:.9rem; font-weight:600; color:var(--ocean);}
.bd-divider{height:1px; background:rgba(26,58,47,.07);}

/* ── price highlight ── */
.price-highlight{
  display:flex; align-items:center; justify-content:space-between;
  background:var(--ocean);
  border-radius:var(--r-md);
  padding:16px 20px;
  margin-bottom:28px;
}
.ph-label{font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:rgba(255,255,255,.55);}
.ph-amount{
  font-family:'Nanum Myeongjo',serif;
  font-size:1.6rem; font-weight:400;
  color:var(--yellow); line-height:1;
}
.ph-cycle{font-size:.72rem; color:rgba(255,255,255,.45); margin-top:2px;}

/* ── next steps ── */
.next-steps-title{
  font-size:.7rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.08em;
  color:var(--muted); margin-bottom:14px;
}
.next-steps{display:flex; flex-direction:column; gap:10px; margin-bottom:28px;}
.ns-item{
  display:flex; align-items:flex-start; gap:12px;
  font-size:.87rem; color:var(--muted); line-height:1.55;
}
.ns-num{
  width:24px; height:24px; flex-shrink:0; border-radius:50%;
  background:var(--ocean); color:#fff;
  display:flex; align-items:center; justify-content:center;
  font-size:.7rem; font-weight:700; margin-top:1px;
}

/* ── whatsapp cta ── */
.wa-btn{
  display:flex; align-items:center; justify-content:center; gap:10px;
  width:100%; padding:15px 24px;
  background:#25D366; color:#fff;
  border:none; border-radius:var(--r-md);
  font-family:'Cabin',sans-serif; font-size:.97rem; font-weight:700;
  text-decoration:none; cursor:pointer;
  box-shadow:0 4px 18px rgba(37,211,102,.3);
  transition:transform .22s var(--spring), box-shadow .22s, opacity .2s;
}
.wa-btn:hover{transform:translateY(-2px); box-shadow:0 8px 28px rgba(37,211,102,.45);}
.wa-btn i{font-size:20px;}
.wa-hint{
  text-align:center; font-size:.75rem; color:var(--muted);
  margin-top:10px; line-height:1.5;
}
</style>
@endpush

@section('content')

<div class="received-page">
  <div class="received-card">

    {{-- check icon --}}
    <div style="text-align:center;">
      <div class="check-wrap">
        <i class="bi bi-check-lg"></i>
      </div>

      <div class="received-pill">
        <span class="received-pill-dot"></span>
        Payment Confirmed
      </div>

      <h1 class="received-title">Booking Received!</h1>
      <p class="received-sub">
        Thank you for choosing CavalCare. Your payment has been confirmed and your booking is now in our system.
        Check your email for your intake link.
      </p>
    </div>

    {{-- Booking details --}}
    <div class="booking-details">
      <div class="bd-row">
        <div class="bd-ico"><i class="bi bi-receipt"></i></div>
        <div>
          <div class="bd-label">Reference</div>
          <div class="bd-value">{{ $preBooking->reference }}</div>
        </div>
      </div>

      <div class="bd-divider"></div>

      <div class="bd-row">
        <div class="bd-ico"><i class="bi bi-heart-pulse"></i></div>
        <div>
          <div class="bd-label">Care Type</div>
          <div class="bd-value">{{ $preBooking->careType->name ?? '—' }}</div>
        </div>
      </div>

      <div class="bd-row">
        <div class="bd-ico"><i class="bi bi-clipboard-pulse"></i></div>
        <div>
          <div class="bd-label">Plan Selected</div>
          <div class="bd-value">{{ $preBooking->carePlan->name ?? '—' }}</div>
        </div>
      </div>

      <div class="bd-divider"></div>

      <div class="bd-row">
        <div class="bd-ico"><i class="bi bi-person"></i></div>
        <div>
          <div class="bd-label">Booked By</div>
          <div class="bd-value">{{ $preBooking->booker_details['name'] ?? '—' }}</div>
        </div>
      </div>

      <div class="bd-row">
        <div class="bd-ico"><i class="bi bi-envelope"></i></div>
        <div>
          <div class="bd-label">Email</div>
          <div class="bd-value">{{ $preBooking->booker_details['email'] ?? '—' }}</div>
        </div>
      </div>
    </div>

    {{-- Price paid --}}
    @if($preBooking->amount_paid)
    <div class="price-highlight">
      <div>
        <div class="ph-label">Amount Paid</div>
        <div class="ph-cycle">{{ $preBooking->carePlan->billing_label ?? '' }}</div>
      </div>
      <div class="ph-amount">₦{{ number_format($preBooking->amount_paid / 100) }}</div>
    </div>
    @endif

    {{-- Next steps --}}
    <div class="next-steps-title">What happens next</div>
    <div class="next-steps">
      <div class="ns-item">
        <div class="ns-num">1</div>
        <span>Check your email — we've sent you a private link to complete your loved one's care details.</span>
      </div>
      <div class="ns-item">
        <div class="ns-num">2</div>
        <span>Our team reviews your information and matches you with the most suitable nurse.</span>
      </div>
      <div class="ns-item">
        <div class="ns-num">3</div>
        <span>You'll receive a call or WhatsApp message within 24–48 hours to confirm your start date.</span>
      </div>
    </div>

    {{-- WhatsApp CTA --}}
    <a href="https://wa.me/{{ config('app.whatsapp_number') }}?text={{ urlencode('Hello CavalCare, I just completed my booking. My reference is: ' . $preBooking->reference) }}"
       class="wa-btn" target="_blank" rel="noopener">
      <i class="bi bi-whatsapp"></i>
      Message Us on WhatsApp
    </a>
    <p class="wa-hint">Have a question? We're available 8am–8pm daily.</p>

  </div>
</div>

@endsection