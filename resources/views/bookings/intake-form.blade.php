{{-- resources/views/bookings/intake.blade.php --}}
{{--
  ROUTE:   GET  /intake/{token}    → BookingIntakeController@show
           POST /intake/{token}    → BookingIntakeController@submit
  $booking is a PreBooking resolved by token.
--}}
@extends('layouts.intake')

@section('title', 'Complete Your Care Details — CavalCare')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700;800&family=Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<meta name="robots" content="noindex, nofollow">

<style>
/* ═══════════════════════════════════════════════
   TOKENS
═══════════════════════════════════════════════ */
:root{
  --cream:#F0FFDB; --yellow:#04A9A7; --ocean:#1A3A2F;
  --indigo:#04A9A7; --peach:#FFCDAD; --cream-d:#d4f5b8;
  --ocean-h:#0f5c3d; --ocean-soft:#e6f9f9;
  --text:#1A3A2F; --muted:#3d6b57; --white:#fff;
  --r-xs:6px; --r-sm:10px; --r-md:16px; --r-lg:24px; --r-xl:36px;
  --spring:cubic-bezier(.34,1.56,.64,1);
  --out:cubic-bezier(0,0,.2,1);
  --smooth:cubic-bezier(.4,0,.2,1);
  --danger:#c0392b; --danger-bg:rgba(192,57,43,.07);
  --success:#04A9A7; --success-bg:rgba(4,169,167,.07);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%;}
body{
  font-family:'Cabin',system-ui,sans-serif;
  font-size:16px;line-height:1.65;
  color:var(--text);background:#fff;
  -webkit-font-smoothing:antialiased;
  overflow-x:hidden;
}
a{color:inherit;}
:focus-visible{
  outline:2px solid var(--yellow);
  outline-offset:3px;
  border-radius:4px;
  box-shadow:0 0 0 4px rgba(4,169,167,.15);
}

/* Prevent focus artifacts from appearing outside the intended element */
.intake-page,
.intake-page *{
  outline-offset:2px;
}
/* ── keyframes ── */
@keyframes fadeUp  {from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:none}}
@keyframes fadeIn  {from{opacity:0}to{opacity:1}}
@keyframes ripple  {to{transform:scale(2.4);opacity:0}}
@keyframes pulse   {0%,100%{box-shadow:0 0 0 0 rgba(26,58,47,.2)}60%{box-shadow:0 0 0 10px rgba(26,58,47,0)}}
@keyframes shimmerPill{0%{background-position:-300px 0}100%{background-position:300px 0}}
@keyframes spin    {to{transform:rotate(360deg)}}
@keyframes checkBounce{0%{transform:scale(0) rotate(-10deg)}70%{transform:scale(1.2) rotate(4deg)}100%{transform:scale(1) rotate(0)}}
@keyframes progressFill{from{width:0}to{width:var(--prog-w)}}

/* ── scroll reveal ── */
.reveal{opacity:0;transform:translateY(22px);transition:opacity .65s var(--out),transform .65s var(--out);}
.reveal.is-visible{opacity:1;transform:none;}

/* ── layout ── */
.wrap{max-width:860px;margin:0 auto;padding:0 20px;}
@media(min-width:768px){.wrap{padding:0 32px;}}
@media(max-width:767px){.wrap{padding:0 16px;}}

/* ═══════════════════════════════════════════════
   NAV
═══════════════════════════════════════════════ */
.intake-nav{
  position:fixed;top:0;left:0;right:0;z-index:200;
  padding:0 20px;
  transition:background .38s var(--smooth),box-shadow .38s var(--smooth);
}
.intake-nav.scrolled{
  background:rgba(240,255,219,.93);
  backdrop-filter:blur(22px);-webkit-backdrop-filter:blur(22px);
  box-shadow:0 1px 0 rgba(26,58,47,.08);
}
.intake-nav-inner{
  max-width:860px;margin:0 auto;
  height:60px;display:flex;align-items:center;justify-content:space-between;
}
.intake-nav-logo{display:flex;align-items:center;text-decoration:none;}
.intake-nav-logo img{height:36px;width:auto;object-fit:contain;}
.intake-nav-secure{
  display:flex;align-items:center;gap:6px;
  font-size:.72rem;font-weight:600;color:var(--muted);
}
.intake-nav-secure i{font-size:14px;color:var(--ocean);}

/* ═══════════════════════════════════════════════
   PAGE WRAPPER
═══════════════════════════════════════════════ */
.intake-page{
  min-height:100svh;
  background:var(--cream);
  padding-top:88px;
  padding-bottom: 110px;
  position:relative;
  overflow:hidden;
}

:focus-visible{
  outline:2px solid var(--yellow);
  outline-offset:3px;
  border-radius:4px;
  box-shadow:0 0 0 4px rgba(4,169,167,.15);
}

/* Prevent focus artifacts from appearing outside the intended element */
.intake-page,
.intake-page *{
  outline-offset:2px;
}

.intake-glow{position:absolute;border-radius:50%;pointer-events:none;}
.ig1{width:min(500px,110vw);height:min(500px,110vw);
  background:radial-gradient(circle,rgba(4,169,167,.18) 0%,transparent 65%);
  top:-15%;right:-12%;}
.ig2{width:min(360px,80vw);height:min(360px,80vw);
  background:radial-gradient(circle,rgba(255,205,173,.22) 0%,transparent 65%);
  bottom:-12%;left:-8%;}

/* ═══════════════════════════════════════════════
   PAGE HERO
═══════════════════════════════════════════════ */
.intake-hero{padding:44px 0 36px;text-align:center;}
@media(max-width:767px){.intake-hero{padding:32px 0 24px;}}

.intake-hero-pill{
  display:inline-flex;align-items:center;gap:6px;
  font-size:.7rem;font-weight:600;
  letter-spacing:.07em;text-transform:uppercase;
  padding:5px 14px;border-radius:100px;
  background:rgba(26,58,47,.1);color:var(--ocean);
  margin-bottom:14px;
  position:relative;overflow:hidden;
}
.intake-hero-pill::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.3) 50%,transparent);
  background-size:300px 100%;
  animation:shimmerPill 3.5s ease-in-out infinite;
  border-radius:inherit;pointer-events:none;
}
.intake-hero-pill-dot{width:5px;height:5px;border-radius:50%;background:currentColor;}

