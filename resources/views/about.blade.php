{{-- resources/views/about.blade.php --}}
@extends('layouts.app')

@section('title', 'About CavalCare — Our Story, Mission & Values')

@push('head')
<meta name="description" content="Learn how CavalCare was founded to give Nigerian families access to professional, licensed in-home nursing care. Our mission: healing at home, with intention.">
<meta name="keywords" content="CavalCare about, in-home nursing Nigeria story, home healthcare Nigeria mission, CavalCare values, Nigerian healthcare startup">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url('/about') }}">

<meta property="og:type"        content="website">
<meta property="og:url"         content="{{ url('/about') }}">
<meta property="og:title"       content="About CavalCare — Our Story, Mission & Values">
<meta property="og:description" content="CavalCare was founded because too many Nigerian families were navigating post-hospital care alone. We provide verified healthcare professionals so healing happens at home, with intention.">
<meta property="og:image"       content="{{ asset('images/og-about.jpg') }}">
<meta property="og:site_name"   content="CavalCare">

<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="About CavalCare — Our Story, Mission & Values">
<meta name="twitter:description" content="Founded to ensure no Nigerian family navigates post-hospital care alone. Licensed healthcare professionals, real-time reports, 24/7 support.">
<meta name="twitter:image"       content="{{ asset('images/og-about.jpg') }}">

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "AboutPage",
  "url": "{{ url('/about') }}",
  "name": "About CavalCare",
  "description": "CavalCare was founded to ensure Nigerian families have access to professional licensed nursing care at home after hospital discharge.",
  "publisher": {
    "@@type": "Organization",
    "name": "CavalCare",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logo.png') }}"
  }
}
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700;800&family=Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════
   TOKENS — identical to home & services
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
  --r-xs:  6px;  --r-sm: 10px; --r-md: 16px;
  --r-lg:  24px; --r-xl: 36px; --r-2xl: 48px;
  --spring: cubic-bezier(0.34,1.56,0.64,1);
  --out:    cubic-bezier(0,0,0.2,1);
  --smooth: cubic-bezier(0.4,0,0.2,1);
}

/* ── RESET & BASE ───────────────────────────────── */
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
html { scroll-behavior:smooth; -webkit-text-size-adjust:100%; }
body {
  font-family:'Cabin',system-ui,sans-serif;
  font-size:16px; line-height:1.65;
  color:var(--text); background:#fff;
  -webkit-font-smoothing:antialiased;
  overflow-x:hidden;
}
img  { display:block; max-width:100%; }
a    { color:inherit; }
:focus-visible { outline:2px solid var(--yellow); outline-offset:3px; border-radius:4px;
  box-shadow:0 0 0 4px rgba(4,169,167,.15); }

/* ── KEYFRAMES ──────────────────────────────────── */
@keyframes fadeUp   { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:none} }
@keyframes fadeIn   { from{opacity:0} to{opacity:1} }
@keyframes drawLine { from{transform:scaleX(0)} to{transform:scaleX(1)} }
@keyframes floatY   { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-7px)} }
@keyframes pulse    { 0%,100%{box-shadow:0 0 0 0 rgba(26,58,47,.22)} 60%{box-shadow:0 0 0 9px rgba(26,58,47,0)} }
@keyframes ripple   { to{transform:scale(2.2);opacity:0} }
@keyframes shimmerPill {
  0%   { background-position:-300px 0 }
  100% { background-position: 300px 0 }
}
@keyframes slideR { from{opacity:0;transform:translateX(-22px)} to{opacity:1;transform:none} }
@keyframes slideL { from{opacity:0;transform:translateX(22px)}  to{opacity:1;transform:none} }
@keyframes countUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }

/* ── REVEAL ─────────────────────────────────────── */
.reveal {
  opacity:0; transform:translateY(26px);
  transition:opacity .72s var(--out),transform .72s var(--out);
}
.reveal.is-visible { opacity:1; transform:none; }
.reveal-left  { opacity:0; transform:translateX(-24px);
  transition:opacity .72s var(--out),transform .72s var(--out); }
.reveal-right { opacity:0; transform:translateX(24px);
  transition:opacity .72s var(--out),transform .72s var(--out); }
.reveal-left.is-visible,.reveal-right.is-visible { opacity:1; transform:none; }
.reveal-scale { opacity:0; transform:scale(.94);
  transition:opacity .65s var(--out),transform .65s var(--spring); }
.reveal-scale.is-visible { opacity:1; transform:none; }

/* ── PREFERS REDUCED MOTION ─────────────────────── */
@media(prefers-reduced-motion:reduce){
  *,*::before,*::after { animation-duration:.01ms!important; transition-duration:.01ms!important; }
}

/* ── LAYOUT ─────────────────────────────────────── */
.wrap { max-width:1160px; margin:0 auto; padding:0 20px; }
@media(min-width:768px){ .wrap{ padding:0 32px; } }
@media(max-width:767px){ .wrap{ padding:0 18px; } }

