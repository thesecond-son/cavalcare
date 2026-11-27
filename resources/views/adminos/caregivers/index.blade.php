@extends('adminos.layout')

@section('title', 'Caregivers')
@section('heading', 'Caregivers')

@php
  $tabs = [null => ['All', $counts->sum()]];
  foreach (\App\Models\Care\Professional::STATUSES as $key => $label) {
      $tabs[$key] = [$label, $counts[$key] ?? 0];
  }
  $statusPill = ['applicant' => 'neutral', 'verifying' => 'attn', 'active' => 'done', 'suspended' => 'urgent'];
@endphp

@section('content')
  @if($awaitingReview)
    <div class="alert attn" role="status">
      <div class="alert-title">{{ $awaitingReview }} {{ \Illuminate\Support\Str::plural('credential', $awaitingReview) }} waiting for review</div>
      <p>Open a caregiver marked "Verifying" to approve or reject their uploads.</p>
    </div>
  @endif

  <section class="card">
    <div class="card-head">
      <nav class="tabs" aria-label="Filter by status">
        @foreach($tabs as $key => [$label, $n])
          <a href="{{ route('os.caregivers.index', array_filter(['status' => $key, 'title' => $title, 'q' => $search])) }}"
             class="{{ ($status ?: null) === ($key ?: null) ? 'on' : '' }}">{{ $label }} {{ $n }}</a>
        @endforeach
      </nav>
      <form class="inline-search" method="GET" action="{{ route('os.caregivers.index') }}" role="search">
        @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <select name="title" aria-label="Nurse or caregiver" onchange="this.form.submit()">
          <option value="">Nurses and carers</option>
          @foreach(\App\Models\Care\Professional::TITLES as $key => $label)
            <option value="{{ $key }}" @selected($title === $key)>{{ $key === 'rn' ? 'Nurses (RN)' : 'Carers' }}</option>
          @endforeach
        </select>
        <input type="search" name="q" value="{{ $search }}" placeholder="Name, phone or area" aria-label="Search caregivers" enterkeyhint="search">
      </form>
    </div>

    @if($caregivers->isEmpty())
      <div class="empty">
        @if($search !== '' || $status || $title)
          No caregivers match. <a href="{{ route('os.caregivers.index') }}">Clear filters</a>
        @else
          No caregivers yet. Nurses and carers appear here when they sign up in the CavalPro app.
        @endif
      </div>
    @else
      <div class="table-wrap">
        <table class="os-table cards">
          <thead><tr><th>Caregiver</th><th>Area</th><th>Checks</th><th>Rating</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($caregivers as $pro)
              @php [$done, $total] = $pro->verificationProgress(); @endphp
              <tr class="row-link" data-href="{{ route('os.caregivers.show', $pro) }}">
                <td class="cell-main">
                  <div class="who">
                    <div class="avatar alt">{{ $pro->initials }}</div>
                    <div>
                      <a href="{{ route('os.caregivers.show', $pro) }}"><b>{{ $pro->name }}</b></a>
                      <small>{{ \App\Models\Care\Professional::TITLES[$pro->title] ?? $pro->title }}{{ $pro->employment === 'agency' ? ' · agency' : '' }}</small>
                    </div>
                  </div>
                </td>
                <td data-label="Area">{{ $pro->home_area ?: '–' }}</td>
                <td data-label="Checks">
                  <div class="progress-cell">
                    <span class="progress" role="img" aria-label="{{ $done }} of {{ $total }} required checks verified"><span style="width: {{ $total ? round($done / $total * 100) : 0 }}%"></span></span>
                    <span class="num">{{ $done }}/{{ $total }}</span>
                  </div>
                </td>
                <td class="num" data-label="Rating">{{ $pro->rating_avg ? number_format($pro->rating_avg, 1) . ' ★ (' . $pro->reviews_count . ')' : 'No reviews' }}</td>
                <td data-label="Status"><span class="pill {{ $statusPill[$pro->status] ?? 'neutral' }}">{{ \App\Models\Care\Professional::STATUSES[$pro->status] ?? $pro->status }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @include('adminos.partials.pager', ['page' => $caregivers])
    @endif
  </section>
@endsection
