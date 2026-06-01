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
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .animate-float { animation: float 3s ease-in-out infinite; }

    
/* ─── NAV ─────────────────────────────────────────── */
.nav-wrap{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 24px;transition:background .4s var(--ease-smooth),box-shadow .4s var(--ease-smooth);}
.nav-wrap.scrolled{background:rgba(255,252,207,.88);backdrop-filter:blur(22px);-webkit-backdrop-filter:blur(22px);box-shadow:0 1px 0 rgba(6,58,71,.08);}
.nav-inner{max-width:1200px;margin:0 auto;height:72px;display:flex;align-items:center;justify-content:space-between;}
.nav-logo {
  display: flex;
  align-items: center;
  text-decoration: none;
}
/* Logo image */
.nav-logo-img {
  height: 98px;   /* adjust to fit navbar */
  width: auto;
  object-fit: contain;
  display: block;
}
.nav-logo:hover{opacity:.72;}
.nav-logo-dot{width:8px;height:8px;border-radius:50%;background:var(--indigo);display:inline-block;animation:pulse 2.8s ease-in-out infinite;}
.nav-links{display:flex;align-items:center;gap:34px;list-style:none;}
.nav-links a{font-size:.875rem;font-weight:500;color:var(--muted);text-decoration:none;position:relative;padding:4px 0;transition:color .2s;}
.nav-links a::after{content:'';position:absolute;bottom:-2px;left:0;right:0;height:1.5px;background:var(--ocean);transform:scaleX(0);transform-origin:left;transition:transform .3s var(--ease-spring);}
.nav-links a:hover{color:var(--ocean);}
.nav-links a:hover::after{transform:scaleX(1);}
.nav-cta{background:var(--ocean)!important;color:#fff!important;padding:10px 22px!important;border-radius:var(--r-sm)!important;transition:background .2s,transform .2s var(--ease-spring)!important;}
.nav-cta::after{display:none!important;}
.nav-cta:hover{background:var(--ocean-hover)!important;transform:translateY(-1px)!important;}
.nav-mob-btn{display:none;background:none;border:none;cursor:pointer;padding:8px;color:var(--ocean);flex-direction:column;gap:5px;}
.nav-mob-btn span{display:block;width:24px;height:2px;background:currentColor;border-radius:2px;transition:all .3s;}

/* ─── MOBILE STYLES ─────────────────────────── */
@media (max-width: 900px){

.nav-mob-btn{
  display:flex;
  flex-direction:column;
  gap:5px;
  background:none;
  border:none;
  cursor:pointer;
  z-index:1001;
}

.nav-links{
  position:absolute;
  top:72px;
  left:0;
  right:0;

  display:none; /* IMPORTANT */

  flex-direction:column;
  align-items:flex-start;
  gap:20px;

  background:rgba(255,252,207,.96);
  backdrop-filter:blur(22px);

  padding:24px;
  box-shadow:0 8px 32px rgba(6,58,71,.12);

  z-index:1000;
}

.nav-links.active{
  display:flex; /* IMPORTANT */
}
}

.nav-wrap{
  position:fixed;
}

.nav-inner{
  position:relative;
}

.site-footer{
  background: var(--cream); /* 🔥 match your sections */
  border-top: 1px solid rgba(6,58,71,.08);
  padding: clamp(56px, 6vw, 80px) 0 clamp(20px, 3vw, 32px);
  position: relative;
}

.site-footer .container{
  max-width: 1200px; 
  margin: 0 auto;
  padding: 0 24px;
}

.site-footer::before{
  content:'';
  position:absolute;
  top:24px;
  left:0;
  right:0;
  height:24px;
  background: linear-gradient(
    to bottom,
    transparent,
    var(--cream)
  );
}

/* Grid */
.footer-grid{
  display:grid;
  grid-template-columns: repeat(4,1fr);
  gap: clamp(20px, 2.5vw, 32px);
  margin-bottom: 36px;
}

/* Brand */
.footer-brand p{
  font-size: 0.9rem;
  color: rgba(6,58,71,0.65);
  line-height: 1.7;
  margin-top: 12px;
}

/* Logo */
.footer-logo{
  display:flex;
  align-items:center;
  gap:10px;
  font-weight:800;
  color: var(--navy);
}

.footer-logo img{
  height: 36px;
}

/* Headings */
.footer-grid h4{
  font-family: 'Fraunces', serif; /* 🔥 match headings */
  font-size: 1.05rem;
  margin-bottom: 12px;
  color: var(--ink);
}

/* Links */
.footer-grid ul{
  list-style:none;
  padding:0;
  margin:0;
}

.footer-grid li{
  margin-bottom:10px;
}

.footer-grid a{
  font-size: 0.9rem;
  color: rgba(6,58,71,0.65);
}

.footer-grid a:hover{
  color: var(--ink);
}

/* Socials */
.footer-socials{
  display:flex;
  gap:10px;
  margin-top:16px;
}

.footer-socials a{
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:50%;
  background:#fff;
  border:1px solid rgba(6,58,71,.08);
  color: var(--ink);
  transition: all .25s ease;
}

.footer-socials a:hover{
  background: var(--ocean);
  color:#fff;
  transform: translateY(-3px);
}

/* Contact */
.footer-contact li{
  display:flex;
  gap:10px;
  align-items:flex-start;
}

.footer-contact i{
  color: var(--gold);
  margin-top: 3px;
}

.footer-contact span{
  display:block;
  font-size: 0.8rem;
  color: rgba(6,58,71,0.65);
}

/* Bottom */
.footer-bottom{
  border-top: 1px solid rgba(0,0,0,0.06);
  padding-top: 20px;
  text-align:center;
  font-size: 0.85rem;
  color: rgba(6,58,71,0.65);
}

/* Responsive */
@media (max-width: 900px){
  .footer-grid{
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 600px){
  .footer-grid{
    grid-template-columns: 1fr;
  }
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