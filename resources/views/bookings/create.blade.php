{{-- resources/views/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Book ' . $careType->name . ' — CavalCare')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700;800&family=Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1111010055204698');
fbq('track', 'PageView');

// InitiateCheckout — fired as soon as the booking page for this care type loads.
// No plan is selected yet, so we report the care type's cheapest plan as the
// starting value/context; the actual Purchase value is set later at received.blade.php.
fbq('track', 'InitiateCheckout', {
  content_name:  @json($careType->name),
  content_type:  'product',
  currency:      'NGN',
  value:         {{ $careType->carePlans->min('price') ?? 0 }}
});
</script>
<noscript>
  <img height="1" width="1" style="display:none"
       src="https://www.facebook.com/tr?id=1111010055204698&ev=PageView&noscript=1" />
</noscript>
<!-- End Meta Pixel Code -->

<style>
/* ═══════════════════════════════════════════════
   TOKENS
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
@keyframes ripple  { to{transform:scale(2.4);opacity:0} }
@keyframes revealPrice { 0%{opacity:0;transform:translateY(16px) scale(.96)} 100%{opacity:1;transform:none} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes pulse   { 0%,100%{box-shadow:0 0 0 0 rgba(26,58,47,.2)} 60%{box-shadow:0 0 0 10px rgba(26,58,47,0)} }
@keyframes checkPop { 0%{transform:scale(0)} 70%{transform:scale(1.2)} 100%{transform:scale(1)} }
@keyframes drawLine { from{transform:scaleX(0)} to{transform:scaleX(1)} }
@keyframes spin { to { transform:rotate(360deg); } }

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
.btn-ripple {
  position:absolute; border-radius:50%; pointer-events:none;
  background:rgba(255,255,255,.26);
  transform:scale(0); animation:ripple .6s ease-out forwards;
}

/* ══════════════════════════════════════════════
   PAGE LAYOUT
══════════════════════════════════════════════ */
.booking-page {
  min-height:100svh;
  background:var(--cream);
  padding-top:40px; padding-bottom:56px;
  position:relative; overflow:hidden;
}
@media(min-width:768px){ .booking-page{ padding-top:56px; } }
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
  max-width:360px; margin:0 auto 28px;
}
.step-node {
  display:flex; flex-direction:column; align-items:center; gap:6px;
  flex:1; position:relative;
}
.step-node::after {
  content:''; position:absolute;
  top:15px; left:calc(50% + 15px); right:calc(-50% + 15px);
  height:1.5px; background:rgba(26,58,47,.15); transition:background .4s;
}
.step-node:last-child::after { display:none; }
.step-node.done::after { background:var(--ocean); }
.step-circle {
  width:30px; height:30px; border-radius:50%;
  border:2px solid rgba(26,58,47,.18);
  display:flex; align-items:center; justify-content:center;
  font-size:.72rem; font-weight:700; color:var(--muted);
  background:#fff; transition:all .3s var(--spring);
  position:relative; z-index:2;
}
.step-node.active .step-circle {
  border-color:var(--ocean); background:var(--ocean); color:#fff;
  box-shadow:0 0 0 4px rgba(26,58,47,.1); animation:pulse 2s infinite;
}
.step-node.done .step-circle {
  border-color:var(--ocean); background:var(--ocean); color:#fff;
  animation:checkPop .4s var(--spring);
}
.step-node.done .step-circle::after { content:'✓'; font-size:.75rem; }
.step-node.done .step-num { display:none; }
.step-lbl {
  font-size:.62rem; font-weight:600;
  text-transform:uppercase; letter-spacing:.06em;
  color:var(--muted); text-align:center; white-space:nowrap;
}
.step-node.active .step-lbl { color:var(--ocean); }

/* ── two-column layout ── */
.booking-grid {
  display:grid; grid-template-columns:1fr;
  gap:24px; align-items:start;
  max-width:1000px; margin:0 auto;
}
@media(min-width:900px){
  .booking-grid { grid-template-columns:1fr 420px; gap:32px; }
}

