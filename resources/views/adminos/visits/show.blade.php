@extends('adminos.layout')

@php
  $p = $v->patient;
  $ci = $v->checkIn;
  $co = $v->checkOut;
  $statusPill = ['stable' => 'stable', 'attention' => 'attn', 'urgent' => 'urgent'];
  $vitalLabel = ['bp' => 'Blood pressure', 'pulse' => 'Pulse', 'sugar' => 'Blood sugar', 'spo2' => 'Oxygen (SpO2)', 'temp' => 'Temperature', 'resp' => 'Breathing rate', 'pain' => 'Pain score'];
@endphp

@section('title', 'Visit · ' . ($p->display_name ?: $p->full_name))
@section('heading', ($p->display_name ?: $p->full_name) . ' · ' . $v->scheduled_start->timezone('Africa/Lagos')->format('j M'))

@section('content')
  <div><a class="back" href="{{ route('os.visits.index', ['day' => $v->scheduled_start->timezone('Africa/Lagos')->toDateString()]) }}">← Visits on {{ $v->scheduled_start->timezone('Africa/Lagos')->format('j M') }}</a></div>

  <div class="grid-dash">
    <div class="stack">
      <section class="card card-pad">
        <div class="section-head">
          <h2 class="card-title">Visit</h2>
          <span>
            <span class="pill neutral">{{ \App\Models\Care\Visit::STATES[$v->state] ?? $v->state }}</span>
            <span class="pill {{ $statusPill[$v->status] }}">{{ ucfirst($v->status) }}</span>
          </span>
        </div>
        <dl class="dl">
          <dt>Scheduled</dt><dd>{{ $v->scheduled_start->timezone('Africa/Lagos')->format('j M Y, H:i') }}–{{ $v->scheduled_end->timezone('Africa/Lagos')->format('H:i') }} (Lagos)</dd>
          <dt>Caregiver</dt><dd>@if($v->professional)<a href="{{ route('os.caregivers.show', $v->professional) }}">{{ $v->professional->name }}</a>@else Unassigned @endif</dd>
          <dt>Check-in</dt>
          <dd>
            @if($ci)
              {{ $ci->method === 'qr' ? 'QR code on the patient\'s phone (verified)' : 'Manual · ' . str_replace('_', ' ', $ci->reason) . ($ci->reason_note ? ': ' . $ci->reason_note : '') }}
              at {{ $ci->at->timezone('Africa/Lagos')->format('H:i') }}
              · {{ $ci->distance_m !== null ? number_format($ci->distance_m) . ' m from the home' : 'no GPS' }}
              @if($ci->method === 'manual') · family: {{ $ci->confirmation_answer ? ($ci->confirmation_answer === 'yes' ? 'confirmed' : 'said NOT arrived') : 'no answer yet' }}@endif
              @if($ci->resolved_at) · resolved: {{ $ci->resolution_note }}@endif
            @else – @endif
          </dd>
          <dt>Check-out</dt><dd>{{ $co ? $co->at->timezone('Africa/Lagos')->format('H:i') . ' · ' . ($co->distance_m !== null ? number_format($co->distance_m) . ' m from the home' : 'no GPS') : '–' }}</dd>
          <dt>Report</dt><dd>{{ $v->report_submitted_at ? 'Submitted ' . $v->report_submitted_at->timezone('Africa/Lagos')->format('j M H:i') : 'Not submitted' }}</dd>
          <dt>Pay</dt><dd>{{ \App\Services\Care\BookingPricing::naira($v->pay_kobo) }} · {{ str_replace('_', ' ', $v->pay_state) }}{{ $v->pay_hold_reason ? ' (' . $v->pay_hold_reason . ')' : '' }}</dd>
        </dl>
      </section>

      <section class="card" aria-labelledby="vt-h">
        <div class="card-head"><h2 id="vt-h">Vital signs</h2><span class="sub">Flags use the default ranges until the clinical lead sets them</span></div>
        @if($v->vitals->isEmpty())
          <p class="empty">None recorded.</p>
        @else
          <div class="table-wrap">
            <table class="os-table cards">
              <thead><tr><th>Reading</th><th>Value</th><th>Time</th><th>Range</th></tr></thead>
              <tbody>
                @foreach($v->vitals as $r)
                  <tr>
                    <td class="cell-main"><b>{{ $vitalLabel[$r->type] ?? $r->type }}</b>{{ $r->context ? ' · ' . str_replace('_', ' ', $r->context) : '' }}{{ $r->recheck_of ? ' · recheck' : '' }}</td>
                    <td class="num" data-label="Value">{{ $r->value }} {{ $r->unit }}</td>
                    <td class="num" data-label="Time">{{ $r->recorded_at->timezone('Africa/Lagos')->format('H:i') }}@if($r->clock_drift_flag) <span class="pill attn" title="Phone clock differed from the server by over 10 minutes">clock</span>@endif</td>
                    <td data-label="Range">@if($r->out_of_range)<span class="pill urgent">Out of range</span>@else<span class="pill stable">In range</span>@endif</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>

      <section class="card card-pad">
        <h2 class="card-title section-head">Tasks and medicines</h2>
        @forelse($v->tasks as $t)
          <div class="list-row">{{ $t->state === 'done' ? '✓' : '✗' }} {{ $t->task_title ?: $t->task_id }} <span class="sub">{{ $t->recorded_at->timezone('Africa/Lagos')->format('H:i') }}{{ $t->reason ? ' · ' . $t->reason : '' }}</span></div>
        @empty
          <p class="muted flush">No tasks recorded.</p>
        @endforelse
        @foreach($v->medicationEvents as $m)
          <div class="list-row">Medicine: {{ $m->medication_name }} · {{ str_replace('_', ' ', $m->outcome) }} <span class="sub">{{ $m->recorded_at->timezone('Africa/Lagos')->format('H:i') }}{{ $m->reason ? ' · ' . $m->reason : '' }}</span></div>
        @endforeach
      </section>

      <section class="card card-pad">
        <h2 class="card-title section-head">Care note and observations</h2>
        <p class="flush">{{ $v->careNotes->last()?->text ?? 'No care note yet.' }}</p>
        @if($o = $v->observations->last())
          <p class="sub" style="margin-top:10px">Mood {{ $o->mood ?? '–' }} · mobility {{ $o->mobility ?? '–' }} · appetite {{ $o->appetite ?? '–' }} · water {{ $o->water_glasses ?? '–' }} glasses · pain {{ $o->pain_score ?? '–' }}/10</p>
        @endif
        @if($v->amendments->isNotEmpty())
          <h3 class="card-title" style="font-size:15px;margin-top:16px">Amendments</h3>
          @foreach($v->amendments as $a)
            <div class="list-row sub">{{ $a->at->timezone('Africa/Lagos')->format('j M H:i') }} · {{ $a->record_type }} {{ $a->field }}: "{{ $a->old_value }}" → "{{ $a->new_value }}" · {{ $a->reason }}</div>
          @endforeach
        @endif
      </section>
    </div>

    <div class="stack">
      <section class="card card-pad">
        <h2 class="card-title section-head">Client</h2>
        <p class="flush"><a href="{{ route('os.clients.show', $p) }}"><b>{{ $p->full_name }}</b></a></p>
        <p class="sub flush">{{ $p->age_sex }} · {{ $p->address_line }}</p>
        @foreach($p->allergies->where('severe', true) as $a)
          <div class="alert urgent" style="margin-top:10px"><div class="alert-title">Severe allergy: {{ $a->name }}</div></div>
        @endforeach
      </section>
      @if($v->concerns->isNotEmpty())
        <section class="card card-pad">
          <h2 class="card-title section-head">Concerns</h2>
          @foreach($v->concerns as $c)
            <div class="alert {{ $c->resolved_at ? '' : ($c->severity === 'urgent' ? 'urgent' : 'attn') }}" style="{{ $c->resolved_at ? 'background:var(--canvas);border-color:var(--line)' : '' }}">
              <div class="alert-title">{{ ucfirst($c->severity) }} · {{ ucfirst(str_replace('_', ' ', $c->kind)) }}{{ $c->where_on_body ? ' (' . $c->where_on_body . ')' : '' }}</div>
              <p>{{ $c->description }}</p>
              <p>{{ $c->resolved_at ? 'Resolved: ' . $c->resolution_note : ($c->acknowledged_at ? 'Acknowledged, open' : 'Open, not acknowledged') }}</p>
            </div>
          @endforeach
        </section>
      @endif
    </div>
  </div>
@endsection
