@extends('adminos.layout')

@section('title', 'Clients')
@section('heading', 'Clients')

@php
  $tabs = [
    null      => ['All', $counts['all']],
    'app'     => ['Using the app', $counts['app']],
    'website' => ['Website only', $counts['website']],
  ];
@endphp

@section('content')
  <section class="card">
    <div class="card-head">
      <nav class="tabs" aria-label="Filter clients">
        @foreach($tabs as $key => [$label, $n])
          <a href="{{ route('os.clients.index', array_filter(['filter' => $key, 'q' => $search])) }}"
             class="{{ ($filter ?: null) === ($key ?: null) ? 'on' : '' }}">{{ $label }} {{ $n }}</a>
        @endforeach
      </nav>
      <form class="inline-search" method="GET" action="{{ route('os.clients.index') }}" role="search">
        @if($filter)<input type="hidden" name="filter" value="{{ $filter }}">@endif
        <input type="search" name="q" value="{{ $search }}" placeholder="Name, area or family member" aria-label="Search clients" enterkeyhint="search">
      </form>
    </div>

    @if($clients->isEmpty())
      <div class="empty">
        @if($search !== '' || $filter)
          No clients match. <a href="{{ route('os.clients.index') }}">Clear filters</a>
        @else
          No clients yet. They appear here when a family adds a parent in the app, or submits the care details form after booking on the website.
        @endif
      </div>
    @else
      <div class="table-wrap">
        <table class="os-table cards">
          <thead><tr><th>Client</th><th>Family</th><th>Area</th><th>Latest care</th><th>Health</th></tr></thead>
          <tbody>
            @foreach($clients as $c)
              @php
                $owner = $c->members->firstWhere('role', 'owner') ?? $c->members->first();
                $latest = $c->preBookings->first();
                $booker = $latest?->booker_details ?? [];
              @endphp
              <tr class="row-link" data-href="{{ route('os.clients.show', $c) }}">
                <td class="cell-main">
                  <div class="who">
                    <div class="avatar">{{ $c->initials }}</div>
                    <div>
                      <a href="{{ route('os.clients.show', $c) }}"><b>{{ $c->display_name ?: $c->full_name }}</b></a>
                      <small>{{ $c->age_sex ?? 'Age not given' }}</small>
                    </div>
                  </div>
                </td>
                <td data-label="Family">
                  <div>
                    {{ $owner?->user?->name ?: ($booker['name'] ?? 'Not linked') }}
                    <div class="sub">{{ $owner ? 'App · ' . ucfirst($owner->role) : 'Website booking' }}</div>
                  </div>
                </td>
                <td data-label="Area">{{ $c->area ?: ($c->state ?: '–') }}</td>
                <td data-label="Latest care">
                  <div>
                    {{ $latest?->carePlan?->display_name ?? '–' }}
                    @if($latest)<div class="sub">{{ $latest->careType?->name }}</div>@endif
                  </div>
                </td>
                <td data-label="Health">
                  @if($c->severe_allergies_count)
                    <span class="pill urgent">{{ $c->severe_allergies_count }} severe {{ \Illuminate\Support\Str::plural('allergy', $c->severe_allergies_count) }}</span>
                  @elseif($c->allergies_count)
                    <span class="pill attn">{{ $c->allergies_count }} {{ \Illuminate\Support\Str::plural('allergy', $c->allergies_count) }}</span>
                  @else
                    <span class="pill neutral">No allergies recorded</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @include('adminos.partials.pager', ['page' => $clients])
    @endif
  </section>
@endsection
