{{-- resources/views/layouts/app.blade.php - TAILWIND VERSION --}}
<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>@yield('title', 'CavalCare — Trusted In-Home Care')</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=Nunito+Sans:wght@300;400;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <script src="https://cdn.tailwindcss.com"></script>
  {{-- Bootstrap Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#04A9A7',   // main action color (WhatsApp vibe)
            secondary: '#FFCDAD', // soft accent
            dark: '#1A3A2F',      // text / strong contrast
            light: '#F0FFDB',     // background
          },
          fontFamily: {
            sans: ['Nunito Sans', 'sans-serif'],
            serif: ['Lora', 'serif'],
            heading: ['Poppins', 'sans-serif'],
          },
        }
      }
    }
  </script>

<style>
/* ═══════════════════════════════════════════════
   CAVALCARE DESIGN TOKENS
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

    --r-xs:         6px;
    --r-sm:         10px;
    --r-md:         16px;
    --r-lg:         24px;
    --r-xl:         36px;
    --r-2xl:        48px;

    --spring:       cubic-bezier(0.34, 1.56, 0.64, 1);
    --out:          cubic-bezier(0, 0, 0.2, 1);
    --smooth:       cubic-bezier(0.4, 0, 0.2, 1);
}


/* ═══════════════════════════════════════════════
   GLOBAL BASE
═══════════════════════════════════════════════ */

*,
*::before,
*::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html {
    scroll-behavior: smooth;
    -webkit-text-size-adjust: 100%;
}

body {
    font-family: 'Cabin', system-ui, sans-serif;
    font-size: 16px;
    line-height: 1.65;
    color: var(--text);
    background: #fff;

    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;

    overflow-x: hidden;
}

img {
    display: block;
    max-width: 100%;
}

a {
    color: inherit;
}

button,
input,
textarea,
select {
    font: inherit;
}

button {
    color: inherit;
}

:focus-visible {
    outline: 2px solid var(--yellow);
    outline-offset: 3px;
    border-radius: 5px;
    box-shadow: 0 0 0 4px rgba(4, 169, 167, 0.18);
}


/* ═══════════════════════════════════════════════
   GLOBAL ANIMATIONS
═══════════════════════════════════════════════ */

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(24px);
    }

    to {
        opacity: 1;
        transform: none;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.93);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes floatY {
    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-8px);
    }
}

@keyframes pulse {
    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(26, 58, 47, 0.22);
    }

    60% {
        box-shadow: 0 0 0 9px rgba(26, 58, 47, 0);
    }
}

@keyframes slideR {
    from {
        opacity: 0;
        transform: translateX(-26px);
    }

    to {
        opacity: 1;
        transform: none;
    }
}

@keyframes slideL {
    from {
        opacity: 0;
        transform: translateX(26px);
    }

    to {
        opacity: 1;
        transform: none;
    }
}

@keyframes marquee {
    from {
        transform: translateX(0);
    }

    to {
        transform: translateX(-50%);
    }
}

@keyframes ripple {
    to {
        transform: scale(2.2);
        opacity: 0;
    }
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@keyframes blink {
    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.35;
    }
}


/* ═══════════════════════════════════════════════
   GLOBAL SCROLL REVEAL
═══════════════════════════════════════════════ */

.reveal {
    opacity: 0;
    transform: translateY(28px);
    transition:
        opacity 0.7s var(--out),
        transform 0.7s var(--out);
}

.reveal.is-visible {
    opacity: 1;
    transform: none;
}


/* ═══════════════════════════════════════════════
   GLOBAL CONTAINER
═══════════════════════════════════════════════ */

.wrap {
    width: 100%;
    max-width: 1160px;
    margin: 0 auto;
    padding: 0 20px;
}

@media (min-width: 768px) {
    .wrap {
        padding-left: 32px;
        padding-right: 32px;
    }
}


/* ═══════════════════════════════════════════════
   GLOBAL TYPOGRAPHY
═══════════════════════════════════════════════ */

