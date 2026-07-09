{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'CavalCare — Licensed In-Home Healthcare in Nigeria')

@push('head')
{{-- Primary SEO --}}
<meta name="description" content="CavalCare connects Nigerian families with verified, licensed healthcare professionals for in-home care; elderly care, post-surgery recovery, post-natal support & more. Real-time visit reports. 24/7 support.">
<meta name="keywords" content="in-home healthcare Nigeria, home healthcare professional Lagos, elderly care healthcare professional Nigeria, post-surgery home care, post-natal healthcare professional, licensed home healthcare professional, CavalCare">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url('/') }}">

{{-- Open Graph --}}
<meta property="og:type"        content="website">
<meta property="og:url"         content="{{ url('/') }}">
<meta property="og:title"       content="CavalCare — Licensed In-Home Healthcare in Nigeria">
<meta property="og:description" content="Verified, licensed healthcare professionals sent to your loved one's home. Elderly care, post-surgery recovery, post-natal support. Real-time visit reports after every visit.">
<meta property="og:image"       content="{{ asset('images/og-home.jpg') }}">
<meta property="og:locale"      content="en_NG">
<meta property="og:site_name"   content="CavalCare">

{{-- Twitter Card --}}
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="CavalCare — Licensed In-Home Healthcare in Nigeria">
<meta name="twitter:description" content="Verified, licensed healthcare professionals sent to your loved one's home. Real-time visit reports. 24/7 support.">
<meta name="twitter:image"       content="{{ asset('images/og-home.jpg') }}">

{{-- Structured Data: LocalBusiness --}}
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "MedicalBusiness",
  "name": "CavalCare",
  "description": "CavalCare provides professional licensed in-home healthcare across Nigeria, including elderly care, post-surgery recovery, and post-natal support.",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('images/logo.png') }}",
  "telephone": "+2347063112134",
  "areaServed": "Nigeria",
  "address": {
    "@@type": "PostalAddress",
    "addressCountry": "NG"
  },
  "sameAs": [],
  "offers": {
    "@@type": "Offer",
    "description": "Licensed in-home healthcare plans for Nigerian families"
  }
}
</script>

{{-- FAQPage Schema (mirrors the FAQ accordion) --}}
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "How do I know the healthcare professional sent to my home is qualified and trustworthy?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Every CavalCare healthcare professional has their healthcare licence verified and passes a thorough background check before being assigned to any family."
      }
    },
    {
      "@type": "Question",
      "name": "How will I know what happened during a visit if I was not there?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "After every single visit you receive a detailed post-visit care report covering vitals, medications administered, and the plan for the next visit."
      }
    },
    {
      "@type": "Question",
      "name": "What if I am not happy with the healthcare professional assigned to us?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Just let us know and we will reassign a different healthcare professional at no extra cost and with no questions asked."
      }
    },
    {
      "@type": "Question",
      "name": "Can I reach someone at CavalCare outside of visit hours?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. Our care coordinators are available 24 hours a day, 7 days a week."
      }
    }
  ]
}
</script>
@endverbatim

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700;800&family=Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════
   TOKENS
═══════════════════════════════════════════════ */
:root {
  --cream:        #F0FFDB;
  --yellow:       #04A9A7;
  --ocean:        #1A3A2F;
  --indigo:       #04A9A7;
  --peach:        #FFCDAD;
  --cream-d:      #d4f5b8;
  --ocean-h:      #0f5c3d;
  --ocean-soft:   #e6f9f9;
  --text:         #1A3A2F;
  --muted:        #3d6b57;
  --white:        #ffffff;
  --r-xs:  6px;
  --r-sm:  10px;
  --r-md:  16px;
  --r-lg:  24px;
  --r-xl:  36px;
  --r-2xl: 48px;
  --spring: cubic-bezier(0.34,1.56,0.64,1);
  --out:    cubic-bezier(0,0,0.2,1);
  --smooth: cubic-bezier(0.4,0,0.2,1);
}

/* ═══════════════════════════════════════════════
   BASE — mobile first
═══════════════════════════════════════════════ */
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
html { scroll-behavior:smooth; -webkit-text-size-adjust:100%; }
body {
  font-family:'Cabin',system-ui,sans-serif;
  font-size:16px; line-height:1.65;
  color:var(--text); background:#fff;
  -webkit-font-smoothing:antialiased;
  overflow-x:hidden;
}
img { display:block; max-width:100%; }
a { color:inherit; }
:focus-visible { outline:2px solid var(--yellow); outline-offset:3px; border-radius:4px; }