.intake-title{
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.8rem,6vw,2.8rem);
  font-weight:400;line-height:1.1;
  letter-spacing:-.035em;color:var(--ocean);
  margin-bottom:12px;
}
.intake-title em{font-style:italic;color:var(--indigo);}
.intake-subtitle{
  font-size:.97rem;color:var(--muted);
  max-width:500px;margin:0 auto;line-height:1.72;
}

/* ── booking ribbon ── */
.booking-ribbon{
  display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:8px 20px;
  margin:20px auto 0;
  padding:14px 20px;
  background:#fff;border-radius:var(--r-lg);
  border:1px solid rgba(26,58,47,.08);
  max-width:620px;
  font-size:.82rem;color:var(--muted);
}
.br-item{display:flex;align-items:center;gap:7px;}
.br-item i{font-size:14px;color:var(--ocean);}
.br-item strong{color:var(--ocean);font-weight:600;}
.br-sep{width:1px;height:16px;background:rgba(26,58,47,.12);}
@media(max-width:479px){.br-sep{display:none;}.booking-ribbon{gap:6px;}}

/* ═══════════════════════════════════════════════
   PROGRESS BAR
═══════════════════════════════════════════════ */
.progress-wrap{max-width:600px;margin:0 auto 36px;}
@media(max-width:767px){.progress-wrap{margin-bottom:24px;}}
.progress-steps{
  display:flex;align-items:flex-start;justify-content:space-between;
  position:relative;
}
.progress-steps::before{
  content:'';position:absolute;
  top:14px;left:14px;right:14px;
  height:2px;background:rgba(26,58,47,.12);z-index:0;
}
.progress-fill{
  position:absolute;top:14px;left:14px;
  height:2px;background:var(--ocean);z-index:1;
  width:var(--prog-w,0%);transition:width .5s var(--smooth);
}
.ps-step{
  display:flex;flex-direction:column;align-items:center;gap:6px;
  position:relative;z-index:2;flex:1;
}
.ps-dot{
  width:28px;height:28px;border-radius:50%;
  border:2px solid rgba(26,58,47,.18);
  background:#fff;
  display:flex;align-items:center;justify-content:center;
  font-size:.68rem;font-weight:700;color:var(--muted);
  transition:all .3s var(--spring);
}
.ps-step.active .ps-dot{
  border-color:var(--ocean);background:var(--ocean);color:#fff;
  animation:pulse 2s infinite;
}
.ps-step.done .ps-dot{
  border-color:var(--ocean);background:var(--ocean);color:#fff;
  font-size:.8rem;animation:checkBounce .4s var(--spring);
}
.ps-label{
  font-size:.6rem;font-weight:600;
  text-transform:uppercase;letter-spacing:.06em;
  color:var(--muted);text-align:center;white-space:nowrap;
}
.ps-step.active .ps-label,.ps-step.done .ps-label{color:var(--ocean);}