.sec-eyebrow {
    display: block;
    margin-bottom: 14px;
}

.sec-title {
    font-family: 'Nanum Myeongjo', serif;
    font-size: clamp(1.9rem, 6vw, 3.2rem);
    font-weight: 400;
    line-height: 1.1;
    letter-spacing: -0.03em;
    color: var(--ocean);
}

.sec-sub {
    max-width: 480px;
    margin-top: 12px;

    font-size: 1rem;
    line-height: 1.75;
    color: var(--muted);
}


/* ═══════════════════════════════════════════════
   GLOBAL PILLS
═══════════════════════════════════════════════ */

.pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 5px 13px;

    border-radius: 100px;

    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;

    position: relative;
    overflow: hidden;
}

.pill-dot {
    width: 5px;
    height: 5px;
    flex-shrink: 0;

    border-radius: 50%;
    background: currentColor;
}

.pill-ocean {
    background: rgba(26, 58, 47, 0.1);
    color: var(--ocean);
}

.pill-yellow {
    background: var(--yellow);
    color: var(--ocean);
}

.pill-indigo {
    background: rgba(4, 169, 167, 0.13);
    color: var(--indigo);
}

.pill-peach {
    background: rgba(255, 205, 173, 0.35);
    color: #7a4016;
}

.pill-white {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
}

.pill::after {
    content: '';

    position: absolute;
    inset: 0;

    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3) 50%,
        transparent
    );

    background-size: 300px 100%;
    animation: shimmerPill 3.5s ease-in-out infinite;

    pointer-events: none;
    border-radius: inherit;
}

@keyframes shimmerPill {
    0% {
        background-position: -300px 0;
    }

    100% {
        background-position: 300px 0;
    }
}


/* ═══════════════════════════════════════════════
   GLOBAL BUTTONS
═══════════════════════════════════════════════ */

.btn {
    display: inline-flex;
    align-items: center;
    gap: 9px;

    position: relative;
    overflow: hidden;

    border: none;

    font-family: 'Cabin', sans-serif;
    font-size: 0.93rem;
    font-weight: 600;

    text-decoration: none;

    cursor: pointer;

    -webkit-tap-highlight-color: transparent;

    transition:
        transform 0.22s var(--spring),
        box-shadow 0.22s,
        background 0.18s,
        color 0.18s,
        border-color 0.18s;
}

.btn:active {
    transform: scale(0.96) !important;
}

.btn-primary {
    padding: 14px 26px;

    background: var(--ocean);
    color: #fff;

    border-radius: var(--r-sm);

    box-shadow: 0 4px 16px rgba(26, 58, 47, 0.22);
}

.btn-primary:hover {
    color: #fff;
    transform: translateY(-3px);

    box-shadow: 0 8px 28px rgba(26, 58, 47, 0.32);
}

.btn-outline {
    padding: 13px 26px;

    background: transparent;
    color: var(--ocean);

    border: 1.5px solid rgba(26, 58, 47, 0.24);
    border-radius: var(--r-sm);
}

.btn-outline:hover {
    background: var(--ocean);
    color: #fff;
    border-color: var(--ocean);

    transform: translateY(-2px);
}

.btn-yellow {
    padding: 14px 26px;

    background: var(--yellow);
    color: var(--ocean);

    border-radius: var(--r-sm);

    box-shadow: 0 4px 16px rgba(4, 169, 167, 0.22);
}

.btn-yellow:hover {
    background: var(--cream-d);

    transform: translateY(-3px);

    box-shadow: 0 8px 28px rgba(4, 169, 167, 0.35);
}

.btn-ghost {
    padding: 14px 26px;

    display: inline-flex;
    align-items: center;
    gap: 9px;

    background: rgba(255, 255, 255, 0.1);
    color: #fff;

    border: 1.5px solid rgba(255, 255, 255, 0.22);
    border-radius: var(--r-sm);

    font-size: 0.93rem;
    font-weight: 600;
    text-decoration: none;

    transition:
        background 0.2s,
        transform 0.22s var(--spring);
}

