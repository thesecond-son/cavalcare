@extends('adminos.layout')

@section('title', $p->full_name)
@section('heading', $p->display_name ?: $p->full_name)

@section('content')
  <div><a class="back" href="{{ route('os.clients.index') }}">← All clients</a></div>

  <div class="grid-dash">
    <div class="stack">
      {{-- Allergies always come first (spec: "always shown first on the patient brief"). --}}
      <section class="card card-pad" aria-labelledby="al-h">
        <div class="section-head">
          <h2 id="al-h" class="card-title">Allergies</h2>
          @if($p->allergies->where('severe', true)->isNotEmpty())
            <span class="pill urgent">Severe allergy</span>
          @endif
        </div>
        @forelse($p->allergies as $a)
          <div class="alert {{ $a->severe ? 'urgent' : 'attn' }}">
            <div class="alert-title">{{ $a->name }}{{ $a->severe ? ' · severe' : '' }}</div>
            @if($a->reaction)<p>Reaction: {{ $a->reaction }}</p>@endif
          </div>
        @empty
          <p class="muted flush">No allergies recorded. Confirm with the family before the first visit.</p>
        @endforelse
      </section>

      <section class="card card-pad" aria-labelledby="hx-h">
        <h2 id="hx-h" class="card-title section-head">Health summary</h2>
        <dl class="dl">
          <dt>Conditions</dt>
          <dd>{{ $p->conditions->pluck('name')->join(', ') ?: 'None recorded' }}</dd>
          <dt>Mobility</dt>
          <dd>{{ $p->mobility_level ? ucfirst($p->mobility_level) : 'Not recorded' }}</dd>
          <dt>Medicines</dt>
          <dd>
            @forelse($p->medications as $m)
              <div class="{{ $m->active ? '' : 'muted' }}">
                {{ $m->name }}@if($m->dose) · {{ $m->dose }}@endif
                @if(! empty($m->schedule['times'])) <span class="sub">({{ implode(', ', $m->schedule['times']) }})</span>@endif
                @unless($m->active) <span class="sub">stopped</span>@endunless
              </div>
            @empty
              None recorded
            @endforelse
          </dd>
          <dt>Languages</dt>
          <dd>{{ collect($p->languages)->join(', ') ?: 'Not recorded' }}</dd>
        </dl>
      </section>

      <section class="card" aria-labelledby="bk-h">
        <div class="card-head"><h2 id="bk-h">Bookings</h2></div>
        @if($p->preBookings->isEmpty())
          <p class="empty">No bookings yet.</p>
        @else
          <div class="table-wrap">
            <table class="os-table cards">
              <thead><tr><th>Booking</th><th>Plan</th><th>Amount</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($p->preBookings as $pb)
                  <tr class="row-link" data-href="{{ route('os.bookings.show', $pb->reference) }}">
                    <td class="cell-main">
                      <a href="{{ route('os.bookings.show', $pb->reference) }}"><b>{{ $pb->reference }}</b></a>
                      <div class="sub">{{ $pb->created_at->timezone('Africa/Lagos')->format('j M Y') }} · website</div>
                    </td>
                    <td data-label="Plan"><div>{{ $pb->carePlan?->display_name ?? '–' }}<div class="sub">{{ $pb->careType?->name }}</div></div></td>
                    <td class="num" data-label="Amount">{{ $pb->amount_paid ? '₦' . number_format($pb->amount_paid / 100) : '–' }}</td>
                    <td data-label="Status">@include('adminos.partials.status-pill', ['status' => $pb->status])</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>
    </div>

    <div class="stack">
      <section class="card card-pad" aria-labelledby="pr-h">
        <h2 id="pr-h" class="card-title section-head">Profile</h2>
        <dl class="dl narrow">
          <dt>Name</dt><dd>{{ $p->full_name }}</dd>
          @if($p->address_as)<dt>Called</dt><dd>{{ $p->address_as }}</dd>@endif
          <dt>Age</dt><dd>{{ $p->age ?? '–' }}</dd>
          <dt>Sex</dt><dd>{{ $p->sex ? ucfirst($p->sex) : '–' }}</dd>
          <dt>Address</dt>
          <dd>
            {{ $p->address_line ?: '–' }}
            @if($p->address_note)<div class="sub">{{ $p->address_note }}</div>@endif
            @if($p->area || $p->state)<div class="sub">{{ collect([$p->area, $p->city, $p->state])->filter()->unique()->join(', ') }}</div>@endif
          </dd>
          @if($p->phone_e164)<dt>Phone</dt><dd><a href="tel:{{ $p->phone_e164 }}">{{ $p->phone_e164 }}</a></dd>@endif
          <dt>Consent</dt><dd>{{ $p->consent_given_at ? 'Given ' . $p->consent_given_at->timezone('Africa/Lagos')->format('j M Y') : 'Not recorded yet' }}</dd>
          <dt>Added</dt><dd>{{ $p->created_at->timezone('Africa/Lagos')->format('j M Y') }} · {{ $p->source === 'website' ? 'website form' : ($p->source === 'os' ? 'Caval OS' : 'app') }}</dd>
        </dl>
      </section>

      <section class="card card-pad" aria-labelledby="fm-h">
        <h2 id="fm-h" class="card-title section-head">Family</h2>
        @forelse($p->members as $m)
          <div class="who list-row">
            <div class="avatar alt">{{ mb_strtoupper(mb_substr($m->user?->name ?: '?', 0, 1)) }}</div>
            <div>
              <b>{{ $m->user?->name ?: 'Name not set' }}</b>
              <small>{{ ucfirst($m->role) }}{{ $m->relationship ? ' · ' . $m->relationship : '' }}{{ $m->city ? ' · ' . $m->city : '' }}</small>
              <small>
                @if($m->user?->email)<a href="mailto:{{ $m->user->email }}">{{ $m->user->email }}</a>@endif
                @if($m->user?->phone_e164) · <a href="tel:{{ $m->user->phone_e164 }}">{{ $m->user->phone_e164 }}</a>@endif
              </small>
            </div>
          </div>
        @empty
          @php $booker = $p->preBookings->first()?->booker_details ?? []; @endphp
          @if($booker)
            <div class="who list-row">
              <div class="avatar alt">{{ mb_strtoupper(mb_substr($booker['name'] ?? '?', 0, 1)) }}</div>
              <div>
                <b>{{ $booker['name'] ?? 'Unknown' }}</b>
                <small>Booked on the website · not in the app yet</small>
                <small>
                  @if(! empty($booker['email']))<a href="mailto:{{ $booker['email'] }}">{{ $booker['email'] }}</a>@endif
                  @if(! empty($booker['phone'])) · <a href="tel:{{ preg_replace('/[^\d+]/', '', $booker['phone']) }}">{{ $booker['phone'] }}</a>@endif
                </small>
              </div>
            </div>
          @else
            <p class="muted flush">No family linked.</p>
          @endif
        @endforelse
      </section>

      <section class="card card-pad" aria-labelledby="ec-h">
        <h2 id="ec-h" class="card-title section-head">Emergency contacts</h2>
        @forelse($p->emergencyContacts as $ec)
          <div class="list-row">
            <b>{{ $ec->name ?: 'Emergency contact' }}</b>{{ $ec->relation ? ' · ' . $ec->relation : '' }}
            <div>
              @if($ec->phone_e164)<a href="tel:{{ $ec->phone_e164 }}">{{ $ec->phone_e164 }}</a>@else <span class="muted">{{ $ec->phone_raw ?: 'No number' }}</span>@endif
              @if($ec->location)<span class="sub"> · {{ $ec->location }}</span>@endif
            </div>
          </div>
        @empty
          <p class="muted flush">None recorded.</p>
        @endforelse
      </section>
    </div>
  </div>
@endsection