/* ═══════════════════════════════════════════════
   FORM CARD
═══════════════════════════════════════════════ */
.intake-form-card{
  background:#fff;
  border-radius:var(--r-xl);
  border:1px solid rgba(26,58,47,.08);
  box-shadow:0 20px 60px rgba(26,58,47,.08);
  overflow:hidden;
  margin-bottom:24px;
}
.isec-header{
  padding:22px 28px 0;
  display:flex;align-items:flex-start;gap:14px;
}
@media(max-width:479px){.isec-header{padding:18px 18px 0;}}
.isec-ico{
  width:42px;height:42px;flex-shrink:0;
  border-radius:12px;
  display:flex;align-items:center;justify-content:center;
  margin-top:2px;
}
.isec-ico i{font-size:19px;}
.ico-cream{background:rgba(4,169,167,.1);}.ico-cream i{color:var(--indigo);}
.ico-peach{background:rgba(255,205,173,.28);}.ico-peach i{color:#a0522d;}
.ico-ocean{background:var(--ocean);}.ico-ocean i{color:#fff;}
.ico-teal{background:var(--ocean-soft);}.ico-teal i{color:var(--ocean);}

.isec-ttl{
  font-family:'Nanum Myeongjo',serif;
  font-size:1.15rem;font-weight:400;line-height:1.2;color:var(--ocean);
}
.isec-sub{font-size:.8rem;color:var(--muted);margin-top:3px;line-height:1.5;}
.isec-body{padding:20px 28px 28px;}
@media(max-width:479px){.isec-body{padding:16px 18px 22px;}}

/* ── fields ── */
.field-group{display:flex;flex-direction:column;gap:5px;margin-bottom:16px;}
.field-group:last-child{margin-bottom:0;}
.field-label{font-size:.8rem;font-weight:600;color:var(--ocean);letter-spacing:.01em;}
.field-label .req{color:var(--danger);margin-left:2px;}
.field-label .opt{color:var(--muted);font-weight:400;font-size:.73rem;margin-left:4px;}
.field-hint{font-size:.72rem;color:var(--muted);margin-top:2px;line-height:1.45;}

.field-input,.field-select,.field-textarea{
  width:100%;padding:13px 16px;
  border:1.5px solid rgba(26,58,47,.14);
  border-radius:var(--r-sm);
  font-family:'Cabin',sans-serif;font-size:.93rem;
  color:var(--ocean);background:#fff;
  transition:border-color .2s,box-shadow .2s,background .2s;
  appearance:none;-webkit-appearance:none;outline:none;
}
.field-input::placeholder,.field-textarea::placeholder{color:rgba(61,107,87,.35);}
.field-input:focus,.field-select:focus,.field-textarea:focus{
  border-color:var(--ocean);
  box-shadow:0 0 0 3px rgba(26,58,47,.08);
  background:#fafffe;
}
.field-input.has-error,.field-select.has-error,.field-textarea.has-error{
  border-color:var(--danger);box-shadow:0 0 0 3px rgba(192,57,43,.08);
}
.field-input.is-valid,.field-select.is-valid,.field-textarea.is-valid{border-color:var(--success);}
.field-textarea{resize:vertical;min-height:80px;line-height:1.6;}

.field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
@media(max-width:479px){.field-row{grid-template-columns:1fr;gap:0;}}

.select-wrap{position:relative;}
.select-wrap::after{
  content:'\F282';font-family:'bootstrap-icons';
  position:absolute;right:14px;top:50%;transform:translateY(-50%);
  color:var(--muted);font-size:.9rem;pointer-events:none;
}

.v-error{display:flex;align-items:center;gap:5px;font-size:.72rem;color:var(--danger);margin-top:3px;}
.v-error i{font-size:11px;flex-shrink:0;}

/* ── repeater ── */
.repeater-wrap{display:flex;flex-direction:column;gap:10px;}
.repeater-item{
  display:flex;align-items:flex-start;gap:8px;
  background:var(--cream);border-radius:var(--r-sm);
  padding:10px 12px;
  border:1px solid rgba(26,58,47,.08);
  transition:border-color .2s,box-shadow .2s;
  animation:fadeUp .3s var(--out) both;
}
.repeater-item:focus-within{border-color:rgba(26,58,47,.2);box-shadow:0 2px 12px rgba(26,58,47,.05);}
.repeater-item .field-input,.repeater-item .field-textarea{
  border:none;background:transparent;padding:4px 6px;box-shadow:none;font-size:.9rem;
}
.repeater-item .field-input:focus,.repeater-item .field-textarea:focus{box-shadow:none;background:transparent;}
.repeater-remove{
  width:30px;height:30px;flex-shrink:0;
  background:none;border:none;cursor:pointer;
  border-radius:8px;display:flex;align-items:center;justify-content:center;
  color:var(--muted);margin-top:2px;
  transition:background .2s,color .2s,transform .2s var(--spring);
  -webkit-tap-highlight-color:transparent;
}
.repeater-remove:hover{background:rgba(192,57,43,.1);color:var(--danger);transform:scale(1.1);}
.add-row-btn{
  display:inline-flex;align-items:center;gap:7px;
  font-size:.82rem;font-weight:600;
  color:var(--ocean);background:transparent;border:none;cursor:pointer;
  padding:8px 0;
  transition:opacity .2s,transform .2s var(--spring);
  -webkit-tap-highlight-color:transparent;
}
.add-row-btn:hover{opacity:.75;transform:translateX(2px);}
.add-row-btn i{font-size:14px;}

/* ── char counter ── */
.char-count{font-size:.68rem;color:var(--muted);text-align:right;margin-top:2px;}
.char-count.over{color:var(--danger);}

/* ── section divider ── */
.isec-divider{height:1px;background:rgba(26,58,47,.07);margin:0 28px;}
@media(max-width:479px){.isec-divider{margin:0 18px;}}

/* ═══════════════════════════════════════════════
   STICKY SUBMIT FOOTER
═══════════════════════════════════════════════ */
.submit-footer{
  position:sticky;bottom:0;left:0;right:0;
  background:rgba(240,255,219,.94);
  backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);
  border-top:1px solid rgba(26,58,47,.08);
  padding:14px 20px max(14px, env(safe-area-inset-bottom));
  z-index:100;transition:box-shadow .3s;
}
.submit-footer.elevated{box-shadow:0 -4px 24px rgba(26,58,47,.1);}
.submit-footer-inner{
  max-width:860px;margin:0 auto;
  display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;
}
.sf-left{display:flex;align-items:center;gap:8px;font-size:.78rem;color:var(--muted);}
.sf-left i{font-size:15px;color:var(--ocean);}
.sf-right{display:flex;align-items:center;gap:10px;}

.submit-btn{
  display:inline-flex;align-items:center;gap:9px;
  font-family:'Cabin',sans-serif;font-size:.95rem;font-weight:600;
  background:var(--ocean);color:#fff;
  padding:13px 28px;border-radius:var(--r-sm);
  border:none;cursor:pointer;
  box-shadow:0 4px 16px rgba(26,58,47,.22);
  position:relative;overflow:hidden;
  transition:transform .22s var(--spring),box-shadow .22s,background .18s,opacity .2s;
  -webkit-tap-highlight-color:transparent;min-height:48px;
}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(26,58,47,.3);}
.submit-btn:active{transform:scale(.96)!important;}
.submit-btn:disabled{opacity:.55;cursor:not-allowed;transform:none!important;box-shadow:none;}
.submit-btn i{display:inline-block;transition:transform .22s var(--spring);}
.submit-btn:not(:disabled):hover i{transform:translateX(4px);}
.btn-ripple{
  position:absolute;border-radius:50%;pointer-events:none;
  background:rgba(255,255,255,.25);
  transform:scale(0);animation:ripple .6s ease-out forwards;
}

.save-draft-btn{
  display:inline-flex;align-items:center;gap:7px;
  font-family:'Cabin',sans-serif;font-size:.85rem;font-weight:600;
  background:transparent;color:var(--ocean);
  padding:12px 20px;border-radius:var(--r-sm);
  border:1.5px solid rgba(26,58,47,.22);cursor:pointer;
  transition:background .2s,border-color .2s,transform .22s var(--spring);
  -webkit-tap-highlight-color:transparent;min-height:48px;
}
.save-draft-btn:hover{background:rgba(26,58,47,.05);border-color:rgba(26,58,47,.35);}

/* ═══════════════════════════════════════════════
   SUCCESS STATE
═══════════════════════════════════════════════ */
.intake-success{display:none;text-align:center;padding:72px 24px 80px;}
.success-ico-wrap{
  width:80px;height:80px;border-radius:50%;
  background:rgba(4,169,167,.12);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 24px;animation:checkBounce .6s var(--spring);
}
.success-ico-wrap i{font-size:36px;color:var(--yellow);}
.success-title{
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.7rem,5vw,2.4rem);
  font-weight:400;line-height:1.1;color:var(--ocean);margin-bottom:12px;
}
.success-sub{font-size:.97rem;color:var(--muted);max-width:440px;margin:0 auto 28px;line-height:1.72;}
.success-next{display:flex;flex-direction:column;gap:10px;max-width:400px;margin:0 auto;list-style:none;}
.sn-item{
  display:flex;align-items:flex-start;gap:12px;
  background:#fff;border-radius:var(--r-md);
  padding:14px 16px;border:1px solid rgba(26,58,47,.07);
  text-align:left;font-size:.88rem;color:var(--muted);line-height:1.5;
}
.sn-num{
  width:24px;height:24px;flex-shrink:0;border-radius:50%;
  background:var(--ocean);color:#fff;
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;font-weight:700;margin-top:1px;
}

