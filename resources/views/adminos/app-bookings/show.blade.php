@extends('adminos.layout')

@php
  use App\Services\Care\BookingPricing;
  $p = $b->patient;
  $pay = $b->payments->first();
  $pill = ['pending_payment' => 'neutral', 'confirmed' => 'attn', 'matching' => 'attn', 'matched' => 'done', 'active' => 'stable', 'completed' => 'done', 'cancelled' => 'urgent'];
  $offerPill = ['open' => 'attn', 'accepted' => 'done', 'declined' => 'urgent', 'expired' => 'neutral', 'taken' => 'neutral', 'withdrawn' => 'neutral'];
  $canAssign = auth()->user()->can('schedule.manage') && $b->status === 'matching';
@endphp

@section('title', $b->reference)
@section('heading', ($p?->display_name ?: $p?->full_name) . ' · ' . $b->reference)

@section('content')
  <div><a class="back" href="{{ route('os.app-bookings.index') }}">← All app bookings</a></div>

  <div class="grid-dash">
    <div class="stack">
      <section class="card card-pad" aria-labelledby="b-h">
        <div class="section-head">
          <h2 id="b-h" class="card-title">Booking</h2>
          <span class="pill {{ $pill[$b->status] ?? 'neutral' }}">{{ \App\Models\Care\CareBooking::STATUSES[$b->status] ?? $b->status }}</span>
        </div>
        <dl class="dl">
          <dt>Plan</dt><dd>{{ $b->service_level === 'nursing' ? 'Licensed Nurse' : 'Caretaker' }} – {{ $b->plan_name }} <span class="sub">({{ $b->careType?->name }})</span></dd>
          <dt>Dates</dt><dd>{{ $b->start_date->format('j M Y') }}{{ $b->end_date && ! $b->end_date->equalTo($b->start_date) ? ' to ' . $b->end_date->format('j M Y') : '' }}{{ $b->start_hour !== null ? ' · from ' . str_pad($b->start_hour, 2, '0', STR_PAD_LEFT) . ':00' : '' }}</dd>
          <dt>Caregiver</dt><dd>{{ $b->caregiver_choice === 'chosen' ? 'Family chose ' . ($b->requestedProfessional?->name ?? 'a caregiver') : 'Match me' }}@if(! empty($b->preferences)) <span class="sub">· prefers {{ collect($b->preferences)->join(', ') }}</span>@endif</dd>
          <dt>Total</dt><dd class="num">{{ BookingPricing::naira($b->total_kobo) }} <span class="sub">({{ BookingPricing::naira($b->price_per_unit_kobo) }} × {{ $b->units }}@if($b->add_ons_total_kobo) + add-ons {{ BookingPricing::naira($b->add_ons_total_kobo) }}@endif)</span></dd>
          <dt>Payment</dt><dd>{{ $pay ? ucfirst($pay->status) . ' · ' . $pay->reference . ($pay->paid_at ? ' · ' . $pay->paid_at->timezone('Africa/Lagos')->format('j M Y, H:i') : '') : 'None' }}</dd>
          @if($b->notes)<dt>Notes</dt><dd>{{ $b->notes }}</dd>@endif
          @if($b->status === 'cancelled')<dt>Cancelled</dt><dd>{{ $b->cancelled_at?->timezone('Africa/Lagos')->format('j M Y, H:i') }} · {{ $b->cancel_reason }}@if($b->paid_at) <span class="pill attn">Paid: refund to review</span>@endif</dd>@endif
        </dl>
      </section>

      <section class="card" aria-labelledby="o-h">
        <div class="card-head"><h2 id="o-h">Offers</h2></div>
        @if($b->offers->isEmpty())
          <p class="empty">No offers yet{{ $b->status === 'pending_payment' ? ': matching starts once payment is confirmed' : '' }}.</p>
        @else
          <div class="table-wrap">
            <table class="os-table cards">
              <thead><tr><th>Caregiver</th><th>Sent</th><th>Pay / visit</th><th>Result</th></tr></thead>
              <tbody>
                @foreach($b->offers as $o)
                  <tr>
                    <td class="cell-main"><b>{{ $o->source === 'open' && ! $o->professional ? 'Open shift (anyone qualified)' : ($o->professional?->name ?? '–') }}</b><div class="sub">{{ $o->matched_by ? 'Sent by staff' : 'Automatic' }}{{ $o->why_you ? ' · ' . $o->why_you : '' }}</div></td>
                    <td class="num" data-label="Sent">{{ $o->created_at->timezone('Africa/Lagos')->format('j M, H:i') }}</td>
                    <td class="num" data-label="Pay / visit">{{ BookingPricing::naira($o->pay_per_visit_kobo) }}</td>
                    <td data-label="Result"><span class="pill {{ $offerPill[$o->status] ?? 'neutral' }}">{{ ucfirst($o->status) }}</span>@if($o->decline_reason)<div class="sub">{{ $o->decline_reason }}</div>@endif</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>

      @if($canAssign)
        <section class="card" aria-labelledby="c-h">
          <div class="card-head">
            <h2 id="c-h">Qualified caregivers</h2>
            <form method="POST" action="{{ route('os.app-bookings.open', $b) }}" onsubmit="return confirm('Post as an open shift? Any qualified caregiver can accept it.')">
              @csrf <button class="btn" type="submit">Post as open shift</button>
            </form>
          </div>
          @if($candidates->isEmpty())
            <p class="empty">Nobody qualifies right now (role, checks, area, working days or a clash). Posting it as an open shift lets newly verified caregivers take it.</p>
          @else
            <div class="table-wrap">
              <table class="os-table cards">
                <thead><tr><th>Caregiver</th><th>Why</th><th>Score</th><th></th></tr></thead>
                <tbody>
                  @foreach($candidates as $c)
                    <tr>
                      <td class="cell-main"><a href="{{ route('os.caregivers.show', $c['pro']) }}"><b>{{ $c['pro']->name }}</b></a><div class="sub">{{ \App\Models\Care\Professional::TITLES[$c['pro']->title] }} · {{ $c['pro']->home_area }}</div></td>
                      <td data-label="Why">{{ $c['why'] }}</td>
                      <td class="num" data-label="Score">{{ $c['score'] }}</td>
                      <td data-label="">
                        <form method="POST" action="{{ route('os.app-bookings.offer', $b) }}">
                          @csrf <input type="hidden" name="professional_id" value="{{ $c['pro']->id }}">
                          <button class="btn primary" type="submit">Send offer</button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </section>
      @endif
    </div>

    <div class="stack">
      <section class="card card-pad" aria-labelledby="p-h">
        <h2 id="p-h" class="card-title section-head">Client</h2>
        @if($p)
          <p class="flush"><a href="{{ route('os.clients.show', $p) }}"><b>{{ $p->full_name }}</b></a></p>
          <p class="sub flush">{{ $p->age_sex }} · {{ collect([$p->area, $p->city])->filter()->join(', ') }}</p>
          @foreach($p->allergies->where('severe', true) as $a)
            <div class="alert urgent" style="margin-top:10px"><div class="alert-title">Severe allergy: {{ $a->name }}</div></div>
          @endforeach
        @endif
        <p class="sub" style="margin:12px 0 0">Booked by {{ $b->booker?->name ?: '–' }} @if($b->booker?->email)· <a href="mailto:{{ $b->booker->email }}">{{ $b->booker->email }}</a>@endif</p>
      </section>
      @if($b->professional)
        <section class="card card-pad">
          <h2 class="card-title section-head">Caregiver</h2>
          <p class="flush"><a href="{{ route('os.caregivers.show', $b->professional) }}"><b>{{ $b->professional->name }}</b></a></p>
          <p class="sub flush">Accepted {{ $b->matched_at?->timezone('Africa/Lagos')->format('j M Y, H:i') }}</p>
        </section>
      @endif
    </div>
  </div>
@endsection
