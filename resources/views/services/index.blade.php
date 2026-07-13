{{-- resources/views/services.blade.php --}}
@extends('layouts.app')

@section('title', 'In-Home Care Services in Nigeria — CavalCare')

@push('head')
<meta name="description" content="Explore CavalCare's in-home healthcare services in Nigeria: elderly care, post-surgery recovery, post-natal care, and more. Licensed, verified healthcare professionals matched to your loved one's needs.">
<meta name="keywords" content="in-home healthcare services Nigeria, elderly care at home Nigeria, post-surgery home healthcare professional, post-natal care Nigeria, licensed home healthcare, CavalCare services">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url('/services') }}">

<meta property="og:type"        content="website">
<meta property="og:url"         content="{{ url('/services') }}">
<meta property="og:title"       content="In-Home Care Services in Nigeria — CavalCare">
<meta property="og:description" content="Elderly care, post-surgery recovery, post-natal support and more — all delivered by licensed, verified healthcare professionals in your home.">
<meta property="og:image"       content="{{ asset('images/og-services.jpg') }}">
<meta property="og:site_name"   content="CavalCare">

<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="In-Home Care Services — CavalCare Nigeria">
<meta name="twitter:description" content="Licensed healthcare professionals for elderly care, post-surgery recovery, post-natal care and more. Book online.">
<meta name="twitter:image"       content="{{ asset('images/og-services.jpg') }}">

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Service",
  "provider": {
    "@@type": "MedicalBusiness",
    "name": "CavalCare",
    "url": "{{ url('/') }}"
  },
  "name": "In-Home HealthCare Services",
  "description": "CavalCare provides licensed, verified healthcare professionals for elderly care, post-surgery recovery, post-natal support and specialist in-home healthcare across Nigeria.",
  "areaServed": "Nigeria",
  "url": "{{ url('/services') }}"
}
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700;800&family=Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ═══════════════════════════════════════
   TOKENS — inherited from home
═══════════════════════════════════════ */
:root {
  --cream:       #F0FFDB;
  --yellow:      #04A9A7;
  --ocean:       #1A3A2F;
  --indigo:      #04A9A7;
  --peach:       #FFCDAD;
  --cream-d:     #d4f5b8;
  --ocean-h:     #0f5c3d;
  --ocean-soft:  #e6f9f9;
  --text:        #1A3A2F;
  --muted:       #3d6b57;
  --r-sm: 10px; --r-md: 16px; --r-lg: 24px; --r-xl: 36px;
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
img { display:block; max-width:100%; }
:focus-visible { outline:2px solid var(--yellow); outline-offset:3px; border-radius:4px; }

/* ═══════════════════════════════════════
   KEYFRAMES
═══════════════════════════════════════ */
@keyframes fadeUp   { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:none} }
@keyframes fadeIn   { from{opacity:0} to{opacity:1} }
@keyframes drawLine { from{transform:scaleX(0)} to{transform:scaleX(1)} }
@keyframes floatY   { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-7px)} }
@keyframes pulse    { 0%,100%{box-shadow:0 0 0 0 rgba(26,58,47,.22)} 60%{box-shadow:0 0 0 9px rgba(26,58,47,0)} }
@keyframes ripple   { to{transform:scale(2.5);opacity:0} }
@keyframes slideIn  { from{opacity:0;transform:translateX(-20px)} to{opacity:1;transform:none} }
@keyframes marquee  { from{transform:translateX(0)} to{transform:translateX(-50%)} }

/* ═══════════════════════════════════════
   REVEAL
═══════════════════════════════════════ */
.reveal {
  opacity:0; transform:translateY(26px);
  transition:opacity .7s var(--out),transform .7s var(--out);
}
.reveal.is-visible { opacity:1; transform:none; }

/* ═══════════════════════════════════════
   LAYOUT
═══════════════════════════════════════ */
.wrap { max-width:1160px; margin:0 auto; padding:0 20px; }
@media(min-width:768px){ .wrap{ padding:0 32px; } }

