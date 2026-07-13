{{-- resources/views/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Begin Your Care Journey — CavalCare')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700;800&family=Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════
   TOKENS  (mirrors home.blade.php exactly)
═══════════════════════════════════════════════ */
:root {
  --cream:      #F0FFDB;
  --yellow:     #04A9A7;
  --ocean:      #1A3A2F;
  --indigo:     #04A9A7;
  --peach:      #FFCDAD;
  --cream-d:    #d4f5b8;
  --ocean-h:    #0f5c3d;
  --ocean-soft: #e6f9f9;
  --text:       #1A3A2F;
  --muted:      #3d6b57;
  --white:      #ffffff;
  --r-xs:  6px; --r-sm: 10px; --r-md: 16px;
  --r-lg:  24px; --r-xl: 36px; --r-2xl: 48px;
  --spring: cubic-bezier(0.34,1.56,0.64,1);
  --out:    cubic-bezier(0,0,0.2,1);
  --smooth: cubic-bezier(0.4,0,0.2,1);
}

*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
html { scroll-behavior:smooth; }
body {
  font-family:'Cabin',system-ui,sans-serif;
  font-size:16px; line-height:1.65;
  color:var(--text); background:#fff;
  -webkit-font-smoothing:antialiased;
  overflow-x:hidden;
}
a { color:inherit; text-decoration:none; }
:focus-visible { outline:2px solid var(--yellow); outline-offset:3px; border-radius:4px; }

/* ── keyframes ── */
@keyframes fadeUp  { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:none} }
@keyframes fadeIn  { from{opacity:0} to{opacity:1} }
@keyframes scaleIn { from{opacity:0;transform:scale(.94)} to{opacity:1;transform:scale(1)} }
@keyframes floatY  { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-7px)} }
@keyframes ripple  { to{transform:scale(2.4);opacity:0} }
@keyframes revealPrice { 0%{opacity:0;transform:translateY(16px) scale(.96)} 100%{opacity:1;transform:none} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes pulse   { 0%,100%{box-shadow:0 0 0 0 rgba(26,58,47,.2)} 60%{box-shadow:0 0 0 10px rgba(26,58,47,0)} }
@keyframes checkPop { 0%{transform:scale(0)} 70%{transform:scale(1.2)} 100%{transform:scale(1)} }
@keyframes drawLine { from{transform:scaleX(0)} to{transform:scaleX(1)} }

/* ── scroll reveal ── */
.reveal { opacity:0; transform:translateY(26px); transition:opacity .7s var(--out),transform .7s var(--out); }
.reveal.is-visible { opacity:1; transform:none; }

/* ── layout ── */
.wrap { max-width:1160px; margin:0 auto; padding:0 20px; }
@media(min-width:768px){ .wrap{ padding:0 32px; } }

/* ── pill badges ── */
.pill {
  display:inline-flex; align-items:center; gap:6px;
  font-size:.7rem; font-weight:600;
  letter-spacing:.07em; text-transform:uppercase;
  padding:5px 13px; border-radius:100px;
}
.pill-dot { width:5px; height:5px; border-radius:50%; background:currentColor; }
.pill-ocean  { background:rgba(26,58,47,.1); color:var(--ocean); }
.pill-yellow { background:var(--yellow); color:var(--ocean); }
.pill-indigo { background:rgba(4,169,167,.13); color:var(--indigo); }

/* ── typography ── */
.sec-title {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.9rem,5vw,3rem);
  font-weight:400; line-height:1.1;
  letter-spacing:-.03em; color:var(--ocean);
}
.sec-title em { font-style:italic; color:var(--indigo); position:relative; }
.sec-title em::after {
  content:''; position:absolute;
  left:0; right:0; bottom:1px; height:2px;
  background:var(--peach); border-radius:2px;
  transform-origin:left;
  animation:drawLine .7s 1.2s var(--spring) both;
}

