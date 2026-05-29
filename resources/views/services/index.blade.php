{{-- resources/views/services.blade.php --}}
@extends('layouts.app')

@section('title', 'Our Care Services — CavalCare')

@push('head')
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
        Our nurses are trained with clinical certifications to handle any health situations 
        that your loved one might be facing
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
      ['i'=>'bi-patch-check', 't'=>'Licensed Nurses'],
      ['i'=>'bi-shield-check', 't'=>'Background Verified'],
      ['i'=>'bi-bell',         't'=>'Real-Time Reports'],
      ['i'=>'bi-clock',        't'=>'24/7 Coordinators'],
      ['i'=>'bi-heart',        't'=>'Compassionate Care'],
      ['i'=>'bi-house-heart',  't'=>'In-Home Service'],
      ['i'=>'bi-star',         't'=>'98% Satisfaction'],
      ['i'=>'bi-patch-check', 't'=>'Licensed Nurses'],
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

          <a href="{{ url('/book') }}?service={{ $careType->id }}"
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
    
            <a href="{{ url('/book') }}?service={{ $careType->id }}" class="chip-book">
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
          Schedule Free Consultation
        </a>
        <a href="tel:+2348000000000" class="btn-ghost">
          <i class="bi bi-telephone"></i>
          Call: +234 800 000 0000
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

/* ── MOBILE NAV ───────────────────────── */
const mobBtn = document.getElementById('mobBtn');
const drawer = document.getElementById('navDrawer');
let open = false;
const toggleDrawer = (force) => {
  open = force !== undefined ? force : !open;
  mobBtn.classList.toggle('active', open);
  mobBtn.setAttribute('aria-expanded', open);
  if (open) {
    drawer.style.display = 'flex';
    requestAnimationFrame(() => { drawer.style.opacity='1'; drawer.style.transform='none'; });
    drawer.classList.add('open');
  } else {
    drawer.style.opacity='0'; drawer.style.transform='translateY(-8px)';
    setTimeout(() => { if(!open){ drawer.style.display='none'; drawer.classList.remove('open'); } }, 260);
  }
};
mobBtn?.addEventListener('click', () => toggleDrawer());
drawer?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => toggleDrawer(false)));
document.addEventListener('keydown', e => { if(e.key==='Escape' && open) toggleDrawer(false); });

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

});
</script>
@endpush