/* ── form card ── */
.form-card {
  background:#fff; border-radius:var(--r-xl);
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
.field-label { font-size:.8rem; font-weight:600; color:var(--ocean); letter-spacing:.01em; }
.field-label span { color:#c0392b; margin-left:2px; }
.field-input, .field-select {
  width:100%; padding:13px 16px;
  border:1.5px solid rgba(26,58,47,.14); border-radius:var(--r-sm);
  font-family:'Cabin',sans-serif; font-size:.93rem;
  color:var(--ocean); background:#fff;
  transition:border-color .2s, box-shadow .2s, background .2s;
  appearance:none; -webkit-appearance:none; outline:none;
}
.field-input::placeholder { color:rgba(61,107,87,.38); }
.field-input:focus, .field-select:focus {
  border-color:var(--ocean); box-shadow:0 0 0 3px rgba(26,58,47,.09); background:#fafffe;
}
.field-input.error, .field-select.error {
  border-color:#e53935; box-shadow:0 0 0 3px rgba(229,57,53,.1);
}
.field-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@media(max-width:479px){ .field-row{ grid-template-columns:1fr; } }
.field-hint { font-size:.73rem; color:var(--muted); margin-top:3px; }
.form-section { margin-bottom:28px; }
.form-section:last-of-type { margin-bottom:0; }
.v-error { font-size:.75rem; color:#e53935; margin-top:4px; display:flex; align-items:center; gap:5px; }
.v-error i { font-size:12px; }
.field-success .field-input, .field-success .field-select { border-color:rgba(4,169,167,.5); }

/* ══════════════════════════════════════════════
   PLAN SELECTOR
══════════════════════════════════════════════ */
.plan-selector { display:grid; grid-template-columns:1fr; gap:10px; }
@media(min-width:560px){
  .plan-selector { grid-template-columns:1fr 1fr; }
}

.plan-option {
  display:flex; align-items:flex-start; gap:12px;
  padding:14px 16px;
  border:1.5px solid rgba(26,58,47,.12);
  border-radius:var(--r-md);
  cursor:pointer;
  transition:border-color .2s, background .2s, box-shadow .2s, transform .18s var(--spring);
  -webkit-tap-highlight-color:transparent;
  user-select:none;
  position:relative;
}
.plan-option:hover {
  border-color:rgba(26,58,47,.28);
  background:rgba(26,58,47,.02);
}
.plan-option.selected {
  border-color:var(--ocean);
  background:rgba(26,58,47,.04);
  box-shadow:0 4px 16px rgba(26,58,47,.1);
}
.plan-option input[type="radio"] { display:none; }

/* custom radio dot */
.plan-radio {
  width:20px; height:20px; flex-shrink:0; margin-top:2px;
  border:2px solid rgba(26,58,47,.25); border-radius:50%;
  background:#fff;
  display:flex; align-items:center; justify-content:center;
  transition:all .2s var(--spring);
}
.plan-option.selected .plan-radio {
  border-color:var(--ocean); background:var(--ocean);
  animation:checkPop .3s var(--spring);
}
.plan-radio::after {
  content:''; width:7px; height:7px; border-radius:50%;
  background:#fff; opacity:0; transition:opacity .15s;
}
.plan-option.selected .plan-radio::after { opacity:1; }

.plan-info { flex:1; }
.plan-name {
  font-size:.93rem; font-weight:600; color:var(--ocean);
  margin-bottom:3px; line-height:1.3;
}
.plan-desc {
  font-size:.78rem; color:var(--muted);
  line-height:1.55; margin-top:4px;
  display:none; /* toggled by JS when selected, or always show if you prefer */
}
.plan-option.selected .plan-desc { display:block; }

.plan-price-tag { display:none; } /* prices are calculated in the background and only revealed via "See My Price" */

/* ══════════════════════════════════════════════
   RIGHT COLUMN — PLAN SUMMARY CARD
══════════════════════════════════════════════ */
.summary-col {
  display:flex; flex-direction:column; gap:20px;
  animation:fadeUp .6s .25s var(--out) both;
}
.plan-summary {
  background:var(--ocean); border-radius:var(--r-xl);
  padding:28px 26px; position:relative; overflow:hidden;
  box-shadow:0 18px 48px rgba(26,58,47,.2);
}
.plan-summary::before {
  content:''; position:absolute; top:-40%; right:-30%;
  width:260px; height:260px; border-radius:50%;
  background:radial-gradient(circle,rgba(4,169,167,.18) 0%,transparent 65%);
  pointer-events:none;
}
.plan-summary::after {
  content:''; position:absolute; bottom:-30%; left:-20%;
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
  font-weight:400; color:#fff; line-height:1.15; margin-bottom:4px;
}
.ps-type { font-size:.78rem; color:rgba(255,255,255,.55); margin-bottom:20px; }
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

/* price reveal */
.price-reveal-wrap { position:relative; }
.price-locked {
  display:flex; align-items:center; gap:12px;
  padding:18px 20px; border-radius:var(--r-md);
  background:rgba(255,255,255,.07);
  border:1.5px dashed rgba(255,255,255,.18);
  transition:all .3s var(--smooth);
}
.lock-icon {
  width:38px; height:38px; border-radius:10px;
  background:rgba(255,255,255,.1);
  display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.lock-icon i { font-size:17px; color:rgba(255,255,255,.5); }
.lock-text p { font-size:.82rem; color:rgba(255,255,255,.65); line-height:1.4; }
.lock-text strong { color:rgba(255,255,255,.9); }
.price-shimmer {
  display:none; height:64px; border-radius:var(--r-md);
  background:linear-gradient(90deg,rgba(255,255,255,.05) 0%,rgba(255,255,255,.14) 40%,rgba(255,255,255,.05) 80%);
  background-size:200% 100%; animation:shimmer 1.4s linear infinite;
}
.price-revealed {
  display:none; flex-direction:column; gap:4px;
  padding:20px 22px; border-radius:var(--r-md);
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

/* trust badges */
.trust-badges { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
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

/* add-ons */
.addons-section { margin-bottom:28px; }
.addons-intro { font-size:.85rem; color:var(--muted); line-height:1.65; margin-bottom:14px; }
.addons-intro strong { color:var(--ocean); }
.addon-list { display:flex; flex-direction:column; gap:10px; overflow:hidden; max-height:0; transition:max-height .35s var(--out); }
.addon-list.open { max-height:2000px; margin-top:14px; }
.addons-toggle {
  display:flex; align-items:center; justify-content:space-between;
  width:100%; padding:13px 16px; cursor:pointer;
  background:rgba(26,58,47,.04); border:1.5px solid rgba(26,58,47,.1);
  border-radius:var(--r-md); font-family:'Cabin',sans-serif;
  font-size:.88rem; font-weight:600; color:var(--ocean);
}
.addons-toggle i.chev { transition:transform .3s var(--out); }
.addons-toggle.open i.chev { transform:rotate(180deg); }
.addons-count {
  font-size:.7rem; font-weight:700; color:#fff; background:var(--ocean);
  border-radius:100px; padding:2px 8px; margin-left:8px; display:none;
}
.addons-count.show { display:inline-block; }
.addon-item {
  display:flex; align-items:flex-start; gap:12px;
  padding:13px 16px;
  border:1.5px solid rgba(26,58,47,.1); border-radius:var(--r-md);
  cursor:pointer;
  transition:border-color .2s, background .2s, transform .18s var(--spring);
  -webkit-tap-highlight-color:transparent; user-select:none;
}
.addon-item:hover { border-color:rgba(26,58,47,.25); background:rgba(26,58,47,.03); }
.addon-item.selected { border-color:var(--ocean); background:rgba(26,58,47,.05); }
.addon-cb {
  width:20px; height:20px; flex-shrink:0;
  border:2px solid rgba(26,58,47,.22); border-radius:5px;
  display:flex; align-items:center; justify-content:center;
  transition:all .2s var(--spring); background:#fff; margin-top:1px;
}
.addon-item.selected .addon-cb { background:var(--ocean); border-color:var(--ocean); animation:checkPop .3s var(--spring); }
.addon-cb i { font-size:11px; color:#fff; opacity:0; transition:opacity .15s; }
.addon-item.selected .addon-cb i { opacity:1; }
.addon-text { flex:1; }
.addon-name { font-size:.88rem; font-weight:500; color:var(--ocean); }
.addon-desc { font-size:.75rem; color:var(--muted); margin-top:2px; line-height:1.45; }
.addon-price-tag { display:none; } /* price stays hidden until total is revealed */
.addon-hidden { display:none; }

/* pay / reveal buttons */
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
.pay-btn-hint {
  display:flex; align-items:center; justify-content:center; gap:6px;
  font-size:.72rem; color:var(--muted); margin-top:10px;
}
.pay-btn-hint i { font-size:13px; color:var(--ocean); }
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

/* tip card */
.tip-card {
  background:#fff; border-radius:var(--r-lg);
  border:1px solid rgba(26,58,47,.07);
  box-shadow:0 8px 24px rgba(26,58,47,.07);
  padding:20px 22px; display:flex; gap:14px; align-items:flex-start;
  animation:fadeUp .5s .4s var(--out) both;
}
.tip-ico {
  width:40px; height:40px; border-radius:12px; flex-shrink:0;
  background:rgba(26,58,47,.06); display:flex; align-items:center; justify-content:center;
}
.tip-ico i { font-size:18px; color:var(--ocean); }
.tip-text p { font-size:.82rem; color:var(--muted); line-height:1.55; }
.tip-text strong { color:var(--ocean); }

/* mobile */
@media(max-width:899px){
  .booking-grid { grid-template-columns:1fr; }
  .summary-col { order:2; }
  .form-card   { order:1; }
  .booking-page { padding-bottom:110px; } /* room for sticky bar */
}
.paystack-backdrop { z-index:9999!important; }

/* sticky mobile summary bar */
.mobile-bar {
  display:none;
  position:fixed; left:0; right:0; bottom:0; z-index:500;
  background:#fff; border-top:1px solid rgba(26,58,47,.1);
  box-shadow:0 -8px 24px rgba(26,58,47,.1);
  padding:12px 16px; align-items:center; gap:12px;
  transform:translateY(100%); transition:transform .35s var(--out);
}
.mobile-bar.show { transform:translateY(0); }
@media(max-width:899px){ .mobile-bar { display:flex; } }
.mobile-bar-price { flex:1; min-width:0; }
.mobile-bar-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
.mobile-bar-amount { font-family:'Nanum Myeongjo',serif; font-size:1.2rem; color:var(--ocean); line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.mobile-bar-btn {
  flex-shrink:0; padding:12px 18px; border:none; border-radius:var(--r-sm);
  background:var(--yellow); color:var(--ocean); font-weight:700; font-size:.85rem;
  display:flex; align-items:center; gap:6px; cursor:pointer;
}
.mobile-bar-btn:disabled { opacity:.5; }
</style>
@endpush

@section('content')

<div class="booking-page">
  <div class="glow glow-1" aria-hidden="true"></div>
  <div class="glow glow-2" aria-hidden="true"></div>

  <div class="wrap">

    {{-- PAGE HEADER --}}
    <div style="text-align:center; margin-bottom:24px; animation:fadeUp .6s var(--out) both;">
      <span class="pill pill-indigo" style="margin-bottom:16px;">
        <span class="pill-dot"></span>
        {{ $careType->name }}
      </span>
      <h1 class="sec-title">
        You're moments away from<br>
        <em>compassionate care</em>
      </h1>
      <p style="font-size:1rem; color:var(--muted); margin-top:12px; max-width:440px; margin-left:auto; margin-right:auto; line-height:1.7;">
        Choose your preferred plan, fill in a few quick details, and get started today.
      </p>
    </div>

    {{-- STEP INDICATOR --}}
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

    {{-- MAIN GRID --}}
    <div class="booking-grid">

      {{-- ═══════════ FORM CARD ═══════════ --}}
      <div class="form-card">
        <form id="bookingForm" novalidate>
          @csrf

          {{-- SECTION 1: Personal Info --}}
          <div class="form-section">
            <div class="form-section-label">
              <div class="fsl-ico"><i class="bi bi-person"></i></div>
              Your Information
            </div>

            <div class="field-group" id="fg-name">
              <label class="field-label" for="name">Full Name <span>*</span></label>
              <input type="text" id="name" name="name" class="field-input"
                placeholder="e.g. Amina Okafor" autocomplete="name" required>
            </div>

            <div class="field-row">
              <div class="field-group" id="fg-email">
                <label class="field-label" for="email">Email Address <span>*</span></label>
                <input type="email" id="email" name="email" class="field-input"
                  placeholder="you@email.com" autocomplete="email" required>
              </div>
              <div class="field-group" id="fg-phone">
                <label class="field-label" for="phone">Phone Number <span>*</span></label>
                <input type="tel" id="phone" name="phone" class="field-input"
                  placeholder="+234 800 000 0000" autocomplete="tel" required>
              </div>
            </div>
          </div>

          {{-- SECTION 2: Plan Selection --}}
          <div class="form-section">
            <div class="form-section-label">
              <div class="fsl-ico"><i class="bi bi-clipboard-heart"></i></div>
              Choose Your Plan
            </div>

            <div class="plan-selector" id="planSelector">
              @foreach($plans as $plan)
              <label class="plan-option" id="plan-option-{{ $plan->id }}"
                data-price="{{ $plan->price }}"
                data-cycle="{{ $plan->billing_label }}"
                data-name="{{ $plan->name }}"
                data-slug="{{ $plan->slug }}">

                <input type="radio" name="care_plan_id" value="{{ $plan->id }}" required>

                <div class="plan-radio"></div>

                <div class="plan-info">
                  <div class="plan-name">{{ $plan->name }}</div>
                  @if($plan->description)
                    <div class="plan-desc">{{ $plan->description }}</div>
                  @endif
                </div>

                <div class="plan-price-tag">
                  @if($plan->price_from ?? false)
                    <div class="plan-price-amt">from ₦{{ number_format($plan->price) }}</div>
                  @else
                    <div class="plan-price-amt">₦{{ number_format($plan->price) }}</div>
                  @endif
                  <span class="plan-price-cycle">{{ $plan->billing_label }}</span>
                </div>
              </label>
              @endforeach
            </div>
            <p id="planError" class="v-error" style="display:none; margin-top:8px;">
              <i class="bi bi-exclamation-circle"></i> Please select a plan to continue.
            </p>
          </div>

          {{-- SECTION 3: Add-Ons --}}
          @if($addOns->isNotEmpty())
          <div class="form-section addons-section">
            <div class="form-section-label">
              <div class="fsl-ico"><i class="bi bi-plus-circle"></i></div>
              Additional Services <span style="font-weight:400; text-transform:none; letter-spacing:0; font-size:.75rem; color:var(--muted);">(optional)</span>
            </div>

            <p class="addons-intro">
              Our nurses handle all core care needs.
              <strong>Would you like to add any specialist services?</strong>
            </p>

            <button type="button" class="addons-toggle" id="addonsToggle" aria-expanded="false">
              <span>Show optional add-ons <span class="addons-count" id="addonsCount">0</span></span>
              <i class="bi bi-chevron-down chev"></i>
            </button>

            <div class="addon-list" id="addonList">
              @foreach($addOns as $addOn)
              <label class="addon-item" id="addon-label-{{ $addOn->id }}" data-price="{{ $addOn->price }}">
                <div class="addon-cb"><i class="bi bi-check"></i></div>
                <div class="addon-text">
                  <div class="addon-name">{{ $addOn->name }}</div>
                  @if($addOn->description)
                    <div class="addon-desc">{{ $addOn->description }}</div>
                  @endif
                </div>
                <div class="addon-price-tag">{{ $addOn->formatted_price }}</div>
                <input type="checkbox" class="addon-hidden" name="add_on_ids[]"
                  value="{{ $addOn->id }}" data-price="{{ $addOn->price }}">
              </label>
              @endforeach
            </div>
          </div>
          @endif

          {{-- REVEAL BUTTON --}}
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
      {{-- end form card --}}

      {{-- ═══════════ RIGHT COLUMN ═══════════ --}}
      <div class="summary-col">

        <div class="plan-summary">
          <p class="ps-eyebrow">Care Category</p>
          <h2 class="ps-name" id="summaryPlanName">Select a plan</h2>
          <p class="ps-type">{{ $careType->name }}</p>

          <ul class="ps-feats">
            <li class="ps-feat"><span class="ps-check"><i class="bi bi-check"></i></span>Professional, licensed caregivers</li>
            <li class="ps-feat"><span class="ps-check"><i class="bi bi-check"></i></span>24/7 support line</li>
            <li class="ps-feat"><span class="ps-check"><i class="bi bi-check"></i></span>Personalised care schedule</li>
            <li class="ps-feat"><span class="ps-check"><i class="bi bi-check"></i></span>Weekly progress reports</li>
          </ul>

          <div class="ps-divider"></div>

          {{-- PRICE REVEAL --}}
          <div class="price-reveal-wrap" id="priceRevealWrap">
            <div class="price-locked" id="priceLocked">
              <div class="lock-icon"><i class="bi bi-lock-fill"></i></div>
              <div class="lock-text">
                <p><strong>Select a plan and fill your details</strong><br>to reveal your exact price.</p>
              </div>
            </div>
            <div class="price-shimmer" id="priceShimmer"></div>
            <div class="price-revealed" id="priceRevealed">
              <p class="pr-label">Your Quoted Price</p>
              <p class="pr-amount" id="priceAmount">₦0</p>
              <p class="pr-cycle" id="priceCycle"></p>
              <div class="pr-breakdown">
                <div class="pr-row">
                  <span>Plan</span>
                  <span id="brkPlanVal">—</span>
                </div>
                <div class="pr-row" id="brkAddOns" style="display:none;">
                  <span>Add-ons</span>
                  <span id="brkAddOnsVal">—</span>
                </div>
                <div class="pr-row total">
                  <span>Total</span>
                  <span id="brkTotalVal">—</span>
                </div>
              </div>
            </div>
          </div>

          {{-- PAY BUTTON --}}
          <div class="pay-btn-wrap" id="payBtnWrap" style="display:none; margin-top:16px;">
            <button type="button" id="payBtn" class="pay-btn">
              <i class="bi bi-credit-card-2-front"></i>
              Pay Now &amp; Get Started
            </button>
            <p class="pay-btn-hint">
              <i class="bi bi-shield-lock"></i>
              Secured by Paystack · SSL encrypted
            </p>
          </div>
        </div>

        {{-- trust badges --}}
        <div class="trust-badges reveal">
          <div class="tb"><i class="bi bi-patch-check-fill"></i><span>Licensed &amp; Insured</span></div>
          <div class="tb"><i class="bi bi-people-fill"></i><span>Verified Caregivers</span></div>
          <div class="tb"><i class="bi bi-clock-fill"></i><span>24/7 Support</span></div>
        </div>

        <div class="tip-card">
          <div class="tip-ico"><i class="bi bi-stars"></i></div>
          <div class="tip-text">
            <p><strong>After payment</strong> you'll receive an email with a private link to complete your loved one's care details — this helps us match the perfect nurse.</p>
          </div>
        </div>

      </div>
      {{-- end summary-col --}}
    </div>
    {{-- end booking-grid --}}

  </div>
</div>

{{-- STICKY MOBILE ACTION BAR --}}
<div class="mobile-bar" id="mobileBar">
  <div class="mobile-bar-price">
    <div class="mobile-bar-label" id="mobileBarLabel">Your price</div>
    <div class="mobile-bar-amount" id="mobileBarAmount">Fill your details to see it</div>
  </div>
  <button type="button" class="mobile-bar-btn" id="mobileBarBtn" disabled>
    <i class="bi bi-eye"></i> See My Price
  </button>
</div>

@endsection

@push('scripts')
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

  /* ── CONFIG ──────────────────────────────────────── */
  const PAYSTACK_KEY = '{{ config("services.paystack.public_key", "pk_test_xxxxxxxxxxxxxxxxxxxxxxxx") }}';

  /* ── DOM refs ────────────────────────────────────── */
  const revealBtn     = document.getElementById('revealBtn');
  const revealText    = document.getElementById('revealBtnText');
  const payBtn        = document.getElementById('payBtn');
  const payBtnWrap    = document.getElementById('payBtnWrap');
  const priceLocked   = document.getElementById('priceLocked');
  const priceShimmer  = document.getElementById('priceShimmer');
  const priceRevealed = document.getElementById('priceRevealed');
  const priceAmount   = document.getElementById('priceAmount');
  const priceCycle    = document.getElementById('priceCycle');
  const brkPlanVal    = document.getElementById('brkPlanVal');
  const brkTotalVal   = document.getElementById('brkTotalVal');
  const brkAddOns     = document.getElementById('brkAddOns');
  const brkAddOnsVal  = document.getElementById('brkAddOnsVal');
  const summaryName   = document.getElementById('summaryPlanName');
  const planError     = document.getElementById('planError');

  const mobileBar       = document.getElementById('mobileBar');
  const mobileBarLabel  = document.getElementById('mobileBarLabel');
  const mobileBarAmount = document.getElementById('mobileBarAmount');
  const mobileBarBtn    = document.getElementById('mobileBarBtn');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  const stepNodes = [
    document.getElementById('stepNode1'),
    document.getElementById('stepNode2'),
    document.getElementById('stepNode3'),
  ];

  /* ── Helpers ─────────────────────────────────────── */
  const fmt = n => '₦' + Number(n).toLocaleString('en-NG');
  const getVal = id => document.getElementById(id)?.value.trim() ?? '';

  function getSelectedPlan() {
    const radio = document.querySelector('input[name="care_plan_id"]:checked');
    if (!radio) return null;
    const label = radio.closest('.plan-option');
    return {
      id:    radio.value,
      price: parseInt(label?.dataset.price ?? 0),
      cycle: label?.dataset.cycle ?? '',
      name:  label?.dataset.name ?? '',
    };
  }

  function getAddOnsTotal() {
    return Array.from(document.querySelectorAll('.addon-hidden:checked'))
      .reduce((sum, cb) => sum + parseInt(cb.dataset.price ?? 0), 0);
  }

  function getSelectedAddOnIds() {
    return Array.from(document.querySelectorAll('.addon-hidden:checked'))
      .map(cb => parseInt(cb.value));
  }

  function isFormFilled() {
    const plan = getSelectedPlan();
    return (
      plan !== null &&
      getVal('name')  !== '' &&
      getVal('email') !== '' &&
      getVal('phone') !== ''
    );
  }

  /* ── Step tracker ────────────────────────────────── */
  function setStep(n) {
    stepNodes.forEach((node, i) => {
      node.classList.remove('active', 'done');
      if (i + 1 < n) node.classList.add('done');
      if (i + 1 === n) node.classList.add('active');
    });
  }

  /* ── Reveal button state ─────────────────────────── */
  function updateRevealBtn() {
    const filled = isFormFilled();
    revealBtn.disabled = !filled;
    revealText.textContent = filled ? 'See My Price' : 'Fill all fields to see your price';
    syncMobileBar();
  }

  /* ── Mobile sticky bar sync ──────────────────────── */
  function syncMobileBar() {
    if (!mobileBar) return;
    const revealed = priceRevealed.classList.contains('show');

    if (revealed) {
      mobileBar.classList.add('show');
      mobileBarLabel.textContent = 'Your total';
      mobileBarAmount.textContent = priceAmount.textContent + ' ' + priceCycle.textContent;
      mobileBarBtn.disabled = false;
      mobileBarBtn.innerHTML = '<i class="bi bi-credit-card-2-front"></i> Pay Now';
      return;
    }

    const filled = isFormFilled();
    mobileBar.classList.toggle('show', filled);
    mobileBarLabel.textContent = 'Your price';
    mobileBarAmount.textContent = filled ? 'Tap to reveal your price' : 'Fill your details to see it';
    mobileBarBtn.disabled = !filled;
    mobileBarBtn.innerHTML = '<i class="bi bi-eye"></i> See My Price';
  }

  mobileBarBtn?.addEventListener('click', () => {
    if (priceRevealed.classList.contains('show')) {
      payBtn.click();
    } else {
      revealBtn.click();
    }
  });

  /* ── Plan option selection ───────────────────────── */
  document.querySelectorAll('.plan-option').forEach(label => {
    label.addEventListener('click', () => {
      // deselect all
      document.querySelectorAll('.plan-option').forEach(l => l.classList.remove('selected'));
      // select clicked
      label.classList.add('selected');
      label.querySelector('input[type="radio"]').checked = true;
      // update summary name
      summaryName.textContent = label.dataset.name;
      planError.style.display = 'none';
      updateRevealBtn();
    });
  });

  /* ── Personal field live validation ─────────────── */
  ['name', 'email', 'phone'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input',  updateRevealBtn);
    el.addEventListener('change', updateRevealBtn);
    el.addEventListener('blur', () => {
      const fg = document.getElementById('fg-' + id);
      const valid = el.checkValidity() && el.value.trim() !== '';
      el.classList.toggle('error', !valid);
      if (fg) fg.classList.toggle('field-success', valid);
    });
  });

  /* ── Add-on toggle ───────────────────────────────── */
  document.querySelectorAll('.addon-item').forEach(label => {
    label.addEventListener('click', () => {
      const cb = label.querySelector('.addon-hidden');
      cb.checked = !cb.checked;
      label.classList.toggle('selected', cb.checked);
      updateRevealBtn();
      updateAddonsCount();
    });
  });

  /* ── Add-ons accordion (collapsed by default to cut down scrolling) ── */
  const addonsToggle = document.getElementById('addonsToggle');
  const addonList    = document.getElementById('addonList');
  const addonsCount  = document.getElementById('addonsCount');

  function updateAddonsCount() {
    if (!addonsCount) return;
    const n = document.querySelectorAll('.addon-hidden:checked').length;
    addonsCount.textContent = n;
    addonsCount.classList.toggle('show', n > 0);
  }

  if (addonsToggle && addonList) {
    addonsToggle.addEventListener('click', () => {
      const isOpen = addonList.classList.toggle('open');
      addonsToggle.classList.toggle('open', isOpen);
      addonsToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      addonsToggle.querySelector('span').firstChild.textContent = isOpen ? 'Hide optional add-ons ' : 'Show optional add-ons ';
    });
  }
  updateAddonsCount();

  /* ── Reveal price ────────────────────────────────── */
  revealBtn.addEventListener('click', () => {
    if (!isFormFilled()) return;

    const plan = getSelectedPlan();
    if (!plan) {
      planError.style.display = 'flex';
      return;
    }

    // Validate personal fields
    let allValid = true;
    ['name', 'email', 'phone'].forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      const valid = el.checkValidity() && el.value.trim() !== '';
      el.classList.toggle('error', !valid);
      if (!valid) allValid = false;
    });
    if (!allValid) return;

    setStep(2);

    priceLocked.style.display  = 'none';
    priceShimmer.style.display = 'block';

    setTimeout(() => {
      const addOnsTotal = getAddOnsTotal();
      const total       = plan.price + addOnsTotal;

      priceAmount.textContent = fmt(total);
      priceCycle.textContent  = plan.cycle;
      brkPlanVal.textContent  = plan.name;
      brkTotalVal.textContent = fmt(total);

      if (addOnsTotal > 0) {
        brkAddOnsVal.textContent = fmt(addOnsTotal);
        brkAddOns.style.display  = 'flex';
      } else {
        brkAddOns.style.display  = 'none';
      }

      priceShimmer.style.display = 'none';
      priceRevealed.classList.add('show');
      payBtnWrap.style.display   = 'block';

      if (window.innerWidth < 900) {
        document.querySelector('.plan-summary')?.scrollIntoView({ behavior:'smooth', block:'start' });
      }

      revealBtn.innerHTML = '<i class="bi bi-check-circle"></i> Details Confirmed';
      revealBtn.style.background = 'rgba(26,58,47,.08)';
      revealBtn.style.color      = 'var(--ocean)';
      revealBtn.style.boxShadow  = 'none';
      revealBtn.disabled         = true;

      syncMobileBar();
    }, 900);
  });

  /* ── Paystack payment ────────────────────────────── */
  let pbReference = null;

  payBtn.addEventListener('click', async () => {
    const plan        = getSelectedPlan();
    const addOnsTotal = getAddOnsTotal();
    const total       = (plan?.price ?? 0) + addOnsTotal;
    const email       = getVal('email');
    const name        = getVal('name');

    payBtn.disabled = true;
    payBtn.innerHTML = '<i class="bi bi-arrow-clockwise" style="animation:spin .8s linear infinite;display:inline-block;"></i> Please wait…';
    if (mobileBarBtn) { mobileBarBtn.disabled = true; mobileBarBtn.innerHTML = payBtn.innerHTML; }

    try {
      const initiateRes = await fetch('{{ route("booking.initiate") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          name:         name,
          email:        email,
          phone:        getVal('phone'),
          care_plan_id: plan?.id,
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
      amount:   total * 100,
      currency: 'NGN',
      ref:      'CC-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7).toUpperCase(),
      metadata: {
        custom_fields: [
          { display_name: 'Customer Name', variable_name: 'name',         value: name },
          { display_name: 'Care Plan',     variable_name: 'care_plan',    value: plan?.name ?? '' },
          { display_name: 'PB Reference',  variable_name: 'pb_reference', value: pbReference },
        ],
      },

      onClose() {
        setStep(2);
        payBtn.disabled = false;
        payBtn.innerHTML = '<i class="bi bi-credit-card-2-front"></i> Pay Now & Get Started';
        syncMobileBar();
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

            let storeData;
            try {
              storeData = await storeRes.json();
            } catch (parseErr) {
              throw new Error('The server took too long or returned an unexpected response. Please try again — if this keeps happening, contact us on WhatsApp.');
            }

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

  /* ── Ripple ──────────────────────────────────────── */
  [revealBtn, payBtn].forEach(btn => {
    if (!btn) return;
    btn.addEventListener('pointerdown', e => {
      const r    = document.createElement('span');
      const rect = btn.getBoundingClientRect();
      const sz   = Math.max(rect.width, rect.height) * 1.4;
      r.className = 'btn-ripple';
      r.style.cssText = `width:${sz}px;height:${sz}px;left:${e.clientX-rect.left-sz/2}px;top:${e.clientY-rect.top-sz/2}px;`;
      btn.appendChild(r);
      r.addEventListener('animationend', () => r.remove());
    });
  });

  /* ── Scroll reveal ───────────────────────────────── */
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); } });
  }, { threshold:.1 });
  document.querySelectorAll('.reveal').forEach(el => io.observe(el));

  /* ── Parallax glows ──────────────────────────────── */
  if (window.matchMedia('(min-width:900px) and (hover:hover)').matches) {
    document.addEventListener('mousemove', e => {
      const cx = e.clientX / window.innerWidth - .5;
      const cy = e.clientY / window.innerHeight - .5;
      document.querySelectorAll('.booking-page .glow').forEach((g, i) => {
        g.style.transform = `translate(${cx*(i+1)*14}px,${cy*(i+1)*14}px)`;
      });
    }, { passive:true });
  }

  /* ── Preselect plan from ?plan= query param ──────── */
  function preselectPlanFromQuery() {
    const params = new URLSearchParams(window.location.search);
    const planSlug = params.get('plan');
    if (!planSlug) return;

    const label = document.querySelector(`.plan-option[data-slug="${CSS.escape(planSlug)}"]`);
    if (!label) return;

    document.querySelectorAll('.plan-option').forEach(l => l.classList.remove('selected'));
    label.classList.add('selected');
    label.querySelector('input[type="radio"]').checked = true;
    summaryName.textContent = label.dataset.name;
    planError.style.display = 'none';

    label.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  /* ── Init ────────────────────────────────────────── */
  preselectPlanFromQuery();
  updateRevealBtn();
  setStep(1);
});
</script>
@endpush