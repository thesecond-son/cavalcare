@extends('layouts.app')

@section('title', 'Care Details Already Submitted — CavalCare')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700&family=Cabin:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
  --cream:#F0FFDB;
  --yellow:#04A9A7;
  --ocean:#1A3A2F;
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

@keyframes iconPop{
  0%{
    opacity:0;
    transform:scale(.6);
  }
  70%{
    transform:scale(1.08);
  }
  100%{
    opacity:1;
    transform:scale(1);
  }
}

@keyframes fadeUp{
  from{
    opacity:0;
    transform:translateY(20px);
  }
  to{
    opacity:1;
    transform:none;
  }
}

.intake-page{
  min-height:100svh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:40px 20px;
}

.intake-card{
  background:#fff;
  border-radius:24px;
  border:1px solid rgba(26,58,47,.08);
  box-shadow:0 16px 56px rgba(26,58,47,.1);
  max-width:520px;
  width:100%;
  padding:52px 40px;
  text-align:center;
  animation:fadeUp .6s var(--out) both;
}

@media(max-width:479px){
  .intake-card{
    padding:36px 24px;
  }
}

.intake-icon{
  width:76px;
  height:76px;
  border-radius:50%;
  background:rgba(4,169,167,.12);
  color:var(--yellow);
  display:flex;
  align-items:center;
  justify-content:center;
  margin:0 auto 24px;
  animation:iconPop .55s var(--spring) both;
}

.intake-icon i{
  font-size:34px;
}

.intake-title{
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.6rem,5vw,2.2rem);
  font-weight:400;
  line-height:1.18;
  margin-bottom:14px;
}

.intake-text{
  font-size:.96rem;
  color:var(--muted);
  line-height:1.72;
  max-width:390px;
  margin:0 auto 28px;
}

.booking-box{
  background:var(--cream);
  border-radius:12px;
  padding:16px 20px;
  margin-bottom:28px;
  text-align:left;
}

.booking-row{
  display:flex;
  justify-content:space-between;
  gap:20px;
  font-size:.88rem;
  line-height:1.5;
}

.booking-row + .booking-row{
  margin-top:8px;
}

.booking-label{
  color:var(--muted);
}

.booking-value{
  color:var(--ocean);
  font-weight:600;
  text-align:right;
}

.info-box{
  background:#f8fbf6;
  border:1px solid rgba(26,58,47,.08);
  border-radius:12px;
  padding:16px 18px;
  margin-bottom:28px;
  text-align:left;
}

.info-box p{
  font-size:.86rem;
  color:var(--muted);
  line-height:1.65;
}

.info-box strong{
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

.reference{
  margin-top:22px;
  font-size:.76rem;
  color:var(--muted);
}

.reference strong{
  color:var(--ocean);
}
</style>
@endpush

@section('content')

<div class="intake-page">

  <div class="intake-card">

    <div class="intake-icon">
      <i class="bi bi-check2-circle"></i>
    </div>

    <h1 class="intake-title">
      Your care details<br>
      have already been submitted.
    </h1>

    <p class="intake-text">
      We've already received the care information for this booking.
      There's no need to complete the form again.
    </p>

    <div class="booking-box">

      <div class="booking-row">
        <span class="booking-label">Care Plan</span>

        <span class="booking-value">
          {{ $booking->carePlan?->name ?? '—' }}
        </span>
      </div>

      <div class="booking-row">
        <span class="booking-label">Care Type</span>

        <span class="booking-value">
          {{ $booking->careType?->name ?? '—' }}
        </span>
      </div>

      <div class="booking-row">
        <span class="booking-label">Status</span>

        <span class="booking-value">
          Details Received
        </span>
      </div>

    </div>

    <div class="info-box">

      <p>
        <strong>What happens next?</strong><br>

        Our care team will review the information you've provided
        and contact you regarding the next steps for arranging care.
      </p>

    </div>

    <a
      href="https://wa.me/{{ config('app.whatsapp_number') }}"
      class="conf-wa"
    >
      <i class="bi bi-whatsapp"></i>
      Questions? Message us on WhatsApp
    </a>

    <div class="reference">
      Booking reference:
      <strong>{{ $booking->reference }}</strong>
    </div>

  </div>

</div>

@endsection