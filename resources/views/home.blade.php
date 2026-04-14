{{-- resources/views/home.blade.php - TAILWIND VERSION --}}
@extends('layouts.app')

@section('title', 'CavalCare — Trusted In-Home Care for Your Loved Ones')

@push('head')
<style>
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeInUp 0.8s ease-out forwards; }
.gradient-text { background: linear-gradient(135deg, #28527A 0%, #A9C7A2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.reveal { opacity: 0; transform: translateY(50px); transition: all 0.8s ease-out; }
.reveal.active { opacity: 1; transform: translateY(0); }
</style>
@endpush

@section('content')

{{-- HERO SECTION --}}
<section class="relative overflow-hidden bg-gradient-to-br from-white via-[#FFF8F2] to-[#F0F4F8] pt-10 pb-24 lg:pt-26 lg:pb-40">
  <!-- Decorative circles -->
  <div class="absolute w-96 h-96 bg-secondary/10 rounded-full top-0 right-0 translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
  <div class="absolute w-80 h-80 bg-accent2/10 rounded-full bottom-0 left-0 -translate-x-1/2 translate-y-1/2 pointer-events-none"></div>
  
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
      
      {{-- Text Content --}}
      <div class="lg:w-1/2 space-y-8 animate-fade-in">
        <!-- Trust badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm shadow-sm border border-primary/10">
          <svg class="w-5 h-5 text-secondary" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          <span class="text-sm font-semibold text-primary">Trusted by 500+ Nigerian Families</span>
        </div>

        <!-- Heading -->
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-heading font-bold leading-tight">
          <span class="gradient-text block mb-2">Compassionate Care,</span>
          <span class="text-primary block">Dignified Living</span>
        </h1>

        <p class="text-xl sm:text-2xl text-gray-600 leading-relaxed max-w-xl">
          Professional in-home care that brings warmth, dignity, and peace of mind to Nigerian families. From elderly care to post-surgery support.
        </p>

        <!-- Benefits -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-accent2/20 flex items-center justify-center mt-1">
              <svg class="w-4 h-4 text-accent2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-800">Verified Caregivers</p>
              <p class="text-sm text-gray-600">Background-checked professionals</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-accent2/20 flex items-center justify-center mt-1">
              <svg class="w-4 h-4 text-accent2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-800">Flexible Plans</p>
              <p class="text-sm text-gray-600">Hourly to full-time care</p>
            </div>
          </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 pt-6">
          <a href="#care-plans" class="group inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold rounded-2xl text-white bg-primary shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
            <span>Schedule Care</span>
            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </a>
          <a href="#care-types" class="inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold rounded-2xl text-primary bg-white border-2 border-primary/20 hover:border-primary hover:shadow-lg transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Watch How It Works</span>
          </a>
        </div>

        <!-- Stats -->
        <div class="flex items-center gap-8 pt-8 border-t border-gray-200">
          <div class="text-center">
            <div class="text-3xl font-bold text-primary">15+</div>
            <div class="text-sm text-gray-600 mt-1">Years Experience</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-primary">500+</div>
            <div class="text-sm text-gray-600 mt-1">Families Served</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-primary">98%</div>
            <div class="text-sm text-gray-600 mt-1">Satisfaction</div>
          </div>
        </div>
      </div>

      {{-- Hero Image --}}
      <div class="lg:w-1/2 relative animate-fade-in" style="animation-delay: 0.2s;">
        <div class="relative rounded-3xl overflow-hidden shadow-2xl animate-float">
          <img src="https://images.unsplash.com/photo-1584515933487-779824d29309?w=800&h=900&fit=crop" 
               alt="Handholding care" 
               class="w-full h-[500px] lg:h-[600px] object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-primary/20 to-transparent"></div>
          
          <!-- Floating testimonial -->
          <div class="absolute bottom-8 left-8 right-8 bg-white/70 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-white/30">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-full bg-secondary flex items-center justify-center text-white font-bold text-xl flex-shrink-0">A</div>
              <div class="flex-1">
                <div class="flex items-center gap-1 mb-2">
                  @for($i = 0; $i < 5; $i++)
                  <svg class="w-4 h-4 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                  @endfor
                </div>
                <p class="text-sm text-gray-700 italic">"Exceptional care. True peace of mind."</p>
                <p class="text-xs text-gray-600 mt-2 font-medium">— Amina T., Lagos</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 24/7 Badge -->
        <div class="absolute -top-6 -right-6 w-32 h-32 bg-accent1 rounded-full flex items-center justify-center text-white shadow-2xl animate-float" style="animation-delay: 0.5s;">
          <div class="text-center">
            <div class="text-2xl font-bold">24/7</div>
            <div class="text-xs">Support</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- CARE SERVICES --}}