/* ═══════════════════════════════════════════════
   ALREADY SUBMITTED STATE
═══════════════════════════════════════════════ */
.intake-already{text-align:center;padding:72px 24px;}
.already-ico{font-size:48px;color:var(--yellow);margin-bottom:20px;}
.already-title{
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.5rem,4vw,2rem);font-weight:400;
  color:var(--ocean);margin-bottom:10px;
}
.already-sub{font-size:.95rem;color:var(--muted);max-width:400px;margin:0 auto;line-height:1.7;}

/* ═══════════════════════════════════════════════
   MOBILE
═══════════════════════════════════════════════ */
@media(max-width:767px){
  .intake-hero{padding:28px 0 20px;}
  .intake-form-card{border-radius:var(--r-lg);}
  .submit-footer-inner{justify-content:stretch;}
  .sf-left{display:none;}
  .sf-right{flex:1;justify-content:space-between;width:100%;}
  .submit-btn{flex:1;justify-content:center;}
  .save-draft-btn{padding:12px 14px;}
}
@media(max-width:479px){
  .field-label{font-size:.78rem;}
  .progress-steps::before{left:12px;right:12px;top:12px;}
  .progress-fill{top:12px;left:12px;}
  .ps-dot{width:24px;height:24px;font-size:.62rem;}
  .ps-label{font-size:.55rem;}
  .isec-ttl{font-size:1.05rem;}
}
@media(prefers-reduced-motion:reduce){
  *,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important;}
}
</style>
@endpush

@section('content')

{{-- MINIMAL NAV --}}
<nav class="intake-nav" id="intakeNav" aria-label="Page navigation">
  <div class="intake-nav-inner">
    <a href="{{ url('/') }}" class="intake-nav-logo" aria-label="CavalCare home">
      <img src="{{ asset('images/caval-care-logo.png') }}" alt="CavalCare">
    </a>
    <div class="intake-nav-secure" role="status" aria-label="Secure page">
      <i class="bi bi-shield-lock-fill"></i>
      <span>Secure &amp; Private</span>
    </div>
  </div>
</nav>