/* ── PILL ───────────────────────────────────────── */
.pill {
  display:inline-flex; align-items:center; gap:6px;
  font-size:.7rem; font-weight:600;
  letter-spacing:.07em; text-transform:uppercase;
  padding:5px 13px; border-radius:100px;
  position:relative; overflow:hidden;
}
.pill::after {
  content:''; position:absolute; inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.32) 50%,transparent);
  background-size:300px 100%;
  animation:shimmerPill 3.5s ease-in-out infinite;
  pointer-events:none; border-radius:inherit;
}
.pill-dot   { width:5px; height:5px; border-radius:50%; background:currentColor; }
.pill-ocean { background:rgba(26,58,47,.1); color:var(--ocean); }
.pill-peach { background:rgba(255,205,173,.35); color:#7a4016; }
.pill-white { background:rgba(255,255,255,.15); color:#fff; }
.pill-teal  { background:rgba(4,169,167,.13); color:var(--indigo); }

/* ── BUTTONS ────────────────────────────────────── */
.btn {
  display:inline-flex; align-items:center; gap:9px;
  font-family:'Cabin',sans-serif;
  font-size:.93rem; font-weight:600;
  text-decoration:none; border:none; cursor:pointer;
  position:relative; overflow:hidden;
  transition:transform .22s var(--spring),box-shadow .22s,background .18s,color .18s,border-color .18s;
  -webkit-tap-highlight-color:transparent;
}
.btn:active { transform:scale(.96)!important; }
.btn-ripple {
  position:absolute; border-radius:50%; pointer-events:none;
  background:rgba(255,255,255,.25);
  transform:scale(0); animation:ripple .6s ease-out forwards;
}
.btn-primary {
  background:var(--ocean); color:#fff;
  padding:14px 26px; border-radius:var(--r-sm);
  box-shadow:0 4px 16px rgba(26,58,47,.22);
}
.btn-primary:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(26,58,47,.32); }
.btn i { display:inline-block; transition:transform .22s var(--spring); }
.btn:hover i { transform:translateX(4px); }
.btn-ghost {
  background:rgba(255,255,255,.1); color:#fff;
  padding:14px 26px; border-radius:var(--r-sm);
  border:1.5px solid rgba(255,255,255,.22);
  display:inline-flex; align-items:center; gap:9px;
  font-weight:600; font-size:.93rem; text-decoration:none;
  transition:background .2s,transform .22s var(--spring);
}
.btn-ghost:hover { background:rgba(255,255,255,.18); transform:translateY(-2px); }

/* ── TOUCH STATES ───────────────────────────────── */
@media(hover:none){
  .btn:active,.btn-ghost:active { transform:scale(.95)!important; filter:brightness(.92); }
}

/* ═══════════════════════════════════════════════
   NAV — identical to home & services
═══════════════════════════════════════════════ */
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
.nav-inner {
  max-width:1160px; margin:0 auto;
  height:64px; display:flex; align-items:center; justify-content:space-between;
}
@media(max-width:767px){ .nav-inner{ height:60px; } }
.nav-logo { display:flex; align-items:center; text-decoration:none; transition:opacity .2s; }
.nav-logo:hover { opacity:.72; }
.nav-logo-img { height:96px; width:auto; object-fit:contain; display:block; }
@media(max-width:767px){ .nav-logo-img{ height:52px; } }
.nav-links { display:none; align-items:center; gap:30px; list-style:none; }
@media(min-width:768px){ .nav-links{ display:flex; } }
.nav-links a {
  font-size:.85rem; font-weight:500; color:var(--muted);
  text-decoration:none; position:relative; padding:4px 0; transition:color .2s;
}
.nav-links a::after {
  content:''; position:absolute; bottom:-2px; left:0; right:0;
  height:1.5px; background:var(--ocean);
  transform:scaleX(0); transform-origin:left;
  transition:transform .28s var(--spring);
}
.nav-links a:hover,.nav-links a.active { color:var(--ocean); }
.nav-links a:hover::after,.nav-links a.active::after { transform:scaleX(1); }
.nav-cta {
  background:var(--ocean)!important; color:#fff!important;
  padding:9px 20px!important; border-radius:var(--r-sm)!important;
  transition:background .2s,transform .2s var(--spring)!important;
}
.nav-cta::after { display:none!important; }
.nav-cta:hover { background:var(--ocean-h)!important; transform:translateY(-1px)!important; }
.nav-mob {
  display:flex; flex-direction:column; gap:5px;
  background:none; border:none; cursor:pointer; padding:8px;
  color:var(--ocean); -webkit-tap-highlight-color:transparent;
}
@media(min-width:768px){ .nav-mob{ display:none; } }
.nav-mob span { display:block; width:22px; height:2px; background:currentColor; border-radius:2px; transition:all .28s var(--smooth); }
.nav-mob.active span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
.nav-mob.active span:nth-child(2) { opacity:0; transform:scaleX(0); }
.nav-mob.active span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }
.nav-drawer {
  display:none; flex-direction:column;
  position:absolute; top:60px; left:0; right:0;
  background:rgba(240,255,219,.97);
  backdrop-filter:blur(24px); -webkit-backdrop-filter:blur(24px);
  padding:16px 18px 24px; gap:4px;
  box-shadow:0 12px 40px rgba(26,58,47,.1);
  border-top:1px solid rgba(26,58,47,.07);
  transform:translateY(-8px); opacity:0;
  transition:opacity .25s var(--out),transform .25s var(--out);
}
.nav-drawer.open { display:flex; opacity:1; transform:none; }
.nav-drawer a {
  font-size:.95rem; font-weight:500; color:var(--ocean);
  text-decoration:none; padding:13px 0;
  border-bottom:1px solid rgba(26,58,47,.07);
  display:flex; align-items:center; justify-content:space-between;
  transition:opacity .2s;
}
.nav-drawer a:last-child { border-bottom:none; }
.nav-drawer a:hover { opacity:.65; }
.nav-drawer-cta {
  margin-top:12px; display:block;
  background:var(--ocean); color:#fff!important;
  padding:14px 20px; border-radius:var(--r-sm);
  text-align:center; font-weight:600; border:none!important;
}

