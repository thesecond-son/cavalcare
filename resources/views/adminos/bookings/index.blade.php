@extends('adminos.layout')

@section('title', 'Bookings')
@section('heading', 'Bookings')

@php
  $tabs = [
    null              => ['All', $counts->sum()],
    'paid'            => ['Awaiting intake', $counts['paid'] ?? 0],
    'converted'       => ['Intake received', $counts['converted'] ?? 0],
    'pending_payment' => ['Checkout not finished', ($counts['pending_payment'] ?? 0) + ($counts['pending'] ?? 0)],
    'failed'          => ['Failed', $counts['failed'] ?? 0],
  ];
@endphp

@section('content')
  <section class="card">
    <div class="card-head" style="flex-wrap:wrap">
      <nav class="tabs" aria-label="Filter by status">
        @foreach($tabs as $key => [$label, $n])
          <a href="{{ route('os.bookings.index', array_filter(['status' => $key, 'q' => $search])) }}"
             class="{{ ($status ?: null) === ($key ?: null) ? 'on' : '' }}">{{ $label }} {{ $n }}</a>
        @endforeach
      </nav>
      @if($search !== '')
        <span class="muted">Results for “{{ $search }}” · <a href="{{ route('os.bookings.index', array_filter(['status' => $status])) }}">Clear</a></span>
      @endif
    </div>

    @if($bookings->isEmpty())
      <p class="empty">No bookings match.</p>
    @else
      <div class="table-wrap">
        <table class="os-table">
          <thead>
            <tr><th>Booked by</th><th>Care</th><th>Amount</th><th>Booked</th><th>Status</th></tr>
          </thead>
          <tbody>
            @foreach($bookings as $pb)
              @php $name = $pb->booker_details['name'] ?? 'Unknown'; @endphp
              <tr class="row-link" data-href="{{ route('os.bookings.show', $pb->reference) }}">
                <td>
                  <div class="who">
                    <div class="avatar">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</div>
                    <div>
                      <a href="{{ route('os.bookings.show', $pb->reference) }}"><b>{{ $name }}</b></a>
                      <small>{{ $pb->booker_details['email'] ?? '' }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  {{ $pb->carePlan?->display_name ?? 'Plan removed' }}
                  <div class="muted" style="font-size:13px">{{ $pb->careType?->name }} · {{ $pb->reference }}</div>
                </td>
                <td class="num">{{ $pb->amount_paid ? '₦' . number_format($pb->amount_paid / 100) : '–' }}</td>
                <td class="num muted">{{ $pb->created_at->timezone('Africa/Lagos')->format('j M Y, H:i') }}</td>
                <td>@include('adminos.partials.status-pill', ['status' => $pb->status])</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="pager">
        <span class="muted">{{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }}</span>
        @if($bookings->previousPageUrl()) <a class="btn" href="{{ $bookings->previousPageUrl() }}">Previous</a> @endif
        @if($bookings->nextPageUrl()) <a class="btn" href="{{ $bookings->nextPageUrl() }}">Next</a> @endif
      </div>
    @endif
  </section>
@endsection