<section id="care-types" class="py-20 lg:py-32 bg-white relative overflow-hidden">
  <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-gray-50 to-transparent"></div>
  
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16 lg:mb-24 reveal">
      <span class="inline-block px-4 py-2 rounded-full bg-accent2/10 text-primary font-semibold text-sm mb-4">OUR SERVICES</span>
      <h2 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-primary mb-6">
        Care Services Tailored<br/>to Your Loved Ones
      </h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
        Professional expertise with genuine compassion, creating care that feels like family.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

    @foreach ($careTypes as $index => $careType)
        @php
            $meta = $careType->meta ?? [];

            $icon = $meta['icon'] ?? '💙';
            $description = $meta['description'] ?? 'No description available';

            // Optional: rotating gradients
            $gradients = [
                'from-blue-50 to-blue-100/50',
                'from-purple-50 to-purple-100/50',
                'from-green-50 to-green-100/50',
                'from-pink-50 to-pink-100/50',
                'from-yellow-50 to-yellow-100/50',
                'from-orange-50 to-orange-100/50',
            ];

            $gradient = $gradients[$index % count($gradients)];
        @endphp

        <div
            class="reveal group bg-gradient-to-br {{ $gradient }} rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-3"
            style="animation-delay: {{ $index * 0.1 }}s;"
        >
            <div class="w-20 h-20 rounded-2xl bg-white shadow-lg flex items-center justify-center text-4xl mb-6">
                {{ $icon }}
            </div>

            <h3 class="text-2xl font-heading font-bold text-primary mb-4 group-hover:text-accent2 transition-colors">
                {{ $careType->name }}
            </h3>

            <p class="text-gray-700 leading-relaxed mb-6">
                {{ $description }}
            </p>

            <a href="#"
               class="inline-flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all">
                <span>Learn More</span>
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    @endforeach

</div>

  </div>
</section>

{{-- HOW IT WORKS --}}
<section id="how-it-works" class="py-20 lg:py-32 bg-gradient-to-br from-gray-50 to-white relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16 lg:mb-24 reveal">
      <span class="inline-block px-4 py-2 rounded-full bg-secondary/10 text-primary font-semibold text-sm mb-4">SIMPLE PROCESS</span>
      <h2 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-primary mb-6">
        Getting Started is Simple
      </h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto">
        From consultation to consistent care, we're with you every step.
      </p>
    </div>

    <div class="relative">
      <div class="hidden lg:block absolute left-1/2 top-0 bottom-0 w-1 bg-gradient-to-b from-secondary via-accent1 to-accent2 transform -translate-x-1/2"></div>

      @php
      $steps = [
        ['num'=>1, 'title'=>'Schedule Consultation', 'desc'=>'Book a free consultation to discuss your specific care needs and preferences.', 'icon'=>'📞'],
        ['num'=>2, 'title'=>'Create Care Plan', 'desc'=>'We develop a personalized care plan tailored to your loved one\'s needs.', 'icon'=>'📋'],
        ['num'=>3, 'title'=>'Meet Your Caregiver', 'desc'=>'Connect with your verified, background-checked caregiver.', 'icon'=>'🤝'],
        ['num'=>4, 'title'=>'Begin Quality Care', 'desc'=>'Enjoy peace of mind with consistent, professional care and 24/7 support.', 'icon'=>'💝'],
      ];
      @endphp

      <div class="space-y-12 lg:space-y-24">
        @foreach($steps as $index => $step)
        <div class="reveal relative" style="animation-delay: {{ $index * 0.15 }}s;">
          <div class="flex flex-col lg:flex-row items-center gap-8 {{ $index % 2 == 0 ? 'lg:flex-row' : 'lg:flex-row-reverse' }}">
            <div class="lg:w-5/12 {{ $index % 2 == 0 ? 'lg:text-right' : 'lg:text-left' }}">
              <div class="inline-block px-3 py-1 rounded-full bg-primary/10 text-primary font-semibold text-sm mb-3">
                Step {{ $step['num'] }}
              </div>
              <h3 class="text-3xl font-heading font-bold text-primary mb-4">{{ $step['title'] }}</h3>
              <p class="text-lg text-gray-600 leading-relaxed">{{ $step['desc'] }}</p>
            </div>

            <div class="lg:w-2/12 flex justify-center">
              <div class="w-24 h-24 rounded-full bg-gradient-to-br from-secondary to-accent1 shadow-2xl flex items-center justify-center text-4xl relative z-10 hover:shadow-3xl transition-shadow">
                {{ $step['icon'] }}
              </div>
            </div>

            <div class="lg:w-5/12">
              <div class="rounded-2xl overflow-hidden shadow-xl bg-gradient-to-br from-white to-gray-50 p-8 h-48 flex items-center justify-center">
                <p class="text-6xl opacity-20">{{ $step['icon'] }}</p>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- CARE PLANS --}}