/* ═══════════════════════════════════════════════
   PAGE HERO
═══════════════════════════════════════════════ */
.about-hero {
  padding-top:64px;
  background:var(--cream);
  position:relative; overflow:hidden;
  min-height:80svh;
  display:flex; align-items:center;
}
.ah-glow {
  position:absolute; border-radius:50%; pointer-events:none;
}
.ah-g1 {
  width:min(600px,130vw); height:min(600px,130vw);
  background:radial-gradient(circle,rgba(4,169,167,.2) 0%,transparent 65%);
  top:-20%; right:-15%;
}
.ah-g2 {
  width:min(400px,90vw); height:min(400px,90vw);
  background:radial-gradient(circle,rgba(255,205,173,.28) 0%,transparent 65%);
  bottom:-10%; left:-8%;
}
.ah-inner {
  padding:72px 0 80px;
  width:100%;
}
@media(max-width:767px){ .ah-inner{ padding:52px 0 56px; } }

.ah-grid {
  display:grid; grid-template-columns:1fr;
  gap:48px; align-items:center;
}
@media(min-width:900px){ .ah-grid{ grid-template-columns:1fr 1fr; gap:72px; } }

/* Hero text */
.ah-eyebrow { animation:fadeUp .7s .05s var(--out) both; }
.ah-h1 {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(2.2rem,7.5vw,4rem);
  font-weight:400; line-height:1.08;
  letter-spacing:-.04em; color:var(--ocean);
  margin:16px 0 0;
  animation:fadeUp .7s .14s var(--out) both;
}
.ah-h1 em {
  font-style:italic; color:var(--indigo);
  position:relative;
}
.ah-h1 em::after {
  content:''; position:absolute;
  left:0; right:0; bottom:2px;
  height:2.5px; background:var(--peach); border-radius:2px;
  transform-origin:left;
  animation:drawLine .75s 1.1s var(--spring) both;
}
.ah-lead {
  font-size:1.1rem; color:var(--muted);
  line-height:1.82; max-width:520px;
  margin-top:20px;
  animation:fadeUp .7s .22s var(--out) both;
}
@media(max-width:767px){ .ah-lead{ font-size:1rem; } }

/* Hero visual */
.ah-visual { animation:fadeIn .9s .3s both; }
@media(max-width:899px){ .ah-visual{ display:none; } }
.ah-photo-wrap {
  position:relative;
  border-radius:var(--r-xl); overflow:hidden;
  height:440px;
}
.ah-photo-wrap img {
  width:100%; height:100%; object-fit:cover;
  transition:transform 6s ease;
}
.ah-photo-wrap:hover img { transform:scale(1.04); }
.ah-photo-overlay {
  position:absolute; inset:0;
  background:linear-gradient(160deg,transparent 45%,rgba(26,58,47,.4) 100%);
}
/* Floating quote card */
.ah-quote-card {
  position:absolute; bottom:24px; left:-28px;
  background:rgba(255,255,255,.93);
  backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px);
  border:1px solid rgba(255,255,255,.72);
  border-radius:var(--r-md);
  padding:18px 20px;
  max-width:240px;
  box-shadow:0 10px 36px rgba(26,58,47,.14);
  animation:slideR .8s .95s both;
  transition:transform .3s var(--spring);
}
.ah-quote-card:hover { transform:translateY(-4px) scale(1.015); }
.ah-quote-text {
  font-family:'Nanum Myeongjo',serif;
  font-style:italic; font-size:.92rem;
  color:var(--ocean); line-height:1.55;
}
.ah-quote-attr {
  font-size:.68rem; font-weight:600;
  color:var(--muted); margin-top:10px;
  text-transform:uppercase; letter-spacing:.06em;
}

/* ═══════════════════════════════════════════════
   MARQUEE BAND
═══════════════════════════════════════════════ */
.marquee-sec { background:var(--ocean); padding:16px 0; overflow:hidden; }
@keyframes marquee { from{transform:translateX(0)} to{transform:translateX(-50%)} }
.marquee-track {
  display:flex; align-items:center;
  width:max-content;
  animation:marquee 30s linear infinite;
}
.marquee-track:hover { animation-play-state:paused; }
.marquee-item {
  display:flex; align-items:center; gap:9px;
  padding:0 32px; white-space:nowrap;
  font-size:.8rem; font-weight:500;
  color:rgba(255,255,255,.6);
  border-right:1px solid rgba(255,255,255,.12);
}
.marquee-item strong { color:#fff; }
.marquee-item i { font-size:14px; color:var(--yellow); transition:transform .2s var(--spring); }
.marquee-item:hover i { transform:scale(1.3) rotate(10deg); }
@media(max-width:767px){ .marquee-item{ padding:0 22px; font-size:.75rem; } }

/* ═══════════════════════════════════════════════
   ORIGIN STORY
═══════════════════════════════════════════════ */
.origin-sec {
  padding:96px 0;
  background:#fff;
  position:relative;
}
@media(max-width:767px){ .origin-sec{ padding:64px 0; } }

.origin-grid {
  display:grid; grid-template-columns:1fr;
  gap:56px; align-items:start;
}
@media(min-width:900px){ .origin-grid{ grid-template-columns:5fr 4fr; gap:80px; } }

/* Pull quote accent */
.origin-body h2 {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.75rem,4.5vw,2.6rem);
  font-weight:400; line-height:1.12;
  letter-spacing:-.03em; color:var(--ocean);
  margin-bottom:24px;
}
.origin-body p {
  font-size:1rem; color:var(--muted);
  line-height:1.82; margin-bottom:20px;
}
.origin-body p:last-child { margin-bottom:0; }