/* ═══════════════════════════════════════════════
   KEYFRAMES
═══════════════════════════════════════════════ */
@keyframes fadeUp   { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:none} }
@keyframes fadeIn   { from{opacity:0} to{opacity:1} }
@keyframes scaleIn  { from{opacity:0;transform:scale(.93)} to{opacity:1;transform:scale(1)} }
@keyframes floatY   { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
@keyframes drawLine { from{transform:scaleX(0)} to{transform:scaleX(1)} }
@keyframes pulse    { 0%,100%{box-shadow:0 0 0 0 rgba(26,58,47,.22)} 60%{box-shadow:0 0 0 9px rgba(26,58,47,0)} }
@keyframes slideR   { from{opacity:0;transform:translateX(-26px)} to{opacity:1;transform:none} }
@keyframes slideL   { from{opacity:0;transform:translateX(26px)} to{opacity:1;transform:none} }
@keyframes marquee  { from{transform:translateX(0)} to{transform:translateX(-50%)} }
@keyframes ripple   { to{transform:scale(2.2);opacity:0} }
@keyframes spin     { to{transform:rotate(360deg)} }
@keyframes blink    { 0%,100%{opacity:1} 50%{opacity:.35} }

/* ═══════════════════════════════════════════════
   SCROLL REVEAL
═══════════════════════════════════════════════ */
.reveal {
  opacity:0;
  transform:translateY(28px);
  transition:opacity .7s var(--out), transform .7s var(--out);
}
.reveal.is-visible { opacity:1; transform:none; }

/* ═══════════════════════════════════════════════
   LAYOUT HELPERS
═══════════════════════════════════════════════ */
.wrap { max-width:1160px; margin:0 auto; padding:0 20px; }
@media(min-width:768px){ .wrap { padding:0 32px; } }

.pill {
  display:inline-flex; align-items:center; gap:6px;
  font-size:.7rem; font-weight:600;
  letter-spacing:.07em; text-transform:uppercase;
  padding:5px 13px; border-radius:100px;
}
.pill-dot { width:5px; height:5px; border-radius:50%; background:currentColor; }
.pill-ocean  { background:rgba(26,58,47,.1);   color:var(--ocean); }
.pill-yellow { background:var(--yellow);       color:var(--ocean); }
.pill-indigo { background:rgba(4,169,167,.13);color:var(--indigo); }
.pill-peach  { background:rgba(255,205,173,.35);color:#7a4016; }
.pill-white  { background:rgba(255,255,255,.15);color:#fff; }

.sec-eyebrow { display:block; margin-bottom:14px; }
.sec-title {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(1.9rem,6vw,3.2rem);
  font-weight:400; line-height:1.1;
  letter-spacing:-.03em; color:var(--ocean);
}
.sec-sub {
  font-size:1rem; color:var(--muted);
  max-width:480px; margin-top:12px; line-height:1.75;
}

/* ═══════════════════════════════════════════════
   BUTTONS
═══════════════════════════════════════════════ */
.btn {
  display:inline-flex; align-items:center; gap:9px;
  font-family:'Cabin',sans-serif;
  font-size:.93rem; font-weight:600;
  text-decoration:none; border:none; cursor:pointer;
  position:relative; overflow:hidden;
  transition:transform .22s var(--spring), box-shadow .22s, background .18s, color .18s, border-color .18s;
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
.btn-ghost {
  background:rgba(255,255,255,.1); color:#fff;
  padding:14px 26px; border-radius:var(--r-sm);
  border:1.5px solid rgba(255,255,255,.22);
  display:inline-flex; align-items:center; gap:9px;
  font-weight:600; font-size:.93rem; text-decoration:none;
  transition:background .2s,transform .22s var(--spring);
}
.btn-ghost:hover { background:rgba(255,255,255,.18); transform:translateY(-2px); }

/* ═══════════════════════════════════════════════
   NAV
═══════════════════════════════════════════════ */
.nav-wrap {
  position:fixed; top:0; left:0; right:0; z-index:200;
  padding:0 20px;
  transition:background .38s var(--smooth), box-shadow .38s var(--smooth);
}
@media(min-width:768px){ .nav-wrap{ padding:0 32px; } }
.nav-wrap.scrolled {
  background:rgba(240,255,219,.92);
  backdrop-filter:blur(24px);
  -webkit-backdrop-filter:blur(24px);
  box-shadow:0 1px 0 rgba(26,58,47,.08);
}
.nav-inner {
  max-width:1160px; margin:0 auto;
  height:64px; display:flex; align-items:center; justify-content:space-between;
}
.nav-logo {
  display: flex;
  align-items: center;
  text-decoration: none;
}

/* Logo image */
.nav-logo-img {
  height: 96px;   /* adjust to fit navbar */
  width: auto;
  object-fit: contain;
  display: block;
}
.nav-logo:hover { opacity:.72; }


/* Desktop links */
.nav-links {
  display:none; align-items:center; gap:30px; list-style:none;
}
@media(min-width:768px){ .nav-links { display:flex; } }
.nav-links a {
  font-size:.85rem; font-weight:500; color:var(--muted);
  text-decoration:none; position:relative; padding:4px 0;
  transition:color .2s;
}
.nav-links a::after {
  content:''; position:absolute; bottom:-2px; left:0; right:0;
  height:1.5px; background:var(--ocean);
  transform:scaleX(0); transform-origin:left;
  transition:transform .28s var(--spring);
}
.nav-links a:hover { color:var(--ocean); }
.nav-links a:hover::after { transform:scaleX(1); }
.nav-cta {
  background:var(--ocean)!important; color:#fff!important;
  padding:9px 20px!important; border-radius:var(--r-sm)!important;
  transition:background .2s,transform .2s var(--spring)!important;
}
.nav-cta::after { display:none!important; }
.nav-cta:hover { background:var(--ocean-h)!important; transform:translateY(-1px)!important; }

/* Mobile hamburger */
.nav-mob {
  display:flex; flex-direction:column; gap:5px;
  background:none; border:none; cursor:pointer; padding:8px;
  color:var(--ocean); -webkit-tap-highlight-color:transparent;
}
@media(min-width:768px){ .nav-mob { display:none; } }
.nav-mob span { display:block; width:22px; height:2px; background:currentColor; border-radius:2px; transition:all .28s var(--smooth); }
.nav-mob.active span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
.nav-mob.active span:nth-child(2) { opacity:0; transform:scaleX(0); }
.nav-mob.active span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }

/* Mobile drawer */
.nav-drawer {
  display:none; flex-direction:column;
  position:absolute; top:64px; left:0; right:0;
  background:rgba(240,255,219,.97);
  backdrop-filter:blur(24px); -webkit-backdrop-filter:blur(24px);
  padding:20px 24px 28px;
  gap:4px;
  box-shadow:0 12px 40px rgba(26,58,47,.1);
  border-top:1px solid rgba(26,58,47,.07);
  transform:translateY(-8px); opacity:0;
  transition:opacity .25s var(--out),transform .25s var(--out);
}
.nav-drawer.open {
  display:flex; opacity:1; transform:none;
}
.nav-drawer a {
  font-size:1rem; font-weight:500; color:var(--ocean);
  text-decoration:none; padding:12px 0;
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
   HERO
═══════════════════════════════════════════════ */
.hero {
  min-height:100svh;
  background:var(--cream);
  padding-top:64px;
  display:flex; align-items:center;
  position:relative; overflow:hidden;
}
/* ambient glows */
.glow {
  position:absolute; border-radius:50%; pointer-events:none;
  will-change:transform;
}
.glow-1 {
  width:min(680px,120vw); height:min(680px,120vw);
  background:radial-gradient(circle,rgba(4,169,167,.22) 0%,transparent 65%);
  top:-20%; right:-15%;
}
.glow-2 {
  width:min(480px,90vw); height:min(480px,90vw);
  background:radial-gradient(circle,rgba(255,205,173,.3) 0%,transparent 65%);
  bottom:-15%; left:-10%;
}
.glow-3 {
  width:min(320px,70vw); height:min(320px,70vw);
  background:radial-gradient(circle,rgba(4,169,167,.12) 0%,transparent 65%);
  top:38%; left:28%;
}

.hero-inner {
  width:100%; padding:52px 0 60px;
}
@media(min-width:768px){ .hero-inner{ padding:72px 0 80px; } }

.hero-grid {
  display:grid;
  grid-template-columns:1fr;
  gap:40px; align-items:center;
}
@media(min-width:900px){
  .hero-grid { grid-template-columns:1fr 1fr; gap:56px; }
}

/* text column */
.hero-eyebrow { animation:fadeUp .7s .05s var(--out) both; }
.hero-h1 {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(2.4rem,8vw,4.4rem);
  font-weight:400; line-height:1.06;
  letter-spacing:-.04em; color:var(--ocean);
  margin:16px 0 20px;
  animation:fadeUp .7s .14s var(--out) both;
}
.hero-h1 em {
  font-style:italic; color:var(--indigo); position:relative;
}
.hero-h1 em::after {
  content:''; position:absolute;
  left:0; right:0; bottom:2px;
  height:2.5px; background:var(--peach); border-radius:2px;
  transform-origin:left;
  animation:drawLine .75s 1.1s var(--spring) both;
}
.hero-p {
  font-size:1.05rem; color:var(--muted);
  max-width:440px; line-height:1.78;
  animation:fadeUp .7s .22s var(--out) both;
}
.hero-actions {
  display:flex; flex-wrap:wrap; gap:12px;
  margin-top:28px;
  animation:fadeUp .7s .3s var(--out) both;
}
.hero-stats {
  display:flex; align-items:center; gap:20px;
  margin-top:36px; padding-top:28px;
  border-top:1px solid rgba(26,58,47,.1);
  animation:fadeUp .7s .42s var(--out) both;
}
.stat-num {
  font-family:'Nanum Myeongjo',serif;
  font-size:2rem; font-weight:400;
  color:var(--ocean); line-height:1; letter-spacing:-.03em;
}
.stat-lbl {
  font-size:.72rem; color:var(--muted);
  margin-top:3px; font-weight:500;
  text-transform:uppercase; letter-spacing:.05em;
}
.stat-div { width:1px; height:34px; background:rgba(26,58,47,.12); }

/* visual column */
.hero-visual {
  position:relative;
  animation:scaleIn .85s .22s var(--spring) both;
}
@media(max-width:899px){ .hero-visual{ display:none; } }

/* Dual-photo collage */
.hero-collage {
  display:grid;
  grid-template-columns:1.1fr .9fr;
  grid-template-rows:auto auto;
  gap:12px;
}
.hero-photo {
  border-radius:var(--r-xl);
  overflow:hidden; position:relative;
}
.hero-photo img {
  width:100%; height:100%; object-fit:cover;
  transition:transform 5.5s ease;
}
.hero-photo:hover img { transform:scale(1.04); }
.hp-main { grid-row:1/3; height:440px; }
.hp-top  { height:200px; }
.hp-bot  { height:228px; }
.hero-photo-overlay {
  position:absolute; inset:0;
  background:linear-gradient(160deg,transparent 50%,rgba(26,58,47,.32) 100%);
}

/* floating UI cards on the visual */
.fc {
  position:absolute;
  background:rgba(255,255,255,.93);
  backdrop-filter:blur(16px);
  -webkit-backdrop-filter:blur(16px);
  border:1px solid rgba(255,255,255,.72);
  border-radius:var(--r-md);
  padding:14px 18px;
  box-shadow:0 8px 32px rgba(26,58,47,.12);
  transition:transform .3s var(--spring);
  z-index:10;
}
.fc:hover { transform:translateY(-4px) scale(1.015); }
.fc-tl {
  top:-20px; left:-22px; min-width:180px;
  animation:slideR .75s .9s both;
}
.fc-br {
  bottom:24px; right:-22px; min-width:200px;
  animation:slideL .75s 1s both;
}
.fc-lbl {
  font-size:.65rem; font-weight:600; color:var(--muted);
  text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px;
}
.fc-val {
  font-family:'Nanum Myeongjo',serif; font-size:1.5rem;
  color:var(--ocean); line-height:1; letter-spacing:-.02em;
}
.fc-hint { font-size:.7rem; color:var(--muted); margin-top:3px; }
.fc-dots { display:flex; gap:4px; margin-top:8px; }
.fc-dots span { width:7px; height:7px; border-radius:50%; }
.fc-row { display:flex; align-items:center; gap:9px; margin-top:7px; }
.fc-av {
  width:32px; height:32px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  font-family:'Nanum Myeongjo',serif; font-size:.85rem; flex-shrink:0;
}
.fc-rtext p { font-size:.76rem; font-weight:600; color:var(--ocean); line-height:1.2; }
.fc-rtext span { font-size:.68rem; color:var(--muted); }
.fc-btns { display:flex; gap:6px; margin-top:9px; }
.fc-btn {
  font-size:.72rem; font-weight:600;
  padding:7px 11px; border-radius:6px;
  text-decoration:none; transition:all .18s; cursor:pointer;
}
.fc-btn.dk { background:var(--ocean); color:#fff; }
.fc-btn.dk:hover { background:var(--ocean-h); }
.fc-btn.lt { background:rgba(26,58,47,.07); color:var(--ocean); }
.fc-btn.lt:hover { background:rgba(26,58,47,.13); }

.badge-247 {
  position:absolute; top:-16px; right:48px;
  width:58px; height:58px; border-radius:50%;
  background:var(--ocean); color:#fff;
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  font-family:'Nanum Myeongjo',serif; font-size:1.05rem; line-height:1;
  box-shadow:0 6px 18px rgba(26,58,47,.28);
  animation:fadeIn .6s 1.1s both, floatY 4.5s 1.5s ease-in-out infinite;
  z-index:11;
}
.badge-247 small {
  font-family:'Cabin',sans-serif; font-size:.55rem;
  font-weight:500; opacity:.8; margin-top:2px;
}

/* ═══════════════════════════════════════════════
   MARQUEE TRUST STRIP
═══════════════════════════════════════════════ */
.marquee-sec {
  background:var(--ocean);
  padding:18px 0;
  overflow:hidden;
  position:relative;
}
.marquee-track {
  display:flex; align-items:center;
  width:max-content;
  animation:marquee 28s linear infinite;
  gap:0;
}
.marquee-track:hover { animation-play-state:paused; }
.marquee-item {
  display:flex; align-items:center; gap:10px;
  padding:0 36px; white-space:nowrap;
  font-size:.82rem; font-weight:500;
  color:rgba(255,255,255,.65);
  border-right:1px solid rgba(255,255,255,.12);
}
.marquee-item strong { color:#fff; }
.marquee-item svg { width:16px; height:16px; color:var(--yellow); flex-shrink:0; }

/* ═══════════════════════════════════════════════
   TRUST PILLARS
═══════════════════════════════════════════════ */
.trust-sec { background:var(--cream); padding:64px 0; }
.trust-grid {
  display:grid;
  grid-template-columns:1fr;
  gap:16px;
}
@media(min-width:640px){ .trust-grid { grid-template-columns:repeat(3,1fr); } }

.trust-card {
  background:#fff;
  border-radius:var(--r-lg);
  padding:28px 24px;
  display:flex; flex-direction:column; gap:14px;
  border:1px solid rgba(26,58,47,.07);
  transition:transform .28s var(--spring), box-shadow .28s;
}
.trust-card:hover { transform:translateY(-4px); box-shadow:0 14px 40px rgba(26,58,47,.1); }
.trust-ico {
  width:44px; height:44px; border-radius:12px;
  background:rgba(26,58,47,.06);
  display:flex; align-items:center; justify-content:center;
  transition:background .2s, transform .2s var(--spring);
}
.trust-ico i { font-size:20px; color:var(--ocean); }
.trust-card:hover .trust-ico { background:var(--ocean); transform:scale(1.06); }
.trust-card:hover .trust-ico i { color:#fff; }
.trust-t { font-family:'Nanum Myeongjo',serif; font-size:1.15rem; font-weight:400; color:var(--ocean); }
.trust-d { font-size:.85rem; color:var(--muted); line-height:1.6; }

/* ═══════════════════════════════════════════════
   SERVICES
═══════════════════════════════════════════════ */
.svc-sec { padding:80px 0; background:#fff; }
@media(min-width:768px){ .svc-sec{ padding:100px 0; } }

.svc-header {
  display:flex; justify-content:space-between;
  align-items:flex-end; flex-wrap:wrap; gap:20px;
  margin-bottom:40px;
}
@media(min-width:768px){ .svc-header{ margin-bottom:52px; } }

/* Scrollable cards on mobile, grid on desktop */
.svc-scroll {
  display:flex; gap:14px;
  overflow-x:auto; overflow-y:visible;
  scroll-snap-type:x mandatory;
  -webkit-overflow-scrolling:touch;
  padding-bottom:16px;
  margin:0 -20px; padding-left:20px; padding-right:20px;
}
.svc-scroll::-webkit-scrollbar { display:none; }
@media(min-width:768px){
  .svc-scroll {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    overflow:visible; margin:0; padding:0;
  }
}

.svc-card {
  flex:0 0 78vw; max-width:300px;
  scroll-snap-align:start;
  border-radius:var(--r-lg);
  padding:30px 24px;
  display:flex; flex-direction:column; gap:14px;
  text-decoration:none;
  position:relative; overflow:hidden;
  transition:transform .28s var(--spring), box-shadow .28s;
}
@media(min-width:768px){
  .svc-card { flex:none; max-width:none; }
}
.svc-card::before {
  content:''; position:absolute; inset:0; opacity:0;
  transition:opacity .28s;
}
.svc-card:hover { transform:translateY(-5px); box-shadow:0 18px 52px rgba(26,58,47,.1); }
.svc-card:hover::before { opacity:1; }

/* color variants */
.c-ocean { background:var(--ocean); color:#fff; }
.c-ocean::before { background:radial-gradient(circle at 80% 80%,rgba(4,169,167,.12) 0%,transparent 55%); }
.c-cream  { background:var(--cream); color:var(--ocean); }
.c-peach  { background:rgba(255,205,173,.22); color:var(--ocean); }
.c-indigo { background:rgba(4,169,167,.1); color:var(--ocean); }
.c-yellow { background:var(--yellow); color:var(--ocean); }
.c-soft   { background:var(--ocean-soft); color:var(--ocean); }

.svc-icon-wrap {
  width:48px; height:48px; border-radius:14px;
  display:flex; align-items:center; justify-content:center;
  transition:transform .28s var(--spring);
}
.c-ocean .svc-icon-wrap  { background:rgba(255,255,255,.14); }
.c-cream .svc-icon-wrap  { background:rgba(26,58,47,.07); }
.c-peach .svc-icon-wrap  { background:rgba(255,205,173,.3); }
.c-indigo .svc-icon-wrap { background:rgba(4,169,167,.15); }
.c-yellow .svc-icon-wrap { background:rgba(26,58,47,.07); }
.c-soft .svc-icon-wrap   { background:rgba(26,58,47,.06); }
.svc-icon-wrap i { font-size:22px; }
.c-ocean .svc-icon-wrap i { color:var(--yellow); }
.svc-card:hover .svc-icon-wrap { transform:scale(1.1) rotate(6deg); }

.svc-name {
  font-family:'Nanum Myeongjo',serif; font-size:1.25rem;
  font-weight:400; line-height:1.22;
}
.svc-desc { font-size:.85rem; line-height:1.65; opacity:.75; flex-grow:1; }
.svc-link {
  display:inline-flex; align-items:center; gap:7px;
  font-size:.78rem; font-weight:600;
  text-transform:uppercase; letter-spacing:.04em;
  margin-top:4px; opacity:.6;
  transition:gap .22s var(--spring), opacity .2s;
}
.svc-link i { font-size:14px; }
.svc-card:hover .svc-link { gap:12px; opacity:1; }

/* scroll indicator dots */
.svc-dots {
  display:flex; justify-content:center; gap:7px;
  margin-top:18px;
}
@media(min-width:768px){ .svc-dots{ display:none; } }
.svc-dot {
  width:6px; height:6px; border-radius:50%;
  background:rgba(26,58,47,.2);
  transition:background .2s, transform .2s;
}
.svc-dot.active { background:var(--ocean); transform:scale(1.3); }

/* ═══════════════════════════════════════════════
   HOW IT WORKS — timeline style
═══════════════════════════════════════════════ */
.hiw-sec { padding:80px 0; background:var(--cream); }
@media(min-width:768px){ .hiw-sec{ padding:100px 0; } }

.hiw-header { text-align:center; margin-bottom:52px; }

.hiw-grid {
  display:grid;
  grid-template-columns:1fr;
  gap:16px;
}
@media(min-width:640px){ .hiw-grid { grid-template-columns:repeat(2,1fr); } }
@media(min-width:900px){ .hiw-grid { grid-template-columns:repeat(4,1fr); } }

.hiw-card {
  border-radius:var(--r-lg);
  padding:30px 24px;
  position:relative; overflow:hidden;
  transition:transform .26s var(--spring), box-shadow .26s;
}
.hiw-card:hover { transform:translateY(-5px); box-shadow:0 16px 40px rgba(0,0,0,.09); }
.h-white  { background:#fff; border:1px solid rgba(26,58,47,.08); }
.h-ocean  { background:var(--ocean); }
.h-yellow { background:var(--yellow); }
.h-peach  { background:var(--peach); }

.hiw-step-num {
  font-family:'Nanum Myeongjo',serif;
  font-size:3rem; font-weight:400;
  line-height:1; letter-spacing:-.05em;
  opacity:.12; margin-bottom:18px;
}
.hiw-ico {
  width:44px; height:44px;
  display:flex; align-items:center; justify-content:center;
  border-radius:12px; margin-bottom:14px;
  transition:transform .26s var(--spring);
}
.hiw-card:hover .hiw-ico { transform:scale(1.08) rotate(5deg); }
.h-white  .hiw-ico { background:rgba(26,58,47,.06); }
.h-ocean  .hiw-ico { background:rgba(255,255,255,.16); }
.h-yellow .hiw-ico { background:rgba(26,58,47,.07); }
.h-peach  .hiw-ico { background:rgba(255,255,255,.35); }
.hiw-ico i { font-size:20px; }
.h-white  .hiw-ico i,.h-yellow .hiw-ico i,.h-peach .hiw-ico i { color:var(--ocean); }
.h-ocean  .hiw-ico i { color:#fff; }

.hiw-title {
  font-family:'Nanum Myeongjo',serif; font-size:1.12rem; font-weight:400;
  line-height:1.28; margin-bottom:8px;
}
.hiw-desc { font-size:.83rem; line-height:1.65; opacity:.7; }
.h-ocean .hiw-step-num,.h-ocean .hiw-title,.h-ocean .hiw-desc { color:#fff; }

/* connector line between steps (desktop only) */
@media(min-width:900px){
  .hiw-grid { position:relative; }
  .hiw-grid::before {
    content:''; position:absolute;
    top:38px; left:calc(25% - 10px); right:calc(25% - 10px);
    height:1.5px;
    background:linear-gradient(90deg, transparent, rgba(26,58,47,.12) 20%, rgba(26,58,47,.12) 80%, transparent);
    pointer-events:none; z-index:0;
  }
  .hiw-card { z-index:1; }
}

/* ═══════════════════════════════════════════════
   PLANS
═══════════════════════════════════════════════ */
.plans-sec { padding:80px 0; background:#fff; }
@media(min-width:768px){ .plans-sec{ padding:100px 0; } }

/* horizontal scroll on mobile */
.plans-scroll {
  display:flex; gap:14px;
  overflow-x:auto; scroll-snap-type:x mandatory;
  -webkit-overflow-scrolling:touch;
  padding-bottom:16px;
  margin:40px -20px 0; padding-left:20px; padding-right:20px;
}
.plans-scroll::-webkit-scrollbar { display:none; }
@media(min-width:768px){
  .plans-scroll {
    display:grid; grid-template-columns:repeat(2,1fr);
    overflow:visible; margin:40px 0 0; padding:0;
  }
}
@media(min-width:1024px){ .plans-scroll { grid-template-columns:repeat(4,1fr); } }

.plan-card {
  flex:0 0 80vw; max-width:300px;
  scroll-snap-align:start;
  border-radius:var(--r-lg); padding:28px 22px;
  display:flex; flex-direction:column; gap:20px;
  border:1.5px solid rgba(26,58,47,.08); background:#fff;
  position:relative; overflow:hidden;
  transition:transform .28s var(--spring), border-color .28s, box-shadow .28s;
}
@media(min-width:768px){ .plan-card{ flex:none; max-width:none; } }
.plan-card:hover { transform:translateY(-5px); border-color:rgba(26,58,47,.2); box-shadow:0 14px 44px rgba(26,58,47,.1); }
.plan-featured { background:var(--ocean); border-color:var(--ocean); }
.plan-featured:hover { border-color:var(--ocean); }

.plan-badge {
  position:absolute; top:-1px; right:16px;
  background:var(--yellow); color:var(--ocean);
  font-size:.65rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.07em;
  padding:4px 10px; border-radius:0 0 7px 7px;
}
.plan-name {
  font-family:'Nanum Myeongjo',serif; font-size:1.28rem;
  font-weight:400; color:var(--ocean);
}
.plan-featured .plan-name { color:var(--yellow); }
.plan-meta { font-size:.76rem; color:var(--muted); margin-top:2px; }
.plan-featured .plan-meta { color:rgba(255,255,255,.55); }
.plan-feats { list-style:none; flex-grow:1; display:flex; flex-direction:column; gap:9px; }
.plan-feat { display:flex; align-items:flex-start; gap:9px; font-size:.82rem; color:var(--muted); }
.plan-featured .plan-feat { color:rgba(255,255,255,.72); }
.plan-check {
  width:17px; height:17px; flex-shrink:0; margin-top:1px;
  border-radius:50%; background:rgba(26,58,47,.08);
  display:flex; align-items:center; justify-content:center;
}
.plan-featured .plan-check { background:rgba(255,255,255,.14); }
.plan-check i { font-size:9px; color:var(--ocean); }
.plan-featured .plan-check i { color:var(--yellow); }
.plan-cta {
  display:block; text-align:center;
  padding:12px 18px; border-radius:var(--r-sm);
  font-size:.86rem; font-weight:600; text-decoration:none;
  transition:all .22s var(--spring);
}
.plan-cta-default {
  background:rgba(26,58,47,.06); color:var(--ocean);
  border:1.5px solid rgba(26,58,47,.1);
}
.plan-cta-default:hover { background:var(--ocean); color:#fff; border-color:var(--ocean); transform:translateY(-1px); }
.plan-cta-featured { background:var(--yellow); color:var(--ocean); }
.plan-cta-featured:hover { background:var(--cream-d); transform:translateY(-2px); box-shadow:0 5px 16px rgba(4,169,167,.3); }

/* ═══════════════════════════════════════════════
   FAQ
═══════════════════════════════════════════════ */
.faq-sec { padding:80px 0; background:var(--cream); }
@media(min-width:768px){ .faq-sec{ padding:100px 0; } }

.faq-header {
  display:flex; justify-content:space-between;
  align-items:flex-end; flex-wrap:wrap; gap:16px;
  margin-bottom:48px;
}

.faq-list {
  display:flex; flex-direction:column; gap:0;
  max-width:760px; margin:0 auto;
}

.faq-item {
  border-bottom:1px solid rgba(26,58,47,.1);
}
.faq-item:first-child { border-top:1px solid rgba(26,58,47,.1); }

.faq-btn {
  width:100%; display:flex; align-items:center;
  justify-content:space-between; gap:16px;
  background:none; border:none; cursor:pointer;
  padding:22px 0; text-align:left;
  font-family:'Cabin',sans-serif; font-size:1rem;
  font-weight:600; color:var(--ocean);
  transition:color .2s;
  -webkit-tap-highlight-color:transparent;
}
.faq-btn:hover { color:var(--indigo); }

.faq-btn-icon {
  flex-shrink:0;
  width:32px; height:32px; border-radius:50%;
  background:rgba(26,58,47,.07);
  display:flex; align-items:center; justify-content:center;
  transition:background .2s var(--spring), transform .3s var(--spring);
}
.faq-btn-icon i { font-size:14px; color:var(--ocean); transition:transform .3s var(--spring); }
.faq-item.open .faq-btn-icon { background:var(--ocean); }
.faq-item.open .faq-btn-icon i { color:#fff; transform:rotate(45deg); }

.faq-answer {
  display:grid; grid-template-rows:0fr;
  transition:grid-template-rows .32s var(--out);
}
.faq-item.open .faq-answer { grid-template-rows:1fr; }
.faq-answer-inner {
  overflow:hidden;
}
.faq-answer-body {
  padding:0 0 22px;
  font-size:.97rem; color:var(--muted); line-height:1.78;
  max-width:640px;
}

/* ═══════════════════════════════════════════════
   SOCIAL PROOF SNAPSHOT — Huru-inspired app card
═══════════════════════════════════════════════ */
.snapshot-sec { padding:80px 0; background:#fff; }
@media(min-width:768px){ .snapshot-sec{ padding:100px 0; } }

.snapshot-grid {
  display:grid; grid-template-columns:1fr;
  gap:40px; align-items:center;
}
@media(min-width:768px){ .snapshot-grid{ grid-template-columns:1fr 1fr; gap:60px; } }

.snapshot-text .sec-sub { max-width:400px; }

.snapshot-checklist { list-style:none; margin-top:28px; display:flex; flex-direction:column; gap:14px; }
.sc-item {
  display:flex; align-items:flex-start; gap:13px;
  font-size:.95rem; color:var(--muted); line-height:1.55;
}
.sc-check {
  width:24px; height:24px; flex-shrink:0; margin-top:1px;
  border-radius:50%; background:rgba(26,58,47,.08);
  display:flex; align-items:center; justify-content:center;
  transition:background .2s var(--spring), transform .2s var(--spring);
}
.sc-check i { font-size:11px; color:var(--ocean); }
.sc-item:hover .sc-check { background:var(--ocean); transform:scale(1.1); }
.sc-item:hover .sc-check i { color:#fff; }

/* app mockup card */
.mockup-card {
  background:#fff;
  border-radius:var(--r-xl);
  box-shadow:0 24px 64px rgba(26,58,47,.13);
  padding:24px;
  max-width:340px; margin:0 auto;
  border:1px solid rgba(26,58,47,.06);
}
.mockup-top {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:18px;
}
.mockup-logo {
  font-family:'Nanum Myeongjo',serif; font-size:1rem;
  color:var(--ocean); display:flex; align-items:center; gap:5px;
}
.mockup-logo span { color:var(--indigo); }
.mockup-badge {
  font-size:.68rem; font-weight:600; padding:4px 10px;
  border-radius:100px; background:#d1fae5; color:#065f46;
}
.mockup-metrics {
  display:grid; grid-template-columns:repeat(3,1fr);
  gap:8px; margin-bottom:16px;
}
.mockup-metric {
  background:var(--cream);
  border-radius:var(--r-sm);
  padding:10px 8px; text-align:center;
}
.mockup-metric .m-val {
  font-family:'Nanum Myeongjo',serif; font-size:1.25rem;
  color:var(--ocean); line-height:1;
}
.mockup-metric .m-lbl { font-size:.62rem; color:var(--muted); margin-top:3px; }

.mockup-next {
  background:var(--ocean); border-radius:var(--r-sm);
  padding:12px 14px; margin-bottom:12px;
}
.mockup-next .mn-label { font-size:.65rem; font-weight:600; color:rgba(255,255,255,.6); text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px; }
.mockup-next .mn-time  { font-size:.9rem; font-weight:600; color:#fff; }
.mockup-next .mn-nurse { font-size:.72rem; color:rgba(255,255,255,.65); margin-top:2px; }

.mockup-services { display:flex; flex-direction:column; gap:7px; }
.mockup-svc {
  display:flex; align-items:center; justify-content:space-between;
  padding:9px 12px; border-radius:var(--r-sm);
  font-size:.78rem; font-weight:500;
  cursor:pointer; transition:filter .2s;
  text-decoration:none;
}
.mockup-svc:hover { filter:brightness(.95); }
.ms-ocean  { background:var(--ocean); color:#fff; }
.ms-yellow { background:var(--yellow); color:var(--ocean); }
.ms-peach  { background:var(--peach); color:var(--ocean); }
.mockup-svc-price { font-size:.7rem; opacity:.7; }

/* ═══════════════════════════════════════════════
   FINAL CTA
═══════════════════════════════════════════════ */
.cta-sec {
  padding:80px 0;
  background:var(--ocean);
  position:relative; overflow:hidden;
}
@media(min-width:768px){ .cta-sec{ padding:100px 0; } }
.cta-orb {
  position:absolute; border-radius:50%; pointer-events:none;
}
.co1 {
  width:min(580px,140vw); height:min(580px,140vw);
  background:radial-gradient(circle,rgba(4,169,167,.12) 0%,transparent 65%);
  top:-25%; right:-15%;
}
.co2 {
  width:min(380px,90vw); height:min(380px,90vw);
  background:radial-gradient(circle,rgba(255,205,173,.1) 0%,transparent 65%);
  bottom:-15%; left:-10%;
}
.cta-inner { text-align:center; position:relative; z-index:2; }
.cta-h2 {
  font-family:'Nanum Myeongjo',serif;
  font-size:clamp(2.1rem,7vw,3.7rem);
  font-weight:400; line-height:1.1;
  letter-spacing:-.03em; color:#fff;
  margin:18px 0 14px;
}
.cta-h2 em { color:var(--yellow); font-style:italic; }
.cta-p {
  font-size:1rem; color:rgba(255,255,255,.68);
  max-width:460px; margin:0 auto 36px; line-height:1.75;
}
.cta-actions {
  display:flex; align-items:center; justify-content:center;
  flex-wrap:wrap; gap:12px; margin-bottom:44px;
}
.cta-trust { display:flex; align-items:center; justify-content:center; flex-wrap:wrap; gap:24px; }
.ct-item { display:flex; align-items:center; gap:7px; font-size:.82rem; color:rgba(255,255,255,.58); }
.ct-item i { font-size:15px; color:var(--yellow); }

/* ═══════════════════════════════════════════════
   RESPONSIVE CLEANUP
═══════════════════════════════════════════════ */
@media(max-width:767px){
  .plans-scroll::-webkit-scrollbar,
  .svc-scroll::-webkit-scrollbar { display:none; }
}

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

{{-- ── HERO ─────────────────────────────────────────── --}}
<section class="hero" aria-label="Hero">
  <div class="glow glow-1" aria-hidden="true"></div>
  <div class="glow glow-2" aria-hidden="true"></div>
  <div class="glow glow-3" aria-hidden="true"></div>

  <div class="wrap">
    <div class="hero-inner">
      <div class="hero-grid">

        {{-- TEXT COLUMN --}}
        <div>
          <div class="hero-eyebrow">
            <span class="pill pill-ocean">
              <span class="pill-dot"></span>
              Trusted by Nigerian Families
            </span>
          </div>

          <h1 class="hero-h1">
          For the family member who can’t
            <em>always</em>
            be there for their loved one
          </h1>

          <p class="hero-p">
          Caval Care sends Verified licensed healthcare professionals to your loved one's home 
          for elderly care, post-surgery recovery, post-natal support, 
          and we would keep you updated.
          </p>

          <div class="hero-actions">
            <a href="#care-plans" class="btn btn-primary">
              Get Started Here
              <i class="bi bi-arrow-right"></i>
            </a>
            <a href="#how-it-works" class="btn btn-outline">
              <i class="bi bi-play-circle"></i>
              How It Works
            </a>
          </div>

          <!-- <div class="hero-stats">
            <div>
              <div class="stat-num" data-count="15" data-suffix="+">15+</div>
              <div class="stat-lbl">Years Experience</div>
            </div>
            <div class="stat-div" aria-hidden="true"></div>
            <div>
              <div class="stat-num" data-count="500" data-suffix="+">500+</div>
              <div class="stat-lbl">Families Served</div>
            </div>
            <div class="stat-div" aria-hidden="true"></div>
            <div>
              <div class="stat-num" data-count="98" data-suffix="%">98%</div>
              <div class="stat-lbl">Satisfaction</div>
            </div>
          </div> -->

          {{-- Mobile visit snapshot (hidden ≥ 900px) --}}
          <div class="hero-snap" aria-hidden="true">
            <div class="hs-head">
              <span class="hs-label">Last Visit Report</span>
              <span class="hs-badge">Completed ✓</span>
            </div>
            <div class="hs-metrics">
              <div class="hs-metric"><div class="hs-val">2.5h</div><div class="hs-lbl">Duration</div></div>
              <div class="hs-metric"><div class="hs-val">Stable</div><div class="hs-lbl">Vitals</div></div>
              <div class="hs-metric"><div class="hs-val">100%</div><div class="hs-lbl">Meds</div></div>
            </div>
          </div>
        </div>

        {{-- VISUAL COLUMN --}}
        <div class="hero-visual" aria-hidden="true">
          <div class="hero-collage">
            <div class="hero-photo hp-main">
              <img src="https://images.unsplash.com/photo-1584515933487-779824d29309?w=600&h=880&fit=crop"
                   alt="Nurse with elderly patient" loading="eager">
              <div class="hero-photo-overlay"></div>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;">
              <div class="hero-photo hp-top">
                <img src="https://images.unsplash.com/photo-1576765607924-3f7b8410a787?w=400&h=400&fit=crop&crop=face"
                     alt="Happy family" loading="eager">
              </div>
              <div class="hero-photo hp-bot">
                <img src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?w=400&h=460&fit=crop"
                     alt="Caregiver at home" loading="eager">
                <div class="hero-photo-overlay"></div>
              </div>
            </div>
          </div>

          {{-- floating cards --}}
          <div class="fc fc-tl">
            <div class="fc-lbl">Last Visit Report</div>
            <div class="fc-val">Stable ✓</div>
            <div class="fc-hint">All vitals normal · Meds taken</div>
            <div class="fc-dots">
              <span style="background:var(--yellow)"></span>
              <span style="background:var(--peach)"></span>
              <span style="background:var(--indigo);opacity:.4"></span>
            </div>
          </div>

          <div class="fc fc-br">
            <div class="fc-lbl">Next Scheduled Visit</div>
            <div class="fc-row">
              <div class="fc-av" style="background:var(--cream);color:var(--ocean);">NA</div>
              <div class="fc-rtext">
                <p>Thursday · 10:00 AM</p>
                <span>Healthcare Professional Adaeze · Post-surgery</span>
              </div>
            </div>
            <div class="fc-btns">
              <a href="#" class="fc-btn dk">View report</a>
              <a href="#" class="fc-btn lt">Contact healthcare professional</a>
            </div>
          </div>

          <div class="badge-247" aria-hidden="true">
            24/7
            <small>Support</small>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>


{{-- ── MARQUEE TRUST STRIP ─────────────────────────── --}}
<div class="marquee-sec" role="marquee" aria-label="Trust signals">
  <div class="marquee-track">
    @php
    $marqueeItems = [
      ['icon'=>'bi-patch-check','text'=>'Licensed & Insured Healthcare Professionals'],
      ['icon'=>'bi-shield-check','text'=>'Background Verified'],
      ['icon'=>'bi-bell','text'=>'Real-Time Visit Reports'],
      ['icon'=>'bi-clock','text'=>'24/7 Care Coordinators'],
      ['icon'=>'bi-heart','text'=>'500+ Families Served'],
      ['icon'=>'bi-star','text'=>'98% Satisfaction Rate'],
      ['icon'=>'bi-telephone','text'=>'Always Reachable'],
      ['icon'=>'bi-patch-check','text'=>'Licensed & Insured Healthcare Professionals'],
      ['icon'=>'bi-shield-check','text'=>'Background Verified'],
      ['icon'=>'bi-bell','text'=>'Real-Time Visit Reports'],
      ['icon'=>'bi-clock','text'=>'24/7 Care Coordinators'],
      ['icon'=>'bi-heart','text'=>'500+ Families Served'],
      ['icon'=>'bi-star','text'=>'98% Satisfaction Rate'],
      ['icon'=>'bi-telephone','text'=>'Always Reachable'],
    ];
    @endphp
    @foreach($marqueeItems as $item)
    <div class="marquee-item">
      <i class="bi {{ $item['icon'] }}"></i>
      <strong>{{ $item['text'] }}</strong>
    </div>
    @endforeach
  </div>
</div>


{{-- ── TRUST PILLARS ───────────────────────────────── --}}
<section class="trust-sec" aria-label="Why CavalCare">
  <div class="wrap">
    <div class="trust-grid">
      <div class="trust-card reveal">
        <div class="trust-ico"><i class="bi bi-patch-check"></i></div>
        <div class="trust-t">Verified Credentials</div>
        <div class="trust-d">All healthcare professionals are licenses and verified to ensure you and your loved one feels secure.</div>
      </div>
      <div class="trust-card reveal" style="transition-delay:.1s">
        <div class="trust-ico"><i class="bi bi-bell"></i></div>
        <div class="trust-t">Real-Time Updates</div>
        <div class="trust-d">A detailed care report would be sent to you at every visit.</div>
      </div>
      <div class="trust-card reveal" style="transition-delay:.2s">
        <div class="trust-ico"><i class="bi bi-heart"></i></div>
        <div class="trust-t">24/7 Support</div>
        <div class="trust-d">No matter your concern, there is someone that would there to talk to you.</div>
      </div>
    </div>
  </div>
</section>


{{-- ── SERVICES ─────────────────────────────────────── --}}
<section id="care-types" class="svc-sec" aria-label="Care services">
  <div class="wrap">

    <div class="svc-header reveal">
      <div>
        <span class="pill pill-indigo sec-eyebrow">Our Services</span>
        <h2 class="sec-title">
          Care services <em style="font-style:italic;color:var(--indigo)">tailored</em><br>
          to your loved ones
        </h2>
      </div>
      <p class="sec-sub" style="margin-top:0;max-width:320px;">
      We send trained, licensed and thoroughly verified healthcare professionals that is specific to what your loved one needs.
      </p>
    </div>

    @php $svcColors = ['c-ocean','c-cream','c-peach','c-indigo','c-yellow','c-soft'];
    $svcIcons = ['bi-heart-pulse','bi-people','bi-hospital','bi-activity','bi-stars','bi-house-heart'];
    @endphp

    <div class="svc-scroll" id="svcScroll">
      @foreach ($careTypes as $index => $careType)
        @php
          $meta = $careType->meta ?? [];
          $icon = $meta['bi_icon'] ?? ($svcIcons[$index % count($svcIcons)]);
          $description = $meta['description'] ?? 'Professional care tailored to your family\'s needs.';
          $col = $svcColors[$index % count($svcColors)];
        @endphp
        <a href="#" class="svc-card {{ $col }} reveal" style="transition-delay:{{ $index * 0.07 }}s;" aria-label="{{ $careType->name }}">
          <div class="svc-icon-wrap">
            <i class="bi {{ $icon }}"></i>
          </div>
          <h3 class="svc-name">{{ $careType->name }}</h3>
          <p class="svc-desc">{{ $description }}</p>
          <span class="svc-link">
            Learn More
            <i class="bi bi-arrow-right"></i>
          </span>
        </a>
      @endforeach
    </div>

    <div class="svc-dots" id="svcDots" aria-hidden="true">
      @foreach ($careTypes as $index => $careType)
        <div class="svc-dot{{ $index === 0 ? ' active' : '' }}" data-index="{{ $index }}"></div>
      @endforeach
    </div>

  </div>
</section>


{{-- ── HOW IT WORKS ─────────────────────────────────── --}}
<section id="how-it-works" class="hiw-sec" aria-label="How it works">
  <div class="wrap">
    <div class="hiw-header reveal">
      <span class="pill pill-yellow sec-eyebrow">Simple Process</span>
      <h2 class="sec-title">Getting started is simple</h2>
      <p class="sec-sub" style="margin-left:auto;margin-right:auto;text-align:center;max-width:420px;">
      You can book a Care plan in 4 simple steps
      </p>
    </div>

    @php
    $steps = [
      ['n'=>'01','t'=>'Choose a Care plan','d'=>'We have different service plans, you can read through them and pick the one that matches what your loved one is going through right now.','i'=>'bi-telephone','bg'=>'h-white'],
      ['n'=>'02','t'=>'Fill the form','d'=>'This is where you give us the details we need to match the right healthcare professional. It would take just a few minutes.','i'=>'bi-clipboard-check','bg'=>'h-ocean'],
      ['n'=>'03','t'=>'Meet Your Caregiver','d'=>'Once your form is submitted, you will receive a summary of your selected care plan and the cost. Review it, then proceed to payment.','i'=>'bi-people','bg'=>'h-yellow'],
      ['n'=>'04','t'=>'Begin Quality Care','d'=>'Once your payment is confirmed, our team gets to work. We review your loved one\'s details and match them with a licensed, verified healthcare professional who fits their specific needs within 48 hours.','i'=>'bi-heart','bg'=>'h-peach'],
    ];
    @endphp

    <div class="hiw-grid">
      @foreach($steps as $idx => $s)
      <div class="hiw-card {{ $s['bg'] }} reveal" style="transition-delay:{{ $idx * 0.09 }}s;">
        <div class="hiw-step-num" aria-hidden="true">{{ $s['n'] }}</div>
        <div class="hiw-ico" aria-hidden="true">
          <i class="bi {{ $s['i'] }}"></i>
        </div>
        <h3 class="hiw-title">{{ $s['t'] }}</h3>
        <p class="hiw-desc">{{ $s['d'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>


{{-- ── CARE PLANS ───────────────────────────────────── --}}
<section id="care-plans" class="plans-sec" aria-label="Care plans">
  <div class="wrap">

    <div class="reveal">
      <span class="pill pill-peach sec-eyebrow">Flexible Pricing</span>
      <h2 class="sec-title">
      There is specific Care plan that works<br>
        <em style="font-style:italic;color:var(--indigo)">for you</em>
      </h2>
      <p class="sec-sub">
      We have daily plans, weekend plans, flexible and monthly plans depending on what you think your loved ones need, 
      so you can feel at peace
      </p>
    </div>

    <div class="plans-scroll">
      @foreach($carePlans as $index => $plan)
        @php
          $meta = $plan->meta ?? [];
          $recommended = $meta['recommended_for'] ?? [];
          $pop = ($index === 1);
        @endphp
        <div class="plan-card {{ $pop ? 'plan-featured' : '' }} reveal"
             style="transition-delay:{{ $index * 0.08 }}s;" role="article">
          @if($pop)
            <div class="plan-badge" aria-label="Most popular plan">Most Popular</div>
          @endif
          <div>
            <div class="plan-name">{{ $plan->name }}</div>
            <div class="plan-meta">
              {{ $meta['daily_hours'] ?? 'Flexible' }} hrs/day ·
              {{ $meta['duration_days'] ?? 'Ongoing' }} days
            </div>
          </div>
          <ul class="plan-feats" aria-label="Plan features">
            <li class="plan-feat">
              <span class="plan-check" aria-hidden="true">
                <i class="bi bi-check"></i>
              </span>
              Up to {{ $meta['max_caregivers'] ?? 1 }} dedicated caregiver(s)
            </li>
            @foreach($recommended as $ctId)
            <li class="plan-feat">
              <span class="plan-check" aria-hidden="true">
                <i class="bi bi-check"></i>
              </span>
              {{ \App\Models\CareType::find($ctId)?->name }}
            </li>
            @endforeach
          </ul>
          <a href="{{ url('/book/' . $plan->id) }}"
             class="plan-cta {{ $pop ? 'plan-cta-featured' : 'plan-cta-default' }}">
            {{ $pop ? 'Get Started' : 'Choose Plan' }}
          </a>
        </div>
      @endforeach
    </div>

    <div class="reveal" style="text-align:center;margin-top:36px;transition-delay:.3s;">
      <p style="color:var(--muted);font-size:.92rem;">
        Need something more tailored?
        <a href="#contact"
           style="color:var(--ocean);font-weight:600;text-decoration:none;border-bottom:1.5px solid var(--yellow);padding-bottom:1px;transition:border-color .2s;"
           onmouseover="this.style.borderColor='var(--indigo)'"
           onmouseout="this.style.borderColor='var(--yellow)'">
          Speak with our care team →
        </a>
      </p>
    </div>
  </div>
</section>


{{-- ── APP SNAPSHOT (Huru-inspired mockup) ─────────── --}}
<section class="snapshot-sec" aria-label="Platform overview">
  <div class="wrap">
    <div class="snapshot-grid">

      <div class="snapshot-text reveal">
        <span class="pill pill-ocean sec-eyebrow">The CavalCare Experience</span>
        <h2 class="sec-title">
        We don’t compromise<br>
          <em style="font-style:italic;color:var(--indigo)">on the safety of your loved ones.</em>
        </h2>
        <p class="sec-sub">
        We have licensed verified healthcare professionals and you will be updated within the care hours.
        </p>

        <ul class="snapshot-checklist">
          <li class="sc-item">
            <span class="sc-check"><i class="bi bi-check"></i></span>
            You would be sent updates about your loved one directly to your phone
          </li>
          <li class="sc-item">
            <span class="sc-check"><i class="bi bi-check"></i></span>
            You can message your healthcare professional directly during the visit hours
          </li>
          <li class="sc-item">
            <span class="sc-check"><i class="bi bi-check"></i></span>
            You can adjust your care plans when necessary
          </li>
        </ul>

        <div style="margin-top:28px;">
          <a href="#care-plans" class="btn btn-primary">
            Get Started Here
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>

      <div class="reveal" style="transition-delay:.15s;">
        <div class="mockup-card">
          <div class="mockup-top">
            <div class="mockup-logo">
              Caval<span>Care</span>
            </div>
            <span class="mockup-badge">Visit Completed ✓</span>
          </div>

          <div style="font-size:.72rem;color:var(--muted);margin-bottom:12px;">Post-Visit Care Report</div>

          <div class="mockup-metrics">
            <div class="mockup-metric">
              <div class="m-val">2.5hrs</div>
              <div class="m-lbl">Visit Duration</div>
            </div>
            <div class="mockup-metric">
              <div class="m-val">Stable</div>
              <div class="m-lbl">Vitals</div>
            </div>
            <div class="mockup-metric">
              <div class="m-val">100%</div>
              <div class="m-lbl">Meds Taken</div>
            </div>
          </div>

          <div class="mockup-next">
            <div class="mn-label">Next Scheduled Visit</div>
            <div class="mn-time">Thursday · 10:00 AM</div>
            <div class="mn-nurse">Healthcare Professional Adaeze · Post-surgery care</div>
          </div>

          <div style="font-size:.7rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Our Services</div>
          <div class="mockup-services">
            <a href="#care-types" class="mockup-svc ms-ocean">
              <span>Elderly Care</span>
              <!-- <span class="mockup-svc-price">From ₦65,000</span> -->
            </a>
            <a href="#care-types" class="mockup-svc ms-yellow">
              <span>Post-Natal Care</span>
              <!-- <span class="mockup-svc-price">From ₦60,000</span> -->
            </a>
            <a href="#care-types" class="mockup-svc ms-peach">
              <span>Post-Surgery Care</span>
              <!-- <span class="mockup-svc-price">From ₦65,000</span> -->
            </a>
            <a href="#care-types" class="mockup-svc ms-peach">
              <span>Terminal Illness Support</span>
              <!-- <span class="mockup-svc-price">From ₦65,000</span> -->
            </a>
          </div>

          <a href="#care-plans" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:14px;font-size:.86rem;padding:12px;">
            Book a Healthcare Professional →
          </a>
        </div>
      </div>

    </div>
  </div>
</section>


{{-- ── FAQ ──────────────────────────────────────────── --}}
<section class="faq-sec" aria-label="Frequently Asked Questions" id="faq">
  <div class="wrap">

    <div class="faq-header reveal">
      <div>
        <span class="pill pill-ocean sec-eyebrow">FAQ</span>
        <h2 class="sec-title">Questions families<br>ask us most</h2>
      </div>
      <p style="color:var(--muted);max-width:280px;font-size:.92rem;line-height:1.7;">
        Everything you need to know before getting started with CavalCare.
      </p>
    </div>

    @php
    $faqs = [
      [
        'q' => 'How are your healthcare professionals verified?',
        'a' => 'Every healthcare professional on the CavalCare platform goes through a rigorous vetting process that includes verification of their healthcare licence with the Nursing and Midwifery Council of Nigeria (NMCN), a criminal background check, reference checks from previous employers, and an in-person interview with our care team. Only healthcare professionals who pass all stages are approved to take assignments.',
      ],
      [
        'q' => 'How quickly can you assign a healthcare professional to my loved one?',
        'a' => 'Once your booking form is submitted and payment is confirmed, our team begins matching immediately. In most cases we assign a qualified healthcare professional within 48 hours. For urgent situations, please call us directly and we will do our best to expedite the process.',
      ],
      [
        'q' => 'How will I know what happens during each visit?',
        'a' => 'After every visit you will receive a detailed care report sent directly to your phone. The report covers vitals, medications administered, any observations the healthcare professional has noted, and the scheduled time of the next visit. You can also message your assigned healthcare professional directly during active visit hours.',
      ],
      [
        'q' => 'Can I change or cancel my care plan?',
        'a' => 'Yes. You can upgrade, downgrade, or pause your care plan at any time by contacting our care coordinators. Cancellations made at least 24 hours before the next scheduled visit are fully refunded for unused days. We understand that care needs change, and we are here to be flexible with you.',
      ],
      [
        'q' => 'What types of care do your healthcare professionals provide?',
        'a' => 'Our healthcare professionals are trained to handle a range of in-home care needs including elderly care, post-surgery recovery, post-natal support, and chronic illness management. Each healthcare professional is matched to your loved one based on their specific clinical background and your family\'s particular requirements.',
      ],
      [
        'q' => 'What happens if I am not happy with my assigned healthcare professional?',
        'a' => 'Your satisfaction matters deeply to us. If for any reason the assigned healthcare professional is not the right fit, simply contact our support team and we will arrange a replacement as quickly as possible, no questions asked. Our goal is to give both you and your loved one full peace of mind.',
      ],
      [
        'q' => 'Are your services available outside Lagos?',
        'a' => 'We currently operate primarily in Lagos but are actively expanding to Abuja, Port Harcourt, and other major cities. If you are outside our current coverage area, reach out to us and we will let you know our timeline for your location.',
      ],
    ];
    @endphp

    <div class="faq-list reveal" style="transition-delay:.1s;" role="list">
      @foreach($faqs as $i => $faq)
      <div class="faq-item" role="listitem">
        <button class="faq-btn"
                id="faq-btn-{{ $i }}"
                aria-expanded="false"
                aria-controls="faq-answer-{{ $i }}">
          {{ $faq['q'] }}
          <span class="faq-btn-icon" aria-hidden="true">
            <i class="bi bi-plus"></i>
          </span>
        </button>
        <div class="faq-answer" id="faq-answer-{{ $i }}" role="region" aria-labelledby="faq-btn-{{ $i }}">
          <div class="faq-answer-inner">
            <div class="faq-answer-body">{{ $faq['a'] }}</div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

  </div>
</section>

    {{-- Testimonial scroll dots (mobile only) --}}
    <div class="testi-dots" id="testiDots" aria-hidden="true">
      <div class="testi-dot active"></div>
      <div class="testi-dot"></div>
      <div class="testi-dot"></div>
    </div>


{{-- ── FINAL CTA ────────────────────────────────────── --}}
<section id="contact" class="cta-sec" aria-label="Get started with CavalCare">
  <div class="cta-orb co1" aria-hidden="true"></div>
  <div class="cta-orb co2" aria-hidden="true"></div>

  <div class="wrap">
    <div class="cta-inner reveal">
      <span class="pill pill-white">
        <span class="pill-dot"></span>
        Get Care Today
      </span>

      <h2 class="cta-h2">
      Your loved one deserves proper care<br>
        <em>and we have the right healthcare professional fit for them</em>
      </h2>

      <p class="cta-p">
        Join the families who trust CavalCare for professional, dignified in-home care across Nigeria.
      </p>

      <div class="cta-actions">
        <a href="#" class="btn btn-yellow">
          <i class="bi bi-calendar-check"></i>
          Schedule Free Consultation
        </a>
        <a href="https://wa.me/message/4PNWSMS5KTZUB1" class="btn-ghost">
          <i class="bi bi-telephone"></i>
          Call: +234 706 311 2134
        </a>
      </div>

      <div class="cta-trust">
        <div class="ct-item">
          <i class="bi bi-patch-check"></i>
          Licensed & Insured
        </div>
        <div class="ct-item">
          <i class="bi bi-people"></i>
          Verified Caregivers
        </div>
        <div class="ct-item">
          <i class="bi bi-clock"></i>
          24/7 Support
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

/* ── NAV SCROLL ──────────────────────────────── */
const nav = document.getElementById('mainNav');

if (nav) {
  const onScroll = () => nav.classList.toggle('scrolled', scrollY > 16);

  window.addEventListener('scroll', onScroll, { passive: true });

  onScroll();
}

/* ── SCROLL REVEAL ───────────────────────────── */
const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('is-visible');
      io.unobserve(e.target);
    }
  });
}, { threshold:.1, rootMargin:'0px 0px -50px 0px' });
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

/* ── SMOOTH ANCHOR SCROLL ────────────────────── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));

    if (target) {
      e.preventDefault();

      const offset = 70;
      const top = target.getBoundingClientRect().top + scrollY - offset;

      window.scrollTo({
        top,
        behavior: 'smooth'
      });
    }
  });
});

/* ── BUTTON RIPPLE ───────────────────────────── */
const rippleStyle = document.createElement('style');
rippleStyle.textContent = '@keyframes ripple{to{transform:scale(2.5);opacity:0}}';
document.head.appendChild(rippleStyle);

document.querySelectorAll('.btn, .btn-ghost, .btn-yellow, .plan-cta, .fc-btn, .nav-cta').forEach(btn => {
  btn.style.position = 'relative';
  btn.style.overflow = 'hidden';
  btn.addEventListener('pointerdown', e => {
    const r   = document.createElement('span');
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

/* ── STAT COUNT-UP ───────────────────────────── */
const statsIO = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.querySelectorAll('[data-count]').forEach(el => {
      const target = +el.dataset.count;
      const suffix = el.dataset.suffix || '';
      let cur = 0, start = null;
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
}, { threshold:.6 });
const hs = document.querySelector('.hero-stats');
if (hs) statsIO.observe(hs);

/* ── SERVICES SCROLL DOTS ────────────────────── */
const svcScroll = document.getElementById('svcScroll');
const svcDots   = document.querySelectorAll('.svc-dot');
if (svcScroll && svcDots.length) {
  const updateDots = () => {
    const { scrollLeft, scrollWidth, clientWidth } = svcScroll;
    const cards = svcScroll.querySelectorAll('.svc-card');
    const cardW = cards[0]?.offsetWidth + 14 || 1;
    const active = Math.round(scrollLeft / cardW);
    svcDots.forEach((d, i) => d.classList.toggle('active', i === active));
  };
  svcScroll.addEventListener('scroll', updateDots, {passive:true});
  updateDots();
}

/* ── PARALLAX GLOWS ON MOUSE (desktop only) ── */
if (window.matchMedia('(min-width:900px) and (hover:hover)').matches) {
  const glows = document.querySelectorAll('.glow');
  document.addEventListener('mousemove', e => {
    const cx = e.clientX / window.innerWidth - .5;
    const cy = e.clientY / window.innerHeight - .5;
    glows.forEach((g, i) => {
      const factor = (i + 1) * 18;
      g.style.transform = `translate(${cx * factor}px, ${cy * factor}px)`;
    });
  }, {passive:true});
}

/* ── STAGGERED CARD ENTRY ────────────────────── */
document.querySelectorAll('.trust-card').forEach((card, i) => {
  card.style.transitionDelay = `${i * 0.08}s`;
});

/* ── FAQ ACCORDION ───────────────────────────── */
document.querySelectorAll('.faq-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    // Close all
    document.querySelectorAll('.faq-item.open').forEach(el => {
      el.classList.remove('open');
      el.querySelector('.faq-btn').setAttribute('aria-expanded', 'false');
    });
    // Open clicked (unless it was already open)
    if (!isOpen) {
      item.classList.add('open');
      btn.setAttribute('aria-expanded', 'true');
    }
  });
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