.btn-ghost:hover {
    background: rgba(255, 255, 255, 0.18);
    color: #fff;
    transform: translateY(-2px);
}

.btn i,
.btn-ghost i,
.btn-yellow i,
.btn-outline i {
    display: inline-block;

    transition: transform 0.22s var(--spring);
}

.btn:hover i,
.btn-ghost:hover i,
.btn-yellow:hover i {
    transform: translateX(4px);
}

.btn-outline:hover i {
    transform: scale(1.2);
}


/* ═══════════════════════════════════════════════
   GLOBAL NAVIGATION
═══════════════════════════════════════════════ */

.nav-wrap {
    position: fixed;

    top: 0;
    left: 0;
    right: 0;

    z-index: 200;

    padding-left: max(20px, env(safe-area-inset-left));
    padding-right: max(20px, env(safe-area-inset-right));

    transition:
        background 0.38s var(--smooth),
        box-shadow 0.38s var(--smooth);
}

@media (min-width: 768px) {
    .nav-wrap {
        padding-left: 32px;
        padding-right: 32px;
    }
}

.nav-wrap.scrolled {
    background: rgba(240, 255, 219, 0.92);

    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);

    box-shadow: 0 1px 0 rgba(26, 58, 47, 0.08);
}

.nav-inner {
    width: 100%;
    max-width: 1160px;

    height: 64px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav-logo {
    display: flex;
    align-items: center;

    text-decoration: none;
}

.nav-logo-img {
    width: auto;
    height: 96px;

    display: block;
    object-fit: contain;
}

.nav-logo:hover {
    opacity: 0.72;
}


/* Desktop navigation */

.nav-links {
    display: none;

    align-items: center;
    gap: 30px;

    list-style: none;
}

@media (min-width: 768px) {
    .nav-links {
        display: flex;
    }
}

.nav-links a {
    position: relative;

    padding: 4px 0;

    font-size: 0.85rem;
    font-weight: 500;

    color: var(--muted);

    text-decoration: none;

    transition: color 0.2s;
}

.nav-links a::after {
    content: '';

    position: absolute;

    left: 0;
    right: 0;
    bottom: -2px;

    height: 1.5px;

    background: var(--ocean);

    transform: scaleX(0);
    transform-origin: left;

    transition: transform 0.28s var(--spring);
}

.nav-links a:hover {
    color: var(--ocean);
}

.nav-links a:hover::after {
    transform: scaleX(1);
}

.nav-cta {
    padding: 9px 20px !important;

    background: var(--ocean) !important;
    color: #fff !important;

    border-radius: var(--r-sm) !important;
}

.nav-cta::after {
    display: none !important;
}

.nav-cta:hover {
    background: var(--ocean-h) !important;
    color: #fff !important;

    transform: translateY(-1px) !important;
}


/* ═══════════════════════════════════════════════
   MOBILE NAVIGATION
═══════════════════════════════════════════════ */

.nav-mob {
    display: flex;

    flex-direction: column;
    gap: 5px;

    min-width: 44px;
    min-height: 44px;

    align-items: center;
    justify-content: center;

    padding: 8px;

    background: none;
    border: none;

    color: var(--ocean);

    cursor: pointer;

    -webkit-tap-highlight-color: transparent;
}

@media (min-width: 768px) {
    .nav-mob {
        display: none;
    }
}

.nav-mob span {
    display: block;

    width: 22px;
    height: 2px;

    background: currentColor;

    border-radius: 2px;

    transition: all 0.28s var(--smooth);
}

.nav-mob.active span:nth-child(1) {
    transform: translateY(7px) rotate(45deg);
}

.nav-mob.active span:nth-child(2) {
    opacity: 0;
    transform: scaleX(0);
}

.nav-mob.active span:nth-child(3) {
    transform: translateY(-7px) rotate(-45deg);
}


/* Mobile drawer */

.nav-drawer {
    display: none;

    position: absolute;

    top: 64px;
    left: 0;
    right: 0;

    flex-direction: column;
    gap: 4px;

    padding: 20px 24px 28px;

    background: rgba(240, 255, 219, 0.97);

    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);

    border-top: 1px solid rgba(26, 58, 47, 0.07);

    box-shadow: 0 12px 40px rgba(26, 58, 47, 0.1);

    transform: translateY(-8px);
    opacity: 0;

    transition:
        opacity 0.25s var(--out),
        transform 0.25s var(--out);
}