/* ═══════════════════════════════════════
   PILLS / LABELS
═══════════════════════════════════════ */
.pill {
  display:inline-flex; align-items:center; gap:6px;
  font-size:.7rem; font-weight:600;
  letter-spacing:.07em; text-transform:uppercase;
  padding:5px 13px; border-radius:100px;
}
.pill-dot { width:5px; height:5px; border-radius:50%; background:currentColor; }
.pill-ocean  { background:rgba(26,58,47,.1);    color:var(--ocean); }
.pill-yellow { background:var(--yellow);        color:var(--ocean); }
.pill-indigo { background:rgba(4,169,167,.13); color:var(--indigo); }
.pill-peach  { background:rgba(255,205,173,.35);color:#7a4016; }
.pill-white  { background:rgba(255,255,255,.15);color:#fff; }

/* ═══════════════════════════════════════
   BUTTONS
═══════════════════════════════════════ */
.btn {
  display:inline-flex; align-items:center; gap:9px;
  font-family:'Cabin',sans-serif; font-size:.93rem; font-weight:600;
  text-decoration:none; border:none; cursor:pointer;
  position:relative; overflow:hidden;
  transition:transform .22s var(--spring),box-shadow .22s,background .18s,color .18s,border-color .18s;
  -webkit-tap-highlight-color:transparent;
}
.btn:active { transform:scale(.96)!important; }
.btn-ripple {
  position:absolute; border-radius:50%; pointer-events:none;
  background:rgba(255,255,255,.25); transform:scale(0);
  animation:ripple .6s ease-out forwards;
}
.btn-primary { background:var(--ocean);color:#fff;padding:14px 26px;border-radius:var(--r-sm);box-shadow:0 4px 16px rgba(26,58,47,.22); }
.btn-primary:hover { transform:translateY(-3px);box-shadow:0 8px 28px rgba(26,58,47,.32); }
.btn-yellow  { background:var(--yellow);color:var(--ocean);padding:14px 26px;border-radius:var(--r-sm);box-shadow:0 4px 16px rgba(4,169,167,.22); }
.btn-yellow:hover  { background:var(--cream-d);transform:translateY(-3px);box-shadow:0 8px 28px rgba(4,169,167,.35); }
.btn-outline { background:transparent;color:var(--ocean);padding:13px 26px;border-radius:var(--r-sm);border:1.5px solid rgba(26,58,47,.24); }
.btn-outline:hover { background:var(--ocean);color:#fff;border-color:var(--ocean);transform:translateY(-2px); }
.btn-ghost   { background:rgba(255,255,255,.1);color:#fff;padding:14px 26px;border-radius:var(--r-sm);border:1.5px solid rgba(255,255,255,.22);display:inline-flex;align-items:center;gap:9px;font-weight:600;font-size:.93rem;text-decoration:none;transition:background .2s,transform .22s var(--spring); }
.btn-ghost:hover { background:rgba(255,255,255,.18);transform:translateY(-2px); }

/* ═══════════════════════════════════════
   NAV (identical to homepage)
═══════════════════════════════════════ */
.nav-wrap {
  position:fixed; top:0; left:0; right:0; z-index:200;
  padding:0 20px;
  transition:background .38s var(--smooth),box-shadow .38s var(--smooth);
}
@media(min-width:768px){ .nav-wrap{ padding:0 32px; } }
.nav-wrap.scrolled {
  background:rgba(240,255,219,.92);
  backdrop-filter:blur(24px); -webkit-backdrop-filter:blur(24px);
  box-shadow:0 1px 0 rgba(26,58,47,.08);
}
.nav-inner { max-width:1160px;margin:0 auto;height:64px;display:flex;align-items:center;justify-content:space-between; }
.nav-logo { font-family:'Nanum Myeongjo',serif;font-size:1.25rem;font-weight:500;color:var(--ocean);text-decoration:none;letter-spacing:-.02em;display:flex;align-items:center;gap:6px;transition:opacity .2s; }
.nav-logo:hover { opacity:.72; }
.nav-logo-dot { width:7px;height:7px;border-radius:50%;background:var(--indigo);animation:pulse 2.8s ease-in-out infinite; }
.nav-links { display:none;align-items:center;gap:30px;list-style:none; }
@media(min-width:768px){ .nav-links{ display:flex; } }
.nav-links a { font-size:.85rem;font-weight:500;color:var(--muted);text-decoration:none;position:relative;padding:4px 0;transition:color .2s; }
.nav-links a::after { content:'';position:absolute;bottom:-2px;left:0;right:0;height:1.5px;background:var(--ocean);transform:scaleX(0);transform-origin:left;transition:transform .28s var(--spring); }
.nav-links a:hover,.nav-links a.active { color:var(--ocean); }
.nav-links a:hover::after,.nav-links a.active::after { transform:scaleX(1); }
.nav-cta { background:var(--ocean)!important;color:#fff!important;padding:9px 20px!important;border-radius:var(--r-sm)!important;transition:background .2s,transform .2s var(--spring)!important; }
.nav-cta::after { display:none!important; }
.nav-cta:hover { background:var(--ocean-h)!important;transform:translateY(-1px)!important; }
.nav-mob { display:flex;flex-direction:column;gap:5px;background:none;border:none;cursor:pointer;padding:8px;color:var(--ocean);-webkit-tap-highlight-color:transparent; }
@media(min-width:768px){ .nav-mob{ display:none; } }
.nav-mob span { display:block;width:22px;height:2px;background:currentColor;border-radius:2px;transition:all .28s var(--smooth); }
.nav-mob.active span:nth-child(1){ transform:translateY(7px) rotate(45deg); }
.nav-mob.active span:nth-child(2){ opacity:0;transform:scaleX(0); }
.nav-mob.active span:nth-child(3){ transform:translateY(-7px) rotate(-45deg); }
.nav-drawer { display:none;flex-direction:column;position:absolute;top:64px;left:0;right:0;background:rgba(240,255,219,.97);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);padding:20px 24px 28px;gap:4px;box-shadow:0 12px 40px rgba(26,58,47,.1);border-top:1px solid rgba(26,58,47,.07);transform:translateY(-8px);opacity:0;transition:opacity .25s var(--out),transform .25s var(--out); }
.nav-drawer.open { display:flex;opacity:1;transform:none; }
.nav-drawer a { font-size:1rem;font-weight:500;color:var(--ocean);text-decoration:none;padding:12px 0;border-bottom:1px solid rgba(26,58,47,.07);display:flex;align-items:center;justify-content:space-between;transition:opacity .2s; }
.nav-drawer a:last-child { border-bottom:none; }
.nav-drawer a:hover { opacity:.65; }
.nav-drawer-cta { margin-top:12px;display:block;background:var(--ocean);color:#fff!important;padding:14px 20px;border-radius:var(--r-sm);text-align:center;font-weight:600;border:none!important; }

/* ═══════════════════════════════════════
   PAGE HERO
═══════════════════════════════════════ */
.page-hero {
  padding-top:64px;
  background:var(--cream);
  position:relative; overflow:hidden;
}
.ph-glow {
  position:absolute; border-radius:50%; pointer-events:none;
}
.ph-g1 { width:min(500px,110vw);height:min(500px,110vw);background:radial-gradient(circle,rgba(4,169,167,.22) 0%,transparent 65%);top:-20%;right:-10%; }
.ph-g2 { width:min(300px,80vw);height:min(300px,80vw);background:radial-gradient(circle,rgba(255,205,173,.28) 0%,transparent 65%);bottom:-10%;left:-5%; }

.ph-inner {
  padding:60px 0 0;
  display:grid; grid-template-columns:1fr;
  gap:32px; align-items:end;
}
@media(min-width:768px){ .ph-inner{ padding:72px 0 0;grid-template-columns:1fr auto;gap:48px; } }

.ph-label { margin-bottom:14px; animation:fadeUp .7s .05s var(--out) both; }
.ph-title {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(2.4rem,7vw,4rem);
  font-weight:400; line-height:1.06;
  letter-spacing:-.04em; color:var(--ocean);
  animation:fadeUp .7s .14s var(--out) both;
}
.ph-title em { font-style:italic; color:var(--indigo); position:relative; }
.ph-title em::after {
  content:''; position:absolute; left:0; right:0; bottom:2px;
  height:2.5px; background:var(--peach); border-radius:2px;
  transform-origin:left;
  animation:drawLine .75s 1.1s var(--spring) both;
}
.ph-sub {
  font-size:1.05rem; color:var(--muted); max-width:480px;
  line-height:1.75; margin-top:14px;
  animation:fadeUp .7s .22s var(--out) both;
}
.ph-actions { animation:fadeUp .7s .3s var(--out) both; }

/* wave divider */
.ph-wave {
  width:100%; margin-top:48px;
  display:block; overflow:hidden; line-height:0;
}
.ph-wave svg { display:block; width:100%; height:60px; }

/* quick-nav pills */
.quick-nav {
  display:flex; flex-wrap:wrap; gap:10px;
  padding:24px 0 0;
  animation:fadeUp .7s .4s var(--out) both;
}
.qn-pill {
  display:inline-flex; align-items:center; gap:7px;
  font-size:.8rem; font-weight:500;
  padding:7px 16px; border-radius:100px;
  background:#fff; color:var(--ocean);
  border:1.5px solid rgba(26,58,47,.12);
  text-decoration:none; cursor:pointer;
  transition:background .2s, border-color .2s, transform .22s var(--spring), color .2s;
  white-space:nowrap;
}
.qn-pill:hover, .qn-pill.active {
  background:var(--ocean); color:#fff;
  border-color:var(--ocean); transform:translateY(-2px);
}
.qn-pill i { font-size:13px; }

/* ═══════════════════════════════════════
   MARQUEE (matches homepage)
═══════════════════════════════════════ */
.marquee-sec { background:var(--ocean);padding:16px 0;overflow:hidden; }
.marquee-track { display:flex;align-items:center;width:max-content;animation:marquee 28s linear infinite;gap:0; }
.marquee-track:hover { animation-play-state:paused; }
.marquee-item { display:flex;align-items:center;gap:9px;padding:0 32px;white-space:nowrap;font-size:.8rem;font-weight:500;color:rgba(255,255,255,.6);border-right:1px solid rgba(255,255,255,.12); }
.marquee-item strong { color:#fff; }
.marquee-item i { font-size:14px; color:var(--yellow); }

/* ═══════════════════════════════════════
   SERVICE CARDS — main list
═══════════════════════════════════════ */
.services-list {
  padding:80px 0 100px;
  background:#fff;
}
@media(min-width:768px){ .services-list{ padding:100px 0 120px; } }

/* alternating rows */
.svc-row {
  display:grid; grid-template-columns:1fr;
  gap:40px; align-items:start;
  padding:64px 0;
  border-top:1px solid rgba(26,58,47,.07);
  position:relative;
}
.svc-row:first-child { padding-top:0; border-top:none; }
@media(min-width:900px){
  .svc-row {
    grid-template-columns:1fr 1fr;
    gap:80px; align-items:start;
  }
  .svc-row.reverse .svc-info  { order:2; }
  .svc-row.reverse .svc-panel { order:1; }
}

/* row index badge */
.svc-row-idx {
  font-family:'Nanum Myeongjo',serif;
  font-size:5rem; font-weight:400; line-height:1;
  letter-spacing:-.07em; color:var(--ocean); opacity:.06;
  position:absolute; top:-10px; right:0;
  pointer-events:none; user-select:none;
}
@media(max-width:899px){ .svc-row-idx{ font-size:3.5rem; top:-4px; right:0; } }

/* info column */
.svc-info {}
.svc-number {
  font-family:'Nanum Myeongjo',serif; font-size:.85rem; font-weight:400;
  color:var(--indigo); letter-spacing:.06em;
  margin-bottom:12px; display:block;
}
.svc-name {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.7rem,4vw,2.6rem);
  font-weight:400; line-height:1.1;
  letter-spacing:-.03em; color:var(--ocean);
  margin-bottom:16px;
}
.svc-desc {
  font-size:1.02rem; color:var(--muted);
  line-height:1.78; margin-bottom:20px;
}
.svc-more {
  font-size:.92rem; color:var(--muted);
  line-height:1.78; padding:16px 20px;
  background:var(--cream);
  border-left:3px solid var(--yellow);
  border-radius:0 var(--r-sm) var(--r-sm) 0;
  margin-bottom:28px;
}

/* tasks list */
.svc-tasks { list-style:none; display:flex; flex-direction:column; gap:10px; margin-bottom:32px; }
.svc-task {
  display:flex; align-items:flex-start; gap:12px;
  font-size:.92rem; color:var(--muted); line-height:1.55;
  padding:12px 16px;
  background:#fff; border-radius:var(--r-sm);
  border:1px solid rgba(26,58,47,.07);
  transition:background .2s, border-color .2s, transform .22s var(--spring);
}
.svc-task:hover {
  background:var(--cream); border-color:rgba(26,58,47,.13);
  transform:translateX(4px);
}
.svc-task-check {
  width:20px; height:20px; flex-shrink:0; margin-top:1px;
  border-radius:50%; background:rgba(26,58,47,.07);
  display:flex; align-items:center; justify-content:center;
  transition:background .2s var(--spring), transform .2s var(--spring);
}
.svc-task-check i { font-size:10px; color:var(--ocean); }
.svc-task:hover .svc-task-check {
  background:var(--ocean); transform:scale(1.12);
}
.svc-task:hover .svc-task-check i { color:#fff; }

/* panel / visual column */
.svc-panel {
  position:sticky; top:88px;
  display:flex; flex-direction:column; gap:14px;
}
@media(max-width:899px){ .svc-panel{ position:static; } }

.svc-card-visual {
  border-radius:var(--r-xl); overflow:hidden;
  position:relative;
  height:clamp(200px, 50vw, 320px);
}
.svc-card-visual img {
  width:100%; height:100%; object-fit:cover;
  transition:transform 5s ease;
}
.svc-card-visual:hover img { transform:scale(1.04); }
.svc-card-overlay {
  position:absolute; inset:0;
  background:linear-gradient(160deg,transparent 40%,rgba(26,58,47,.42) 100%);
}

/* colour chip card */
.svc-chip {
  border-radius:var(--r-lg);
  padding:24px;
  display:flex; flex-direction:column; gap:10px;
}
.svc-chip-title {
  font-family:'Nanum Myeongjo',serif; font-size:1.1rem;
  font-weight:400; line-height:1.22;
}
.svc-chip-sub {
  font-size:.82rem; line-height:1.6; opacity:.75;
}
.chip-book {
  display:inline-flex; align-items:center; gap:7px;
  font-size:.8rem; font-weight:600; margin-top:6px;
  text-decoration:none; opacity:.7;
  transition:gap .2s var(--spring), opacity .2s;
}
.chip-book i { font-size:13px; }
.svc-chip:hover .chip-book { gap:12px; opacity:1; }

/* chip colour variants tied to service index */
.chip-0 { background:var(--ocean); color:#fff; }
.chip-0 .chip-book { color:#fff; }
.chip-1 { background:var(--cream); color:var(--ocean); }
.chip-2 { background:rgba(255,205,173,.25); color:var(--ocean); }
.chip-3 { background:rgba(4,169,167,.1); color:var(--ocean); }
.chip-4 { background:var(--yellow); color:var(--ocean); }
.chip-5 { background:var(--ocean-soft); color:var(--ocean); }

/* "What to expect" heading */
.what-to-expect {
  margin-bottom:16px;
}
.wte-heading {
  display:inline-flex; align-items:center; gap:7px;
  font-size:.75rem; font-weight:700;
  letter-spacing:.07em; text-transform:uppercase;
  color:var(--ocean); margin-bottom:14px;
  padding:5px 12px; border-radius:100px;
  background:rgba(26,58,47,.07);
}
.wte-heading i { font-size:12px; color:var(--indigo); }
  margin-bottom:14px;


/* ═══════════════════════════════════════
   IMAGES GRID (between sections)
═══════════════════════════════════════ */
.care-gallery {
  padding:0 0 80px;
  background:#fff;
}
.gallery-grid {
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:12px;
}
@media(min-width:640px){ .gallery-grid{ grid-template-columns:repeat(4,1fr); } }
.gallery-item {
  border-radius:var(--r-lg); overflow:hidden;
  position:relative;
  height:clamp(100px,22vw,160px);
  cursor:pointer;
  transition:transform .28s var(--spring);
}
.gallery-item:hover { transform:scale(1.02); }
.gallery-item img { width:100%; height:100%; object-fit:cover; transition:transform 4s ease; }
.gallery-item:hover img { transform:scale(1.06); }
.gallery-item:nth-child(1) { grid-column:span 2; height:clamp(140px,28vw,220px); }
@media(min-width:640px){
  .gallery-item:nth-child(1) { grid-column:span 2; grid-row:span 2; height:auto; min-height:300px; }
}

/* ═══════════════════════════════════════
   CTA SECTION (matches homepage)
═══════════════════════════════════════ */
.cta-sec {
  padding:80px 0; background:var(--ocean);
  position:relative; overflow:hidden;
}
@media(min-width:768px){ .cta-sec{ padding:100px 0; } }
.cta-orb { position:absolute;border-radius:50%;pointer-events:none; }
.co1 { width:min(560px,140vw);height:min(560px,140vw);background:radial-gradient(circle,rgba(4,169,167,.12) 0%,transparent 65%);top:-25%;right:-15%; }
.co2 { width:min(360px,90vw);height:min(360px,90vw);background:radial-gradient(circle,rgba(255,205,173,.10) 0%,transparent 65%);bottom:-15%;left:-10%; }
.cta-inner { text-align:center;position:relative;z-index:2; }
.cta-h2 { font-family:'Nanum Myeongjo',serif;font-size:clamp(2rem,6vw,3.6rem);font-weight:400;line-height:1.1;letter-spacing:-.03em;color:#fff;margin:18px 0 14px; }
.cta-h2 em { color:var(--yellow);font-style:italic; }
.cta-p { font-size:1rem;color:rgba(255,255,255,.68);max-width:440px;margin:0 auto 36px;line-height:1.75; }
.cta-actions { display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:12px;margin-bottom:44px; }
.cta-trust { display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:24px; }
.ct-item { display:flex;align-items:center;gap:7px;font-size:.82rem;color:rgba(255,255,255,.58); }
.ct-item i { font-size:15px;color:var(--yellow); }

/* ═══════════════════════════════════════
   UTILITY
═══════════════════════════════════════ */
.dot-bg { background-image:radial-gradient(circle,rgba(26,58,47,.07) 1.5px,transparent 1.5px);background-size:24px 24px; }

/* ══════════════════════════════════════════════════════════════
   MOBILE-FIRST POLISH  ·  Pure additive — no colour changes
══════════════════════════════════════════════════════════════ */

/* Respect reduced motion */
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: .01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: .01ms !important;
  }
}

/* ── Safe area insets (notch / home bar) ────── */
.nav-wrap    { padding-left: max(20px, env(safe-area-inset-left));
               padding-right: max(20px, env(safe-area-inset-right)); }
.cta-sec,
.about-cta,
.what-sec    { padding-bottom: max(80px, calc(64px + env(safe-area-inset-bottom))); }

/* ── Typography scale (fluid) ───────────────── */
@media (max-width: 767px) {
  html   { font-size: 15px; }
  .wrap  { padding: 0 18px; }
  .sec-title { font-size: clamp(1.7rem, 7.2vw, 2.5rem); letter-spacing: -.025em; }
  .sec-sub   { font-size: .95rem; margin-top: 10px; }
}

/* ── Nav ────────────────────────────────────── */
@media (max-width: 767px) {
  .nav-inner      { height: 60px; }
  .nav-logo-img   { height: 52px; }
  .nav-drawer     { top: 60px; padding: 16px 18px 24px; }
  .nav-drawer a   { font-size: .95rem; padding: 14px 0; }
  .nav-wrap.scrolled { box-shadow: 0 2px 16px rgba(26,58,47,.12); }
}

/* ── Hero mobile ────────────────────────────── */
@media (max-width: 899px) {
  .hero-inner { padding: 44px 0 52px; }
  .hero-h1 {
    font-size: clamp(2rem, 10.5vw, 3.4rem);
    letter-spacing: -.032em;
    margin: 12px 0 16px;
  }
  .hero-p  { font-size: .98rem; max-width: 100%; line-height: 1.75; }
  .hero-actions {
    margin-top: 24px;
    gap: 10px;
    flex-direction: column;
    align-items: stretch;
  }
  .hero-actions .btn {
    width: 100%;
    justify-content: center;
    padding: 16px 20px;
    font-size: 1rem;
    min-height: 52px;      /* comfortable touch target */
  }
  .hero-stats {
    margin-top: 28px;
    padding-top: 22px;
    gap: 14px;
    flex-wrap: nowrap;
    justify-content: space-between;
  }
  .stat-num { font-size: 1.65rem; }
  .stat-lbl { font-size: .65rem; }
}

/* ── Mobile hero snapshot card ──────────────── */
.hero-snap {
  display: none;
  margin-top: 24px;
  background: rgba(255,255,255,.9);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,.72);
  border-radius: var(--r-lg);
  padding: 16px 18px;
  box-shadow: 0 8px 28px rgba(26,58,47,.1);
}
@media (max-width: 899px) { .hero-snap { display: block; } }
.hs-head  { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.hs-label { font-size: .62rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; }
.hs-badge { font-size: .66rem; font-weight: 700; padding: 3px 9px; border-radius: 100px; background: #d1fae5; color: #065f46; }
.hs-metrics { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; }
.hs-metric  { background: var(--cream); border-radius: 8px; padding: 10px 6px; text-align: center; }
.hs-val { font-family: 'Nanum Myeongjo', serif; font-size: 1.1rem; font-weight: 700; color: var(--ocean); line-height: 1; }
.hs-lbl { font-size: .57rem; color: var(--muted); margin-top: 3px; }

/* ── Pill shimmer ───────────────────────────── */
.pill { position: relative; overflow: hidden; }
.pill::after {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.3) 50%, transparent);
  background-size: 300px 100%;
  animation: shimmerPill 3.5s ease-in-out infinite;
  pointer-events: none; border-radius: inherit;
}
@keyframes shimmerPill {
  0%   { background-position: -300px 0; }
  100% { background-position:  300px 0; }
}

/* ── Button icon nudge ──────────────────────── */
.btn i, .btn-ghost i, .btn-yellow i {
  display: inline-block;
  transition: transform .22s var(--spring);
}
.btn:hover i, .btn-ghost:hover i, .btn-yellow:hover i { transform: translateX(4px); }
.btn-outline i { display: inline-block; transition: transform .22s var(--spring); }
.btn-outline:hover i { transform: scale(1.2); }

/* ── Touch feedback ─────────────────────────── */
@media (hover: none) {
  .btn:active, .btn-ghost:active, .btn-yellow:active {
    transform: scale(.95) !important; filter: brightness(.92);
  }
  .svc-card:active, .trust-card:active, .testi-card:active,
  .hiw-card:active, .plan-card:active, .value-card:active,
  .mv-card:active  { transform: scale(.98) !important; }
  .qn-pill:active  { transform: scale(.96) !important; }
  .gallery-item:active { transform: scale(.97) !important; }
  .faq-btn:active  { opacity: .8; }
  .svc-entry:active { transform: scale(.99) !important; }
}

/* ── Focus upgrade ──────────────────────────── */
:focus-visible {
  outline: 2px solid var(--yellow);
  outline-offset: 3px;
  border-radius: 5px;
  box-shadow: 0 0 0 4px rgba(4,169,167,.18);
}

/* ── Minimum touch target sizes ─────────────── */
@media (max-width: 767px) {
  .faq-btn   { min-height: 52px; }
  .nav-mob   { min-height: 44px; min-width: 44px; }
  .btn, .btn-ghost, .btn-yellow,
  .btn-outline, .btn-outline-dark { min-height: 48px; }
  .plan-cta, .svc-entry-book { min-height: 48px; }
  .nav-drawer a { min-height: 48px; }
}

/* ── Trust cards mobile ─────────────────────── */
@media (max-width: 639px) {
  .trust-sec  { padding: 44px 0; }
  .trust-grid { gap: 10px; }
  .trust-card {
    padding: 20px 18px;
    flex-direction: row;
    align-items: flex-start;
    gap: 14px;
  }
  .trust-ico  { flex-shrink: 0; margin-top: 2px; }
}
/* Tilt on hover */
.trust-ico { transition: background .22s var(--spring), transform .3s var(--spring); }
.trust-card:hover .trust-ico { transform: scale(1.14) rotate(-7deg); }

/* ── Services scroll ────────────────────────── */
@media (max-width: 767px) {
  .svc-sec    { padding: 60px 0; }
  .svc-header { flex-direction: column; align-items: flex-start; gap: 8px; margin-bottom: 24px; }
  .svc-header .sec-sub { max-width: 100%; }
  .svc-scroll {
    margin: 0 -18px;
    padding-left: 18px; padding-right: 18px; padding-bottom: 20px;
  }
  .svc-card  { flex: 0 0 74vw; min-width: 220px; max-width: 270px; }
  .svc-dots  { margin-top: 14px; }
}
/* Scroll dot pill */
.svc-dot {
  border-radius: 4px;
  transition: background .25s var(--spring), width .3s var(--spring);
}
.svc-dot.active { width: 18px; border-radius: 4px; }

/* ── How it works ───────────────────────────── */
@media (max-width: 767px) {
  .hiw-sec     { padding: 60px 0; }
  .hiw-header  { margin-bottom: 32px; }
  .hiw-card    { padding: 24px 20px; }
  .hiw-step-num { font-size: 2.4rem; margin-bottom: 12px; }
}
.hiw-ico { transition: transform .28s var(--spring); }
.hiw-card:hover .hiw-ico { transform: scale(1.14) rotate(8deg); }

/* ── Plans ──────────────────────────────────── */
@media (max-width: 767px) {
  .plans-sec { padding: 60px 0; }
  .plans-scroll {
    margin: 28px -18px 0;
    padding-left: 18px; padding-right: 18px; padding-bottom: 20px;
  }
  .plan-card { flex: 0 0 76vw; min-width: 230px; max-width: 280px; }
}
/* Featured breathe pulse */
.plan-featured { animation: breathePlan 4.5s ease-in-out infinite; }
.plan-featured:hover { animation: none; }
@keyframes breathePlan { 0%,100%{transform:scale(1)} 50%{transform:scale(1.025)} }

/* ── App snapshot ───────────────────────────── */
@media (max-width: 767px) {
  .snapshot-sec  { padding: 60px 0; }
  .snapshot-grid { gap: 32px; }
  .mockup-card   { max-width: 100%; padding: 18px; }
}
/* Mockup float */
.mockup-card {
  animation: floatY 5s ease-in-out infinite;
  transition: box-shadow .3s, transform .3s var(--spring);
}
.mockup-card:hover { animation: none; transform: translateY(-6px); box-shadow: 0 32px 80px rgba(26,58,47,.18); }
.mockup-metric { transition: background .2s, transform .22s var(--spring); cursor: default; }
.mockup-metric:hover { background: var(--ocean); transform: scale(1.04); }
.mockup-metric:hover .m-val, .mockup-metric:hover .m-lbl { color: #fff !important; }

/* ── Checklist items ────────────────────────── */
.sc-item { transition: transform .22s var(--spring); }
.sc-item:hover { transform: translateX(5px); }
.sc-check { transition: background .22s var(--spring), transform .3s var(--spring), box-shadow .22s; }
.sc-item:hover .sc-check { transform: scale(1.18) rotate(12deg); box-shadow: 0 3px 10px rgba(26,58,47,.18); }

/* ── FAQ ────────────────────────────────────── */
@media (max-width: 767px) {
  .faq-sec    { padding: 60px 0; }
  .faq-header { flex-direction: column; align-items: flex-start; margin-bottom: 28px; }
  .faq-btn    { padding: 18px 0; font-size: .97rem; }
  .faq-answer-body { font-size: .93rem; }
}

/* ── Testimonial dots ───────────────────────── */
.testi-dots {
  display: none;
  justify-content: center;
  gap: 7px;
  margin-top: 16px;
}
@media (max-width: 767px) { .testi-dots { display: flex; } }
.testi-dot {
  width: 6px; height: 6px; border-radius: 4px;
  background: rgba(26,58,47,.2);
  transition: background .25s var(--spring), width .3s var(--spring);
}
.testi-dot.active { width: 18px; background: var(--ocean); }

/* ── CTA mobile ─────────────────────────────── */
@media (max-width: 767px) {
  .cta-sec    { padding: 64px 0 max(64px, calc(48px + env(safe-area-inset-bottom))); }
  .cta-h2     { font-size: clamp(1.85rem, 8vw, 3rem); }
  .cta-actions {
    flex-direction: column; align-items: stretch;
  }
  .cta-actions .btn, .cta-actions .btn-ghost {
    width: 100%; justify-content: center; padding: 16px 20px;
  }
  .cta-trust  { gap: 14px; flex-direction: column; align-items: center; }
}

/* ── Scroll snap momentum ───────────────────── */
.svc-scroll, .testi-scroll, .plans-scroll {
  scroll-padding-left: 18px;
  overscroll-behavior-x: contain;
  -webkit-overflow-scrolling: touch;
}

/* ── Marquee mobile ─────────────────────────── */
@media (max-width: 767px) {
  .marquee-item { padding: 0 22px; font-size: .75rem; }
}
.marquee-item i { display: inline-block; transition: transform .2s var(--spring); }
.marquee-item:hover i { transform: scale(1.3) rotate(10deg); }

/* ── Lazy image shimmer ─────────────────────── */
img[loading="lazy"] {
  background: linear-gradient(90deg,rgba(240,255,219,.5) 25%,rgba(255,255,255,.3) 50%,rgba(240,255,219,.5) 75%);
  background-size: 400px 100%;
  animation: shimmerPill 1.8s ease-in-out infinite;
}
img[loading="lazy"].img-loaded { animation: none; background: none; }

/* ── Page-hero (services & about) ───────────── */
@media (max-width: 767px) {
  .page-hero, .about-hero { padding-bottom: 0; }
  .ph-inner, .ah-inner    { padding-top: 48px; }
  .ph-title, .ah-h1 {
    font-size: clamp(2rem, 9.5vw, 3rem); letter-spacing: -.032em;
  }
  .ph-sub, .ah-lead { font-size: .96rem; max-width: 100%; }
  .ph-actions .btn, .ah-actions .btn { width: 100%; justify-content: center; }
  .ph-wave svg { height: 36px; }
  .ph-inner {
    grid-template-columns: 1fr !important;
    gap: 18px !important;
  }
}

/* ── Quick-nav (services) ───────────────────── */
@media (max-width: 767px) {
  .quick-nav {
    flex-wrap: nowrap;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
    margin: 0 -18px;
    padding: 18px 18px 6px;
    gap: 8px;
  }
  .quick-nav::-webkit-scrollbar { display: none; }
  .qn-pill {
    flex: 0 0 auto; scroll-snap-align: start;
    font-size: .74rem; padding: 6px 12px; white-space: nowrap;
  }
}

/* ── Service rows (services page) ───────────── */
@media (max-width: 899px) {
  .services-list { padding: 52px 0 72px; }
  .svc-row       { padding: 40px 0; gap: 26px; }
  .svc-row:first-child { padding-top: 0; }
  .svc-name      { font-size: clamp(1.5rem, 5.5vw, 2.2rem); }
  .svc-desc      { font-size: .94rem; }
  .svc-more      { font-size: .87rem; padding: 12px 15px; }
}
@media (max-width: 640px) {
  .svc-cards-grid { grid-template-columns: 1fr; }
  .svc-entry-img  { height: clamp(160px, 42vw, 220px); }
}

/* ── Service card tasks hover ───────────────── */
.svc-task { transition: background .2s, border-color .2s, transform .25s var(--spring), box-shadow .22s; }
.svc-task:hover { transform: translateX(5px); box-shadow: 3px 0 0 0 var(--yellow) inset; }
.svc-task-check { transition: background .22s var(--spring), transform .3s var(--spring); }
.svc-task:hover .svc-task-check { transform: scale(1.2) rotate(10deg); }

/* ── Gallery (services) ─────────────────────── */
@media (max-width: 767px) {
  .care-gallery  { padding-bottom: 52px; }
  .gallery-grid  { gap: 8px; }
  .gallery-item  { height: clamp(88px, 20vw, 140px); }
  .gallery-item:nth-child(1) { height: clamp(130px, 26vw, 200px); }
}
.gallery-item { transition: transform .28s var(--spring), box-shadow .28s; }
.gallery-item:hover { transform: scale(1.025); box-shadow: 0 10px 32px rgba(26,58,47,.13); }

/* ── About page mobile ──────────────────────── */
@media (max-width: 767px) {
  .origin-sec  { padding: 64px 0; }
  .what-sec    { padding: 64px 0; }
  .values-sec  { padding: 64px 0; }
  .mv-sec      { padding: 64px 0; }
  .about-cta   { padding: 64px 0; }
  .origin-grid { grid-template-columns: 1fr !important; gap: 36px; }
  .what-grid   { grid-template-columns: 1fr !important; gap: 28px; }
  .values-grid { grid-template-columns: 1fr; }
  .mv-grid     { grid-template-columns: 1fr !important; gap: 16px; }
  .mv-card     { padding: 28px 22px; }
  .ac-actions  { flex-direction: column; align-items: stretch; }
  .ac-actions .btn, .ac-actions .btn-outline-dark { width: 100%; justify-content: center; }
}
@media (max-width: 639px) {
  .ah-h1       { font-size: clamp(2rem, 9vw, 2.8rem); }
}
.origin-stat-card { transition: transform .28s var(--spring), box-shadow .28s; }
.origin-stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 32px rgba(26,58,47,.08); }
.value-card:hover .value-ico { transform: scale(1.12) rotate(-6deg); }

/* ── About CTA mobile ───────────────────────── */
@media (max-width: 767px) {
  .about-cta .ac-actions { flex-direction: column; align-items: stretch; }
}

</style>
@endpush

@section('content')


{{-- ── PAGE HERO ──────────────────────────────────────── --}}
<section class="page-hero" aria-label="Services hero">
  <div class="ph-glow ph-g1" aria-hidden="true"></div>
  <div class="ph-glow ph-g2" aria-hidden="true"></div>

  <div class="wrap">
    <div class="ph-inner">

      <div>
        <div class="ph-label">
          <span class="pill pill-indigo">
            <span class="pill-dot"></span>
            Professional In-Home Care
          </span>
        </div>
        <h1 class="ph-title">
        Find the Right Care plan <br>
        that makes you feel relieved about your <em>loved one</em>
        </h1>
        <p class="ph-sub">
        Our healthcare professionals are trained with clinical certifications to handle any health situations that your loved one might be facing
        </p>
      </div>

      <div class="ph-actions">
        <a href="#care-list" class="btn btn-primary">
          Explore Services
          <i class="bi bi-arrow-down"></i>
        </a>
      </div>

    </div>

    {{-- quick-nav pills --}}
    <nav class="quick-nav" aria-label="Jump to service">
      @foreach($careTypes as $index => $careType)
        <a href="#service-{{ $careType->id }}" class="qn-pill{{ $index === 0 ? ' active' : '' }}"
           data-target="service-{{ $careType->id }}">
          <i class="bi bi-arrow-right-circle"></i>
          {{ $careType->name }}
        </a>
      @endforeach
    </nav>

    <div class="ph-wave" aria-hidden="true">
      <!-- <svg viewBox="0 0 1440 60" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 0 C240 60 480 0 720 30 C960 60 1200 0 1440 30 L1440 60 L0 60 Z" fill="#1A3A2F"/>
      </svg> -->
    </div>
  </div>
</section>


{{-- ── MARQUEE ──────────────────────────────────────────── --}}
<!-- <div class="marquee-sec" role="marquee" aria-label="Care credentials">
  <div class="marquee-track">
    @php
    $mItems = [
      ['i'=>'bi-patch-check', 't'=>'Licensed Healthcare Professionals'],
      ['i'=>'bi-shield-check', 't'=>'Background Verified'],
      ['i'=>'bi-bell',         't'=>'Real-Time Reports'],
      ['i'=>'bi-clock',        't'=>'24/7 Coordinators'],
      ['i'=>'bi-heart',        't'=>'Compassionate Care'],
      ['i'=>'bi-house-heart',  't'=>'In-Home Service'],
      ['i'=>'bi-star',         't'=>'98% Satisfaction'],
      ['i'=>'bi-patch-check', 't'=>'Licensed Healthcare Professionals'],
      ['i'=>'bi-shield-check', 't'=>'Background Verified'],
      ['i'=>'bi-bell',         't'=>'Real-Time Reports'],
      ['i'=>'bi-clock',        't'=>'24/7 Coordinators'],
      ['i'=>'bi-heart',        't'=>'Compassionate Care'],
      ['i'=>'bi-house-heart',  't'=>'In-Home Service'],
      ['i'=>'bi-star',         't'=>'98% Satisfaction'],
    ];
    @endphp
    @foreach($mItems as $m)
      <div class="marquee-item">
        <i class="bi {{ $m['i'] }}"></i>
        <strong>{{ $m['t'] }}</strong>
      </div>
    @endforeach
  </div>
</div> -->


{{-- ── SERVICES LIST ─────────────────────────────────────── --}}
<section id="care-list" class="services-list" aria-label="All care services">
  <div class="wrap">

    @php
    $chipClass  = ['chip-0','chip-1','chip-2','chip-3','chip-4','chip-5'];
    $svcIcons = [
      'bi-heart-pulse','bi-people','bi-hospital',
      'bi-activity','bi-stars','bi-house-heart',
    ];
    @endphp

    @foreach($careTypes as $index => $careType)
      @php
        $meta    = $careType->meta ?? [];
        $desc    = $meta['description'] ?? '';
        $more    = $meta['more_info']   ?? '';
        $tasks   = $meta['tasks']       ?? [];
        $chip    = $chipClass[$index % count($chipClass)];
        $img = $meta['image_path'] ?? null;
        $icon    = $svcIcons[$index % count($svcIcons)];
        $isEven  = ($index % 2 === 0);
        $num     = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
      @endphp

      <div id="service-{{ $careType->id }}"
           class="svc-row{{ $isEven ? '' : ' reverse' }} reveal"
           style="transition-delay:{{ $index * 0.05 }}s;"
           role="article"
           aria-label="{{ $careType->name }}">

        <span class="svc-row-idx" aria-hidden="true">{{ $num }}</span>

        {{-- INFO COLUMN --}}
        <div class="svc-info">

          <span class="svc-number">{{ $num }} / {{ str_pad(count($careTypes), 2, '0', STR_PAD_LEFT) }}</span>

          <h2 class="svc-name">{{ $careType->name }}</h2>

          @if($desc)
            <p class="svc-desc">{{ $desc }}</p>
          @endif

          @if($more)
            <div class="svc-more">{{ $more }}</div>
          @endif

          @if(!empty($tasks))
            <div class="what-to-expect">
              <div class="wte-heading">
                <i class="bi bi-list-check"></i>
                What to expect
              </div>
              <ul class="svc-tasks" aria-label="What to expect from {{ $careType->name }}">
                @foreach($tasks as $taskItem)
                  @php $taskText = is_array($taskItem) ? ($taskItem['task'] ?? '') : $taskItem; @endphp
                  @if($taskText)
                  <li class="svc-task reveal" style="transition-delay:{{ 0.04 + ($loop->index * 0.04) }}s;">
                    <span class="svc-task-check" aria-hidden="true">
                      <i class="bi bi-check"></i>
                    </span>
                    {{ $taskText }}
                  </li>
                  @endif
                @endforeach
              </ul>
            </div>

  @endif

  <a href="{{ route('booking.create.service', $careType->slug) }}"
          class="btn btn-primary">
          Book {{ $careType->name }}
          <i class="bi bi-arrow-right"></i>
        </a>

        </div>

        {{-- PANEL COLUMN --}}
        <div class="svc-panel">

        <div class="svc-card-visual">
        <img
            src="{{ $img ? asset('storage/' . $img) : 'https://via.placeholder.com/700x500?text=Care+Service' }}"
            alt="{{ $careType->name }}"
            loading="lazy"
        >
        <div class="svc-card-overlay"></div>
    </div>

          <div class="svc-chip {{ $chip }}">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
              <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;">
                <i class="bi {{ $icon }}" style="font-size:18px;"></i>
              </div>
              <div class="svc-chip-title">{{ $careType->name }}</div>
            </div>
            @if($desc)
              <p class="svc-chip-sub">{{ Str::limit($desc, 100) }}</p>
            @endif
    
            <a href="{{ route('booking.create.service', $careType->slug) }}" class="chip-book">
              Book this service
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>

        </div>
      </div>

    @endforeach

  </div>
</section>


{{-- ── CARE GALLERY ──────────────────────────────────────── --}}
<section class="care-gallery dot-bg" aria-hidden="true">
  <div class="wrap">
    <div class="gallery-grid">
      <div class="gallery-item reveal">
        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&h=500&fit=crop" alt="Care at home" loading="lazy">
      </div>
      <div class="gallery-item reveal" style="transition-delay:.08s">
        <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&h=300&fit=crop" alt="Nurse with patient" loading="lazy">
      </div>
      <div class="gallery-item reveal" style="transition-delay:.16s">
        <img src="https://images.unsplash.com/photo-1527613426441-4da17471b66d?w=400&h=300&fit=crop" alt="Family care" loading="lazy">
      </div>
      <div class="gallery-item reveal" style="transition-delay:.24s">
        <img src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?w=400&h=300&fit=crop" alt="Caregiver" loading="lazy">
      </div>
    </div>
  </div>
</section>


{{-- ── FINAL CTA ──────────────────────────────────────────── --}}
<section id="contact" class="cta-sec" aria-label="Get started">
  <div class="cta-orb co1" aria-hidden="true"></div>
  <div class="cta-orb co2" aria-hidden="true"></div>

  <div class="wrap">
    <div class="cta-inner reveal">
      <span class="pill pill-white">
        <span class="pill-dot"></span>
        Start Today
      </span>

      <h2 class="cta-h2">
        Find the right care<br>
        <em>for your loved one</em>
      </h2>

      <p class="cta-p">
        Book a free consultation and we'll match you with the perfect care plan and caregiver.
      </p>

      <div class="cta-actions">
        <a href="{{ url('/') }}#care-plans" class="btn btn-yellow">
          <i class="bi bi-calendar-check"></i>
          Book a Care Plan
        </a>
        <a href="tel:+2347063112134" class="btn-ghost">
          <i class="bi bi-telephone"></i>
          Call: +234 706 311 2134
        </a>
      </div>

      <div class="cta-trust">
        <div class="ct-item"><i class="bi bi-patch-check"></i> Licensed & Insured</div>
        <div class="ct-item"><i class="bi bi-people"></i> Verified Caregivers</div>
        <div class="ct-item"><i class="bi bi-clock"></i> 24/7 Support</div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

/* ── NAV SCROLL ───────────────────────── */
const nav = document.getElementById('mainNav');
const onScroll = () => nav.classList.toggle('scrolled', scrollY > 16);
window.addEventListener('scroll', onScroll, {passive:true});
onScroll();

/* ── SCROLL REVEAL ────────────────────── */
const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
  });
}, { threshold:.08, rootMargin:'0px 0px -48px 0px' });
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

/* ── SMOOTH SCROLL ────────────────────── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if (t) {
      e.preventDefault();
      window.scrollTo({ top: t.getBoundingClientRect().top + scrollY - 80, behavior:'smooth' });
    }
  });
});

/* ── RIPPLE ───────────────────────────── */
const rs = document.createElement('style');
rs.textContent = '@keyframes ripple{to{transform:scale(2.5);opacity:0}}';
document.head.appendChild(rs);
document.querySelectorAll('.btn, .btn-ghost, .btn-yellow, .nav-cta, .plan-cta').forEach(btn => {
  btn.style.position = 'relative'; btn.style.overflow = 'hidden';
  btn.addEventListener('pointerdown', e => {
    const r = document.createElement('span');
    const rect = btn.getBoundingClientRect();
    const sz = Math.max(rect.width, rect.height) * 1.4;
    r.className = 'btn-ripple';
    r.style.cssText = `width:${sz}px;height:${sz}px;left:${e.clientX-rect.left-sz/2}px;top:${e.clientY-rect.top-sz/2}px;`;
    btn.appendChild(r);
    r.addEventListener('animationend', () => r.remove());
  });
});

/* ── QUICK-NAV ACTIVE STATE ON SCROLL ─── */
const sections = document.querySelectorAll('[id^="service-"]');
const qnPills  = document.querySelectorAll('.qn-pill[data-target]');
const sectionIO = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      qnPills.forEach(p => p.classList.toggle('active', p.dataset.target === e.target.id));
    }
  });
}, { rootMargin:'-20% 0px -60% 0px' });
sections.forEach(s => sectionIO.observe(s));