<section id="care-plans" class="py-20 lg:py-32 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Section Header --}}
    <div class="text-center mb-16 lg:mb-24 reveal">
      <span class="inline-block px-4 py-2 rounded-full bg-accent1/10 text-primary font-semibold text-sm mb-4">
        FLEXIBLE PRICING
      </span>
      <h2 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-primary mb-6">
        Care Plans That Fit<br/>Your Needs
      </h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto">
        From occasional check-ins to continuous care, each plan is structured to meet
        your loved one’s needs — without uncertainty or compromise.
      </p>
    </div>

    {{-- Plans --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

      @foreach($carePlans as $index => $plan)
        @php
          $meta = $plan->meta ?? [];
          $recommended = $meta['recommended_for'] ?? [];
          $isPopular = ($index === 1);
        @endphp

        <div
          class="relative reveal"
          style="animation-delay: {{ $index * 0.1 }}s;"
        >
          {{-- Popular badge --}}
          @if($isPopular)
            <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
              <span class="px-4 py-1 rounded-full bg-primary text-white text-xs font-semibold tracking-wide shadow-lg">
                MOST POPULAR
              </span>
            </div>
          @endif

          {{-- Card --}}
          <div
            class="
              group h-full rounded-3xl bg-white p-8
              border border-primary/10
              shadow-[0_20px_40px_rgba(0,0,0,0.06)]
              transition-all duration-500 ease-out
              hover:-translate-y-2
              hover:shadow-[0_30px_70px_rgba(0,0,0,0.12)]
              {{ $isPopular ? 'ring-2 ring-primary/80' : '' }}
              overflow-hidden
              flex flex-col
            "
          >

            {{-- Name --}}
            <h3 class="text-2xl font-heading font-bold text-primary mb-2">
              {{ $plan->name }}
            </h3>

            {{-- Duration --}}
            <p class="text-sm text-gray-600 mb-6">
              {{ $meta['daily_hours'] ?? 'Flexible hours' }} hrs/day ·
              {{ $meta['duration_days'] ?? 'Ongoing' }} days
            </p>

            {{-- Price --}}
            <!-- <div class="mb-8">
              <div class="flex items-baseline gap-1">
                <span class="text-lg text-gray-500 font-medium">₦</span>
                <span class="text-3xl lg:text-4xl font-semibold text-primary tracking-tight">
                  {{ number_format($plan->price) }}
                </span>
              </div>
              <p class="text-sm text-gray-500 mt-2">
                {{ data_get($meta, 'daily_hours')
                    ? data_get($meta, 'daily_hours').' hrs/day'
                    : data_get($meta, 'duration_days').' day plan' }}
              </p>
            </div> -->

            {{-- Features --}}
            <ul class="space-y-4 mb-10 flex-grow">
              <li class="flex gap-3">
                <svg class="w-5 h-5 text-accent2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-700">
                  Up to {{ $meta['max_caregivers'] ?? 1 }} dedicated caregiver(s)
                </span>
              </li>

              @foreach($recommended as $careTypeId)
                <li class="flex gap-3">
                  <svg class="w-5 h-5 text-accent2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-gray-700">
                    {{ \App\Models\CareType::find($careTypeId)?->name }}
                  </span>
                </li>
              @endforeach
            </ul>

            {{-- CTA --}}
            <a
              href="{{ url('/book/' . $plan->id) }}"
              class="
                mt-auto block text-center px-6 py-4 rounded-2xl font-semibold
                transition-all duration-300
                {{ $isPopular
                  ? 'bg-primary text-white hover:shadow-xl'
                  : 'bg-white text-primary border border-primary/30 hover:border-primary'
                }}
                group-hover:scale-[1.02]
              "
            >
              {{ $isPopular ? 'Get Started' : 'Choose Plan' }}
            </a>
          </div>
        </div>
      @endforeach

    </div>

    {{-- Footer note --}}
    <div class="mt-16 text-center reveal">
      <p class="text-gray-600">
        Need something more tailored?
        <a href="#contact" class="text-primary font-semibold hover:underline">
          Speak with our care team →
        </a>
      </p>
    </div>

  </div>
</section>


{{-- TESTIMONIALS --}}
<section class="py-20 lg:py-32 bg-gradient-to-br from-gray-50 via-white to-gray-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16 reveal">
      <span class="inline-block px-4 py-2 rounded-full bg-secondary/10 text-primary font-semibold text-sm mb-4">TESTIMONIALS</span>
      <h2 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-primary mb-6">
        Trusted by Families<br/>Across Nigeria
      </h2>
      <p class="text-xl text-gray-600 max-w-3xl mx-auto">
        Real stories from families who found peace of mind.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @php
      $testimonials = [
        ['name' => 'Amina Taiwo', 'role' => 'Daughter', 'text' => 'The caregiver assigned to my mother has been absolutely wonderful. She treats her with such dignity and warmth.', 'rating' => 5, 'image' => 'A'],
        ['name' => 'Chidi Okonkwo', 'role' => 'Son', 'text' => 'After my father\'s surgery, CavalCare\'s team was responsive, professional, and genuinely caring. Dad is recovering beautifully.', 'rating' => 5, 'image' => 'C'],
        ['name' => 'Folake Adebayo', 'role' => 'Wife', 'text' => 'My husband has special needs. CavalCare matched us with an amazing caregiver who understands his condition perfectly.', 'rating' => 5, 'image' => 'F'],
      ];
      @endphp

      @foreach($testimonials as $index => $testimonial)
      <div class="reveal bg-gradient-to-br from-white to-[#FFF8F2] border-l-4 border-secondary rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300" style="animation-delay: {{ $index * 0.15 }}s;">
        <div class="flex items-center gap-1 mb-6">
          @for($i = 0; $i < $testimonial['rating']; $i++)
          <svg class="w-5 h-5 text-secondary" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          @endfor
        </div>

        <p class="text-gray-700 leading-relaxed mb-6 italic text-lg">
          "{{ $testimonial['text'] }}"
        </p>

        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary to-accent2 flex items-center justify-center text-white font-bold text-xl shadow-lg">
            {{ $testimonial['image'] }}
          </div>
          <div>
            <p class="font-semibold text-primary text-lg">{{ $testimonial['name'] }}</p>
            <p class="text-sm text-gray-600">{{ $testimonial['role'] }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- FINAL CTA --}}
<section id="contact" class="py-20 lg:py-32 bg-gradient-to-br from-primary to-accent2 relative overflow-hidden">
  <!-- Decorative elements -->
  <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
  <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center reveal">
    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-white mb-6">
      Ready to Experience<br/>Compassionate Care?
    </h2>
    <p class="text-xl sm:text-2xl text-white/90 mb-12 max-w-3xl mx-auto">
      Join the families who trust CavalCare for professional, dignified in-home care.
    </p>
<div class="flex flex-col sm:flex-row gap-6 justify-center">
  <a href="#" class="inline-flex items-center justify-center gap-3 px-10 py-5 text-lg font-bold rounded-2xl text-primary bg-white shadow-2xl hover:shadow-3xl transition-all duration-300 hover:scale-105">
    <span>Schedule Free Consultation</span>
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
  </a>
  <a href="tel:+2348000000000" class="inline-flex items-center justify-center gap-3 px-10 py-5 text-lg font-bold rounded-2xl text-white bg-white/10 backdrop-blur-sm border-2 border-white/30 hover:bg-white/20 transition-all duration-300">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
    </svg>
    <span>Call: +234 800 000 0000</span>
  </a>
</div>

<div class="mt-16 flex flex-wrap items-center justify-center gap-8 text-white/80">
  <div class="flex items-center gap-2">
    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    <span>Licensed & Insured</span>
  </div>
  <div class="flex items-center gap-2">
    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
      <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
    </svg>
    <span>Verified Caregivers</span>
  </div>
  <div class="flex items-center gap-2">
    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
    </svg>
    <span>24/7 Support</span>
  </div>
</div>
  </div>
</section>
@endsection

@push('scripts')
<script>
// Scroll reveal animation
const reveals = document.querySelectorAll('.reveal');
const revealOnScroll = () => {
  reveals.forEach(element => {
    const elementTop = element.getBoundingClientRect().top;
    const windowHeight = window.innerHeight;
    if (elementTop < windowHeight - 100) {
      element.classList.add('active');
    }
  });
};

window.addEventListener('scroll', revealOnScroll);
revealOnScroll(); // Initial check

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});
</script>
@endpush