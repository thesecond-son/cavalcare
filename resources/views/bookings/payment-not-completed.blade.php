@extends('layouts.app')

@section('title', 'Payment Not Completed — CavalCare')

@section('content')

<div class="min-h-screen flex items-center justify-center px-6 py-16 bg-[#F0FFDB]">

    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl p-10 text-center">

        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-[#1A3A2F] flex items-center justify-center">
            <i class="bi bi-credit-card text-3xl text-[#04A9A7]"></i>
        </div>

        <h1 class="text-3xl font-serif text-[#1A3A2F] mb-4">
            Payment not completed
        </h1>

        <p class="text-[#3d6b57] leading-7 mb-7">
            We haven't been able to confirm payment for this booking yet.
            Please complete your payment before continuing.
        </p>

        @if($preBooking?->reference)

            <div class="bg-[#F0FFDB] rounded-xl px-5 py-4 mb-7 text-left">

                <span class="block text-sm text-[#3d6b57]">
                    Booking reference
                </span>

                <strong class="block text-lg text-[#1A3A2F] mt-1">
                    {{ $preBooking->reference }}
                </strong>

            </div>

        @endif

        <p class="text-sm text-[#3d6b57] leading-6 mb-7">
            If you've already made payment, please allow a few moments
            for the payment to be confirmed. If the problem persists,
            contact our care team.
        </p>

        <a
            href="https://wa.me/{{ config('app.whatsapp_number') }}"
            class="inline-flex items-center justify-center gap-2 bg-[#1A3A2F] text-white px-6 py-3 rounded-xl font-semibold"
        >
            <i class="bi bi-whatsapp"></i>
            Contact us on WhatsApp
        </a>

    </div>

</div>

@endsection