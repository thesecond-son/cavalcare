@extends('adminos.layout')

@section('title', $thread->title)
@section('heading', $thread->title)

@section('content')
  <div><a class="back" href="{{ route('os.messages.index') }}">← All conversations</a></div>

  <div class="grid-dash">
    <section class="card card-pad" aria-label="Messages">
      <div class="chat">
        @forelse($messages as $m)
          @if($m->sender_kind === 'system')
            <div class="chat-system">{{ $m->text }} · {{ $m->sent_at->timezone('Africa/Lagos')->format('j M H:i') }}</div>
          @else
            <div class="chat-msg {{ in_array($m->sender_kind, ['coordinator', 'support'], true) ? 'mine' : '' }}">
              <div class="chat-who">{{ in_array($m->sender_kind, ['coordinator', 'support'], true) ? 'CavalCare (' . ($m->sender?->name ?? 'staff') . ')' : ($m->sender?->name ?? 'Unknown') }} · {{ $m->sender_kind }}</div>
              <div class="chat-text">{{ $m->text ?? '(photo)' }}</div>
              <div class="chat-time">{{ $m->sent_at->timezone('Africa/Lagos')->format('j M H:i') }}</div>
            </div>
          @endif
        @empty
          <p class="muted flush">No messages yet.</p>
        @endforelse
      </div>

      @can('messages.manage')
        <form method="POST" action="{{ route('os.messages.reply', $thread) }}" class="stack-form" style="margin-top:16px">
          @csrf
          <label class="sr-only" for="reply">Reply</label>
          <textarea id="reply" name="text" rows="3" maxlength="4000" required placeholder="Reply as CavalCare" class="textarea"></textarea>
          <button class="btn primary" type="submit">Send</button>
        </form>
      @endcan
    </section>

    <div class="stack">
      <section class="card card-pad">
        <h2 class="card-title section-head">People</h2>
        @foreach($thread->participants as $p)
          <div class="list-row"><b>{{ $p->user?->name ?? 'Unknown' }}</b> <span class="sub">{{ $p->role_in_thread }}{{ $p->read_only ? ' · read only' : '' }}</span></div>
        @endforeach
        @if($thread->patient)
          <p class="sub" style="margin-top:12px">About <a href="{{ route('os.clients.show', $thread->patient) }}">{{ $thread->patient->full_name }}</a></p>
        @endif
      </section>
    </div>
  </div>
@endsection