/* Pull-quote block */
.pull-quote {
  border-left:3px solid var(--yellow);
  padding:18px 22px;
  background:var(--cream);
  border-radius:0 var(--r-md) var(--r-md) 0;
  margin:28px 0;
}
.pull-quote p {
  font-family:'Nanum Myeongjo',serif;
  font-style:italic;
  font-size:1.05rem; line-height:1.72;
  color:var(--ocean); margin:0;
}

/* Side stats panel */
.origin-aside { display:flex; flex-direction:column; gap:16px; }
@media(min-width:900px){ .origin-aside{ position:sticky; top:90px; } }

.origin-stat-card {
  background:var(--cream);
  border-radius:var(--r-lg);
  padding:28px 24px;
  border:1px solid rgba(26,58,47,.07);
  transition:transform .28s var(--spring), box-shadow .28s;
}
.origin-stat-card:hover { transform:translateY(-3px); box-shadow:0 10px 32px rgba(26,58,47,.08); }
.origin-stat-num {
  font-family:'Nanum Myeongjo',serif;
  font-size:2.8rem; font-weight:400;
  color:var(--ocean); line-height:1; letter-spacing:-.04em;
}
.origin-stat-lbl {
  font-size:.82rem; color:var(--muted);
  margin-top:6px; line-height:1.55;
}
.origin-photo {
  border-radius:var(--r-lg); overflow:hidden;
  height:220px;
  transition:box-shadow .28s;
}
.origin-photo:hover { box-shadow:0 12px 36px rgba(26,58,47,.12); }
.origin-photo img {
  width:100%; height:100%; object-fit:cover;
  transition:transform 5s ease;
}
.origin-photo:hover img { transform:scale(1.04); }

/* ═══════════════════════════════════════════════
   WHAT WE DO
═══════════════════════════════════════════════ */
.what-sec {
  padding:96px 0;
  background:var(--ocean);
  position:relative; overflow:hidden;
}
@media(max-width:767px){ .what-sec{ padding:64px 0; } }
.what-orb {
  position:absolute; border-radius:50%; pointer-events:none;
}
.wo1 {
  width:min(500px,120vw); height:min(500px,120vw);
  background:radial-gradient(circle,rgba(4,169,167,.14) 0%,transparent 65%);
  top:-20%; right:-15%;
}
.wo2 {
  width:min(350px,80vw); height:min(350px,80vw);
  background:radial-gradient(circle,rgba(255,205,173,.1) 0%,transparent 65%);
  bottom:-15%; left:-8%;
}
.what-inner { position:relative; z-index:2; }

.what-grid {
  display:grid; grid-template-columns:1fr;
  gap:52px; align-items:start;
  margin-top:56px;
}
@media(min-width:768px){ .what-grid{ grid-template-columns:1fr 1fr; gap:48px; } }
@media(max-width:767px){ .what-grid{ margin-top:36px; } }

