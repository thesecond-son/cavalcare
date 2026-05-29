{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'CavalCare — Trusted In-Home Care for Your Loved Ones')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;1,9..144,300;1,9..144,400&family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ─── DESIGN TOKENS ───────────────────────────────── */
:root {
  --cream:       #FFFCCF;
  --yellow:      #F4DF79;
  --ocean:       #063A47;
  --indigo:      #6370A8;
  --peach:       #F2BF9E;
  --cream-deep:  #f5f0a0;
  --ocean-hover: #0a4e62;
  --ocean-soft:  #e8f3f6;
  --text:        #063A47;
  --muted:       #4a6e7a;
  --r-sm: 10px;
  --r-md: 16px;
  --r-lg: 24px;
  --r-xl: 36px;
  --ease-spring: cubic-bezier(0.34,1.56,0.64,1);
  --ease-out:    cubic-bezier(0,0,0.2,1);
  --ease-smooth: cubic-bezier(0.4,0,0.2,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'Geist',system-ui,sans-serif;font-size:16px;line-height:1.65;color:var(--text);background:#fff;-webkit-font-smoothing:antialiased;}
img{display:block;max-width:100%;}

/* ─── KEYFRAMES ───────────────────────────────────── */
@keyframes fadeUp   {from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeIn   {from{opacity:0}to{opacity:1}}
@keyframes scaleIn  {from{opacity:0;transform:scale(0.94)}to{opacity:1;transform:scale(1)}}
@keyframes floatY   {0%,100%{transform:translateY(0)}50%{transform:translateY(-9px)}}
@keyframes drawLine {from{transform:scaleX(0)}to{transform:scaleX(1)}}
@keyframes pulse    {0%,100%{box-shadow:0 0 0 0 rgba(6,58,71,.2)}60%{box-shadow:0 0 0 10px rgba(6,58,71,0)}}
@keyframes slideR   {from{opacity:0;transform:translateX(-30px)}to{opacity:1;transform:translateX(0)}}
@keyframes slideL   {from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:translateX(0)}}
@keyframes ripple   {to{transform:scale(1);opacity:0}}

/* ─── SCROLL REVEAL ───────────────────────────────── */
.reveal{opacity: 0;transform:translateY(36px);transition:opacity .72s var(--ease-out),transform .72s var(--ease-out);}
.reveal.active{opacity:1;transform:none;}

/* ─── SHARED ──────────────────────────────────────── */
.wrap{max-width:1200px;margin:0 auto;padding:0 24px;}
.pill{display:inline-flex;align-items:center;gap:7px;font-size:.72rem;font-weight:600;letter-spacing:.07em;text-transform:uppercase;padding:6px 14px;border-radius:100px;}
.pill-dot{width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;}
.pill-ocean{background:rgba(6,58,71,.1);color:var(--ocean);}
.pill-yellow{background:var(--yellow);color:var(--ocean);}
.pill-indigo{background:rgba(99,112,168,.13);color:var(--indigo);}
.pill-peach{background:rgba(242,191,158,.35);color:#7a4630;}
.sec-title{font-family:'Fraunces',serif;font-size:clamp(2.3rem,4vw,3.4rem);font-weight:400;line-height:1.1;letter-spacing:-.03em;color:var(--ocean);}
.sec-sub{font-size:1.05rem;color:var(--muted);max-width:500px;margin-top:14px;line-height:1.72;}
.dot-bg{background-image:radial-gradient(circle,rgba(6,58,71,.07) 1.5px,transparent 1.5px);background-size:24px 24px;}

/* ─── BUTTONS ─────────────────────────────────────── */
.btn{display:inline-flex;align-items:center;gap:10px;font-family:'Geist',sans-serif;font-size:.95rem;font-weight:600;text-decoration:none;border:none;cursor:pointer;position:relative;overflow:hidden;transition:transform .22s var(--ease-spring),box-shadow .22s,background .2s,color .2s,border-color .2s;}
.btn:active{transform:scale(.97)!important;}
.btn-ripple{position:absolute;border-radius:50%;pointer-events:none;background:rgba(255,255,255,.22);transform:scale(0);animation:ripple .55s ease-out forwards;}
.btn-primary{background:var(--ocean);color:#fff;padding:15px 30px;border-radius:var(--r-sm);box-shadow:0 4px 18px rgba(6,58,71,.22);}
.btn-primary:hover{transform:translateY(-3px);box-shadow:0 8px 32px rgba(6,58,71,.32);}
.btn-outline{background:transparent;color:var(--ocean);padding:14px 30px;border-radius:var(--r-sm);border:1.5px solid rgba(6,58,71,.25);}
.btn-outline:hover{background:var(--ocean);color:#fff;border-color:var(--ocean);transform:translateY(-2px);}
.btn-yellow{background:var(--yellow);color:var(--ocean);padding:15px 30px;border-radius:var(--r-sm);box-shadow:0 4px 18px rgba(244,223,121,.38);}
.btn-yellow:hover{background:var(--cream-deep);transform:translateY(-3px);box-shadow:0 8px 30px rgba(244,223,121,.5);}

/* ─── HERO ────────────────────────────────────────── */
.hero{min-height:100vh;background:var(--cream);padding-top:72px;display:flex;align-items:center;position:relative;overflow:hidden;}
.hero-glow{position:absolute;border-radius:50%;pointer-events:none;}
.g1{width:680px;height:680px;background:radial-gradient(circle,rgba(244,223,121,.35) 0%,transparent 65%);top:-180px;right:-160px;}
.g2{width:480px;height:480px;background:radial-gradient(circle,rgba(242,191,158,.28) 0%,transparent 65%);bottom:-140px;left:-80px;}
.g3{width:360px;height:360px;background:radial-gradient(circle,rgba(99,112,168,.13) 0%,transparent 65%);top:42%;left:33%;}
.hero-grid{display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;width:100%;padding:80px 0;}
.hero-eyebrow{animation:fadeUp .8s .08s var(--ease-out) both;}
.hero-h1{font-family:'Fraunces',serif;font-size:clamp(2.9rem,5vw,4.6rem);font-weight:400;line-height:1.05;letter-spacing:-.04em;color:var(--ocean);margin:18px 0 24px;animation:fadeUp .8s .18s var(--ease-out) both;}
.hero-h1 em{font-style:italic;color:var(--indigo);position:relative;}
.hero-h1 em::after{content:'';position:absolute;left:0;right:0;bottom:3px;height:3px;background:var(--peach);border-radius:2px;transform-origin:left;animation:drawLine .8s 1.1s var(--ease-spring) both;}
.hero-p{font-size:1.08rem;color:var(--muted);max-width:460px;line-height:1.76;animation:fadeUp .8s .28s var(--ease-out) both;}
.hero-actions{display:flex;align-items:center;flex-wrap:wrap;gap:14px;margin-top:36px;animation:fadeUp .8s .4s var(--ease-out) both;}
.hero-stats{display:flex;align-items:center;gap:22px;margin-top:48px;padding-top:36px;border-top:1px solid rgba(6,58,71,.1);animation:fadeUp .8s .52s var(--ease-out) both;}
.stat-num{font-family:'Fraunces',serif;font-size:2.3rem;font-weight:400;color:var(--ocean);line-height:1;letter-spacing:-.03em;}
.stat-lbl{font-size:.75rem;color:var(--muted);margin-top:4px;font-weight:500;text-transform:uppercase;letter-spacing:.05em;}
.stat-divider{width:1px;height:38px;background:rgba(6,58,71,.12);}
/* Hero visual */
.hero-visual{position:relative;animation:scaleIn .9s .28s var(--ease-spring) both;}
.hero-img-wrap{width:100%;height:clamp(260px, 50vw, 480px);border-radius:var(--r-xl);overflow:hidden;position:relative;}
.hero-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform 6s ease;}
.hero-img-wrap:hover img{transform:scale(1.04);}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(6,58,71,.45) 100%);border-radius:var(--r-xl);}
/* Float cards */
.fc{position:absolute;background:rgba(255,255,255,.92);backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.7);border-radius:var(--r-md);padding:16px 20px;box-shadow:0 10px 36px rgba(6,58,71,.13);transition:transform .3s var(--ease-spring);}
.fc:hover{transform:translateY(-4px) scale(1.02);}
.fc-tl{top:-24px;left:-28px;width:196px;animation:slideR .8s .85s both;}
.fc-br{bottom:28px;right:-28px;width:218px;animation:slideL .8s .95s both;}
.fc-lbl{font-size:.68rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;}
.fc-val{font-family:'Fraunces',serif;font-size:1.6rem;color:var(--ocean);line-height:1;letter-spacing:-.02em;}
.fc-hint{font-size:.73rem;color:var(--muted);margin-top:4px;}
.fc-dots{display:flex;gap:5px;margin-top:10px;}
.fc-dots span{width:8px;height:8px;border-radius:50%;}
.fc-row{display:flex;align-items:center;gap:10px;margin-top:8px;}
.fc-av{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:.9rem;flex-shrink:0;}
.fc-rtext p{font-size:.79rem;font-weight:600;color:var(--ocean);line-height:1.2;}
.fc-rtext span{font-size:.71rem;color:var(--muted);}
.fc-btns{display:flex;gap:7px;margin-top:10px;}
.fc-btn-sm{font-size:.74rem;font-weight:600;padding:8px 12px;border-radius:6px;text-decoration:none;transition:all .2s;}
.fc-btn-sm.dark{background:var(--ocean);color:#fff;}
.fc-btn-sm.dark:hover{background:var(--ocean-hover);}
.fc-btn-sm.light{background:rgba(6,58,71,.07);color:var(--ocean);}
.fc-btn-sm.light:hover{background:rgba(6,58,71,.13);}
.badge-247{position:absolute;top:-18px;right:44px;width:62px;height:62px;border-radius:50%;background:var(--ocean);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:1.1rem;line-height:1;box-shadow:0 6px 20px rgba(6,58,71,.3);animation:fadeIn .7s 1.1s both,floatY 4.5s 1.5s ease-in-out infinite;}
.badge-247 small{font-family:'Geist',sans-serif;font-size:.58rem;font-weight:500;opacity:.8;margin-top:2px;}

/* ─── TRUST BAND ──────────────────────────────────── */
.trust-band{background:var(--ocean);padding:56px 0;}
.trust-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:rgba(255,255,255,.1);}
.trust-item{padding:36px 38px;background:var(--ocean);display:flex;align-items:flex-start;gap:18px;transition:background .3s;}
.trust-item:hover{background:var(--ocean-hover);}
.trust-ico{width:46px;height:46px;flex-shrink:0;border-radius:var(--r-sm);background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;transition:background .2s,transform .2s var(--ease-spring);}
.trust-item:hover .trust-ico{background:rgba(255,255,255,.18);transform:scale(1.06);}
.trust-ico svg{width:22px;height:22px;color:var(--yellow);}
.trust-t{font-size:1rem;font-weight:600;color:#fff;margin-bottom:4px;}
.trust-d{font-size:.84rem;color:rgba(255,255,255,.58);line-height:1.55;}

/* ─── SERVICES ────────────────────────────────────── */
.services-sec{padding:100px 0;background:#fff;}
.svc-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:64px;}
.svc-card{border-radius:var(--r-lg);padding:36px 30px;display:flex;flex-direction:column;gap:16px;text-decoration:none;position:relative;overflow:hidden;transition:transform .3s var(--ease-spring),box-shadow .3s;}
.svc-card::before{content:'';position:absolute;inset:0;opacity:0;transition:opacity .3s;}
.svc-card:hover{transform:translateY(-6px);box-shadow:0 20px 60px rgba(6,58,71,.11);}
.svc-card:hover::before{opacity:1;}
.c-ocean{background:var(--ocean);color:#fff;}
.c-ocean::before{background:radial-gradient(circle at 75% 75%,rgba(244,223,121,.13) 0%,transparent 55%);}
.c-cream{background:var(--cream);color:var(--ocean);}
.c-cream::before{background:radial-gradient(circle at 75% 75%,rgba(6,58,71,.05) 0%,transparent 55%);}
.c-peach{background:rgba(242,191,158,.22);color:var(--ocean);}
.c-peach::before{background:radial-gradient(circle at 75% 75%,rgba(242,191,158,.3) 0%,transparent 55%);}
.c-indigo{background:rgba(99,112,168,.1);color:var(--ocean);}
.c-indigo::before{background:radial-gradient(circle at 75% 75%,rgba(99,112,168,.15) 0%,transparent 55%);}
.c-yellow{background:var(--yellow);color:var(--ocean);}
.c-yellow::before{background:radial-gradient(circle at 75% 75%,rgba(6,58,71,.06) 0%,transparent 55%);}
.c-soft{background:var(--ocean-soft);color:var(--ocean);}
.svc-icon{font-size:2rem;line-height:1;transition:transform .3s var(--ease-spring);}
.svc-card:hover .svc-icon{transform:scale(1.15) rotate(6deg);}
.svc-name{font-family:'Fraunces',serif;font-size:1.35rem;font-weight:400;line-height:1.22;}
.svc-desc{font-size:.87rem;line-height:1.65;opacity:.75;flex-grow:1;}
.svc-link{display:inline-flex;align-items:center;gap:8px;font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:6px;opacity:.65;transition:gap .2s var(--ease-spring),opacity .2s;}
.svc-link svg{width:15px;height:15px;}
.svc-card:hover .svc-link{gap:13px;opacity:1;}

/* ─── HOW IT WORKS ────────────────────────────────── */
.hiw-sec{padding:100px 0;background:var(--cream);}
.hiw-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-top:64px;}
.hiw-card{
  border-radius:var(--r-lg);
  padding:34px 28px;
  position:relative;
  overflow:hidden;
  transition:transform .25s var(--ease-spring), box-shadow .25s ease;
  border:1px solid rgba(6,58,71,.08);
  background:#fff;
}

.hiw-card:hover{
  transform:translateY(-6px);
  box-shadow:0 18px 40px rgba(0,0,0,.08);
}
.h-white{background:#fff;border:1px solid rgba(6,58,71,.08);}
.h-ocean{background:var(--ocean);}
.h-yellow{background:var(--yellow);}
.h-peach{background:var(--peach);}
.hiw-num{font-family:'Fraunces',serif;font-size:3.4rem;font-weight:400;line-height:1;letter-spacing:-.05em;opacity:.12;margin-bottom:20px;}
.hiw-ico{
  width:46px;
  height:46px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:14px;
  background:rgba(6,58,71,.06);
  margin-bottom:16px;
}
.hiw-ico i{
  font-size:18px;
  line-height:1;
  color:var(--ink);
}

.hiw-title{
  font-family:'Fraunces',serif;
  font-size:1.18rem;
  font-weight:500;
  line-height:1.3;
  margin-bottom:10px;
  color: var(--ink);
}
.hiw-desc{font-size:.84rem;line-height:1.65;opacity:.7;}
.h-ocean .hiw-ico{
  background:rgba(255,255,255,.18);
  backdrop-filter: blur(4px);
}
.h-ocean .hiw-ico i{
  color:#fff;
}
.h-ocean .hiw-num,.h-ocean .hiw-title,.h-ocean .hiw-desc{color:#fff;}

/* ─── PLANS ───────────────────────────────────────── */
.plans-sec{padding:100px 0;background:#fff;}
.plans-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-top:64px;}
.plan-card{border-radius:var(--r-lg);padding:32px 26px;display:flex;flex-direction:column;gap:22px;border:1.5px solid rgba(6,58,71,.08);background:#fff;position:relative;overflow:hidden;transition:transform .3s var(--ease-spring),border-color .3s,box-shadow .3s;}
.plan-card:hover{transform:translateY(-5px);border-color:rgba(6,58,71,.2);box-shadow:0 16px 48px rgba(6,58,71,.1);}
.plan-featured{background:var(--ocean);border-color:var(--ocean);}
.plan-featured:hover{border-color:var(--ocean);}
.plan-badge{position:absolute;top:-1px;right:18px;background:var(--yellow);color:var(--ocean);font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding:5px 11px;border-radius:0 0 8px 8px;}
.plan-name{font-family:'Fraunces',serif;font-size:1.35rem;font-weight:400;color:var(--ocean);}
.plan-featured .plan-name{color:var(--yellow);}
.plan-meta{font-size:.78rem;color:var(--muted);margin-top:3px;}
.plan-featured .plan-meta{color:rgba(255,255,255,.55);}
.plan-feats{list-style:none;flex-grow:1;display:flex;flex-direction:column;gap:10px;}
.plan-feat{display:flex;align-items:flex-start;gap:10px;font-size:.84rem;color:var(--muted);}
.plan-featured .plan-feat{color:rgba(255,255,255,.72);}
.plan-check{width:18px;height:18px;flex-shrink:0;margin-top:1px;border-radius:50%;background:rgba(6,58,71,.08);display:flex;align-items:center;justify-content:center;}
.plan-featured .plan-check{background:rgba(255,255,255,.14);}
.plan-check svg{width:10px;height:10px;color:var(--ocean);}
.plan-featured .plan-check svg{color:var(--yellow);}
.plan-cta{display:block;text-align:center;padding:13px 20px;border-radius:var(--r-sm);font-size:.88rem;font-weight:600;text-decoration:none;transition:all .22s var(--ease-spring);}
.plan-cta-default{background:rgba(6,58,71,.06);color:var(--ocean);border:1.5px solid rgba(6,58,71,.1);}
.plan-cta-default:hover{background:var(--ocean);color:#fff;border-color:var(--ocean);transform:translateY(-1px);}
.plan-cta-featured{background:var(--yellow);color:var(--ocean);}
.plan-cta-featured:hover{background:var(--cream-deep);transform:translateY(-2px);box-shadow:0 6px 18px rgba(244,223,121,.45);}

/* ─── TESTIMONIALS ────────────────────────────────── */
.testi-sec{padding:100px 0;background:var(--cream);}
.testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:52px;}
.testi-card{background:#fff;border-radius:var(--r-lg);padding:34px 30px;display:flex;flex-direction:column;gap:18px;border:1px solid rgba(6,58,71,.07);transition:transform .3s var(--ease-spring),box-shadow .3s;}
.testi-card:hover{transform:translateY(-5px);box-shadow:0 16px 48px rgba(6,58,71,.1);}
.testi-stars{display:flex;gap:3px;}
.tstar{color:var(--yellow);font-size:1rem;filter:drop-shadow(0 1px 2px rgba(244,223,121,.55));}
.testi-q{font-family:'Fraunces',serif;font-style:italic;font-size:1.04rem;line-height:1.67;color:var(--ocean);flex-grow:1;}
.testi-who{display:flex;align-items:center;gap:13px;}
.testi-av{width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:1.08rem;font-weight:400;flex-shrink:0;transition:transform .2s var(--ease-spring);}
.testi-card:hover .testi-av{transform:scale(1.08);}
.testi-name{font-size:.88rem;font-weight:600;color:var(--ocean);}
.testi-role{font-size:.76rem;color:var(--muted);margin-top:2px;}

/* ─── CTA ─────────────────────────────────────────── */
.cta-sec{padding:100px 0;background:var(--ocean);position:relative;overflow:hidden;}
.cta-orb{position:absolute;border-radius:50%;pointer-events:none;}
.co1{width:580px;height:580px;background:radial-gradient(circle,rgba(244,223,121,.13) 0%,transparent 65%);top:-180px;right:-140px;}
.co2{width:380px;height:380px;background:radial-gradient(circle,rgba(242,191,158,.1) 0%,transparent 65%);bottom:-100px;left:-90px;}
.cta-inner{text-align:center;position:relative;z-index:2;}
.cta-h2{font-family:'Fraunces',serif;font-size:clamp(2.4rem,5vw,3.9rem);font-weight:400;line-height:1.1;letter-spacing:-.03em;color:#fff;margin:18px 0 16px;}
.cta-h2 em{color:var(--yellow);font-style:italic;}
.cta-p{font-size:1.04rem;color:rgba(255,255,255,.68);max-width:500px;margin:0 auto 42px;line-height:1.72;}
.cta-actions{display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:15px;margin-bottom:50px;}
.btn-ghost{background:rgba(255,255,255,.1);color:#fff;padding:15px 30px;border-radius:var(--r-sm);border:1.5px solid rgba(255,255,255,.2);display:inline-flex;align-items:center;gap:10px;font-weight:600;font-size:.95rem;text-decoration:none;transition:background .2s,transform .22s var(--ease-spring);}
.btn-ghost:hover{background:rgba(255,255,255,.18);transform:translateY(-2px);}
.cta-trust{display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:28px;}
.ct-item{display:flex;align-items:center;gap:8px;font-size:.84rem;color:rgba(255,255,255,.58);}
.ct-item svg{width:17px;height:17px;color:var(--yellow);}

/* ─── RESPONSIVE ──────────────────────────────────── */
@media(max-width:1024px){
  .hero-grid{grid-template-columns:1fr;gap:44px;padding:60px 0;}
  .hero-visual{display:none;}
  .svc-grid{grid-template-columns:repeat(2,1fr);}
  .hiw-grid{grid-template-columns:repeat(2,1fr);}
  .plans-grid{grid-template-columns:repeat(2,1fr);}
  .testi-grid{grid-template-columns:repeat(2,1fr);}
  .trust-grid{grid-template-columns:1fr;}
  .trust-item{border-top:1px solid rgba(255,255,255,.08);}
}
@media(max-width:640px){
  .nav-links{display:none;}
  .nav-mob-btn{display:flex;}
  .svc-grid,.hiw-grid,.plans-grid,.testi-grid{grid-template-columns:1fr;}
}

@media (max-width: 768px) {
  .hero-grid,
  .trust-grid,
  .svc-grid,
  .hiw-grid {
    grid-template-columns: 1fr;
  }
}
:focus-visible{outline:2px solid var(--indigo);outline-offset:3px;border-radius:4px;}
</style>
@endpush

@section('content')

{{-- ── HERO ─────────────────────────────────── --}}
<section class="hero" aria-label="Hero">
  <div class="hero-glow g1" aria-hidden="true"></div>
  <div class="hero-glow g2" aria-hidden="true"></div>
  <div class="hero-glow g3" aria-hidden="true"></div>

  <div class="wrap" style="width:100%;">
    <div class="hero-grid">

      <div>
        <div class="hero-eyebrow">
          <span class="pill pill-ocean">
            <span class="pill-dot"></span>
            Trusted by Nigerian Families
          </span>
        </div>

        <h1 class="hero-h1">
          Built for the family member who cannot be there but
          <em>refuses</em>
          to stop caring
        </h1>

        <p class="hero-p">
          Professional in-home nurses with verified credentials.<br>
          Real-time updates after every single visit.
        </p>

        <div class="hero-actions">
          <a href="#care-plans" class="btn btn-primary">
            Get Started Here
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
          <a href="#how-it-works" class="btn btn-outline">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
            Watch How It Works
          </a>
        </div>

        <div class="hero-stats">
          <div>
            <div class="stat-num" data-count="15" data-suffix="+">15+</div>
            <div class="stat-lbl">Years Experience</div>
          </div>
          <div class="stat-divider" aria-hidden="true"></div>
          <div>
            <div class="stat-num" data-count="500" data-suffix="+">500+</div>
            <div class="stat-lbl">Families Served</div>
          </div>
          <div class="stat-divider" aria-hidden="true"></div>
          <div>
            <div class="stat-num" data-count="98" data-suffix="%">98%</div>
            <div class="stat-lbl">Satisfaction</div>
          </div>
        </div>
      </div>

      <div class="hero-visual" aria-hidden="true">
        <div class="hero-img-wrap">
          <img src="https://images.unsplash.com/photo-1584515933487-779824d29309?w=800&h=960&fit=crop"
               alt="Caregiver with patient" loading="eager">
          <div class="hero-overlay"></div>
        </div>

        <div class="fc fc-tl">
          <div class="fc-lbl">Last Visit Report</div>
          <div class="fc-val">Stable ✓</div>
          <div class="fc-hint">All vitals normal · Meds taken</div>
          <div class="fc-dots">
            <span style="background:var(--yellow)"></span>
            <span style="background:var(--peach)"></span>
            <span style="background:var(--indigo);opacity:.45"></span>
          </div>
        </div>

        <div class="fc fc-br">
          <div class="fc-lbl">Next Scheduled Visit</div>
          <div class="fc-row">
            <div class="fc-av" style="background:var(--cream);color:var(--ocean);">NA</div>
            <div class="fc-rtext">
              <p>Thursday · 10:00 AM</p>
              <span>Nurse Adaeze · Post-surgery</span>
            </div>
          </div>
          <div class="fc-btns">
            <a href="#" class="fc-btn-sm dark">View report</a>
            <a href="#" class="fc-btn-sm light">Contact nurse</a>
          </div>
        </div>

        <div class="badge-247" aria-hidden="true">
          24/7
          <small>Support</small>
        </div>
      </div>

    </div>
  </div>
</section>


{{-- ── TRUST BAND ───────────────────────────── --}}
<section class="trust-band" aria-label="Why CavalCare">
  <div class="wrap">
    <div class="trust-grid">
      <div class="trust-item reveal">
        <div class="trust-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
          <div class="trust-t">Verified Credentials</div>
          <div class="trust-d">All nurses are licensed, background-checked, and trained in professional in-home care protocols.</div>
        </div>
      </div>
      <div class="trust-item reveal" style="transition-delay:.1s">
        <div class="trust-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <div>
          <div class="trust-t">Real-Time Updates</div>
          <div class="trust-d">A detailed care report is sent to your phone after every single visit — no guessing, ever.</div>
        </div>
      </div>
      <div class="trust-item reveal" style="transition-delay:.2s">
        <div class="trust-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </div>
        <div>
          <div class="trust-t">24/7 Support</div>
          <div class="trust-d">Our care coordinators are available around the clock — whenever you need reassurance.</div>
        </div>
      </div>
    </div>
  </div>
</section>


{{-- ── SERVICES ─────────────────────────────── --}}
<section id="care-types" class="services-sec" aria-label="Care services">
  <div class="wrap">
    <div class="reveal" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:24px;">
      <div>
        <span class="pill pill-indigo" style="margin-bottom:16px;display:inline-flex;">Our Services</span>
        <h2 class="sec-title">
          Care services <em style="font-style:italic;color:var(--indigo)">tailored</em><br>
          to your loved ones
        </h2>
      </div>
      <p class="sec-sub" style="margin-top:0;">
        Professional expertise with genuine compassion — care that truly feels like family.
      </p>
    </div>

    @php $svcColors = ['c-ocean','c-cream','c-peach','c-indigo','c-yellow','c-soft']; @endphp

    <div class="svc-grid">
      @foreach ($careTypes as $index => $careType)
        @php
          $meta = $careType->meta ?? [];
          $icon = $meta['icon'] ?? '💙';
          $description = $meta['description'] ?? 'Professional care tailored to your family\'s needs.';
          $col = $svcColors[$index % count($svcColors)];
        @endphp
        <a href="#" class="svc-card {{ $col }} reveal" style="transition-delay:{{ $index * 0.07 }}s;" aria-label="{{ $careType->name }}">
          <div class="svc-icon" aria-hidden="true">{{ $icon }}</div>
          <h3 class="svc-name">{{ $careType->name }}</h3>
          <p class="svc-desc">{{ $description }}</p>
          <span class="svc-link">
            Learn More
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </span>
        </a>
      @endforeach
    </div>
  </div>
</section>


{{-- ── HOW IT WORKS ─────────────────────────── --}}
<section id="how-it-works" class="hiw-sec" aria-label="How it works">
  <div class="wrap">
    <div class="reveal" style="text-align:center;">
      <span class="pill pill-yellow" style="margin-bottom:16px;display:inline-flex;">Simple Process</span>
      <h2 class="sec-title" style="text-align:center;">Getting started is simple</h2>
      <p class="sec-sub" style="text-align:center;max-width:440px;margin-left:auto;margin-right:auto;">
        From consultation to consistent care, we're with you every step of the way.
      </p>
    </div>

    @php
    $steps = [
      ['n'=>'01','t'=>'Schedule Consultation','d'=>'Book a free consultation to discuss your specific care needs and preferences.','i'=>'bi-telephone','bg'=>'h-white'],
      ['n'=>'02','t'=>'Create Your Care Plan','d'=>'We develop a personalized care plan tailored to your loved one\'s needs.','i'=>'bi-clipboard-check','bg'=>'h-ocean'],
      ['n'=>'03','t'=>'Meet Your Caregiver','d'=>'Connect with your verified, background-checked caregiver before care begins.','i'=>'bi-people','bg'=>'h-yellow'],
      ['n'=>'04','t'=>'Begin Quality Care','d'=>'Enjoy peace of mind with consistent, professional care and real-time reporting.','i'=>'bi-heart','bg'=>'h-peach'],
    ];
    @endphp
    <div class="hiw-grid">
      @foreach($steps as $i => $s)
      <div class="hiw-card {{ $s['bg'] }} reveal" style="transition-delay:{{ $i * 0.08 }}s;">
        <div class="hiw-num" aria-hidden="true">{{ $s['n'] }}</div>
        <div class="hiw-ico" aria-hidden="true"><i class="bi {{ $s['i'] }}"></i></div>
        <h3 class="hiw-title">{{ $s['t'] }}</h3>
        <p class="hiw-desc">{{ $s['d'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>


{{-- ── PLANS ────────────────────────────────── --}}
<section id="care-plans" class="plans-sec" aria-label="Care plans">
  <div class="wrap">
    <div class="reveal">
      <span class="pill pill-peach" style="margin-bottom:16px;display:inline-flex;">Flexible Pricing</span>
      <h2 class="sec-title">
        Care plans that fit<br>
        <em style="font-style:italic;color:var(--indigo)">your</em> needs
      </h2>
      <p class="sec-sub">
        From occasional check-ins to continuous care — each plan is structured to meet
        your loved one's needs without uncertainty or compromise.
      </p>
    </div>

    <div class="plans-grid">
      @foreach($carePlans as $index => $plan)
        @php
          $meta = $plan->meta ?? [];
          $recommended = $meta['recommended_for'] ?? [];
          $pop = ($index === 1);
        @endphp
        <div class="plan-card {{ $pop ? 'plan-featured' : '' }} reveal" style="transition-delay:{{ $index * 0.08 }}s;" role="article">
          @if($pop)
            <div class="plan-badge" aria-label="Most popular plan">Most Popular</div>
          @endif
          <div>
            <div class="plan-name">{{ $plan->name }}</div>
            <div class="plan-meta">{{ $meta['daily_hours'] ?? 'Flexible' }} hrs/day · {{ $meta['duration_days'] ?? 'Ongoing' }} days</div>
          </div>
          <ul class="plan-feats" aria-label="Plan features">
            <li class="plan-feat">
              <span class="plan-check" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
              </span>
              Up to {{ $meta['max_caregivers'] ?? 1 }} dedicated caregiver(s)
            </li>
            @foreach($recommended as $ctId)
            <li class="plan-feat">
              <span class="plan-check" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
              </span>
              {{ \App\Models\CareType::find($ctId)?->name }}
            </li>
            @endforeach
          </ul>
          <a href="{{ url('/book/' . $plan->id) }}" class="plan-cta {{ $pop ? 'plan-cta-featured' : 'plan-cta-default' }}">
            {{ $pop ? 'Get Started' : 'Choose Plan' }}
          </a>
        </div>
      @endforeach
    </div>

    <div class="reveal" style="text-align:center;margin-top:48px;transition-delay:.3s;">
      <p style="color:var(--muted);font-size:.95rem;">
        Need something more tailored?
        <a href="#contact" style="color:var(--ocean);font-weight:600;text-decoration:none;border-bottom:1.5px solid var(--yellow);padding-bottom:1px;transition:border-color .2s;"
           onmouseover="this.style.borderColor='var(--indigo)'"
           onmouseout="this.style.borderColor='var(--yellow)'">
          Speak with our care team →
        </a>
      </p>
    </div>
  </div>
</section>


{{-- ── TESTIMONIALS ─────────────────────────── --}}
<section class="testi-sec" aria-label="Testimonials">
  <div class="wrap">
    <div class="reveal" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:24px;margin-bottom:52px;">
      <div>
        <span class="pill pill-ocean" style="margin-bottom:16px;display:inline-flex;">Testimonials</span>
        <h2 class="sec-title">Trusted by families<br>across Nigeria</h2>
      </div>
      <p style="color:var(--muted);max-width:300px;font-size:.95rem;line-height:1.7;">
        Real stories from families who found peace of mind with CavalCare.
      </p>
    </div>

    @php
    $testimonials = [
      ['name'=>'Amina Taiwo',    'role'=>'Daughter','init'=>'AT','bg'=>'var(--cream)',  'cl'=>'var(--ocean)', 'text'=>'The caregiver assigned to my mother has been absolutely wonderful. She treats her with such dignity and warmth.', 'r'=>5],
      ['name'=>'Chidi Okonkwo', 'role'=>'Son',     'init'=>'CO','bg'=>'var(--ocean)', 'cl'=>'var(--yellow)','text'=>'After my father\'s surgery, CavalCare\'s team was responsive, professional, and genuinely caring. Dad is recovering beautifully.', 'r'=>5],
      ['name'=>'Folake Adebayo','role'=>'Wife',     'init'=>'FA','bg'=>'var(--peach)',  'cl'=>'var(--ocean)', 'text'=>'My husband has special needs. CavalCare matched us with an amazing caregiver who understands his condition perfectly.', 'r'=>5],
    ];
    @endphp

    <div class="testi-grid">
      @foreach($testimonials as $i => $t)
      <div class="testi-card reveal" style="transition-delay:{{ $i * 0.1 }}s;" role="article">
        <div class="testi-stars" aria-label="{{ $t['r'] }} stars">
          @for($s=0;$s<$t['r'];$s++) <span class="tstar" aria-hidden="true">★</span> @endfor
        </div>
        <blockquote class="testi-q">"{{ $t['text'] }}"</blockquote>
        <div class="testi-who">
          <div class="testi-av" style="background:{{ $t['bg'] }};color:{{ $t['cl'] }};">{{ $t['init'] }}</div>
          <div>
            <div class="testi-name">{{ $t['name'] }}</div>
            <div class="testi-role">{{ $t['role'] }}</div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>


{{-- ── FINAL CTA ────────────────────────────── --}}
<section id="contact" class="cta-sec" aria-label="Get started with CavalCare">
  <div class="cta-orb co1" aria-hidden="true"></div>
  <div class="cta-orb co2" aria-hidden="true"></div>

  <div class="wrap">
    <div class="cta-inner reveal">
      <span class="pill" style="background:rgba(244,223,121,.15);color:var(--yellow);display:inline-flex;">
        <span class="pill-dot"></span>
        Get Care Today
      </span>

      <h2 class="cta-h2">
        Ready to experience<br>
        <em>compassionate care?</em>
      </h2>

      <p class="cta-p">
        Join the families who trust CavalCare for professional, dignified in-home care across Nigeria.
      </p>

      <div class="cta-actions">
        <a href="#" class="btn btn-yellow">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Schedule Free Consultation
        </a>
        <a href="tel:+2348000000000" class="btn-ghost">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.63A2 2 0 012 1h3a2 2 0 012 1.72c.12 1.01.34 2 .64 2.96a2 2 0 01-.45 2.11L6.09 8.9a16 16 0 006 6l1.11-1.1a2 2 0 012.11-.45c.97.3 1.96.53 2.97.64A2 2 0 0122 16.92z"/></svg>
          Call: +234 800 000 0000
        </a>
      </div>

      <div class="cta-trust">
        <div class="ct-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          Licensed & Insured
        </div>
        <div class="ct-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
          Verified Caregivers
        </div>
        <div class="ct-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
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
/* ── NAV SCROLL ───────────────────────────── */
const nav = document.getElementById('mainNav');
window.addEventListener('scroll', () => nav.classList.toggle('scrolled', scrollY > 20), {passive:true});
nav.classList.toggle('scrolled', scrollY > 20);

/* ── MOBILE NAV ───────────────────────────── */
const mobBtn = document.getElementById('mobBtn');
const navLinks = document.getElementById('navLinks');
let open = false;
mobBtn?.addEventListener('click', () => {
  open = !open;
  mobBtn.setAttribute('aria-expanded', open);
  navLinks.style.cssText = open
    ? 'display:flex;flex-direction:column;position:absolute;top:72px;left:0;right:0;background:rgba(255,252,207,.96);backdrop-filter:blur(22px);padding:24px;gap:20px;box-shadow:0 8px 32px rgba(6,58,71,.12);z-index:99;'
    : '';
});


/* ── SCROLL REVEAL ────────────────────────── */
const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('active');
      io.unobserve(e.target); // prevents re-triggering
    }
  });
}, {
  threshold: 0.1,
  rootMargin: '0px 0px -55px 0px'
});

document.querySelectorAll('.reveal').forEach(el => io.observe(el));

console.log(document.querySelectorAll('.reveal').length);

/* ── SMOOTH ANCHOR ────────────────────────── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if(t){ e.preventDefault(); t.scrollIntoView({behavior:'smooth',block:'start'}); if(open){open=false;navLinks.style.cssText='';mobBtn?.setAttribute('aria-expanded','false');} }
  });
});

/* ── RIPPLE ───────────────────────────────── */
const addRippleStyle = () => {
  const s = document.createElement('style');
  s.textContent = '@keyframes ripple{to{transform:scale(1.8);opacity:0}}';
  document.head.appendChild(s);
};
addRippleStyle();
document.querySelectorAll('.btn, .btn-ghost, .nav-cta, .plan-cta, .fc-btn-sm').forEach(btn => {
  btn.addEventListener('click', e => {
    const r = document.createElement('span');
    const rect = btn.getBoundingClientRect();
    const sz = Math.max(rect.width, rect.height);
    r.className = 'btn-ripple';
    r.style.cssText = `width:${sz}px;height:${sz}px;left:${e.clientX-rect.left-sz/2}px;top:${e.clientY-rect.top-sz/2}px;`;
    btn.appendChild(r);
    r.addEventListener('animationend', () => r.remove());
  });
});

/* ── STAT COUNT-UP ────────────────────────── */
const statsIO = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if(!e.isIntersecting) return;
    e.target.querySelectorAll('[data-count]').forEach(el => {
      const target = +el.dataset.count;
      const suffix = el.dataset.suffix || '';
      let cur = 0;
      const step = target / 48;
      const tick = () => {
        cur = Math.min(cur + step, target);
        el.textContent = Math.round(cur) + suffix;
        if(cur < target) requestAnimationFrame(tick);
      };
      tick();
    });
    statsIO.unobserve(e.target);
  });
}, {threshold:.5});
const hs = document.querySelector('.hero-stats');
if(hs) statsIO.observe(hs);
});
</script>
@endpush