/* ── buttons ── */
.btn {
  display:inline-flex; align-items:center; gap:9px;
  font-family:'Cabin',sans-serif; font-size:.93rem; font-weight:600;
  border:none; cursor:pointer; text-decoration:none;
  position:relative; overflow:hidden;
  transition:transform .22s var(--spring),box-shadow .22s,background .18s,color .18s;
  -webkit-tap-highlight-color:transparent;
}
.btn:active { transform:scale(.96)!important; }
.btn-primary {
  background:var(--ocean); color:#fff;
  padding:14px 26px; border-radius:var(--r-sm);
  box-shadow:0 4px 16px rgba(26,58,47,.22);
}
.btn-primary:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(26,58,47,.32); }
.btn-outline {
  background:transparent; color:var(--ocean);
  padding:13px 26px; border-radius:var(--r-sm);
  border:1.5px solid rgba(26,58,47,.24);
}
.btn-outline:hover { background:var(--ocean); color:#fff; border-color:var(--ocean); transform:translateY(-2px); }
.btn-yellow {
  background:var(--yellow); color:var(--ocean);
  padding:14px 26px; border-radius:var(--r-sm);
  box-shadow:0 4px 16px rgba(4,169,167,.22);
}
.btn-yellow:hover { background:var(--cream-d); transform:translateY(-3px); box-shadow:0 8px 28px rgba(4,169,167,.35); }
.btn-ripple {
  position:absolute; border-radius:50%; pointer-events:none;
  background:rgba(255,255,255,.26);
  transform:scale(0); animation:ripple .6s ease-out forwards;
}

/* ════════════════════════════════════════════
   BOOKING PAGE — SPECIFIC STYLES
════════════════════════════════════════════ */

/* page background */
.booking-page {
  min-height:100svh;
  background:var(--cream);
  padding-top:80px; padding-bottom:80px;
  position:relative; overflow:hidden;
}
.booking-page .glow {
  position:absolute; border-radius:50%; pointer-events:none;
}
.booking-page .glow-1 {
  width:min(560px,110vw); height:min(560px,110vw);
  background:radial-gradient(circle,rgba(4,169,167,.18) 0%,transparent 65%);
  top:-20%; right:-12%; will-change:transform;
}
.booking-page .glow-2 {
  width:min(400px,80vw); height:min(400px,80vw);
  background:radial-gradient(circle,rgba(255,205,173,.25) 0%,transparent 65%);
  bottom:-15%; left:-8%; will-change:transform;
}

/* ── step indicator ── */
.steps-bar {
  display:flex; align-items:center; gap:0;
  max-width:360px; margin:0 auto 48px;
}
.step-node {
  display:flex; flex-direction:column; align-items:center; gap:6px;
  flex:1; position:relative;
}
.step-node::after {
  content:''; position:absolute;
  top:15px; left:calc(50% + 15px); right:calc(-50% + 15px);
  height:1.5px;
  background:rgba(26,58,47,.15);
  transition:background .4s;
}
.step-node:last-child::after { display:none; }
.step-node.done::after { background:var(--ocean); }

.step-circle {
  width:30px; height:30px; border-radius:50%;
  border:2px solid rgba(26,58,47,.18);
  display:flex; align-items:center; justify-content:center;
  font-size:.72rem; font-weight:700; color:var(--muted);
  background:#fff;
  transition:all .3s var(--spring);
  position:relative; z-index:2;
}
.step-node.active .step-circle {
  border-color:var(--ocean); background:var(--ocean); color:#fff;
  box-shadow:0 0 0 4px rgba(26,58,47,.1);
  animation:pulse 2s infinite;
}
.step-node.done .step-circle {
  border-color:var(--ocean); background:var(--ocean); color:#fff;
  animation:checkPop .4s var(--spring);
}
.step-node.done .step-circle::after {
  content:'✓'; font-size:.75rem;
}
.step-node.done .step-num { display:none; }
.step-lbl {
  font-size:.62rem; font-weight:600;
  text-transform:uppercase; letter-spacing:.06em;
  color:var(--muted); text-align:center; white-space:nowrap;
}
.step-node.active .step-lbl { color:var(--ocean); }

/* ── two-column layout ── */
.booking-grid {
  display:grid;
  grid-template-columns:1fr;
  gap:24px; align-items:start;
  max-width:1000px; margin:0 auto;
}
@media(min-width:900px){
  .booking-grid { grid-template-columns:1fr 420px; gap:32px; }
}

/* ── form card ── */
.form-card {
  background:#fff;
  border-radius:var(--r-xl);
  border:1px solid rgba(26,58,47,.08);
  box-shadow:0 24px 64px rgba(26,58,47,.1);
  padding:36px 32px;
  animation:scaleIn .6s .1s var(--spring) both;
}
@media(max-width:479px){ .form-card{ padding:24px 20px; } }

.form-section-label {
  display:flex; align-items:center; gap:10px;
  font-size:.7rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.09em;
  color:var(--muted); margin-bottom:20px;
}
.form-section-label::after {
  content:''; flex:1; height:1px; background:rgba(26,58,47,.08);
}
.fsl-ico {
  width:28px; height:28px; border-radius:8px;
  background:rgba(26,58,47,.06);
  display:flex; align-items:center; justify-content:center;
}
.fsl-ico i { font-size:13px; color:var(--ocean); }

/* ── form fields ── */
.field-group { display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.field-group:last-child { margin-bottom:0; }

.field-label {
  font-size:.8rem; font-weight:600;
  color:var(--ocean); letter-spacing:.01em;
}
.field-label span { color:#c0392b; margin-left:2px; }

.field-input,
.field-select {
  width:100%; padding:13px 16px;
  border:1.5px solid rgba(26,58,47,.14);
  border-radius:var(--r-sm);
  font-family:'Cabin',sans-serif; font-size:.93rem;
  color:var(--ocean); background:#fff;
  transition:border-color .2s, box-shadow .2s, background .2s;
  appearance:none; -webkit-appearance:none;
  outline:none;
}
.field-input::placeholder { color:rgba(61,107,87,.38); }
.field-input:focus,
.field-select:focus {
  border-color:var(--ocean);
  box-shadow:0 0 0 3px rgba(26,58,47,.09);
  background:#fafffe;
}
.field-input.error,
.field-select.error {
  border-color:#e53935;
  box-shadow:0 0 0 3px rgba(229,57,53,.1);
}
.field-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@media(max-width:479px){ .field-row{ grid-template-columns:1fr; } }

.select-wrap { position:relative; }
.select-wrap::after {
  content:'\F282'; font-family:'bootstrap-icons';
  position:absolute; right:14px; top:50%; transform:translateY(-50%);
  color:var(--muted); font-size:.9rem; pointer-events:none;
}
.field-hint {
  font-size:.73rem; color:var(--muted); margin-top:3px;
}

/* section spacing */
.form-section { margin-bottom:28px; }
.form-section:last-of-type { margin-bottom:0; }

/* ── right column: plan summary + price reveal ── */
.summary-col {
  display:flex; flex-direction:column; gap:20px;
  animation:fadeUp .6s .25s var(--out) both;
}

/* plan card */
.plan-summary {
  background:var(--ocean);
  border-radius:var(--r-xl);
  padding:28px 26px;
  position:relative; overflow:hidden;
  box-shadow:0 18px 48px rgba(26,58,47,.2);
}
.plan-summary::before {
  content:''; position:absolute;
  top:-40%; right:-30%;
  width:260px; height:260px; border-radius:50%;
  background:radial-gradient(circle,rgba(4,169,167,.18) 0%,transparent 65%);
  pointer-events:none;
}
.plan-summary::after {
  content:''; position:absolute;
  bottom:-30%; left:-20%;
  width:200px; height:200px; border-radius:50%;
  background:radial-gradient(circle,rgba(255,205,173,.1) 0%,transparent 65%);
  pointer-events:none;
}
.ps-eyebrow {
  font-size:.65rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.09em;
  color:rgba(255,255,255,.5); margin-bottom:6px;
}
.ps-name {
  font-family:'Nanum Myeongjo',serif; font-size:1.6rem;
  font-weight:400; color:#fff; line-height:1.15;
  margin-bottom:4px;
}
.ps-type {
  font-size:.78rem; color:rgba(255,255,255,.55); margin-bottom:20px;
}
.ps-feats { list-style:none; display:flex; flex-direction:column; gap:10px; margin-bottom:24px; }
.ps-feat {
  display:flex; align-items:flex-start; gap:10px;
  font-size:.82rem; color:rgba(255,255,255,.7); line-height:1.45;
}
.ps-check {
  width:18px; height:18px; border-radius:50%; flex-shrink:0;
  background:rgba(255,255,255,.12);
  display:flex; align-items:center; justify-content:center; margin-top:1px;
}
.ps-check i { font-size:9px; color:var(--yellow); }
.ps-divider { height:1px; background:rgba(255,255,255,.1); margin-bottom:20px; }

/* ── PRICE REVEAL ── */
.price-reveal-wrap {
  position:relative;
}
/* state: locked (form not filled) */
.price-locked {
  display:flex; align-items:center; gap:12px;
  padding:18px 20px;
  border-radius:var(--r-md);
  background:rgba(255,255,255,.07);
  border:1.5px dashed rgba(255,255,255,.18);
  cursor:default;
  transition:all .3s var(--smooth);
}
.price-locked.shake {
  animation:shake .4s var(--spring);
}
@keyframes shake {
  0%,100%{transform:translateX(0)}
  25%{transform:translateX(-5px)}
  75%{transform:translateX(5px)}
}
.lock-icon {
  width:38px; height:38px; border-radius:10px;
  background:rgba(255,255,255,.1);
  display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.lock-icon i { font-size:17px; color:rgba(255,255,255,.5); }
.lock-text p { font-size:.82rem; color:rgba(255,255,255,.65); line-height:1.4; }
.lock-text strong { color:rgba(255,255,255,.9); }

/* shimmer loading bar */
.price-shimmer {
  display:none;
  height:64px; border-radius:var(--r-md);
  background: linear-gradient(90deg,
    rgba(255,255,255,.05) 0%,
    rgba(255,255,255,.14) 40%,
    rgba(255,255,255,.05) 80%
  );
  background-size:200% 100%;
  animation:shimmer 1.4s linear infinite;
}

/* revealed price */
.price-revealed {
  display:none;
  flex-direction:column; gap:4px;
  padding:20px 22px;
  border-radius:var(--r-md);
  background:rgba(255,255,255,.09);
  border:1.5px solid rgba(4,169,167,.4);
  box-shadow:0 4px 20px rgba(4,169,167,.15);
  animation:revealPrice .55s var(--spring);
}
.price-revealed.show { display:flex; }
.pr-label {
  font-size:.65rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.08em;
  color:rgba(255,255,255,.5); margin-bottom:2px;
}
.pr-amount {
  font-family:'Nanum Myeongjo',serif;
  font-size:2.4rem; font-weight:400;
  color:var(--yellow); line-height:1; letter-spacing:-.03em;
}
.pr-cycle { font-size:.75rem; color:rgba(255,255,255,.5); margin-top:2px; }
.pr-breakdown {
  margin-top:12px; padding-top:12px;
  border-top:1px solid rgba(255,255,255,.1);
  display:flex; flex-direction:column; gap:5px;
}
.pr-row {
  display:flex; justify-content:space-between; align-items:center;
  font-size:.76rem; color:rgba(255,255,255,.55);
}
.pr-row.total { color:#fff; font-weight:600; font-size:.82rem; margin-top:4px; }

/* ── trust badges ── */
.trust-badges {
  display:grid; grid-template-columns:repeat(3,1fr); gap:10px;
}
.tb {
  background:#fff; border-radius:var(--r-md);
  border:1px solid rgba(26,58,47,.07);
  padding:14px 10px; text-align:center;
  display:flex; flex-direction:column; align-items:center; gap:7px;
  transition:transform .22s var(--spring), box-shadow .22s;
}
.tb:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(26,58,47,.09); }
.tb i { font-size:20px; color:var(--ocean); }
.tb span { font-size:.68rem; font-weight:600; color:var(--muted); line-height:1.3; text-align:center; }

/* ── add-ons section ── */
.addons-section { margin-bottom:28px; }
.addons-intro {
  font-size:.85rem; color:var(--muted);
  line-height:1.65; margin-bottom:14px;
}
.addons-intro strong { color:var(--ocean); }
.addon-list { display:flex; flex-direction:column; gap:10px; }
.addon-item {
  display:flex; align-items:center; gap:12px;
  padding:13px 16px;
  border:1.5px solid rgba(26,58,47,.1);
  border-radius:var(--r-md);
  cursor:pointer;
  transition:border-color .2s, background .2s, transform .18s var(--spring);
  -webkit-tap-highlight-color:transparent;
  user-select:none;
}
.addon-item:hover { border-color:rgba(26,58,47,.25); background:rgba(26,58,47,.03); }
.addon-item.selected {
  border-color:var(--ocean);
  background:rgba(26,58,47,.05);
}
.addon-cb {
  width:20px; height:20px; flex-shrink:0;
  border:2px solid rgba(26,58,47,.22);
  border-radius:5px;
  display:flex; align-items:center; justify-content:center;
  transition:all .2s var(--spring);
  background:#fff;
}
.addon-item.selected .addon-cb {
  background:var(--ocean); border-color:var(--ocean);
  animation:checkPop .3s var(--spring);
}
.addon-cb i { font-size:11px; color:#fff; opacity:0; transition:opacity .15s; }
.addon-item.selected .addon-cb i { opacity:1; }
.addon-name {
  font-size:.88rem; font-weight:500; color:var(--ocean); flex:1;
}
/* hidden checkbox — real input for form submission */
.addon-hidden { display:none; }

/* ── pay button ── */
.pay-btn-wrap { margin-top:4px; }
.pay-btn {
  width:100%; padding:17px 24px;
  background:var(--yellow); color:var(--ocean);
  border:none; border-radius:var(--r-md);
  font-family:'Cabin',sans-serif; font-size:1rem; font-weight:700;
  cursor:pointer; position:relative; overflow:hidden;
  display:flex; align-items:center; justify-content:center; gap:10px;
  box-shadow:0 6px 24px rgba(4,169,167,.3);
  transition:transform .22s var(--spring), box-shadow .22s, background .18s, opacity .2s;
}
.pay-btn:hover { transform:translateY(-3px); box-shadow:0 10px 32px rgba(4,169,167,.45); background:var(--cream-d); }
.pay-btn:active { transform:scale(.97)!important; }
.pay-btn:disabled { opacity:.55; cursor:not-allowed; transform:none!important; }
.pay-btn i { font-size:18px; }
.pay-btn-hint {
  display:flex; align-items:center; justify-content:center; gap:6px;
  font-size:.72rem; color:var(--muted); margin-top:10px;
}
.pay-btn-hint i { font-size:13px; color:var(--ocean); }

/* ── form submit in card ── */
.form-submit-area { margin-top:28px; }
.continue-btn {
  width:100%; padding:16px 24px;
  background:var(--ocean); color:#fff;
  border:none; border-radius:var(--r-md);
  font-family:'Cabin',sans-serif; font-size:.97rem; font-weight:700;
  cursor:pointer; position:relative; overflow:hidden;
  display:flex; align-items:center; justify-content:center; gap:10px;
  box-shadow:0 4px 18px rgba(26,58,47,.22);
  transition:transform .22s var(--spring), box-shadow .22s, opacity .2s;
}
.continue-btn:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(26,58,47,.3); }
.continue-btn:disabled { opacity:.5; cursor:not-allowed; transform:none!important; }

/* validation errors from Laravel */
.v-error { font-size:.75rem; color:#e53935; margin-top:4px; display:flex; align-items:center; gap:5px; }
.v-error i { font-size:12px; }

/* success flash */
.field-success .field-input,
.field-success .field-select {
  border-color:rgba(4,169,167,.5);
}

/* ── floating tip card ── */
.tip-card {
  background:#fff;
  border-radius:var(--r-lg);
  border:1px solid rgba(26,58,47,.07);
  box-shadow:0 8px 24px rgba(26,58,47,.07);
  padding:20px 22px;
  display:flex; gap:14px; align-items:flex-start;
  animation:fadeUp .5s .4s var(--out) both;
}
.tip-ico {
  width:40px; height:40px; border-radius:12px; flex-shrink:0;
  background:rgba(26,58,47,.06);
  display:flex; align-items:center; justify-content:center;
}
.tip-ico i { font-size:18px; color:var(--ocean); }
.tip-text p { font-size:.82rem; color:var(--muted); line-height:1.55; }
.tip-text strong { color:var(--ocean); }

/* ── mobile: summary comes after form ── */
@media(max-width:899px){
  .booking-grid { grid-template-columns:1fr; }
  .summary-col { order:2; }
  .form-card   { order:1; }
}

/* ─── Paystack modal override (keep it on top) ─── */
.paystack-backdrop { z-index:9999!important; }
</style>
@endpush

@section('content')

<div class="booking-page">
  <div class="glow glow-1" aria-hidden="true"></div>
  <div class="glow glow-2" aria-hidden="true"></div>

  <div class="wrap">

    {{-- ── PAGE HEADER ── --}}
    <div style="text-align:center; margin-bottom:40px; animation:fadeUp .6s var(--out) both;">
      <span class="pill pill-indigo" style="margin-bottom:16px;">
        <span class="pill-dot"></span>
        Begin Your Care Journey
      </span>
      <h1 class="sec-title">
        You're moments away from<br>
        <em>compassionate care</em>
      </h1>
      <p style="font-size:1rem; color:var(--muted); margin-top:12px; max-width:440px; margin-left:auto; margin-right:auto; line-height:1.7;">
        Fill in a few quick details, see your price instantly, and get started today.
      </p>
    </div>

    {{-- ── STEP INDICATOR ── --}}
    <div class="steps-bar" role="list" aria-label="Booking steps">
      <div class="step-node active" id="stepNode1" role="listitem">
        <div class="step-circle"><span class="step-num">1</span></div>
        <span class="step-lbl">Your Details</span>
      </div>
      <div class="step-node" id="stepNode2" role="listitem">
        <div class="step-circle"><span class="step-num">2</span></div>
        <span class="step-lbl">See Price</span>
      </div>
      <div class="step-node" id="stepNode3" role="listitem">
        <div class="step-circle"><span class="step-num">3</span></div>
        <span class="step-lbl">Pay & Go</span>
      </div>
    </div>

    {{-- ── MAIN GRID ── --}}
    <div class="booking-grid">

      {{-- ═══════════════ FORM CARD ═══════════════ --}}
      <div class="form-card">
        <form id="bookingForm" novalidate>
          @csrf
          <input type="hidden" name="care_plan_id" value="{{ $carePlan->id }}">

          {{-- SECTION 1: Personal Info --}}
          <div class="form-section">
            <div class="form-section-label">
              <div class="fsl-ico"><i class="bi bi-person"></i></div>
              Your Information
            </div>

            <div class="field-group" id="fg-name">
              <label class="field-label" for="name">Full Name <span>*</span></label>
              <input
                type="text" id="name" name="name"
                class="field-input" placeholder="e.g. Amina Okafor"
                autocomplete="name" required
              >
              @error('name')<p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>@enderror
            </div>

            <div class="field-row">
              <div class="field-group" id="fg-email">
                <label class="field-label" for="email">Email Address <span>*</span></label>
                <input
                  type="email" id="email" name="email"
                  class="field-input" placeholder="you@email.com"
                  autocomplete="email" required
                >
                @error('email')<p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>@enderror
              </div>
              <div class="field-group" id="fg-phone">
                <label class="field-label" for="phone">Phone Number <span>*</span></label>
                <input
                  type="tel" id="phone" name="phone"
                  class="field-input" placeholder="+234 800 000 0000"
                  autocomplete="tel" required
                >
                @error('phone')<p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>@enderror
              </div>
            </div>
          </div>

          {{-- SECTION 2: Patient Info --}}
          <div class="form-section">
            <div class="form-section-label">
              <div class="fsl-ico"><i class="bi bi-heart-pulse"></i></div>
              Patient Information
            </div>

            <div class="field-group" id="fg-patient_name">
              <label class="field-label" for="patient_name">Patient's Full Name <span>*</span></label>
              <input
                type="text" id="patient_name" name="patient_name"
                class="field-input" placeholder="Who will be receiving care?"
                required
              >
              <p class="field-hint">Enter your own name if you are the patient.</p>
              @error('patient_name')<p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>@enderror
            </div>

            <div class="field-row">
              <div class="field-group" id="fg-patient_age">
                <label class="field-label" for="patient_age">Patient Age <span>*</span></label>
                <input
                  type="number" id="patient_age" name="patient_age"
                  class="field-input" placeholder="e.g. 72"
                  min="1" max="120" required
                >
                @error('patient_age')<p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>@enderror
              </div>
              <div class="field-group" id="fg-care_type_id">
                <label class="field-label" for="care_type_id">Care Type <span>*</span></label>
                <div class="select-wrap">
                  <select id="care_type_id" name="care_type_id" class="field-select" required>
                    <option value="">Select type…</option>
                    @foreach($careTypes as $type)
                      <option value="{{ $type->id }}" data-price="{{ $type->price ?? 0 }}">
                        {{ $type->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
                @error('care_type_id')<p class="v-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</p>@enderror
              </div>
            </div>

            <div class="field-group" id="fg-notes">
              <label class="field-label" for="notes">Special Notes <span style="font-weight:400; color:var(--muted); font-size:.75rem;">(optional)</span></label>
              <textarea
                id="notes" name="notes" rows="3"
                class="field-input" placeholder="Any conditions, allergies or specific care requirements…"
                style="resize:vertical; min-height:80px;"
              ></textarea>
            </div>

          </div>

          {{-- SECTION 3: Add-Ons --}}
          @if($addOns->isNotEmpty())
          <div class="form-section addons-section">
            <div class="form-section-label">
              <div class="fsl-ico"><i class="bi bi-plus-circle"></i></div>
              Additional Services
            </div>

            <p class="addons-intro">
              Our healthcare professionals handle all of your loved one's core care needs.
              <strong>Is there anything more you'd like them to take care of?</strong>
            </p>

            <div class="addon-list" id="addonList">
              @foreach($addOns as $addOn)
              <label class="addon-item" id="addon-label-{{ $addOn->id }}" data-price="{{ $addOn->price }}">
                <div class="addon-cb">
                  <i class="bi bi-check"></i>
                </div>
                <span class="addon-name">{{ $addOn->name }}</span>
                <input
                  type="checkbox"
                  class="addon-hidden"
                  name="add_on_ids[]"
                  value="{{ $addOn->id }}"
                  data-price="{{ $addOn->price }}"
                >
              </label>
              @endforeach
            </div>
          </div>
          @endif

          {{-- ── SUBMIT BUTTON (mobile: shows below form, above summary) ── --}}
          <div class="form-submit-area" id="formSubmitArea">
            <button type="button" id="revealBtn" class="continue-btn" disabled>
              <i class="bi bi-eye"></i>
              <span id="revealBtnText">Fill all fields to see your price</span>
            </button>
            <p style="font-size:.73rem; color:var(--muted); text-align:center; margin-top:8px;">
              <i class="bi bi-shield-check" style="color:var(--ocean);"></i>
              Your information is encrypted and never shared.
            </p>
          </div>

        </form>
      </div>

      {{-- ═══════════════ RIGHT COLUMN: PLAN SUMMARY ═══════════════ --}}
      <div class="summary-col">

        {{-- plan summary card --}}
        <div class="plan-summary">
          <p class="ps-eyebrow">Selected Plan</p>
          <h2 class="ps-name">{{ $carePlan->name }}</h2>
          <p class="ps-type" id="selectedCareType">Choose a care type to continue</p>

          {{-- Plan features pulled from the care plan model --}}
          @if($carePlan->features && count($carePlan->features))
          <ul class="ps-feats">
            @foreach(array_slice($carePlan->features, 0, 5) as $feat)
            <li class="ps-feat">
              <span class="ps-check"><i class="bi bi-check"></i></span>
              {{ $feat }}
            </li>
            @endforeach
          </ul>
          @else
          <ul class="ps-feats">
            <li class="ps-feat"><span class="ps-check"><i class="bi bi-check"></i></span>Professional, licensed caregivers</li>
            <li class="ps-feat"><span class="ps-check"><i class="bi bi-check"></i></span>24/7 support line</li>
            <li class="ps-feat"><span class="ps-check"><i class="bi bi-check"></i></span>Personalised care schedule</li>
            <li class="ps-feat"><span class="ps-check"><i class="bi bi-check"></i></span>Weekly progress reports</li>
          </ul>
          @endif

          <div class="ps-divider"></div>

          {{-- PRICE REVEAL AREA --}}
          <div class="price-reveal-wrap" id="priceRevealWrap">

            {{-- locked state --}}
            <div class="price-locked" id="priceLocked">
              <div class="lock-icon"><i class="bi bi-lock-fill" id="lockIcon"></i></div>
              <div class="lock-text">
                <p><strong>Your price is ready to reveal.</strong><br>Complete the form and click "See Price".</p>
              </div>
            </div>

            {{-- shimmer --}}
            <div class="price-shimmer" id="priceShimmer"></div>

            {{-- revealed price --}}
            <div class="price-revealed" id="priceRevealed">
              <p class="pr-label">Your Quoted Price</p>
              <p class="pr-amount" id="priceAmount">₦0</p>
              <p class="pr-cycle" id="priceCycle">per month</p>
              <div class="pr-breakdown">
                <div class="pr-row">
                  <span>Plan</span>
                  <span>{{ $carePlan->name }}</span>
                </div>
                <div class="pr-row" id="brkCareType">
                  <span>Care Type</span>
                  <span id="brkCareTypeVal">—</span>
                </div>
                <div class="pr-row" id="brkAddOns" style="display:none;">
                  <span>Add-ons</span>
                  <span id="brkAddOnsVal">—</span>
                </div>
                <div class="pr-row total" id="brkTotal">
                  <span>Total</span>
                  <span id="brkTotalVal">—</span>
                </div>
              </div>
            </div>

          </div>

          {{-- PAY BUTTON (shown after price reveal) --}}
          <div class="pay-btn-wrap" id="payBtnWrap" style="display:none; margin-top:16px;">
            <!-- <button type="button" id="payBtn" class="pay-btn">
              <i class="bi bi-credit-card-2-front"></i>
              Pay Now & Get Started
            </button> -->
            <p class="pay-btn-hint">
              <i class="bi bi-shield-lock"></i>
              Secured by Paystack · SSL encrypted
            </p>
          </div>
        </div>

        {{-- trust badges --}}
        <div class="trust-badges reveal">
          <div class="tb">
            <i class="bi bi-patch-check-fill"></i>
            <span>Licensed &amp; Insured</span>
          </div>
          <div class="tb">
            <i class="bi bi-people-fill"></i>
            <span>Verified Caregivers</span>
          </div>
          <div class="tb">
            <i class="bi bi-clock-fill"></i>
            <span>24/7 Support</span>
          </div>
        </div>

        {{-- tip card --}}
        <div class="tip-card">
          <div class="tip-ico"><i class="bi bi-stars"></i></div>
          <div class="tip-text">
            <p><strong>After payment</strong> your CavalCare account is created automatically. You'll be guided through a short onboarding to schedule your first care visit.</p>
          </div>
        </div>

      </div>
      {{-- end summary-col --}}
    </div>
    {{-- end booking-grid --}}

  </div>
</div>

{{-- No hidden form needed — submission is handled via fetch() --}}

@endsection

@push('scripts')
{{-- Paystack inline JS --}}
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

  /* ── CONFIG ─────────────────────────────────────── */
  const PAYSTACK_KEY = '{{ config("services.paystack.public_key", "pk_test_xxxxxxxxxxxxxxxxxxxxxxxx") }}';
  const PLAN_BASE_PRICE = {{ (int) ($carePlan->price ?? 0) }};  // in kobo or naira – adjust as needed

  /* ── DOM refs ──────────────────────────────────── */
  const form        = document.getElementById('bookingForm');
  const finalForm   = document.getElementById('finalForm');
  const revealBtn   = document.getElementById('revealBtn');
  const revealText  = document.getElementById('revealBtnText');
  const payBtn      = document.getElementById('payBtn');
  const payBtnWrap  = document.getElementById('payBtnWrap');

  const priceLocked   = document.getElementById('priceLocked');
  const priceShimmer  = document.getElementById('priceShimmer');
  const priceRevealed = document.getElementById('priceRevealed');
  const priceAmount   = document.getElementById('priceAmount');
  const priceCycle    = document.getElementById('priceCycle');
  const brkCareTypeVal= document.getElementById('brkCareTypeVal');
  const brkTotalVal   = document.getElementById('brkTotalVal');
  const brkAddOns     = document.getElementById('brkAddOns');
  const brkAddOnsVal  = document.getElementById('brkAddOnsVal');
  const selectedType  = document.getElementById('selectedCareType');

  const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  const stepNodes = [
    document.getElementById('stepNode1'),
    document.getElementById('stepNode2'),
    document.getElementById('stepNode3'),
  ];

  /* ── Required fields list ───────────────────────── */
  const required = ['name', 'email', 'phone', 'patient_name', 'patient_age', 'care_type_id'];

  /* ── Helpers ────────────────────────────────────── */
  const fmt = n => '₦' + Number(n).toLocaleString('en-NG');

  function getVal(id) {
    return document.getElementById(id)?.value.trim() ?? '';
  }

  function isFormFilled() {
    return required.every(id => getVal(id) !== '' && getVal(id) !== '0');
  }

  function getPrice() {
    const sel = document.getElementById('care_type_id');
    const opt = sel?.options[sel.selectedIndex];
    const typePrice = parseInt(opt?.dataset.price ?? 0);
    return PLAN_BASE_PRICE > 0 ? PLAN_BASE_PRICE : typePrice;
  }

  function getAddOnsTotal() {
    return Array.from(document.querySelectorAll('.addon-hidden:checked'))
      .reduce((sum, cb) => sum + parseInt(cb.dataset.price ?? 0), 0);
  }

  function getSelectedAddOnIds() {
    return Array.from(document.querySelectorAll('.addon-hidden:checked'))
      .map(cb => parseInt(cb.value));
  }

  /* ── Validate a single field ────────────────────── */
  function validateField(id) {
    const el = document.getElementById(id);
    if (!el) return true;
    const fg = document.getElementById('fg-' + id);
    const isValid = el.checkValidity() && el.value.trim() !== '';
    el.classList.toggle('error', !isValid);
    if (fg) fg.classList.toggle('field-success', isValid);
    return isValid;
  }

  /* ── Live validation ────────────────────────────── */
  required.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input',  () => { validateField(id); updateRevealBtn(); });
    el.addEventListener('change', () => { validateField(id); updateRevealBtn(); updateTypeLabel(); });
    el.addEventListener('blur',   () => validateField(id));
  });

  /* ── Add-on checkbox toggle ─────────────────────── */
  document.querySelectorAll('.addon-item').forEach(label => {
    label.addEventListener('click', () => {
      const cb = label.querySelector('.addon-hidden');
      cb.checked = !cb.checked;
      label.classList.toggle('selected', cb.checked);
      updateRevealBtn();
    });
  });

  function updateTypeLabel() {
    const sel = document.getElementById('care_type_id');
    const opt = sel?.options[sel.selectedIndex];
    if (opt?.value) {
      selectedType.textContent = opt.text;
    } else {
      selectedType.textContent = 'Choose a care type to continue';
    }
  }

  /* ── Reveal button state ────────────────────────── */
  function updateRevealBtn() {
    const filled = isFormFilled();
    revealBtn.disabled = !filled;
    revealText.textContent = filled ? 'See My Price' : 'Fill all fields to see your price';
    if (filled) {
      revealBtn.querySelector('i').className = 'bi bi-eye';
    }
  }

  /* ── Step tracker ───────────────────────────────── */
  function setStep(n) {
    // n = 1-based step that is NOW active
    stepNodes.forEach((node, i) => {
      node.classList.remove('active', 'done');
      if (i + 1 < n) node.classList.add('done');
      if (i + 1 === n) node.classList.add('active');
    });
  }

  /* ── Reveal price flow ──────────────────────────── */
  revealBtn.addEventListener('click', () => {
    if (!isFormFilled()) return;

    // Validate all before revealing
    const allValid = required.every(id => validateField(id));
    if (!allValid) return;

    setStep(2);

    // Show shimmer for realism
    priceLocked.style.display  = 'none';
    priceShimmer.style.display = 'block';

    setTimeout(() => {
      const price      = getPrice();
      const addOnsTotal = getAddOnsTotal();
      const total      = price + addOnsTotal;
      const sel        = document.getElementById('care_type_id');
      const careTypeName = sel?.options[sel.selectedIndex]?.text ?? '—';

      priceAmount.textContent    = fmt(total);
      brkCareTypeVal.textContent = careTypeName;
      brkTotalVal.textContent    = fmt(total);
      priceCycle.textContent     = 'per month · billed monthly';

      // Show add-ons row in breakdown only if something was selected
      if (addOnsTotal > 0) {
        brkAddOnsVal.textContent = fmt(addOnsTotal);
        brkAddOns.style.display  = 'flex';
      } else {
        brkAddOns.style.display  = 'none';
      }

      priceShimmer.style.display = 'none';
      priceRevealed.classList.add('show');
      payBtnWrap.style.display   = 'block';

      // Scroll to price on mobile
      if (window.innerWidth < 900) {
        document.querySelector('.plan-summary')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      // Update reveal button
      revealBtn.innerHTML = '<i class="bi bi-check-circle"></i> Details Confirmed';
      revealBtn.style.background = 'rgba(26,58,47,.08)';
      revealBtn.style.color      = 'var(--ocean)';
      revealBtn.style.boxShadow  = 'none';
      revealBtn.disabled         = true;

    }, 900);
  });

  /* ── Paystack payment ───────────────────────────── */
  let pbReference = null;

  payBtn.addEventListener('click', async () => {
    const price       = getPrice();
    const addOnsTotal = getAddOnsTotal();
    const total       = price + addOnsTotal;
    const email       = getVal('email');
    const name        = getVal('name');

    payBtn.disabled = true;
    payBtn.innerHTML = '<i class="bi bi-arrow-clockwise" style="animation:spin .8s linear infinite;display:inline-block;"></i> Please wait…';

    try {
      const initiateRes = await fetch('{{ route("booking.initiate") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          name:         getVal('name'),
          email:        getVal('email'),
          phone:        getVal('phone'),
          care_plan_id: getVal('care_plan_id') || '{{ $carePlan->id }}',
          care_type_id: getVal('care_type_id'),
          add_on_ids:   getSelectedAddOnIds(),
        }),
      });

      if (!initiateRes.ok) {
        const err = await initiateRes.json().catch(() => ({}));
        throw new Error(err.message ?? 'Could not initiate booking. Please try again.');
      }

      const initiateData = await initiateRes.json();
      pbReference = initiateData.reference;

    } catch (err) {
      payBtn.disabled = false;
      payBtn.innerHTML = '<i class="bi bi-credit-card-2-front"></i> Pay Now & Get Started';
      alert(err.message);
      return;
    }

    setStep(3);

    const handler = PaystackPop.setup({
      key:      PAYSTACK_KEY,
      email:    email,
      amount:   total * 100,   // total includes add-ons, in kobo
      currency: 'NGN',
      ref:      'CC-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7).toUpperCase(),
      metadata: {
        custom_fields: [
          { display_name: 'Customer Name', variable_name: 'name',         value: name },
          { display_name: 'Care Plan',     variable_name: 'care_plan',    value: '{{ $carePlan->name }}' },
          { display_name: 'PB Reference',  variable_name: 'pb_reference', value: pbReference },
        ],
      },

      onClose() {
        setStep(2);
        payBtn.disabled = false;
        payBtn.innerHTML = '<i class="bi bi-credit-card-2-front"></i> Pay Now & Get Started';
      },

      callback(response) {
        payBtn.innerHTML = '<i class="bi bi-arrow-clockwise" style="animation:spin .8s linear infinite;display:inline-block;"></i> Verifying payment…';

        (async () => {
          try {
            const storeRes = await fetch('{{ route("booking.store") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
              },
              body: JSON.stringify({
                paystack_reference:    response.reference,
                pre_booking_reference: pbReference,
              }),
            });

            const storeData = await storeRes.json();

            if (storeRes.ok && storeData.success) {
              window.location.href = storeData.redirect;
            } else {
              setStep(2);
              payBtn.disabled = false;
              payBtn.innerHTML = '<i class="bi bi-credit-card-2-front"></i> Try Again';
              alert(storeData.message ?? 'Payment could not be verified. Please contact support.');
            }
          } catch (err) {
            setStep(2);
            payBtn.disabled = false;
            payBtn.innerHTML = '<i class="bi bi-credit-card-2-front"></i> Try Again';
            alert('Something went wrong confirming your payment. Please contact us on WhatsApp.');
          }
        })();
      },
    });

    handler.openIframe();
  });

  /* ── Ripple on buttons ──────────────────────────── */
  [revealBtn, payBtn].forEach(btn => {
    btn.addEventListener('pointerdown', e => {
      const r    = document.createElement('span');
      const rect = btn.getBoundingClientRect();
      const sz   = Math.max(rect.width, rect.height) * 1.4;
      r.className = 'btn-ripple';
      r.style.cssText = `
        width:${sz}px; height:${sz}px;
        left:${e.clientX - rect.left - sz/2}px;
        top:${e.clientY - rect.top  - sz/2}px;
      `;
      btn.appendChild(r);
      r.addEventListener('animationend', () => r.remove());
    });
  });

  /* ── Scroll reveal ──────────────────────────────── */
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); } });
  }, { threshold:.1 });
  document.querySelectorAll('.reveal').forEach(el => io.observe(el));

  /* ── Parallax glows (desktop only) ─────────────── */
  if (window.matchMedia('(min-width:900px) and (hover:hover)').matches) {
    document.addEventListener('mousemove', e => {
      const cx = e.clientX / window.innerWidth - .5;
      const cy = e.clientY / window.innerHeight - .5;
      document.querySelectorAll('.booking-page .glow').forEach((g, i) => {
        g.style.transform = `translate(${cx*(i+1)*14}px, ${cy*(i+1)*14}px)`;
      });
    }, { passive:true });
  }

  /* ── Initialise ─────────────────────────────────── */
  updateRevealBtn();
  setStep(1);
});
</script>

<style>
@keyframes spin { to { transform:rotate(360deg); } }
</style>
@endpush