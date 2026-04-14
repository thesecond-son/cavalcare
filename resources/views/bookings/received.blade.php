@extends('layouts.app')

@section('title', 'Booking Received')

@section('content')
<section class="py-20 bg-gray-50">
  <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl p-10">

    <h1 class="text-3xl font-bold text-primary mb-4">
      Booking Received
    </h1>

    <p class="text-gray-600 mb-6">
      Thank you for choosing CavalCare. We’ve received your booking.
    </p>

    <div class="bg-gray-50 rounded-2xl p-6 mb-6">
      <p><strong>Reference:</strong> {{ $preBooking->reference }}</p>
      <p><strong>Care Plan:</strong> {{ $preBooking->carePlan->name }}</p>
      <p><strong>Care Type:</strong> {{ $preBooking->careType->name }}</p>
      <p><strong>Status:</strong> Pending payment confirmation</p>
    </div>

    <h3 class="font-semibold mb-2">Next steps</h3>

    <ul class="list-disc pl-6 text-gray-600 space-y-2">
      <li>Complete payment using the instructions sent to your email</li>
      <li>Our team will confirm payment</li>
      <li>You’ll receive login details to track your care</li>
    </ul>

  </div>
</section>
@endsection