<div class="intake-page">
  <div class="intake-glow ig1" aria-hidden="true"></div>
  <div class="intake-glow ig2" aria-hidden="true"></div>

  <div class="wrap">

    {{-- ── ALREADY SUBMITTED ── --}}
    @if($alreadySubmitted)
    <div class="intake-already">
      <div class="already-ico"><i class="bi bi-check-circle-fill"></i></div>
      <h1 class="already-title">You've already submitted your details</h1>
      <p class="already-sub">
        Your care information was received on
        {{ $booking->booking?->created_at->format('D, d M Y \a\t H:i') }}.
        Our team will be in touch shortly. If you need to make a correction, please
        <a href="https://wa.me/{{ config('app.whatsapp_number') }}" style="color:var(--ocean);font-weight:600;">contact us</a>.
      </p>
    </div>
    @else

    {{-- ── PAGE HERO ── --}}
    <div class="intake-hero reveal">
      <div class="intake-hero-pill">
        <span class="intake-hero-pill-dot"></span>
        Step 2 of 3 — Care Details
      </div>
      <h1 class="intake-title">
        Tell us about your<br><em>loved one</em>
      </h1>
      <p class="intake-subtitle">
        This information helps us match the right nurse and create a personalised care plan.
        It stays private and is never shared.
      </p>

      {{-- Booking summary ribbon --}}
      {{-- Now reads care type from $booking->careType (PreBooking belongsTo CareType)
           and plan name from $booking->carePlan (PreBooking belongsTo CarePlan) --}}
      <div class="booking-ribbon">
        <div class="br-item">
          <i class="bi bi-person-check"></i>
          <span>Booking for <strong>{{ $booking->booker_details['name'] ?? '—' }}</strong></span>
        </div>
        <div class="br-sep"></div>
        <div class="br-item">
          <i class="bi bi-heart-pulse"></i>
          <span>Type: <strong>{{ $booking->careType->name ?? '—' }}</strong></span>
        </div>
        <div class="br-sep"></div>
        <div class="br-item">
          <i class="bi bi-clipboard-pulse"></i>
          <span>Plan: <strong>{{ $booking->carePlan->name ?? '—' }}</strong></span>
        </div>
        @if($booking->carePlan)
        <div class="br-sep"></div>
        <div class="br-item">
          <i class="bi bi-tag"></i>
          <span><strong>{{ $booking->carePlan->formatted_price }}</strong></span>
        </div>
        @endif
      </div>
    </div>

    {{-- ── PROGRESS BAR ── --}}
    <div class="progress-wrap reveal" style="transition-delay:.08s;" aria-label="Form progress">
      <div class="progress-steps" id="progressSteps">
        <div class="ps-step done" id="pStep0">
          <div class="ps-dot"><i class="bi bi-check"></i></div>
          <div class="ps-label">Payment</div>
        </div>
        <div class="ps-step active" id="pStep1">
          <div class="ps-dot">2</div>
          <div class="ps-label">Your Details</div>
        </div>
        <div class="ps-step" id="pStep2">
          <div class="ps-dot">3</div>
          <div class="ps-label">Patient Info</div>
        </div>
        <div class="ps-step" id="pStep3">
          <div class="ps-dot">4</div>
          <div class="ps-label">Care Plan</div>
        </div>
        <div class="ps-step" id="pStep4">
          <div class="ps-dot">5</div>
          <div class="ps-label">Review</div>
        </div>
      </div>
      <div class="progress-fill" id="progressFill" style="--prog-w:25%;"></div>
    </div>

    {{-- ═══════════════════════════════════════════
         MULTI-STEP FORM
    ═══════════════════════════════════════════ --}}
    <form
      id="intakeForm"
      method="POST"
      action="{{ route('booking.intake.submit', $token) }}"
      novalidate
    >
      @csrf

      {{-- ── STEP 1: BOOKER DETAILS ── --}}
      <div class="form-step reveal" id="formStep1" data-step="1" style="transition-delay:.12s;">
        <div class="intake-form-card">
          <div class="isec-header">
            <div class="isec-ico ico-cream"><i class="bi bi-person"></i></div>
            <div>
              <div class="isec-ttl">Your Details</div>
              <div class="isec-sub">The person making this booking</div>
            </div>
          </div>
          <div class="isec-body">

            <div class="field-row">
              <div class="field-group">
                <label class="field-label" for="booker_name">
                  Full Name <span class="req">*</span>
                </label>
                <input
                  type="text" id="booker_name"
                  name="booker_details[name]"
                  class="field-input @error('booker_details.name') has-error @enderror"
                  value="{{ old('booker_details.name', $booking->booker_details['name'] ?? '') }}"
                  placeholder="Surname first"
                  required autocomplete="name"
                >
                @error('booker_details.name')
                  <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
                @enderror
              </div>
              <div class="field-group">
                <label class="field-label" for="booker_phone">
                  Phone Number <span class="req">*</span>
                </label>
                <input
                  type="tel" id="booker_phone"
                  name="booker_details[phone]"
                  class="field-input @error('booker_details.phone') has-error @enderror"
                  value="{{ old('booker_details.phone', $booking->booker_details['phone'] ?? '') }}"
                  placeholder="+234 800 000 0000"
                  required autocomplete="tel"
                >
                @error('booker_details.phone')
                  <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="field-row">
              <div class="field-group">
                <label class="field-label" for="booker_state">
                  State <span class="req">*</span>
                </label>
                <div class="select-wrap">
                  <select
                    id="booker_state"
                    name="booker_details[state]"
                    class="field-select @error('booker_details.state') has-error @enderror"
                    required
                  >
                    <option value="">Select state…</option>
                    @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $state)
                      <option value="{{ $state }}" {{ old('booker_details.state') === $state ? 'selected' : '' }}>
                        {{ $state }}
                      </option>
                    @endforeach
                  </select>
                </div>
                @error('booker_details.state')
                  <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
                @enderror
              </div>
              <div class="field-group">
                <label class="field-label" for="booker_emergency">
                  Emergency Contact <span class="opt">(optional)</span>
                </label>
                <input
                  type="tel" id="booker_emergency"
                  name="booker_details[emergency_number]"
                  class="field-input @error('booker_details.emergency_number') has-error @enderror"
                  value="{{ old('booker_details.emergency_number') }}"
                  placeholder="+234 800 000 0000"
                  autocomplete="tel"
                >
                @error('booker_details.emergency_number')
                  <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="field-group">
              <label class="field-label" for="booker_address">
                Home Address <span class="req">*</span>
              </label>
              <textarea
                id="booker_address"
                name="booker_details[address]"
                class="field-textarea @error('booker_details.address') has-error @enderror"
                placeholder="Street address, area, city"
                rows="2" required
              >{{ old('booker_details.address') }}</textarea>
              @error('booker_details.address')
                <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
              @enderror
            </div>

          </div>
        </div>
      </div>

      {{-- ── STEP 2: PATIENT DETAILS ── --}}
      <div class="form-step reveal" id="formStep2" data-step="2" style="transition-delay:.16s;">
        <div class="intake-form-card">
          <div class="isec-header">
            <div class="isec-ico ico-peach"><i class="bi bi-heart-pulse"></i></div>
            <div>
              <div class="isec-ttl">Patient Information</div>
              <div class="isec-sub">The person who will be receiving care</div>
            </div>
          </div>
          <div class="isec-body">

            <div class="field-group">
              <label class="field-label" for="patient_full_name">
                Patient's Full Name <span class="req">*</span>
              </label>
              <input
                type="text" id="patient_full_name"
                name="patient_details[full_name]"
                class="field-input @error('patient_details.full_name') has-error @enderror"
                value="{{ old('patient_details.full_name') }}"
                placeholder="Surname first"
                required
              >
              @error('patient_details.full_name')
                <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
              @enderror
            </div>

            <div class="field-row">
              <div class="field-group">
                <label class="field-label" for="patient_age">
                  Age <span class="req">*</span>
                </label>
                <input
                  type="number" id="patient_age"
                  name="patient_details[age]"
                  class="field-input @error('patient_details.age') has-error @enderror"
                  value="{{ old('patient_details.age') }}"
                  placeholder="e.g. 72"
                  min="1" max="120" required
                >
                @error('patient_details.age')
                  <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
                @enderror
              </div>
              <div class="field-group">
                <label class="field-label" for="patient_gender">
                  Gender <span class="req">*</span>
                </label>
                <div class="select-wrap">
                  <select
                    id="patient_gender"
                    name="patient_details[gender]"
                    class="field-select @error('patient_details.gender') has-error @enderror"
                    required
                  >
                    <option value="">Select…</option>
                    <option value="Male"   {{ old('patient_details.gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('patient_details.gender') === 'Female' ? 'selected' : '' }}>Female</option>
                  </select>
                </div>
                @error('patient_details.gender')
                  <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="field-row">
              <div class="field-group">
                <label class="field-label" for="patient_mobility">
                  Mobility Level <span class="req">*</span>
                </label>
                <div class="select-wrap">
                  <select
                    id="patient_mobility"
                    name="patient_details[mobility_level]"
                    class="field-select @error('patient_details.mobility_level') has-error @enderror"
                    required
                  >
                    <option value="">Select…</option>
                    <option value="Independent" {{ old('patient_details.mobility_level') === 'Independent' ? 'selected' : '' }}>Independent</option>
                    <option value="Assisted"    {{ old('patient_details.mobility_level') === 'Assisted'    ? 'selected' : '' }}>Needs Assistance</option>
                    <option value="Bedridden"   {{ old('patient_details.mobility_level') === 'Bedridden'   ? 'selected' : '' }}>Bedridden</option>
                  </select>
                </div>
                @error('patient_details.mobility_level')
                  <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
                @enderror
              </div>
              <div class="field-group">
                <label class="field-label" for="patient_emergency">
                  Patient Emergency Contact <span class="opt">(optional)</span>
                </label>
                <input
                  type="tel" id="patient_emergency"
                  name="patient_details[emergency_contact]"
                  class="field-input @error('patient_details.emergency_contact') has-error @enderror"
                  value="{{ old('patient_details.emergency_contact') }}"
                  placeholder="+234 800 000 0000"
                >
                @error('patient_details.emergency_contact')
                  <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="field-group">
              <label class="field-label" for="patient_address">
                Patient's Home Address <span class="req">*</span>
              </label>
              <textarea
                id="patient_address"
                name="patient_details[address]"
                class="field-textarea @error('patient_details.address') has-error @enderror"
                placeholder="Where will care be provided? Street, area, city"
                rows="2" required
              >{{ old('patient_details.address') }}</textarea>
              @error('patient_details.address')
                <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
              @enderror
            </div>

            <div class="field-group">
              <label class="field-label" for="patient_diagnosis">
                Medical Conditions / Diagnosis <span class="opt">(optional)</span>
              </label>
              <textarea
                id="patient_diagnosis"
                name="patient_details[diagnosis]"
                class="field-textarea @error('patient_details.diagnosis') has-error @enderror"
                placeholder="e.g. Type 2 Diabetes, post-op knee replacement, stroke recovery…"
                rows="3" maxlength="800" data-char-max="800"
              >{{ old('patient_details.diagnosis') }}</textarea>
              <div class="char-count" id="diagnosisCount">0 / 800</div>
              @error('patient_details.diagnosis')
                <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
              @enderror
            </div>

          </div>

          <div class="isec-divider"></div>

          {{-- MEDICATIONS --}}
          <div class="isec-header" style="margin-top:20px;">
            <div class="isec-ico ico-teal" style="width:34px;height:34px;border-radius:9px;">
              <i class="bi bi-capsule" style="font-size:15px;"></i>
            </div>
            <div>
              <div class="isec-ttl" style="font-size:1rem;">Current Medications</div>
              <div class="isec-sub">List the medications your loved one is currently taking</div>
            </div>
          </div>
          <div class="isec-body">
            <div class="repeater-wrap" id="medsRepeater">
              @php $meds = old('patient_details.medications', [['medication'=>'']]) @endphp
              @foreach($meds as $i => $med)
              <div class="repeater-item">
                <input
                  type="text"
                  name="patient_details[medications][{{ $i }}][medication]"
                  class="field-input"
                  value="{{ $med['medication'] ?? '' }}"
                  placeholder="e.g. Metformin 500mg, twice daily"
                >
                <button type="button" class="repeater-remove" aria-label="Remove">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
              @endforeach
            </div>
            <button type="button" class="add-row-btn"
              data-target="medsRepeater"
              data-name="patient_details[medications][][medication]"
              data-placeholder="e.g. Metformin 500mg, twice daily">
              <i class="bi bi-plus-circle"></i> Add Medication
            </button>
          </div>

          <div class="isec-divider"></div>

          {{-- ALLERGIES --}}
          <div class="isec-header" style="margin-top:20px;">
            <div class="isec-ico ico-teal" style="width:34px;height:34px;border-radius:9px;">
              <i class="bi bi-exclamation-triangle" style="font-size:15px;"></i>
            </div>
            <div>
              <div class="isec-ttl" style="font-size:1rem;">Known Allergies</div>
              <div class="isec-sub">Any drug, food, or environmental allergies</div>
            </div>
          </div>
          <div class="isec-body">
            <div class="repeater-wrap" id="allergyRepeater">
              @php $allergies = old('patient_details.allergies', []) @endphp
              @foreach($allergies as $i => $allergy)
              <div class="repeater-item">
                <input
                  type="text"
                  name="patient_details[allergies][{{ $i }}][allergy]"
                  class="field-input"
                  value="{{ $allergy['allergy'] ?? '' }}"
                  placeholder="e.g. Penicillin, Latex, Peanuts"
                >
                <button type="button" class="repeater-remove" aria-label="Remove">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
              @endforeach
            </div>
            <button type="button" class="add-row-btn"
              data-target="allergyRepeater"
              data-name="patient_details[allergies][][allergy]"
              data-placeholder="e.g. Penicillin, Latex, Peanuts">
              <i class="bi bi-plus-circle"></i> Add Allergy
            </button>
          </div>

          <div class="isec-divider"></div>

          {{-- DAILY TASKS --}}
          <div class="isec-header" style="margin-top:20px;">
            <div class="isec-ico ico-teal" style="width:34px;height:34px;border-radius:9px;">
              <i class="bi bi-list-check" style="font-size:15px;"></i>
            </div>
            <div>
              <div class="isec-ttl" style="font-size:1rem;">Daily Care Tasks Required</div>
              <div class="isec-sub">What does your loved one need help with each day?</div>
            </div>
          </div>
          <div class="isec-body">
            <div class="repeater-wrap" id="tasksRepeater">
              @php $tasks = old('patient_details.daily_tasks', [['task'=>'']]) @endphp
              @foreach($tasks as $i => $task)
              <div class="repeater-item">
                <input
                  type="text"
                  name="patient_details[daily_tasks][{{ $i }}][task]"
                  class="field-input"
                  value="{{ $task['task'] ?? '' }}"
                  placeholder="e.g. Wound dressing, bathing assistance, medication reminders"
                  required
                >
                <button type="button" class="repeater-remove" aria-label="Remove">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
              @endforeach
            </div>
            <button type="button" class="add-row-btn"
              data-target="tasksRepeater"
              data-name="patient_details[daily_tasks][][task]"
              data-placeholder="e.g. Wound dressing, bathing assistance">
              <i class="bi bi-plus-circle"></i> Add Task
            </button>
          </div>

        </div>
      </div>

      {{-- ── STEP 3: CARE PLAN DATES ── --}}
      {{-- duration_days is optional in meta — if not set, end date is left open --}}
      @php $durationDays = (int) ($booking->carePlan->meta['duration_days'] ?? 0); @endphp
      <div class="form-step reveal" id="formStep3" data-step="3" style="transition-delay:.2s;">
        <div class="intake-form-card">
          <div class="isec-header">
            <div class="isec-ico ico-ocean"><i class="bi bi-calendar-check"></i></div>
            <div>
              <div class="isec-ttl">Care Plan Dates</div>
              <div class="isec-sub">When would you like care to begin?</div>
            </div>
          </div>
          <div class="isec-body">

            <div class="field-row">
              <div class="field-group">
                <label class="field-label" for="start_date">
                  Start Date <span class="req">*</span>
                </label>
                <input
                  type="date" id="start_date"
                  name="booking_details[start_date]"
                  class="field-input @error('booking_details.start_date') has-error @enderror"
                  value="{{ old('booking_details.start_date') }}"
                  min="{{ now()->addDay()->toDateString() }}"
                  required
                >
                @error('booking_details.start_date')
                  <p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>
                @enderror
              </div>
              <div class="field-group">
                <label class="field-label" for="end_date">End Date</label>
                <input
                  type="date" id="end_date"
                  name="booking_details[end_date]"
                  class="field-input"
                  style="background:rgba(26,58,47,.04);cursor:default;"
                  @if($durationDays) readonly @endif
                >
                <p class="field-hint">
                  @if($durationDays)
                    Calculated automatically ({{ $durationDays }} days from start date)
                  @else
                    Optional — leave blank for open-ended or ongoing care
                  @endif
                </p>
              </div>
            </div>

            {{-- Hidden fields required by the controller --}}
            <input type="hidden" name="booking_details[care_type_id]" value="{{ $booking->care_type_id }}">
            <input type="hidden" name="booking_details[care_plan_id]" value="{{ $booking->care_plan_id }}">

          </div>
        </div>
      </div>

      {{-- ── STEP 4: REVIEW ── --}}
      <div class="form-step reveal" id="formStep4" data-step="4" style="transition-delay:.24s;">
        <div class="intake-form-card" style="background:var(--cream);border-color:rgba(26,58,47,.08);">
          <div class="isec-header">
            <div class="isec-ico ico-cream"><i class="bi bi-clipboard-check"></i></div>
            <div>
              <div class="isec-ttl">Before You Submit</div>
              <div class="isec-sub">Please read through this before sending your details</div>
            </div>
          </div>
          <div class="isec-body">
            <div style="font-size:.9rem;color:var(--muted);line-height:1.75;display:flex;flex-direction:column;gap:12px;">
              <div style="display:flex;align-items:flex-start;gap:12px;">
                <i class="bi bi-shield-lock-fill" style="color:var(--ocean);font-size:16px;margin-top:2px;flex-shrink:0;"></i>
                <p>Your details are <strong style="color:var(--ocean);">encrypted and stored securely</strong>. They are only accessible to the CavalCare team and the nurse assigned to your loved one.</p>
              </div>
              <div style="display:flex;align-items:flex-start;gap:12px;">
                <i class="bi bi-person-check-fill" style="color:var(--ocean);font-size:16px;margin-top:2px;flex-shrink:0;"></i>
                <p>Once submitted, our team will review the information and contact you within <strong style="color:var(--ocean);">24–48 hours</strong> with your nurse match.</p>
              </div>
              <div style="display:flex;align-items:flex-start;gap:12px;">
                <i class="bi bi-telephone-fill" style="color:var(--ocean);font-size:16px;margin-top:2px;flex-shrink:0;"></i>
                <p>If you need to make any corrections after submitting, contact us via WhatsApp at <a href="https://wa.me/{{ config('app.whatsapp_number') }}" style="color:var(--ocean);font-weight:600;">+234 706 311 2134</a>.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </form>

    {{-- SUCCESS STATE --}}
    <div class="intake-success" id="intakeSuccess">
      <div class="success-ico-wrap"><i class="bi bi-check-lg"></i></div>
      <h2 class="success-title">All done — thank you!</h2>
      <p class="success-sub">
        Your care details have been received. The CavalCare team will review everything and
        reach out to you within 24–48 hours with your nurse match.
      </p>
      <ul class="success-next">
        <li class="sn-item">
          <span class="sn-num">1</span>
          <span>We review your information and select the best-matched nurse.</span>
        </li>
        <li class="sn-item">
          <span class="sn-num">2</span>
          <span>You receive a call or WhatsApp message to confirm your start date.</span>
        </li>
        <li class="sn-item">
          <span class="sn-num">3</span>
          <span>Your nurse arrives on your selected start date and care begins.</span>
        </li>
      </ul>
    </div>

    @endif {{-- end @if not submitted --}}

  </div>
</div>

{{-- STICKY SUBMIT FOOTER --}}
@unless($alreadySubmitted)
<footer class="submit-footer" id="submitFooter" role="contentinfo">
  <div class="submit-footer-inner">
    <div class="sf-left">
      <i class="bi bi-shield-lock-fill"></i>
      <span>Encrypted &amp; private. Never shared.</span>
    </div>
    <div class="sf-right">
      <button type="button" id="saveDraftBtn" class="save-draft-btn">
        <i class="bi bi-floppy"></i>
        <span class="draft-label">Save Draft</span>
      </button>
      <button type="submit" form="intakeForm" id="submitBtn" class="submit-btn">
        <i class="bi bi-send"></i>
        Submit Details
      </button>
    </div>
  </div>
</footer>
@endunless

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

const form       = document.getElementById('intakeForm');
const submitBtn  = document.getElementById('submitBtn');
const saveDraft  = document.getElementById('saveDraftBtn');
const navEl      = document.getElementById('intakeNav');
const footer     = document.getElementById('submitFooter');
const successEl  = document.getElementById('intakeSuccess');
const DRAFT_KEY  = 'cavalcare_intake_draft_{{ $token }}';
const DURATION   = {{ $durationDays }};

/* ── Nav scroll state ──────────────────────────────── */
window.addEventListener('scroll', () => {
  navEl?.classList.toggle('scrolled', scrollY > 10);
  footer?.classList.toggle('elevated', scrollY > 80);
}, { passive: true });

/* ── Scroll reveal ─────────────────────────────────── */
const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
  });
}, { threshold: .08, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

/* ── Ripple on submit button ───────────────────────── */
submitBtn?.addEventListener('pointerdown', e => {
  const r    = document.createElement('span');
  const rect = submitBtn.getBoundingClientRect();
  const sz   = Math.max(rect.width, rect.height) * 1.4;
  r.className = 'btn-ripple';
  r.style.cssText = `width:${sz}px;height:${sz}px;left:${e.clientX-rect.left-sz/2}px;top:${e.clientY-rect.top-sz/2}px;`;
  submitBtn.appendChild(r);
  r.addEventListener('animationend', () => r.remove());
});

/* ── Character counter ─────────────────────────────── */
document.querySelectorAll('[data-char-max]').forEach(el => {
  const max     = parseInt(el.dataset.charMax);
  const counter = document.getElementById(el.id + 'Count');
  if (!counter) return;
  const update = () => {
    const len = el.value.length;
    counter.textContent = `${len} / ${max}`;
    counter.classList.toggle('over', len > max);
  };
  el.addEventListener('input', update);
  update();
});

/* ── Repeater — add row ────────────────────────────── */
document.querySelectorAll('.add-row-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const wrapperId   = btn.dataset.target;
    const wrapper     = document.getElementById(wrapperId);
    const name        = btn.dataset.name;
    const placeholder = btn.dataset.placeholder || '';
    if (!wrapper) return;

    const item = document.createElement('div');
    item.className = 'repeater-item';
    item.style.animation = 'fadeUp .3s var(--out) both';
    item.innerHTML = `
      <input type="text" name="${name}" class="field-input" placeholder="${placeholder}">
      <button type="button" class="repeater-remove" aria-label="Remove">
        <i class="bi bi-x-lg"></i>
      </button>
    `;
    wrapper.appendChild(item);
    reindexRepeater(wrapper);
    item.querySelector('input')?.focus();
    bindRemove(item.querySelector('.repeater-remove'));
  });
});

