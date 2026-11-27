@extends('adminos.layout')

@section('title', 'App bookings')
@section('heading', 'App bookings')

@php
  $tabs = [null => ['All', $counts->sum()]];
  foreach (['matching' => 'Finding a caregiver', 'pending_payment' => 'Waiting for payment', 'matched' => 'Caregiver confirmed', 'cancelled' => 'Cancelled'] as $k => $label) {
      $tabs[$k] = [$label, $counts[$k] ?? 0];
  }
  $pill = ['pending_payment' => 'neutral', 'confirmed' => 'attn', 'matching' => 'attn', 'matched' => 'done', 'active' => 'stable', 'completed' => 'done', 'cancelled' => 'urgent'];
@endphp

@section('content')
  <section class="card">
    <div class="card-head">
      <nav class="tabs" aria-label="Filter by status">
        @foreach($tabs as $key => [$label, $n])
          <a href="{{ route('os.app-bookings.index', array_filter(['status' => $key])) }}" class="{{ ($status ?: null) === ($key ?: null) ? 'on' : '' }}">{{ $label }} {{ $n }}</a>
        @endforeach
      </nav>
      <a href="{{ route('os.bookings.index') }}">Website bookings →</a>
    </div>

    @if($bookings->isEmpty())
      <p class="empty">No app bookings{{ $status ? ' with this status' : ' yet' }}. They appear when a family books and pays in the Cavalcare app.</p>
    @else
      <div class="table-wrap">
        <table class="os-table cards">
          <thead><tr><th>Client</th><th>Plan</th><th>Starts</th><th>Caregiver</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($bookings as $b)
              <tr class="row-link" data-href="{{ route('os.app-bookings.show', $b) }}">
                <td class="cell-main">
                  <div class="who">
                    <div class="avatar">{{ $b->patient?->initials }}</div>
                    <div>
                      <a href="{{ route('os.app-bookings.show', $b) }}"><b>{{ $b->patient?->display_name ?: $b->patient?->full_name }}</b></a>
                      <small>{{ $b->reference }} · booked by {{ $b->booker?->name ?: $b->booker?->email }}</small>
                    </div>
                  </div>
                </td>
                <td data-label="Plan"><div>{{ $b->service_level === 'nursing' ? 'Licensed Nurse' : 'Caretaker' }} – {{ $b->plan_name }}<div class="sub">{{ \App\Services\Care\BookingPricing::naira($b->total_kobo) }}</div></div></td>
                <td class="num" data-label="Starts">{{ $b->start_date->format('j M Y') }}</td>
                <td data-label="Caregiver">{{ $b->professional?->name ?? '–' }}</td>
                <td data-label="Status"><span class="pill {{ $pill[$b->status] ?? 'neutral' }}">{{ \App\Models\Care\CareBooking::STATUSES[$b->status] ?? $b->status }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @include('adminos.partials.pager', ['page' => $bookings])
    @endif
  </section>
@endsection
