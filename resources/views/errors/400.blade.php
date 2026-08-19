@extends('layouts.app')

@section('title', 'Something went wrong — CavalCare')

@section('content')

<div class="min-h-screen flex items-center justify-center px-6 py-16 bg-[#F0FFDB]">

    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl p-10 text-center">

        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-[#1A3A2F] flex items-center justify-center">
            <i class="bi bi-exclamation-lg text-3xl text-[#04A9A7]"></i>
        </div>

        <p class="text-sm font-semibold tracking-widest uppercase text-[#3d6b57] mb-3">
            400
        </p>

        <h1 class="text-3xl font-serif text-[#1A3A2F] mb-4">
            We couldn't complete that request.
        </h1>

        <p class="text-[#3d6b57] leading-7 mb-8">
            Something about the request wasn't valid. Please try again,
            or contact our care team if the problem continues.
        </p>

        <a
            href="https://wa.me/{{ config('app.whatsapp_number') }}"
            class="inline-flex items-center gap-2 bg-[#1A3A2F] text-white px-6 py-3 rounded-xl font-semibold"
        >
            <i class="bi bi-whatsapp"></i>
            Contact us on WhatsApp
        </a>

    </div>

</div>

@endsection