@extends('adminos.layout')

@php
  $hour = now()->timezone('Africa/Lagos')->hour;
  $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
  $firstName = explode(' ', auth()->user()->name)[0];
  $statePill = ['scheduled' => 'neutral', 'en_route' => 'stable', 'checked_in' => 'stable', 'checked_out' => 'done', 'missed' => 'urgent', 'cancelled' => 'neutral'];
  $statusPill = ['attention' => 'attn', 'urgent' => 'urgent'];
@endphp

@section('title', 'Dashboard')
@section('heading', "{$greeting}, {$firstName}")

@section('content')
  <div class="kpis">
    <a class="card kpi kpi-link" href="{{ route('os.visits.index') }}">
      <div class="kpi-label">Today's visits</div>
      <div class="kpi-value">{{ $stats['visits_today'] }}</div>
      <div class="kpi-sub"><b>{{ $stats['visits_done'] }} done</b> · {{ $stats['visits_live'] }} live · {{ $stats['visits_upcoming'] }} upcoming</div>
    </a>
    <a class="card kpi kpi-link {{ $stats['matching'] ? 'attn' : '' }}" href="{{ route('os.app-bookings.index', ['status' => 'matching']) }}">
      <div class="kpi-label">Looking for a caregiver</div>
      <div class="kpi-value">{{ $stats['matching'] }}</div>
      <div class="kpi-sub">Paid app bookings not yet matched</div>
    </a>
    <a class="card kpi kpi-link" href="{{ route('os.caregivers.index', ['status' => 'verifying']) }}">
      <div class="kpi-label">Checks to review</div>
      <div class="kpi-value">{{ $stats['credentials'] }}</div>
      <div class="kpi-sub">Caregiver documents waiting</div>
    </a>
    <div class="card kpi">
      <div class="kpi-label">Revenue ({{ now()->format('M') }})</div>
      <div class="kpi-value">₦{{ number_format($stats['revenue_month']) }}</div>
      <div class="kpi-sub">{{ $stats['bookings_month'] }} paid {{ \Illuminate\Support\Str::plural('booking', $stats['bookings_month']) }} · website and app</div>
    </div>
  </div>

  <div class="grid-dash">
    <section class="card" aria-labelledby="today-h">
      <div class="card-head">
        <h2 id="today-h">Today's visits</h2>
        <a href="{{ route('os.visits.index') }}">Open visits →</a>
      </div>
      @if($today->isEmpty())
        <p class="empty">No visits today.</p>
      @else
        <div class="table-wrap">
          <table class="os-table cards">
            <thead><tr><th>Client</th><th>Caregiver</th><th>Time</th><th>Status</th></tr></thead>
            <tbody>
              @foreach($today as $v)
                <tr class="row-link" data-href="{{ route('os.visits.show', $v) }}">
                  <td class="cell-main">
                    <div class="who">
                      <div class="avatar">{{ $v->patient->initials }}</div>
                      <div>
                        <a href="{{ route('os.visits.show', $v) }}"><b>{{ $v->patient->display_name ?: $v->patient->full_name }}</b></a>
                        <small>{{ $v->patient->area }}</small>
                      </div>
                    </div>
                  </td>
                  <td data-label="Caregiver">{{ $v->professional?->name ?? 'Unassigned' }}</td>
                  <td class="num" data-label="Time">{{ $v->scheduled_start->timezone('Africa/Lagos')->format('H:i') }}–{{ $v->scheduled_end->timezone('Africa/Lagos')->format('H:i') }}</td>
                  <td data-label="Status">
                    <span class="pill {{ $statePill[$v->state] ?? 'neutral' }}">{{ \App\Models\Care\Visit::STATES[$v->state] ?? $v->state }}</span>
                    @isset($statusPill[$v->status])<span class="pill {{ $statusPill[$v->status] }}">{{ ucfirst($v->status) }}</span>@endisset
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </section>

    <div class="stack">
      <section class="card card-pad" aria-labelledby="att-h">
        <div class="section-head">
          <h2 id="att-h" class="card-title">Needs attention</h2>
          @if($alerts->isNotEmpty())<span class="pill urgent">{{ $alerts->count() }} open</span>@endif
        </div>
        @forelse($alerts->take(6) as [$kind, $title, $text, $url])
          <div class="alert {{ $kind }}">
            <div class="alert-title">{{ $title }}</div>
            <p>{{ $text }} <a href="{{ $url }}">Open →</a></p>
          </div>
        @empty
          <p class="muted flush">Nothing needs a coordinator right now.</p>
        @endforelse
        @if($alerts->count() > 6)<p class="sub" style="margin-top:10px">{{ $alerts->count() - 6 }} more on the Visits page.</p>@endif
      </section>

      <section class="card" aria-labelledby="recent-h">
        <div class="card-head"><h2 id="recent-h">Latest website bookings</h2><a href="{{ route('os.bookings.index') }}">All →</a></div>
        @forelse($recent as $pb)
          <a class="mini-row" href="{{ route('os.bookings.show', $pb->reference) }}">
            <span><b>{{ $pb->booker_details['name'] ?? 'Unknown' }}</b><span class="sub"> · {{ $pb->carePlan?->display_name ?? 'Plan removed' }}</span></span>
            @include('adminos.partials.status-pill', ['status' => $pb->status])
          </a>
        @empty
          <p class="empty">No website bookings yet.</p>
        @endforelse
        @if($stats['awaiting_intake'])<p class="sub" style="padding:0 24px 18px;margin:0">{{ $stats['awaiting_intake'] }} paid, waiting for care details.</p>@endif
      </section>
    </div>
  </div>
@endsection
