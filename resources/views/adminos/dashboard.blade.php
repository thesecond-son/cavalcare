@extends('adminos.layout')

@php
  $hour = now()->timezone('Africa/Lagos')->hour;
  $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
  $firstName = explode(' ', auth()->user()->name)[0];
@endphp

@section('title', 'Dashboard')
@section('heading', "{$greeting}, {$firstName}")

@section('content')
  <div class="kpis">
    <div class="card kpi">
      <div class="kpi-label">Paid bookings ({{ now()->format('M') }})</div>
      <div class="kpi-value">{{ number_format($stats['paid_month']) }}</div>
      <div class="kpi-sub">From the website checkout</div>
    </div>
    <div class="card kpi {{ $stats['awaiting_intake'] ? 'attn' : '' }}">
      <div class="kpi-label">Awaiting intake</div>
      <div class="kpi-value">{{ number_format($stats['awaiting_intake']) }}</div>
      <div class="kpi-sub">Paid, care details not sent yet</div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">Revenue ({{ now()->format('M') }})</div>
      <div class="kpi-value">₦{{ number_format($stats['revenue_month']) }}</div>
      <div class="kpi-sub">Confirmed by Paystack</div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">Unfinished checkouts</div>
      <div class="kpi-value">{{ number_format($stats['abandoned_week']) }}</div>
      <div class="kpi-sub">Last 7 days · worth a follow-up</div>
    </div>
  </div>

  <div class="grid-dash">
    <section class="card" aria-labelledby="recent-h">
      <div class="card-head">
        <h2 id="recent-h">Latest website bookings</h2>
        <a href="{{ route('os.bookings.index') }}">All bookings →</a>
      </div>
      @if($recent->isEmpty())
        <p class="empty">No bookings yet.</p>
      @else
        <div class="table-wrap">
          <table class="os-table cards">
            <thead><tr><th>Booked by</th><th>Plan</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
              @foreach($recent as $pb)
                @php $name = $pb->booker_details['name'] ?? 'Unknown'; @endphp
                <tr class="row-link" data-href="{{ route('os.bookings.show', $pb->reference) }}">
                  <td class="cell-main">
                    <div class="who">
                      <div class="avatar">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</div>
                      <div>
                        <a href="{{ route('os.bookings.show', $pb->reference) }}"><b>{{ $name }}</b></a>
                        <small>{{ $pb->reference }} · {{ $pb->created_at->timezone('Africa/Lagos')->format('j M, H:i') }}</small>
                      </div>
                    </div>
                  </td>
                  <td data-label="Plan">
                    <div>
                      {{ $pb->carePlan?->display_name ?? 'Plan removed' }}
                      <div class="sub">{{ $pb->careType?->name }}</div>
                    </div>
                  </td>
                  <td class="num" data-label="Amount">{{ $pb->amount_paid ? '₦' . number_format($pb->amount_paid / 100) : '–' }}</td>
                  <td data-label="Status">@include('adminos.partials.status-pill', ['status' => $pb->status])</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </section>

    <div class="stack">
      <section class="card card-pad" aria-labelledby="att-h">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <h2 id="att-h" style="font-family:var(--sans);font-weight:800;font-size:19px">Needs attention</h2>
          @if($stalledIntake->isNotEmpty())
            <span class="pill urgent">{{ $stalledIntake->count() }} open</span>
          @endif
        </div>
        @forelse($stalledIntake as $pb)
          <div class="alert attn">
            <div class="alert-title">Intake overdue · {{ $pb->booker_details['name'] ?? $pb->reference }}</div>
            <p>Paid {{ $pb->updated_at->diffForHumans() }} for {{ $pb->carePlan?->display_name ?? 'a plan' }}, care details not sent. Call or resend the intake link.</p>
            <p><a href="{{ route('os.bookings.show', $pb->reference) }}">Open booking →</a></p>
          </div>
        @empty
          <p class="muted" style="margin:0">Nothing waiting. Every paid family has sent their care details.</p>
        @endforelse
      </section>

      <section class="card card-pad" aria-labelledby="cat-h">
        <h2 id="cat-h" style="font-family:var(--sans);font-weight:800;font-size:19px;margin-bottom:10px">Care catalogue</h2>
        <p class="muted" style="margin:0 0 12px">{{ $stats['care_types'] }} care types · {{ $stats['care_plans'] }} Caretaker and Licensed Nurse plans live on the website.</p>
        <div class="note">Live visits, caregivers on shift and escalations appear here once the Cavalcare and CavalPro apps are connected.</div>
      </section>
    </div>
  </div>
@endsection