/* ── PARALLAX GLOWS (desktop) ─────────── */
if (window.matchMedia('(min-width:900px) and (hover:hover)').matches) {
  const glows = document.querySelectorAll('.ph-glow');
  document.addEventListener('mousemove', e => {
    const cx = e.clientX / innerWidth  - .5;
    const cy = e.clientY / innerHeight - .5;
    glows.forEach((g, i) => {
      g.style.transform = `translate(${cx * (i+1) * 20}px, ${cy * (i+1) * 20}px)`;
    });
  }, {passive:true});
}


/* ── Extra reveal directions ─────────────────── */
const ioExtra = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('is-visible'); ioExtra.unobserve(e.target); }
  });
}, { threshold: .08, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal-left, .reveal-right, .reveal-scale')
  .forEach(el => ioExtra.observe(el));

/* ── Lazy image loaded state ─────────────────── */
document.querySelectorAll('img[loading="lazy"]').forEach(img => {
  if (img.complete) img.classList.add('img-loaded');
  else img.addEventListener('load', () => img.classList.add('img-loaded'));
});

/* ── Haptic on button tap (Android) ─────────── */
if ('vibrate' in navigator) {
  document.querySelectorAll('.btn, .btn-yellow, .btn-ghost, .nav-cta, .plan-cta, .btn-outline-dark')
    .forEach(b => b.addEventListener('pointerdown', () => navigator.vibrate(6)));
}

/* ── Testimonial / testi scroll dots ─────────── */
const testiScroll2 = document.querySelector('.testi-scroll');
const testiDots2   = document.querySelectorAll('.testi-dot');
if (testiScroll2 && testiDots2.length) {
  const syncDots = () => {
    const cards = testiScroll2.querySelectorAll('.testi-card');
    const cardW = (cards[0]?.offsetWidth ?? 0) + 14;
    const idx   = Math.round(testiScroll2.scrollLeft / cardW);
    testiDots2.forEach((d, i) => d.classList.toggle('active', i === idx));
  };
  testiScroll2.addEventListener('scroll', syncDots, { passive: true });
  syncDots();
}

/* ── Services card task stagger ──────────────── */
document.querySelectorAll('.svc-expect-list, .svc-tasks').forEach(ul => {
  ul.querySelectorAll('li').forEach((li, i) => {
    li.style.transitionDelay = (i * 0.045) + 's';
  });
});

});
</script>
@endpush