/* ── Repeater — remove row ─────────────────────────── */
function bindRemove(btn) {
  btn.addEventListener('click', () => {
    const item    = btn.closest('.repeater-item');
    const wrapper = item.parentElement;
    if (wrapper.querySelectorAll('.repeater-item').length <= 1 &&
        wrapper.id === 'tasksRepeater') return;
    item.style.opacity   = '0';
    item.style.transform = 'translateX(10px)';
    item.style.transition = 'opacity .2s, transform .2s';
    setTimeout(() => item.remove(), 200);
  });
}
document.querySelectorAll('.repeater-remove').forEach(bindRemove);

function reindexRepeater(wrapper) {
  wrapper.querySelectorAll('.repeater-item').forEach((item, i) => {
    const input = item.querySelector('input, textarea');
    if (input) input.name = input.name.replace(/\[\d+\]/, `[${i}]`);
  });
}

/* ── Date: auto-calculate end date ────────────────── */
const startDateEl = document.getElementById('start_date');
const endDateEl   = document.getElementById('end_date');

startDateEl?.addEventListener('change', () => {
  if (!startDateEl.value || !DURATION) return;
  const start = new Date(startDateEl.value);
  start.setDate(start.getDate() + DURATION);
  endDateEl.value = start.toISOString().split('T')[0];
});