.nav-drawer.open {
    display: flex;

    opacity: 1;
    transform: none;
}

.nav-drawer a {
    display: flex;
    align-items: center;
    justify-content: space-between;

    min-height: 48px;

    padding: 12px 0;

    border-bottom: 1px solid rgba(26, 58, 47, 0.07);

    color: var(--ocean);

    font-size: 1rem;
    font-weight: 500;

    text-decoration: none;
}

.nav-drawer a:last-child {
    border-bottom: none;
}

.nav-drawer a:hover {
    opacity: 0.65;
}

.nav-drawer-cta {
    margin-top: 12px;

    display: block !important;

    padding: 14px 20px !important;

    background: var(--ocean);
    color: #fff !important;

    border-radius: var(--r-sm);

    text-align: center;
    font-weight: 600;
}


/* ═══════════════════════════════════════════════
   ACCESSIBILITY / MOBILE POLISH
═══════════════════════════════════════════════ */

@media (max-width: 767px) {

    html {
        font-size: 15px;
    }

    .wrap {
        padding-left: 18px;
        padding-right: 18px;
    }

    .sec-title {
        font-size: clamp(1.7rem, 7.2vw, 2.5rem);
        letter-spacing: -0.025em;
    }

    .sec-sub {
        font-size: 0.95rem;
        margin-top: 10px;
    }

    .nav-inner {
        height: 60px;
    }

    .nav-logo-img {
        height: 52px;
    }

    .nav-drawer {
        top: 60px;

        padding: 16px 18px 24px;
    }

    .nav-drawer a {
        font-size: 0.95rem;
        padding: 14px 0;
    }

    .nav-wrap.scrolled {
        box-shadow: 0 2px 16px rgba(26, 58, 47, 0.12);
    }

    .btn,
    .btn-ghost,
    .btn-yellow,
    .btn-outline {
        min-height: 48px;
    }
}


/* ═══════════════════════════════════════════════
   REDUCED MOTION
═══════════════════════════════════════════════ */

@media (prefers-reduced-motion: reduce) {

    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}


/* ═══════════════════════════════════════════════
   LAZY IMAGE LOADING
═══════════════════════════════════════════════ */

img[loading="lazy"] {
    background:
        linear-gradient(
            90deg,
            rgba(240, 255, 219, 0.5) 25%,
            rgba(255, 255, 255, 0.3) 50%,
            rgba(240, 255, 219, 0.5) 75%
        );

    background-size: 400px 100%;

    animation: shimmerPill 1.8s ease-in-out infinite;
}

img[loading="lazy"].img-loaded {
    animation: none;
    background: none;
}
</style>

  @stack('head')
</head>
<body class="bg-[#FFF8F2] text-gray-800 font-sans antialiased">

  {{-- ── NAV ─────────────────────────────────── --}}
  <nav class="nav-wrap" id="mainNav">
  <div class="nav-inner">

  <a href="/" class="nav-logo" aria-label="CavalCare home">
      <img src="/images/caval-care-logo.png" alt="CavalCare logo" class="nav-logo-img h-10">
    </a>

    <ul class="nav-links" id="navLinks">
      <li><a href="{{ route('about') }}">About</a></li>
      <li><a href="{{ route('services') }}">Services</a></li>
      <li><a href="#care-plans">Plans</a></li>
      <li><a href="#contact">Contact</a></li>
      <li><a href="#care-plans" class="nav-cta">Get Started</a></li>
    </ul>

    <button class="nav-mob-btn" id="mobBtn" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
    </button>

  </div>
