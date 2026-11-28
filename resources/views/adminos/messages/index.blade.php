@extends('adminos.layout')

@section('title', 'Family messages')
@section('heading', 'Family messages')

@php $labels = ['care_team' => 'Care team', 'coordinator' => 'To CavalCare', 'support' => 'Support']; @endphp

@section('content')
  <section class="card">
    <div class="card-head">
      <nav class="tabs" aria-label="Filter threads">
        <a href="{{ route('os.messages.index') }}" class="{{ ! $kind ? 'on' : '' }}">All</a>
        @foreach($labels as $k => $label)
          <a href="{{ route('os.messages.index', ['kind' => $k]) }}" class="{{ $kind === $k ? 'on' : '' }}">{{ $label }}</a>
        @endforeach
      </nav>
    </div>
    @if($threads->isEmpty())
      <p class="empty">No conversations yet. They start when a caregiver is matched, or when a family or caregiver writes to CavalCare in the app.</p>
    @else
      <div class="table-wrap">
        <table class="os-table cards">
          <thead><tr><th>Conversation</th><th>Last message</th><th>When</th><th></th></tr></thead>
          <tbody>
            @foreach($threads as $t)
              @php $m = $last[$t->id] ?? null; $waiting = $m && in_array($m->sender_kind, ['family', 'professional'], true); @endphp
              <tr class="row-link" data-href="{{ route('os.messages.show', $t) }}">
                <td class="cell-main"><a href="{{ route('os.messages.show', $t) }}"><b>{{ $t->title }}</b></a><div class="sub">{{ $labels[$t->kind] ?? $t->kind }} · {{ $t->participants->count() }} people</div></td>
                <td data-label="Last message">{{ \Illuminate\Support\Str::limit($m?->text ?? '–', 70) }}</td>
                <td class="num" data-label="When">{{ $t->last_message_at?->timezone('Africa/Lagos')->format('j M H:i') ?? '–' }}</td>
                <td data-label="">@if($waiting)<span class="pill attn">Awaiting reply</span>@endif</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @include('adminos.partials.pager', ['page' => $threads])
    @endif
  </section>
@endsection
