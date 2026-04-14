{{-- resources/views/layouts/app.blade.php - TAILWIND VERSION --}}
<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
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
            primary: '#28527A',
            secondary: '#F4C983',
            accent1: '#E98A7D',
            accent2: '#A9C7A2',
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
  </style>

  @stack('head')
</head>
<body class="bg-[#FFF8F2] text-gray-800 font-sans antialiased">

  {{-- Navigation --}}
  <header class="sticky top-0 z-50 bg-[#FFF8F2]/95 backdrop-blur-md border-b border-primary/10">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-gradient-to-br from-primary to-accent2 text-white shadow-lg transition-transform group-hover:rotate-6 group-hover:scale-110">
            <span class="font-bold text-xl">C</span>
          </div>
          <div class="hidden sm:block">
            <span class="text-2xl font-heading font-bold text-primary">CavalCare</span>
            <p class="text-xs text-gray-600 -mt-1">Compassionate Care</p>
          </div>
        </a>

        {{-- Desktop Nav --}}
        <div class="hidden lg:flex items-center space-x-8">
          <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-primary after:transition-all hover:after:w-full">Home</a>
          <a href="#care-types" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-primary after:transition-all hover:after:w-full">Services</a>
          <a href="#care-plans" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-primary after:transition-all hover:after:w-full">Pricing</a>
          <a href="#how-it-works" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-primary after:transition-all hover:after:w-full">How It Works</a>
          <a href="#contact" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-primary after:transition-all hover:after:w-full">Contact</a>
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-4">
          <!-- <a href="#" class="hidden md:inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-primary border-2 border-primary rounded-xl hover:bg-primary hover:text-white transition-all">
            <i class="bi bi-person"></i> Login
          </a> -->
          <a href="#" class="hidden md:inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <i class="bi bi-calendar-check"></i> Book Now
          </a>
          
          {{-- Mobile Toggle --}}
          <button id="mobileToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
            <svg id="menuOpen" class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg id="menuClose" class="w-6 h-6 text-primary hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      {{-- Mobile Menu --}}
      <div id="mobileMenu" class="lg:hidden max-h-0 overflow-hidden transition-all duration-300">
        <div class="flex flex-col space-y-2 py-4">
          <a href="{{ url('/') }}" class="block px-4 py-3 rounded-lg text-base font-medium hover:bg-primary/5 transition-all">Home</a>
          <a href="#care-types" class="block px-4 py-3 rounded-lg text-base font-medium hover:bg-primary/5 transition-all">Services</a>
          <a href="#care-plans" class="block px-4 py-3 rounded-lg text-base font-medium hover:bg-primary/5 transition-all">Pricing</a>
          <a href="#how-it-works" class="block px-4 py-3 rounded-lg text-base font-medium hover:bg-primary/5 transition-all">How It Works</a>
          <a href="#contact" class="block px-4 py-3 rounded-lg text-base font-medium hover:bg-primary/5 transition-all">Contact</a>
          
          <div class="pt-4 space-y-2">
            <!-- <a href="#" class="block text-center px-4 py-3 rounded-lg border-2 border-primary text-primary font-semibold">Login</a> -->
            <a href="#" class="block text-center px-4 py-3 rounded-lg bg-primary text-white font-semibold">Book Now</a>
          </div>
        </div>
      </div>
    </nav>
  </header>

  {{-- Main Content --}}
  <main class="min-h-screen">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="bg-gradient-to-b from-white to-gray-50 border-t border-primary/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
        
        {{-- Company --}}
        <div class="space-y-4">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-gradient-to-br from-primary to-accent2 text-white shadow-lg">
              <span class="font-bold text-xl">C</span>
            </div>
            <h3 class="text-xl font-heading font-bold text-primary">CavalCare</h3>
          </div>
          <p class="text-sm text-gray-600 leading-relaxed">
            Compassionate in-home caregiving for families in Nigeria.
          </p>
          
          <div class="flex items-center gap-3 pt-4">
            <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center bg-white shadow-md hover:shadow-lg hover:-translate-y-1 transition-all">
              <i class="bi bi-facebook text-primary"></i>
            </a>
            <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center bg-white shadow-md hover:shadow-lg hover:-translate-y-1 transition-all">
              <i class="bi bi-twitter text-primary"></i>
            </a>
            <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center bg-white shadow-md hover:shadow-lg hover:-translate-y-1 transition-all">
              <i class="bi bi-instagram text-primary"></i>
            </a>
          </div>
        </div>

        {{-- Services --}}
        <div>
          <h4 class="text-lg font-heading font-bold text-primary mb-4">Services</h4>
          <ul class="space-y-3 text-sm text-gray-600">
            <li><a href="#" class="flex items-center gap-2 hover:text-primary hover:translate-x-1 transition-all">
              <i class="bi bi-chevron-right text-secondary"></i> Elderly Care
            </a></li>
            <li><a href="#" class="flex items-center gap-2 hover:text-primary hover:translate-x-1 transition-all">
              <i class="bi bi-chevron-right text-secondary"></i> Child Disability Care
            </a></li>
            <li><a href="#" class="flex items-center gap-2 hover:text-primary hover:translate-x-1 transition-all">
              <i class="bi bi-chevron-right text-secondary"></i> Post-Surgery Care
            </a></li>
            <li><a href="#" class="flex items-center gap-2 hover:text-primary hover:translate-x-1 transition-all">
              <i class="bi bi-chevron-right text-secondary"></i> Terminal Illness Support
            </a></li>
          </ul>
        </div>

        {{-- Links --}}
        <div>
          <h4 class="text-lg font-heading font-bold text-primary mb-4">Quick Links</h4>
          <ul class="space-y-3 text-sm text-gray-600">
            <li><a href="#" class="flex items-center gap-2 hover:text-primary hover:translate-x-1 transition-all">
              <i class="bi bi-chevron-right text-secondary"></i> How It Works
            </a></li>
            <li><a href="#" class="flex items-center gap-2 hover:text-primary hover:translate-x-1 transition-all">
              <i class="bi bi-chevron-right text-secondary"></i> Care Plans
            </a></li>
            <li><a href="#" class="flex items-center gap-2 hover:text-primary hover:translate-x-1 transition-all">
              <i class="bi bi-chevron-right text-secondary"></i> About Us
            </a></li>
            <li><a href="#" class="flex items-center gap-2 hover:text-primary hover:translate-x-1 transition-all">
              <i class="bi bi-chevron-right text-secondary"></i> Privacy Policy
            </a></li>
          </ul>
        </div>

        {{-- Contact --}}
        <div>
          <h4 class="text-lg font-heading font-bold text-primary mb-4">Contact</h4>
          <ul class="space-y-4 text-sm">
            <li class="flex items-start gap-3">
              <i class="bi bi-geo-alt-fill text-secondary text-lg mt-1"></i>
              <div>
                <p class="font-semibold text-gray-700">Lagos, Nigeria</p>
                <p class="text-gray-600">Headquarters</p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <i class="bi bi-telephone-fill text-secondary text-lg mt-1"></i>
              <div>
                <a href="tel:+2348000000000" class="font-semibold text-gray-700 hover:text-primary">+234 800 000 0000</a>
                <p class="text-gray-600">24/7 Support</p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <i class="bi bi-envelope-fill text-secondary text-lg mt-1"></i>
              <div>
                <a href="mailto:hello@cavalcare.ng" class="font-semibold text-gray-700 hover:text-primary">hello@cavalcare.ng</a>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <div class="border-t border-gray-200 pt-8 text-center text-sm text-gray-600">
        © {{ date('Y') }} CavalCare. All rights reserved.
      </div>
    </div>
  </footer>

  {{-- Floating Contact --}}
  <a href="tel:+2348000000000" class="fixed bottom-8 right-8 w-14 h-14 md:w-16 md:h-16 rounded-full bg-gradient-to-br from-primary to-accent2 shadow-2xl flex items-center justify-center text-white text-xl md:text-2xl hover:scale-110 transition-transform z-50 animate-float">
    <i class="bi bi-telephone-fill"></i>
  </a>

  {{-- Scroll Top --}}
  <button id="scrollTop" class="fixed bottom-8 left-8 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center text-primary opacity-0 invisible transition-all hover:shadow-xl hover:-translate-y-1 z-50">
    <i class="bi bi-arrow-up text-xl"></i>
  </button>

  <script>
    // Mobile menu
    const toggle = document.getElementById('mobileToggle');
    const menu = document.getElementById('mobileMenu');
    const open = document.getElementById('menuOpen');
    const close = document.getElementById('menuClose');
    
    toggle.addEventListener('click', () => {
      menu.style.maxHeight = menu.style.maxHeight ? '' : menu.scrollHeight + 'px';
      open.classList.toggle('hidden');
      close.classList.toggle('hidden');
    });

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
  </script>

  @stack('scripts')
</body>
</html>