</nav>

  {{-- Main Content --}}
  <main class="min-h-screen">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="site-footer">
  <div class="container">

    <div class="footer-grid">

      {{-- Brand --}}
      <div class="footer-brand">
        <div class="footer-logo">
          <img src="/images/caval-care-logo.png" alt="CavalCare">
        </div>

        <p>
          Compassionate in-home caregiving for families in Nigeria.
        </p>

        <div class="footer-socials">
          <a href="https://www.facebook.com/usecavalcare"><i class="bi bi-facebook"></i></a>
          <a href="https://x.com/usecavalcare"><i class="bi bi-twitter"></i></a>
          <a href="https://www.instagram.com/usecavalcare"><i class="bi bi-instagram"></i></a>
        </div>
      </div>

      {{-- Services --}}
      <div>
        <h4>Services</h4>
        <ul>
          <li><a href="#">Elderly Care</a></li>
          <li><a href="#">Child Disability Care</a></li>
          <li><a href="#">Post-Surgery Care</a></li>
          <li><a href="#">Terminal Illness Support</a></li>
        </ul>
      </div>

      {{-- Links --}}
      <div>
        <h4>Quick Links</h4>
        <ul>
          <li><a href="#">How It Works</a></li>
          <li><a href="#">Care Plans</a></li>
          <li><a href="#">About Us</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
      </div>

      {{-- Contact --}}
      <div>
        <h4>Contact</h4>
        <ul class="footer-contact">
          <li>
            <i class="bi bi-geo-alt-fill"></i>
            <div>
              <strong>Lagos, Nigeria</strong>
              <span>Headquarters</span>
            </div>
          </li>
          <li>
            <i class="bi bi-telephone-fill"></i>
            <div>
              <a href="https://wa.me/message/4PNWSMS5KTZUB1">+234 706 311 2134</a>
              <span>24/7 Support</span>
            </div>
          </li>
          <li>
            <i class="bi bi-envelope-fill"></i>
            <div>
              <a href="mailto:hello@cavalcare.org">hello@cavalcare.org</a>
            </div>
          </li>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      © {{ date('Y') }} CavalCare. All rights reserved.
    </div>

  </div>
</footer>

  {{-- Floating Contact --}}
  <a 
  href="https://wa.me/message/4PNWSMS5KTZUB1"
  target="_blank"
  class="fixed bottom-8 right-8 w-14 h-14 md:w-16 md:h-16 rounded-full bg-primary shadow-2xl flex items-center justify-center text-white text-xl md:text-2xl hover:scale-110 transition-transform z-50 animate-float"
>
  <i class="bi bi-whatsapp"></i>
</a>

  {{-- Scroll Top --}}
  <button id="scrollTop" class="fixed bottom-8 left-8 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center text-primary opacity-0 invisible transition-all hover:shadow-xl hover:-translate-y-1 z-50">
    <i class="bi bi-arrow-up text-xl"></i>
  </button>

  <script>
    // Scroll to top
    const scrollBtn = document.getElementById('scrollTop');
    window.addEventListener('scroll', () => {
      if (window.pageYOffset > 300) {
        scrollBtn.classList.remove('opacity-0', 'invisible');
      } else {
        scrollBtn.classList.add('opacity-0', 'invisible');
      }
    });
    scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const href = a.getAttribute('href');
        if (href === '#') return;
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          const offset = target.offsetTop - 80;
          window.scrollTo({ top: offset, behavior: 'smooth' });
        }
      });
    });

    const nav = document.getElementById('mainNav');
    const mobBtn = document.getElementById('mobBtn');
    const navLinks = document.getElementById('navLinks');

    /* scroll effect */
    window.addEventListener('scroll', () => {
      nav.classList.toggle('scrolled', window.scrollY > 20);
    });

    /* mobile toggle */
    mobBtn.addEventListener('click', () => {
      const isOpen = navLinks.classList.toggle('active');
      mobBtn.setAttribute('aria-expanded', isOpen);
    });
  </script>

  @stack('scripts')
</body>
</html>