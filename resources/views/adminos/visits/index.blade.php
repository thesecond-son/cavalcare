@extends('adminos.layout')

@section('title', 'Visits & notes')
@section('heading', 'Visits & notes')

@php
  $statePill = ['scheduled' => 'neutral', 'en_route' => 'stable', 'checked_in' => 'stable', 'checked_out' => 'done', 'missed' => 'urgent', 'cancelled' => 'neutral'];
  $statusPill = ['stable' => 'stable', 'attention' => 'attn', 'urgent' => 'urgent'];
  $canAct = auth()->user()->can('concerns.manage');
  $date = \Illuminate\Support\Carbon::parse($day);
@endphp

@section('content')
  @foreach($safety as $a)
    <div class="alert urgent" role="alert">
      <div class="alert-title">Safety alert · {{ $a->professional?->name }}{{ $a->call_me ? ' asked to be called' : '' }}</div>
      <p>{{ $a->created_at->timezone('Africa/Lagos')->format('j M, H:i') }}{{ $a->note ? ' · ' . $a->note : '' }}
        @if($a->professional?->user?->phone_e164) · <a href="tel:{{ $a->professional->user->phone_e164 }}">Call {{ $a->professional->user->phone_e164 }}</a>@endif</p>
      @if($canAct)
        <form method="POST" action="{{ route('os.safety.acknowledge', $a) }}" class="inline-form">@csrf
          <input type="text" name="resolution_note" placeholder="What was done (optional)" maxlength="2000">
          <button class="btn" type="submit">Acknowledge</button>
        </form>
      @endif
    </div>
  @endforeach

  @if($concerns->isNotEmpty() || $disputed->isNotEmpty() || $late->isNotEmpty())
    <section class="card card-pad" aria-labelledby="att-h">
      <h2 id="att-h" class="card-title section-head">Needs a coordinator</h2>

      @foreach($concerns as $c)
        <div class="alert {{ $c->severity === 'urgent' ? 'urgent' : 'attn' }}">
          <div class="alert-title">{{ ucfirst($c->severity) }} · {{ ucfirst(str_replace('_', ' ', $c->kind)) }} · {{ $c->visit->patient->display_name ?: $c->visit->patient->full_name }}</div>
          <p>{{ $c->description }}{{ $c->action_taken ? ' Action taken: ' . $c->action_taken : '' }} · reported by {{ $c->visit->professional?->name }}, {{ $c->recorded_at->timezone('Africa/Lagos')->format('j M H:i') }}
            · {{ $c->acknowledged_at ? 'acknowledged' : 'not acknowledged yet' }} · <a href="{{ route('os.visits.show', $c->visit) }}">Open visit</a></p>
          @if($canAct)
            <div class="inline-actions">
              @unless($c->acknowledged_at)
                <form method="POST" action="{{ route('os.concerns.acknowledge', $c) }}">@csrf <button class="btn" type="submit">Acknowledge (tells family)</button></form>
              @endunless
              <form method="POST" action="{{ route('os.concerns.resolve', $c) }}" class="inline-form">@csrf
                <input type="text" name="resolution_note" required maxlength="2000" placeholder="How it was resolved">
                <button class="btn primary" type="submit">Resolve</button>
              </form>
            </div>
          @endif
        </div>
      @endforeach

      @foreach($disputed as $ci)
        <div class="alert urgent">
          <div class="alert-title">{{ $ci->disputed ? 'Arrival disputed by family' : 'Arrival not confirmed (15 min)' }} · {{ $ci->visit->patient->display_name ?: $ci->visit->patient->full_name }}</div>
          <p>{{ $ci->visit->professional?->name }} checked in manually at {{ $ci->at->timezone('Africa/Lagos')->format('H:i') }} ({{ str_replace('_', ' ', $ci->reason) }}){{ $ci->distance_m !== null ? ', ' . number_format($ci->distance_m) . ' m from the home' : ', no GPS' }}.
            Call the patient{{ $ci->visit->patient->phone_e164 ? ' on ' . $ci->visit->patient->phone_e164 : '' }} to check. Pay is on hold.</p>
          @if($canAct)
            <form method="POST" action="{{ route('os.check-ins.resolve', $ci) }}" class="inline-form">@csrf
              <select name="outcome" required><option value="confirmed">Caregiver was there</option><option value="not_there">Caregiver was NOT there</option></select>
              <input type="text" name="resolution_note" required maxlength="255" placeholder="Who you spoke to">
              <button class="btn primary" type="submit">Resolve</button>
            </form>
          @endif
        </div>
      @endforeach

      @foreach($late as $lv)
        <div class="alert attn">
          <div class="alert-title">Late · {{ $lv->patient->display_name ?: $lv->patient->full_name }}</div>
          <p>{{ $lv->professional?->name }} has not checked in; due {{ $lv->scheduled_start->timezone('Africa/Lagos')->format('H:i') }}.
            @if($lv->professional?->user?->phone_e164)<a href="tel:{{ $lv->professional->user->phone_e164 }}">Call {{ $lv->professional->user->phone_e164 }}</a>@endif</p>
        </div>
      @endforeach
    </section>
  @endif

  <section class="card">
    <div class="card-head">
      <h2>{{ $date->isToday() ? 'Today' : $date->format('l j M') }} · {{ $visits->count() }} {{ \Illuminate\Support\Str::plural('visit', $visits->count()) }}</h2>
      <nav class="tabs" aria-label="Day">
        <a href="{{ route('os.visits.index', ['day' => $date->copy()->subDay()->toDateString()]) }}">← Previous</a>
        <a href="{{ route('os.visits.index') }}" class="{{ $date->isToday() ? 'on' : '' }}">Today</a>
        <a href="{{ route('os.visits.index', ['day' => $date->copy()->addDay()->toDateString()]) }}">Next →</a>
      </nav>
    </div>
    @if($visits->isEmpty())
      <p class="empty">No visits on this day.</p>
    @else
      <div class="table-wrap">
        <table class="os-table cards">
          <thead><tr><th>Client</th><th>Caregiver</th><th>Time</th><th>Check-in</th><th>State</th></tr></thead>
          <tbody>
            @foreach($visits as $v)
              <tr class="row-link" data-href="{{ route('os.visits.show', $v) }}">
                <td class="cell-main"><a href="{{ route('os.visits.show', $v) }}"><b>{{ $v->patient->display_name ?: $v->patient->full_name }}</b></a><div class="sub">{{ $v->patient->area }}</div></td>
                <td data-label="Caregiver">{{ $v->professional?->name ?? 'Unassigned' }}</td>
                <td class="num" data-label="Time">{{ $v->scheduled_start->timezone('Africa/Lagos')->format('H:i') }}–{{ $v->scheduled_end->timezone('Africa/Lagos')->format('H:i') }}</td>
                <td data-label="Check-in">{{ $v->checkIn ? ($v->checkIn->method === 'qr' ? 'QR code' : 'Manual') . ' ' . $v->checkIn->at->timezone('Africa/Lagos')->format('H:i') : '–' }}</td>
                <td data-label="State">
                  <span class="pill {{ $statePill[$v->state] ?? 'neutral' }}">{{ \App\Models\Care\Visit::STATES[$v->state] ?? $v->state }}</span>
                  @if($v->status !== 'stable')<span class="pill {{ $statusPill[$v->status] }}">{{ ucfirst($v->status) }}</span>@endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>
@endsection