/* ── Live field validation ─────────────────────────── */
const requiredFields = [
  'booker_name','booker_phone','booker_state','booker_address',
  'patient_full_name','patient_age','patient_gender',
  'patient_mobility','patient_address','start_date',
];

function validateField(el) {
  if (!el) return true;
  const ok = el.checkValidity() && el.value.trim() !== '';
  el.classList.toggle('has-error', !ok);
  el.classList.toggle('is-valid', ok);
  return ok;
}

requiredFields.forEach(id => {
  const el = document.getElementById(id);
  if (!el) return;
  el.addEventListener('blur',  () => validateField(el));
  el.addEventListener('input', () => { if (el.classList.contains('has-error')) validateField(el); });
});

/* ── Progress bar ──────────────────────────────────── */
const steps = ['pStep0','pStep1','pStep2','pStep3','pStep4'];
const fill  = document.getElementById('progressFill');

function updateProgress() {
  const filled = requiredFields.filter(id => {
    const el = document.getElementById(id);
    return el && el.value.trim() !== '';
  }).length;
  const pct = Math.min(100, Math.round((filled / requiredFields.length) * 100));
  if (fill) fill.style.setProperty('--prog-w', pct + '%');

  const stepIdx = pct < 25 ? 1 : pct < 50 ? 2 : pct < 75 ? 3 : pct < 100 ? 4 : 5;
  steps.forEach((id, i) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('active','done');
    if (i < stepIdx - 1) el.classList.add('done');
    if (i === stepIdx - 1) el.classList.add('active');
  });
}