.what-text p {
  font-size:1rem; color:rgba(255,255,255,.72);
  line-height:1.82; margin-bottom:18px;
}
.what-text p:last-child { margin-bottom:0; }
.what-text strong { color:#fff; font-weight:600; }

.what-distinction {
  background:rgba(255,255,255,.07);
  border:1px solid rgba(255,255,255,.12);
  border-radius:var(--r-lg);
  padding:28px 24px;
  display:flex; flex-direction:column; gap:14px;
}
.wd-row {
  display:flex; align-items:flex-start; gap:14px;
  padding-bottom:14px;
  border-bottom:1px solid rgba(255,255,255,.08);
}
.wd-row:last-child { padding-bottom:0; border-bottom:none; }
.wd-icon {
  width:40px; height:40px; flex-shrink:0;
  border-radius:10px;
  display:flex; align-items:center; justify-content:center;
  transition:transform .25s var(--spring);
}
.wd-row:hover .wd-icon { transform:scale(1.1) rotate(6deg); }
.wd-icon i { font-size:18px; }
.wd-icon.teal { background:rgba(4,169,167,.2); }
.wd-icon.teal i { color:#04A9A7; }
.wd-icon.peach { background:rgba(255,205,173,.15); }
.wd-icon.peach i { color:var(--peach); }
.wd-icon.cream { background:rgba(240,255,219,.12); }
.wd-icon.cream i { color:var(--cream); }
.wd-label {
  font-family:'Nanum Myeongjo',serif;
  font-size:.95rem; color:#fff;
  line-height:1.35; margin-bottom:4px;
}
.wd-desc { font-size:.83rem; color:rgba(255,255,255,.6); line-height:1.6; }

/* ═══════════════════════════════════════════════
   CORE VALUES
═══════════════════════════════════════════════ */
.values-sec {
  padding:96px 0;
  background:var(--cream);
}
@media(max-width:767px){ .values-sec{ padding:64px 0; } }

.values-intro {
  max-width:600px; margin-bottom:60px;
}
@media(max-width:767px){ .values-intro{ margin-bottom:40px; } }

.values-grid {
  display:grid; grid-template-columns:1fr;
  gap:16px;
}
@media(min-width:640px){ .values-grid{ grid-template-columns:repeat(2,1fr); } }
@media(min-width:1024px){ .values-grid{ grid-template-columns:repeat(3,1fr); } }

.value-card {
  background:#fff;
  border-radius:var(--r-xl);
  padding:32px 28px;
  border:1px solid rgba(26,58,47,.07);
  display:flex; flex-direction:column; gap:16px;
  transition:transform .3s var(--spring), box-shadow .3s, border-color .28s;
  position:relative; overflow:hidden;
}
.value-card::before {
  content:''; position:absolute; inset:0; opacity:0;
  transition:opacity .3s;
}
.value-card:hover { transform:translateY(-5px); box-shadow:0 20px 56px rgba(26,58,47,.1); border-color:rgba(26,58,47,.14); }
.value-card:hover::before { opacity:1; }

/* Card accent colours */
.vc-teal  { }
.vc-teal::before  { background:radial-gradient(circle at 80% 80%,rgba(4,169,167,.07) 0%,transparent 55%); }
.vc-peach { }
.vc-peach::before { background:radial-gradient(circle at 80% 80%,rgba(255,205,173,.14) 0%,transparent 55%); }
.vc-ocean { background:var(--ocean); border-color:var(--ocean); }
.vc-ocean::before { background:radial-gradient(circle at 80% 80%,rgba(4,169,167,.12) 0%,transparent 55%); }

.value-ico {
  width:48px; height:48px; border-radius:14px;
  display:flex; align-items:center; justify-content:center;
  transition:transform .3s var(--spring), background .22s;
}
.vc-teal  .value-ico { background:rgba(4,169,167,.12); }
.vc-peach .value-ico { background:rgba(255,205,173,.25); }
.vc-ocean .value-ico { background:rgba(255,255,255,.12); }
.value-ico i { font-size:22px; color:var(--ocean); }
.vc-ocean .value-ico i { color:#fff; }
.value-card:hover .value-ico { transform:scale(1.12) rotate(-6deg); }
.vc-teal:hover  .value-ico { background:var(--ocean); }
.vc-teal:hover  .value-ico i { color:#fff; }
.vc-peach:hover .value-ico { background:var(--ocean); }
.vc-peach:hover .value-ico i { color:#fff; }

.value-title {
  font-family:'Nanum Myeongjo',serif;
  font-size:1.2rem; font-weight:400;
  color:var(--ocean); line-height:1.25;
}
.vc-ocean .value-title { color:var(--yellow); }

.value-body {
  font-size:.88rem; color:var(--muted); line-height:1.72;
}
.vc-ocean .value-body { color:rgba(255,255,255,.68); }

/* ═══════════════════════════════════════════════
   MISSION + VISION
═══════════════════════════════════════════════ */
.mv-sec {
  padding:96px 0;
  background:#fff;
}
@media(max-width:767px){ .mv-sec{ padding:64px 0; } }

.mv-grid {
  display:grid; grid-template-columns:1fr;
  gap:20px;
}
@media(min-width:768px){ .mv-grid{ grid-template-columns:1fr 1fr; gap:24px; } }

.mv-card {
  border-radius:var(--r-xl);
  padding:44px 36px;
  position:relative; overflow:hidden;
  transition:transform .3s var(--spring), box-shadow .3s;
}
.mv-card:hover { transform:translateY(-5px); box-shadow:0 24px 64px rgba(26,58,47,.13); }
@media(max-width:767px){ .mv-card{ padding:32px 24px; } }

.mv-mission { background:var(--ocean); }
.mv-vision  { background:var(--cream); border:1px solid rgba(26,58,47,.08); }

/* decorative orbs inside cards */
.mv-card-orb {
  position:absolute; border-radius:50%; pointer-events:none;
}
.mv-mission .mv-card-orb {
  width:280px; height:280px;
  background:radial-gradient(circle,rgba(4,169,167,.18) 0%,transparent 65%);
  bottom:-80px; right:-60px;
}
.mv-vision .mv-card-orb {
  width:250px; height:250px;
  background:radial-gradient(circle,rgba(26,58,47,.06) 0%,transparent 65%);
  bottom:-60px; right:-40px;
}
.mv-card-inner { position:relative; z-index:2; }

.mv-label {
  display:inline-flex; align-items:center; gap:7px;
  font-size:.68rem; font-weight:700;
  letter-spacing:.09em; text-transform:uppercase;
  margin-bottom:20px;
}
.mv-mission .mv-label { color:rgba(255,255,255,.55); }
.mv-vision  .mv-label { color:var(--muted); }
.mv-label i { font-size:14px; }
.mv-mission .mv-label i { color:var(--yellow); }
.mv-vision  .mv-label i { color:var(--ocean); }

.mv-heading {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.5rem,3.5vw,2rem);
  font-weight:400; line-height:1.18;
  letter-spacing:-.025em;
  margin-bottom:16px;
}
.mv-mission .mv-heading { color:#fff; }
.mv-vision  .mv-heading { color:var(--ocean); }

.mv-body {
  font-size:.95rem; line-height:1.78;
}
.mv-mission .mv-body { color:rgba(255,255,255,.7); }
.mv-vision  .mv-body { color:var(--muted); }

/* ═══════════════════════════════════════════════
   CTA FOOTER SECTION
═══════════════════════════════════════════════ */
.about-cta {
  padding:96px 0;
  background:var(--cream);
  position:relative; overflow:hidden;
}
@media(max-width:767px){ .about-cta{ padding:64px 0; } }
.ac-glow {
  position:absolute; border-radius:50%; pointer-events:none;
}
.ac-g1 {
  width:min(480px,110vw); height:min(480px,110vw);
  background:radial-gradient(circle,rgba(4,169,167,.18) 0%,transparent 65%);
  top:-20%; right:-10%;
}
.ac-inner { text-align:center; position:relative; z-index:2; }
.ac-heading {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(2rem,6vw,3.4rem);
  font-weight:400; line-height:1.1;
  letter-spacing:-.03em; color:var(--ocean);
  margin:18px 0 14px;
}
.ac-heading em { font-style:italic; color:var(--indigo); }
.ac-sub {
  font-size:1rem; color:var(--muted);
  max-width:440px; margin:0 auto 36px; line-height:1.75;
}
.ac-actions {
  display:flex; align-items:center; justify-content:center;
  flex-wrap:wrap; gap:12px;
}
@media(max-width:767px){
  .ac-actions { flex-direction:column; align-items:stretch; }
  .ac-actions .btn { width:100%; justify-content:center; padding:16px 20px; }
}
.btn-outline-dark {
  background:transparent; color:var(--ocean);
  padding:14px 26px; border-radius:var(--r-sm);
  border:1.5px solid rgba(26,58,47,.28);
  display:inline-flex; align-items:center; gap:9px;
  font-weight:600; font-size:.93rem; text-decoration:none;
  transition:background .2s,color .2s,border-color .2s,transform .22s var(--spring);
}
.btn-outline-dark:hover { background:var(--ocean); color:#fff; border-color:var(--ocean); transform:translateY(-2px); }

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

{{-- ═══════════════════════════════════════════════
     PAGE HERO
═══════════════════════════════════════════════ --}}
<section class="about-hero" aria-label="About CavalCare">
  <div class="ah-glow ah-g1" aria-hidden="true"></div>
  <div class="ah-glow ah-g2" aria-hidden="true"></div>

  <div class="wrap">
    <div class="ah-inner">
      <div class="ah-grid">

        {{-- Text --}}
        <div>
          <div class="ah-eyebrow">
            <span class="pill pill-ocean">
              <span class="pill-dot"></span>
              Our Story
            </span>
          </div>

          <h1 class="ah-h1">
            We started because too many families were going through this
            <em>alone</em>
          </h1>

          <p class="ah-lead">
            Someone gets discharged from the hospital. The doctors say "rest at home."
            
            But at home, there's no healthcare professional. No one monitoring the condition closely as a professional, until there is an emergency...
          </p>
        </div>

        {{-- Visual --}}
        <div class="ah-visual" aria-hidden="true">
          <div class="ah-photo-wrap">
            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&h=900&fit=crop"
                 alt="Nurse caring for patient at home" loading="eager">
            <div class="ah-photo-overlay"></div>
          </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</section>


{{-- ═══════════════════════════════════════════════
     MARQUEE BAND
═══════════════════════════════════════════════ --}}
<div class="marquee-sec" aria-hidden="true">
  <div class="marquee-track">
    @php
    $mItems = [
      ['i'=>'bi-patch-check',  't'=>'Licensed Healthcare Professionals'],
      ['i'=>'bi-shield-check', 't'=>'Background Verified'],
      ['i'=>'bi-heart',        't'=>'Dignity in Every Visit'],
      ['i'=>'bi-bell',         't'=>'Real-Time Reports'],
      ['i'=>'bi-clock',        't'=>'24 / 7 Coordinators'],
      ['i'=>'bi-house-heart',  't'=>'In-Home Professional Care'],
      ['i'=>'bi-people',       't'=>'Family-First Approach'],
      ['i'=>'bi-patch-check',  't'=>'Licensed Healthcare Professionals'],
      ['i'=>'bi-shield-check', 't'=>'Background Verified'],
      ['i'=>'bi-heart',        't'=>'Dignity in Every Visit'],
      ['i'=>'bi-bell',         't'=>'Real-Time Reports'],
      ['i'=>'bi-clock',        't'=>'24 / 7 Coordinators'],
      ['i'=>'bi-house-heart',  't'=>'In-Home Professional Care'],
      ['i'=>'bi-people',       't'=>'Family-First Approach'],
    ];
    @endphp
    @foreach($mItems as $m)
      <div class="marquee-item">
        <i class="bi {{ $m['i'] }}"></i>
        <strong>{{ $m['t'] }}</strong>
      </div>
    @endforeach
  </div>
</div>


{{-- ═══════════════════════════════════════════════
     OUR STORY
═══════════════════════════════════════════════ --}}
<section class="origin-sec" aria-labelledby="story-heading">
  <div class="wrap">
    <div class="origin-grid">

      {{-- Body text --}}
      <div class="origin-body">
        
        <div class="reveal" style="transition-delay:.08s;">
          <h2 id="story-heading">
            In Nigeria, when someone leaves the hospital, the expectation is that the family takes over
          </h2>
        </div>

        <div class="reveal" style="transition-delay:.14s;">
          <p>
            But most families aren't trained healthcare professionals. They're sons and daughters, husbands and wives;
            professionals with jobs and children of their own, who love someone deeply and have no idea
            how to care for them clinically.
          </p>

          <div class="pull-quote">
            <p>
            Meanwhile, the person who just had surgery, or the parent who's getting older, or the mother four weeks postpartum need real medical support. 
            </p>
          </div>

          <p>
            We saw this. And we decided to do something about it.
          </p>
        </div>
      </div>

      {{-- Aside stats --}}
      <div class="origin-aside">
        <div class="origin-stat-card reveal" style="transition-delay:.1s;">
          <div class="origin-stat-num" data-count="500" data-suffix="+">500+</div>
          <div class="origin-stat-lbl">Families supported with professional in-home care across Nigeria</div>
        </div>

        <div class="origin-stat-card reveal" style="transition-delay:.18s;">
          <div class="origin-stat-num" data-count="98" data-suffix="%">98%</div>
          <div class="origin-stat-lbl">Family satisfaction rate, measured after every care cycle</div>
        </div>

        <div class="origin-photo reveal" style="transition-delay:.26s;">
          <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=600&h=440&fit=crop"
               alt="CavalCare nurse with elderly patient" loading="lazy">
        </div>
      </div>

    </div>
  </div>
</section>


{{-- ═══════════════════════════════════════════════
     WHAT WE DO
═══════════════════════════════════════════════ --}}
<section class="what-sec" aria-labelledby="what-heading">
  <div class="what-orb wo1" aria-hidden="true"></div>
  <div class="what-orb wo2" aria-hidden="true"></div>

  <div class="wrap">
    <div class="what-inner">

      <div class="reveal">
        <span class="pill pill-white">
          <span class="pill-dot"></span>
          What We Do
        </span>
        <h2 id="what-heading" style="font-family:'Nanum Myeongjo',serif;font-size:clamp(1.9rem,5vw,3rem);font-weight:400;line-height:1.1;letter-spacing:-.03em;color:#fff;margin:14px 0 0;">
          We connect Nigerian families with trained, verified nursing professionals for in-home care
        </h2>
      </div>

      <div class="what-grid" style="margin-top:52px;">

        <div class="what-text reveal" style="transition-delay:.1s;">
          <p>
          Whether your loved one needs support after surgery, help through a terminal illness, post-natal recovery care, or consistent elderly support, we will make sure a qualified healthcare professional is there.
          </p>
          <p>
            Not an informal helper. Not a trained caregiver.
            <strong>A licensed healthcare professional</strong>, background-checked, matched to your loved one's
            specific situation, and accountable to you every step of the way.
          </p>
        </div>

        <div class="what-distinction reveal" style="transition-delay:.18s;">
          <div class="wd-row">
            <div class="wd-icon teal"><i class="bi bi-patch-check"></i></div>
            <div>
              <div class="wd-label">Licensed &amp; Verified</div>
              <div class="wd-desc">Every healthcare professional carries a valid nursing licence and passes our background verification before being placed with any family.</div>
            </div>
          </div>
          <div class="wd-row">
            <div class="wd-icon peach"><i class="bi bi-person-check"></i></div>
            <div>
              <div class="wd-label">Matched to Your Situation</div>
              <div class="wd-desc">We don't send the nearest available healthcare professional. We match based on your loved one's specific clinical needs.</div>
            </div>
          </div>
          <div class="wd-row">
            <div class="wd-icon cream"><i class="bi bi-bell"></i></div>
            <div>
              <div class="wd-label">Accountable to You</div>
              <div class="wd-desc">A full post-visit report goes to you after every single visit. You always know what happened, what was done, and what comes next.</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>


{{-- ═══════════════════════════════════════════════
     CORE VALUES
═══════════════════════════════════════════════ --}}
<section class="values-sec" aria-labelledby="values-heading">
  <div class="wrap">

    <div class="values-intro reveal">
      <span class="pill pill-ocean sec-eyebrow">Our Core Values</span>
      <h2 id="values-heading" style="font-family:'Nanum Myeongjo',serif;font-size:clamp(1.9rem,5vw,2.8rem);font-weight:400;line-height:1.1;letter-spacing:-.03em;color:var(--ocean);margin-top:10px;">
        The principles we refuse to compromise on
      </h2>
    </div>

    <div class="values-grid">

      {{-- Clinical Integrity --}}
      <div class="value-card vc-teal reveal" style="transition-delay:.07s;">
        <div class="value-ico"><i class="bi bi-hospital"></i></div>
        <div class="value-title">Clinical Integrity</div>
        <div class="value-body">
          We are not replacing doctors or hospitals. Every healthcare professional we place works within the
          clinical plan set by your loved one's medical team. We respect medical boundaries.
          And we escalate to the hospital when a situation needs immediate attention before
          it becomes an emergency.
        </div>
      </div>

      {{-- Client First --}}
      <div class="value-card vc-peach reveal" style="transition-delay:.14s;">
        <div class="value-ico"><i class="bi bi-heart"></i></div>
        <div class="value-title">Client First Approach to Care</div>
        <div class="value-body">
          Many families have experienced loved ones treated as burdens in hospitals.
          We ensure that the elderly parent, the disabled child, the post-natal mother,
          and the terminal patient are always treated with full human dignity.
          This is non-negotiable in every healthcare professional interaction.
        </div>
      </div>

      {{-- Security --}}
      <div class="value-card vc-ocean reveal" style="transition-delay:.21s;">
        <div class="value-ico"><i class="bi bi-shield-check"></i></div>
        <div class="value-title">Security</div>
        <div class="value-body">
        Every family that books with us is letting a stranger into their home, often to care for someone vulnerable. We don't take that lightly. Background checks, license verification are a crucial part of our recruitment process.
        </div>
      </div>

    </div>
  </div>
</section>


{{-- ═══════════════════════════════════════════════
     MISSION + VISION
═══════════════════════════════════════════════ --}}
<section class="mv-sec" aria-label="Mission and Vision">
  <div class="wrap">

    <div class="reveal" style="margin-bottom:48px;">
      <span class="pill pill-teal sec-eyebrow">Where We Are Going</span>
      <h2 style="font-family:'Nanum Myeongjo',serif;font-size:clamp(1.9rem,5vw,2.8rem);font-weight:400;line-height:1.1;letter-spacing:-.03em;color:var(--ocean);margin-top:10px;">
        Our mission &amp; our vision
      </h2>
    </div>

    <div class="mv-grid">

      {{-- Mission --}}
      <div class="mv-card mv-mission reveal" style="transition-delay:.08s;">
        <div class="mv-card-orb" aria-hidden="true"></div>
        <div class="mv-card-inner">
          <div class="mv-label">
            <i class="bi bi-geo-alt"></i>
            Our Mission
          </div>
          <h3 class="mv-heading">
            Bringing professional nursing care into the homes of every Nigerian who needs it
          </h3>
          <p class="mv-body">
          We are bringing professional nursing care in the homes of every Nigerian who needs professional support after hospital discharge, so that healing happens at home, with intention.
          </p>
        </div>
      </div>

      {{-- Vision --}}
      <div class="mv-card mv-vision reveal" style="transition-delay:.16s;">
        <div class="mv-card-orb" aria-hidden="true"></div>
        <div class="mv-card-inner">
          <div class="mv-label">
            <i class="bi bi-eye"></i>
            Our Vision
          </div>
          <h3 class="mv-heading">
            No one leaves the hospital without a care plan waiting at home
          </h3>
          <p class="mv-body">
           
            We see a future where, no one leaves the hospital without a clear, professional, and compassionate care plan waiting for them at home, regardless of location.
          </p>
        </div>
      </div>

    </div>
  </div>
</section>


{{-- ═══════════════════════════════════════════════
     CTA
═══════════════════════════════════════════════ --}}
<section class="about-cta" aria-label="Get started with CavalCare">
  <div class="ac-glow ac-g1" aria-hidden="true"></div>

  <div class="wrap">
    <div class="ac-inner reveal">
      <span class="pill pill-ocean">
        <span class="pill-dot"></span>
        Ready When You Are
      </span>

      <h2 class="ac-heading">
        Your loved one deserves proper care<br>
        <em>and we have the right healthcare professional for them</em>
      </h2>

      <p class="ac-sub">
        Join the families who trust CavalCare for professional, dignified in-home care across Nigeria.
      </p>

      <div class="ac-actions">
        <a href="{{ url('/') }}#care-plans" class="btn btn-primary">
          <i class="bi bi-calendar-check"></i>
          Book a Care Plan
        </a>
        <a href="{{ url('/services') }}" class="btn-outline-dark">
          <i class="bi bi-grid"></i>
          View Our Services
        </a>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

/* ── NAV SCROLL ───────────────────────────── */
const nav = document.getElementById('mainNav');
const onScroll = () => nav.classList.toggle('scrolled', scrollY > 16);
window.addEventListener('scroll', onScroll, { passive: true });
onScroll();


/* ── SMOOTH ANCHOR SCROLL ─────────────────── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      window.scrollTo({ top: target.getBoundingClientRect().top + scrollY - 76, behavior: 'smooth' });
    }
  });
});

/* ── SCROLL REVEAL ────────────────────────── */
const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
  });
}, { threshold: .1, rootMargin: '0px 0px -50px 0px' });
document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale')
  .forEach(el => io.observe(el));

/* ── STAT COUNT-UP ────────────────────────── */
const statsIO = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.querySelectorAll('[data-count]').forEach(el => {
      const target = +el.dataset.count;
      const suffix = el.dataset.suffix || '';
      let start = null;
      const duration = 1400;
      const step = ts => {
        if (!start) start = ts;
        const progress = Math.min((ts - start) / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(ease * target) + suffix;
        if (progress < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    });
    statsIO.unobserve(e.target);
  });
}, { threshold: .6 });
document.querySelector('.origin-aside') && statsIO.observe(document.querySelector('.origin-aside'));

/* ── BUTTON RIPPLE ────────────────────────── */
const rs = document.createElement('style');
rs.textContent = '@keyframes ripple{to{transform:scale(2.2);opacity:0}}';
document.head.appendChild(rs);
document.querySelectorAll('.btn, .btn-ghost, .btn-outline-dark, .nav-cta').forEach(btn => {
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

/* ── HAPTIC ON TAP ────────────────────────── */
if ('vibrate' in navigator) {
  document.querySelectorAll('.btn, .btn-outline-dark, .nav-cta').forEach(b =>
    b.addEventListener('pointerdown', () => navigator.vibrate(6))
  );
}

/* ── PARALLAX HERO GLOWS ──────────────────── */
if (window.matchMedia('(min-width:900px) and (hover:hover)').matches) {
  const glows = document.querySelectorAll('.ah-glow');
  document.addEventListener('mousemove', e => {
    const cx = e.clientX / innerWidth  - .5;
    const cy = e.clientY / innerHeight - .5;
    glows.forEach((g, i) => {
      g.style.transform = `translate(${cx*(i+1)*20}px,${cy*(i+1)*20}px)`;
    });
  }, { passive: true });
}

/* ── LAZY IMAGE LOADED ────────────────────── */
document.querySelectorAll('img[loading="lazy"]').forEach(img => {
  if (img.complete) img.classList.add('img-loaded');
  else img.addEventListener('load', () => img.classList.add('img-loaded'));
});


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