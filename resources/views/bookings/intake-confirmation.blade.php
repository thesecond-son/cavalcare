@extends('layouts.app')

@section('title', 'Care Details Submitted — CavalCare')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700&family=Cabin:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
  --cream:#F0FFDB;
  --yellow:#04A9A7;
  --ocean:#1A3A2F;
  --indigo:#04A9A7;
  --peach:#FFCDAD;
  --muted:#3d6b57;
  --spring:cubic-bezier(.34,1.56,.64,1);
  --out:cubic-bezier(0,0,.2,1);
}

*,*::before,*::after{
  box-sizing:border-box;
  margin:0;
  padding:0;
}

body{
  font-family:'Cabin',system-ui,sans-serif;
  background:var(--cream);
  color:var(--ocean);
  -webkit-font-smoothing:antialiased;
}

@keyframes checkBounce{
  0%{transform:scale(0) rotate(-10deg)}
  70%{transform:scale(1.2) rotate(4deg)}
  100%{transform:scale(1) rotate(0)}
}

@keyframes fadeUp{
  from{
    opacity:0;
    transform:translateY(20px)
  }
  to{
    opacity:1;
    transform:none
  }
}

.conf-page{
  min-height:100svh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:40px 20px;
}

.conf-card{
  background:#fff;
  border-radius:24px;
  border:1px solid rgba(26,58,47,.08);
  box-shadow:0 16px 56px rgba(26,58,47,.1);
  max-width:540px;
  width:100%;
  padding:52px 40px;
  text-align:center;
  animation:fadeUp .6s var(--out) both;
}

@media(max-width:479px){
  .conf-card{
    padding:36px 24px;
  }
}

.conf-ico{
  width:76px;
  height:76px;
  border-radius:50%;
  background:var(--ocean);
  display:flex;
  align-items:center;
  justify-content:center;
  margin:0 auto 24px;
  animation:checkBounce .6s .1s var(--spring) both;
}

.conf-ico i{
  font-size:32px;
  color:var(--yellow);
}

.conf-h1{
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.6rem,5vw,2.2rem);
  font-weight:400;
  line-height:1.15;
  margin-bottom:12px;
}

.conf-p{
  font-size:.96rem;
  color:var(--muted);
  line-height:1.72;
  max-width:390px;
  margin:0 auto 28px;
}

.conf-email-box{
  background:var(--cream);
  border-radius:10px;
  padding:14px 18px;
  margin-bottom:20px;
  font-size:.9rem;
  color:var(--ocean);
  overflow-wrap:anywhere;
}

.conf-email-box strong{
  display:block;
  font-size:1rem;
  margin-top:3px;
}

/* ─────────────────────────────
   BOOKING SUMMARY
───────────────────────────── */

.booking-summary{
  border:1px solid rgba(26,58,47,.09);
  border-radius:14px;
  overflow:hidden;
  margin-bottom:28px;
  text-align:left;
  background:#fff;
}

.booking-summary-title{
  background:var(--ocean);
  color:#fff;
  padding:13px 18px;
  font-size:.78rem;
  font-weight:700;
  letter-spacing:.08em;
  text-transform:uppercase;
}

.booking-row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:20px;
  padding:13px 18px;
  border-bottom:1px solid rgba(26,58,47,.07);
}

.booking-row:last-child{
  border-bottom:0;
}

.booking-label{
  color:var(--muted);
  font-size:.84rem;
}

.booking-value{
  color:var(--ocean);
  font-size:.88rem;
  font-weight:600;
  text-align:right;
}

.booking-reference{
  font-size:1rem;
  letter-spacing:.04em;
}

.conf-steps{
  list-style:none;
  text-align:left;
  display:flex;
  flex-direction:column;
  gap:12px;
  margin-bottom:32px;
}

.cs{
  display:flex;
  gap:12px;
  align-items:flex-start;
}

.cs-num{
  width:24px;
  height:24px;
  flex-shrink:0;
  border-radius:50%;
  background:rgba(26,58,47,.08);
  color:var(--ocean);
  font-size:.68rem;
  font-weight:700;
  display:flex;
  align-items:center;
  justify-content:center;
  margin-top:2px;
}

.cs-text{
  font-size:.88rem;
  color:var(--muted);
  line-height:1.55;
}

.cs-text strong{
  color:var(--ocean);
}

.conf-wa{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:9px;
  background:var(--ocean);
  color:#fff;
  text-decoration:none;
  font-weight:600;
  font-size:.9rem;
  padding:13px 24px;
  border-radius:10px;
  transition:background .18s,transform .22s var(--spring);
}

.conf-wa:hover{
  background:#0f5c3d;
  transform:translateY(-2px);
}
</style>
@endpush

@section('content')

<div class="conf-page">

  <div class="conf-card">

    {{-- Success Icon --}}
    <div class="conf-ico">
      <i class="bi bi-check-lg"></i>
    </div>

    {{-- Heading --}}
    <h1 class="conf-h1">
      Care details received.<br>
      You're all set.
    </h1>

    <p class="conf-p">
      Thank you for completing your care details.
      We've received your information and our care team will now review it
      so we can prepare the right care arrangement for you and your loved one.
    </p>


    {{-- Booking Summary --}}
    <div class="booking-summary">

      <div class="booking-summary-title">
        Booking Summary
      </div>

      {{-- Booking Reference --}}
      <div class="booking-row">
        <span class="booking-label">
          Booking reference
        </span>

        <span class="booking-value booking-reference">
        {{ $booking->preBooking?->reference ?? '—' }}
    </span>
      </div>

      {{-- Care Plan --}}
      <div class="booking-row">
        <span class="booking-label">
          Care plan
        </span>

        <span class="booking-value">
          {{ $booking->carePlan?->name ?? '—' }}
        </span>
      </div>

      {{-- Care Type --}}
      <div class="booking-row">
        <span class="booking-label">
          Care type
        </span>

        <span class="booking-value">
          {{ $booking->careType?->name ?? '—' }}
        </span>
      </div>

      {{-- Start Date --}}
      <div class="booking-row">
        <span class="booking-label">
          Start date
        </span>

        <span class="booking-value">
          @php
              $startDate = data_get($booking->booking_details, 'start_date');
          @endphp

          {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d F Y') : '—' }}
        </span>
      </div>

    </div>


    {{-- Email --}}
    <div class="conf-email-box">

      Your booking email:

      <strong>
        {{ $booking->booker_details['email'] ?? 'your email address' }}
      </strong>

    </div>


    {{-- Next Steps --}}
    <ul class="conf-steps">

      <li class="cs">
        <span class="cs-num">1</span>

        <span class="cs-text">
          <strong>We've received your details.</strong>
          Your care information has been securely submitted to our team.
        </span>
      </li>

      <li class="cs">
        <span class="cs-num">2</span>

        <span class="cs-text">
          Our team will <strong>review your care requirements</strong>
          and determine the appropriate care arrangement.
        </span>
      </li>

      <li class="cs">
        <span class="cs-num">3</span>

        <span class="cs-text">
          We'll <strong>contact you within 24–48 hours</strong>
          with the next steps regarding your care.
        </span>
      </li>

    </ul>


    {{-- WhatsApp --}}
    <a
      href="https://wa.me/{{ config('app.whatsapp_number') }}"
      class="conf-wa"
    >
      <i class="bi bi-whatsapp"></i>
      Questions? Message us on WhatsApp
    </a>

  </div>

</div>

@endsection