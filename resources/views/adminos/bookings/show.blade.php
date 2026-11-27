@extends('adminos.layout')

@php
  $b = $pb->booker_details ?? [];
  $booking = $pb->booking;
  $patient = $booking?->patient_details ?? [];
  $details = $booking?->booking_details ?? [];
  $fmt = fn ($v) => is_array($v) ? implode(', ', array_filter(array_map(fn ($x) => is_scalar($x) ? $x : null, $v))) : $v;
@endphp

@section('title', $pb->reference)
@section('heading', $b['name'] ?? $pb->reference)

@section('content')
  <div><a class="back" href="{{ route('os.bookings.index') }}">← All bookings</a></div>

  <div class="grid-dash">
    <div class="stack">
      <section class="card card-pad" aria-labelledby="bk-h">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap">
          <h2 id="bk-h" style="font-family:var(--sans);font-weight:800;font-size:19px">Booking {{ $pb->reference }}</h2>
          @include('adminos.partials.status-pill', ['status' => $pb->status])
        </div>
        <dl class="dl">
          <dt>Care type</dt><dd>{{ $pb->careType?->name ?? '–' }}</dd>
          <dt>Plan</dt><dd>{{ $pb->carePlan?->display_name ?? 'Plan removed' }} @if($pb->carePlan)<span class="muted">({{ $pb->carePlan->formatted_price }})</span>@endif</dd>
          <dt>Add-ons</dt>
          <dd>
            @forelse($pb->addOns as $addOn)
              {{ $addOn->name }} <span class="muted">₦{{ number_format($addOn->pivot->price_at_booking) }}</span>@if(! $loop->last), @endif
            @empty
              None
            @endforelse
          </dd>
          <dt>Amount paid</dt><dd class="num">{{ $pb->amount_paid ? '₦' . number_format($pb->amount_paid / 100) : 'Not paid' }}</dd>
          <dt>Paystack ref</dt><dd>{{ $pb->paystack_reference ?? '–' }}</dd>
          <dt>Booked</dt><dd>{{ $pb->created_at->timezone('Africa/Lagos')->format('j M Y, H:i') }} (Lagos)</dd>
        </dl>
      </section>

      <section class="card card-pad" aria-labelledby="pt-h">
        <h2 id="pt-h" style="font-family:var(--sans);font-weight:800;font-size:19px;margin-bottom:16px">Care details from the intake form</h2>
        @if(! $booking)
          <p class="muted" style="margin:0">The family has not submitted the intake form yet.</p>
        @else
          <dl class="dl">
            @foreach($patient as $key => $value)
              @continue(blank($value))
              <dt>{{ ucfirst(str_replace('_', ' ', $key)) }}</dt><dd>{{ $fmt($value) }}</dd>
            @endforeach
            @foreach($details as $key => $value)
              @continue(blank($value) || str_ends_with($key, '_id'))
              <dt>{{ ucfirst(str_replace('_', ' ', $key)) }}</dt><dd>{{ $fmt($value) }}</dd>
            @endforeach
          </dl>
        @endif
      </section>
    </div>

    <div class="stack">
      <section class="card card-pad" aria-labelledby="by-h">
        <h2 id="by-h" style="font-family:var(--sans);font-weight:800;font-size:19px;margin-bottom:16px">Booked by</h2>
        <dl class="dl" style="grid-template-columns:90px 1fr">
          <dt>Name</dt><dd>{{ $b['name'] ?? '–' }}</dd>
          <dt>Email</dt><dd>@if(! empty($b['email']))<a href="mailto:{{ $b['email'] }}">{{ $b['email'] }}</a>@else – @endif</dd>
          <dt>Phone</dt><dd>@if(! empty($b['phone']))<a href="tel:{{ preg_replace('/[^\d+]/', '', $b['phone']) }}">{{ $b['phone'] }}</a>@else – @endif</dd>
        </dl>
      </section>
      @php
        $latestCode = $pb->appLinkCodes->first();
        $isPaid = in_array($pb->status?->value, ['paid', 'converted'], true);
      @endphp
      <section class="card card-pad" aria-labelledby="app-h">
        <h2 id="app-h" style="font-family:var(--sans);font-weight:800;font-size:19px;margin-bottom:12px">Cavalcare app</h2>
        @if($pb->user)
          <span class="pill done">Linked</span>
          <p style="margin:10px 0 0">Opened in the app by <b>{{ $pb->user->name ?: $pb->user->email }}</b>{{ $latestCode?->redeemed_at ? ' on ' . $latestCode->redeemed_at->timezone('Africa/Lagos')->format('j M Y') : '' }}.</p>
        @elseif(! $isPaid)
          <p class="muted" style="margin:0">An app code is sent once the booking is paid.</p>
        @else
          @if($latestCode && $latestCode->isUsable())
            <span class="pill attn">Code sent, not used yet</span>
            <p class="muted" style="margin:10px 0 0">Sent {{ ($latestCode->sent_at ?? $latestCode->created_at)->timezone('Africa/Lagos')->format('j M Y, H:i') }}. Expires {{ $latestCode->expires_at->timezone('Africa/Lagos')->format('j M Y') }}.</p>
          @elseif($latestCode)
            <span class="pill neutral">Code expired or replaced</span>
          @else
            <span class="pill neutral">No code yet</span>
            <p class="muted" style="margin:10px 0 0">This family paid before app codes existed.</p>
          @endif
          @can('bookings.manage')
            <form method="POST" action="{{ route('os.bookings.app-code', $pb->reference) }}" style="margin-top:14px"
                  onsubmit="return confirm('Email a new app code to {{ $b['email'] ?? 'the booker' }}? Any earlier code will stop working.')">
              @csrf
              <button class="btn" type="submit">{{ $latestCode ? 'Send a new code' : 'Send app code' }}</button>
            </form>
            <p class="muted" style="margin:10px 0 0;font-size:13px">The code goes only to the booker's email. Staff never see it.</p>
          @endcan
        @endif
      </section>
    </div>
  </div>
@endsection