requiredFields.forEach(id => {
  document.getElementById(id)?.addEventListener('input', updateProgress);
  document.getElementById(id)?.addEventListener('change', updateProgress);
});
updateProgress();

/* ── Save / restore draft ──────────────────────────── */
function collectDraft() {
  const data = {};
  new FormData(form).forEach((v, k) => { data[k] = v; });
  return data;
}

function restoreDraft() {
  try {
    const saved = localStorage.getItem(DRAFT_KEY);
    if (!saved) return;
    const data = JSON.parse(saved);
    Object.entries(data).forEach(([k, v]) => {
      const el = form.querySelector(`[name="${k}"]`);
      if (el && el.type !== 'hidden') el.value = v;
    });
    updateProgress();
  } catch(e) {}
}

function autosaveDraft() {
  try { localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraft())); } catch(e) {}
}

restoreDraft();
setInterval(autosaveDraft, 30000);

saveDraft?.addEventListener('click', () => {
  autosaveDraft();
  const orig = saveDraft.innerHTML;
  saveDraft.innerHTML = '<i class="bi bi-check-circle"></i> Saved!';
  saveDraft.style.color = 'var(--ocean)';
  setTimeout(() => { saveDraft.innerHTML = orig; saveDraft.style.color = ''; }, 2000);
});

/* ── Form submission ───────────────────────────────── */
form?.addEventListener('submit', async e => {
  e.preventDefault();

  let allOk = true;
  requiredFields.forEach(id => {
    const el = document.getElementById(id);
    if (!validateField(el)) allOk = false;
  });

  if (!allOk) {
    form.querySelector('.has-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise" style="animation:spin .8s linear infinite;display:inline-block;"></i> Submitting…';

  try {
    const res = await fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });

    if (res.ok) {
      localStorage.removeItem(DRAFT_KEY);
      document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
      document.querySelector('.intake-hero').style.display  = 'none';
      document.querySelector('.progress-wrap').style.display = 'none';
      if (footer) footer.style.display = 'none';
      successEl.style.display = 'block';
      window.scrollTo({ top: 0, behavior: 'smooth' });
      if (fill) fill.style.setProperty('--prog-w', '100%');
    } else {
      const html = await res.text();
      document.open(); document.write(html); document.close();
    }

  } catch(err) {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit Details';
    alert('Something went wrong. Please try again or contact us on WhatsApp.');
  }
});

/* ── Haptic on submit (Android) ────────────────────── */
if ('vibrate' in navigator) {
  submitBtn?.addEventListener('pointerdown', () => navigator.vibrate(6));
}

});
</script>
@endpush