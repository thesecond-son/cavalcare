@extends('layouts.app')

@section('title', 'Book Care Plan')

@section('content')
<section class="py-20 bg-gradient-to-br from-white via-[#FFF8F2] to-[#F0F4F8]">
  <div class="max-w-4xl mx-auto px-6">

    <div class="mb-12 text-center">
      <span class="inline-block px-4 py-2 rounded-full bg-accent2/10 text-primary font-semibold text-sm mb-4">
        STEP 1 OF 2
      </span>
      <h1 class="text-4xl sm:text-5xl font-heading font-bold text-primary mb-4">
        Begin Your Care Journey
      </h1>
      <p class="text-xl text-gray-600 max-w-2xl mx-auto">
        You’re one step closer to compassionate, dignified care.
      </p>
    </div>

    <div class="bg-white rounded-3xl shadow-2xl p-10 border border-primary/10">
      <form method="POST" action="{{ route('booking.store') }}" class="space-y-8">
        @csrf

        <input type="hidden" name="care_plan_id" value="{{ $carePlan->id }}">

        <div>
          <label class="block font-semibold text-gray-700 mb-2">Full Name</label>
          <input name="name" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"/>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block font-semibold text-gray-700 mb-2">Email</label>
            <input type="email" name="email" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"/>
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-2">Phone Number</label>
            <input name="phone" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"/>
          </div>
        </div>

        <div>
          <label class="block font-semibold text-gray-700 mb-2">Care Type</label>
          <select name="care_type_id" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">
            @foreach($careTypes as $type)
              <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 border border-gray-200">
          <p class="font-semibold text-primary text-lg mb-2">
            Selected Plan: {{ $carePlan->name }}
          </p>
          <!-- <p class="text-gray-600">
            ₦{{ number_format($carePlan->price) }} · Flexible care structure
          </p> -->
        </div>

        <button
          class="w-full py-5 rounded-2xl bg-primary text-white text-lg font-semibold shadow-xl hover:shadow-2xl transition-all hover:scale-[1.02]"
        >
          Continue to Confirmation
        </button>
      </form>
    </div>

  </div>
</section>
@endsection
