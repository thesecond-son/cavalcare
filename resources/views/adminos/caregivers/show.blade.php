@extends('adminos.layout')

@php
  [$done, $total] = $pro->verificationProgress();
  $required = $pro->requiredCredentialTypes();
  $byType = $pro->credentials->keyBy('type');
  // Required checks first, then any optional ones (BLS, references) on file.
  $rows = collect($required)->merge($pro->credentials->pluck('type')->diff($required))->unique()->values();
  $statusPill = ['applicant' => 'neutral', 'verifying' => 'attn', 'active' => 'done', 'suspended' => 'urgent'];
  $canVerify = auth()->user()->can('caregivers.verify');
@endphp

@section('title', $pro->name)
@section('heading', $pro->name)

@section('content')
  <div><a class="back" href="{{ route('os.caregivers.index') }}">← All caregivers</a></div>

  <div class="grid-dash">
    <div class="stack">
      <section class="card card-pad" aria-labelledby="cr-h">
        <div class="section-head">
          <h2 id="cr-h" class="card-title">Checks and credentials</h2>
          <span class="pill {{ $done === $total ? 'done' : 'attn' }}">{{ $done }} of {{ $total }} required verified</span>
        </div>
        <p class="muted flush">
          {{ $pro->title === 'rn' ? 'Nurses need an NMCN licence' : 'Carers need a National ID (NIN)' }}, a background check and CavalCare training before they can take shifts.
        </p>

        <ul class="cred-list">
          @foreach($rows as $type)
            @php $cred = $byType->get($type); @endphp
            <li class="cred">
              <div class="cred-main">
                <div>
                  <b>{{ \App\Models\Care\Credential::TYPES[$type] ?? $type }}</b>
                  @unless(in_array($type, $required, true))<span class="sub"> · optional</span>@endunless
                  <div class="sub">
                    @if(! $cred)
                      Not started in CavalPro yet
                    @else
                      {{ $cred->masked_number ? 'No. ' . $cred->masked_number . ' · ' : '' }}
                      {{ $cred->valid_to ? 'Valid to ' . $cred->valid_to->format('j M Y') : 'No expiry date' }}
                      @if($cred->verified_at) · {{ $cred->status === 'rejected' ? 'Rejected' : 'Checked' }} by {{ $cred->verifier?->name ?? 'staff' }}, {{ $cred->verified_at->timezone('Africa/Lagos')->format('j M Y') }}@endif
                      @if($cred->file_path) · <a href="{{ route('os.caregivers.credentials.document', [$pro, $cred]) }}" target="_blank" rel="noopener">view document</a> @endif
                    @endif
                  </div>
                  @if($cred?->status === 'rejected' && $cred->rejection_reason)
                    <div class="sub reject-reason">Reason: {{ $cred->rejection_reason }}</div>
                  @endif
                </div>
                @if($cred) @include('adminos.partials.credential-pill', ['credential' => $cred]) @else <span class="pill neutral">To do</span> @endif
              </div>

              @if($cred && $canVerify && in_array($cred->status, ['in_progress', 'rejected', 'expiring', 'expired', 'verified'], true))
                <details class="cred-review" @if($cred->status === 'in_progress') open @endif>
                  <summary>{{ $cred->status === 'in_progress' ? 'Review' : 'Change decision' }}</summary>
                  <form method="POST" action="{{ route('os.caregivers.credentials.review', [$pro, $cred]) }}" class="review-form">
                    @csrf
                    <div class="field-row">
                      <label><span>Valid to <span class="sub">(optional)</span></span>
                        <input type="date" name="valid_to" value="{{ old('valid_to', $cred->valid_to?->format('Y-m-d')) }}" min="{{ now()->addDay()->format('Y-m-d') }}">
                      </label>
                      <button class="btn primary" name="decision" value="approve" type="submit">Verify</button>
                    </div>
                    <div class="field-row">
                      <label><span>Reason, if rejecting</span>
                        <input type="text" name="rejection_reason" maxlength="255" placeholder="e.g. Photo is blurred, please re-upload">
                      </label>
                      <button class="btn" name="decision" value="reject" type="submit">Reject</button>
                    </div>
                  </form>
                </details>
              @endif
            </li>
          @endforeach
        </ul>
        @if($errors->any())
          <div class="err" role="alert">{{ $errors->first() }}</div>
        @endif
      </section>

      <section class="card card-pad" aria-labelledby="av-h">
        <h2 id="av-h" class="card-title section-head">Weekly availability</h2>
        @if($pro->availabilityRules->isEmpty())
          <p class="muted flush">Not set yet.</p>
        @else
          <div class="week">
            @foreach(\App\Models\Care\AvailabilityRule::DAYS as $n => $day)
              @php $slots = $pro->availabilityRules->where('weekday', $n); @endphp
              <div class="week-day {{ $slots->isEmpty() ? 'off' : '' }}">
                <b>{{ $day }}</b>
                @forelse($slots as $s)
                  <span>{{ substr($s->start_time, 0, 5) }}–{{ substr($s->end_time, 0, 5) }}</span>
                @empty
                  <span>Off</span>
                @endforelse
              </div>
            @endforeach
          </div>
          <p class="sub flush" style="margin-top:12px">{{ $pro->open_to_extra_shifts ? 'Open to extra shifts.' : 'Not taking extra shifts.' }}</p>
        @endif
      </section>
    </div>

    <div class="stack">
      <section class="card card-pad" aria-labelledby="pf-h">
        <div class="section-head">
          <h2 id="pf-h" class="card-title">Profile</h2>
          <span class="pill {{ $statusPill[$pro->status] ?? 'neutral' }}">{{ \App\Models\Care\Professional::STATUSES[$pro->status] ?? $pro->status }}</span>
        </div>
        <dl class="dl narrow">
          <dt>Role</dt><dd>{{ \App\Models\Care\Professional::TITLES[$pro->title] ?? $pro->title }}</dd>
          <dt>Phone</dt><dd>@if($pro->user?->phone_e164)<a href="tel:{{ $pro->user->phone_e164 }}">{{ $pro->user->phone_e164 }}</a>@else – @endif</dd>
          <dt>Email</dt><dd>@if($pro->user?->email)<a href="mailto:{{ $pro->user->email }}">{{ $pro->user->email }}</a>@else – @endif</dd>
          <dt>Area</dt><dd>{{ $pro->home_area ?: '–' }}<div class="sub">Travels up to {{ $pro->max_travel_km }} km</div></dd>
          <dt>Experience</dt><dd>{{ $pro->years_experience !== null ? $pro->years_experience . ' ' . \Illuminate\Support\Str::plural('year', $pro->years_experience) : '–' }}</dd>
          <dt>Languages</dt><dd>{{ collect($pro->languages)->join(', ') ?: '–' }}</dd>
          <dt>Skills</dt><dd>{{ collect($pro->skills)->join(', ') ?: '–' }}</dd>
          <dt>Employed</dt><dd>{{ $pro->employment === 'agency' ? 'Through a partner agency' : 'By CavalCare' }}</dd>
          <dt>Visits</dt><dd>{{ $pro->visits_count }} · {{ $pro->rating_avg ? number_format($pro->rating_avg, 1) . ' ★ from ' . $pro->reviews_count . ' reviews' : 'no reviews yet' }}</dd>
          <dt>Joined</dt><dd>{{ $pro->created_at->timezone('Africa/Lagos')->format('j M Y') }}</dd>
        </dl>
        @if($pro->bio)<p class="flush" style="margin-top:14px">{{ $pro->bio }}</p>@endif
      </section>

      @if($canVerify)
        <section class="card card-pad" aria-labelledby="st-h">
          <h2 id="st-h" class="card-title section-head">{{ $pro->status === 'suspended' ? 'Suspended' : 'Suspend' }}</h2>
          @if($pro->status === 'suspended')
            <p class="flush">Reason: {{ $pro->suspended_reason }}</p>
            <form method="POST" action="{{ route('os.caregivers.status', $pro) }}" class="stack-form"
                  onsubmit="return confirm('Reinstate {{ addslashes($pro->name) }}? They can take shifts again once all checks are verified.')">
              @csrf
              <input type="hidden" name="action" value="reinstate">
              <button class="btn" type="submit">Reinstate</button>
            </form>
          @else
            <p class="muted flush">Stops {{ explode(' ', $pro->name)[0] }} taking new shifts straight away. The reason is kept in the audit log.</p>
            <form method="POST" action="{{ route('os.caregivers.status', $pro) }}" class="stack-form"
                  onsubmit="return confirm('Suspend {{ addslashes($pro->name) }}?')">
              @csrf
              <input type="hidden" name="action" value="suspend">
              <label class="sr-only" for="reason">Reason</label>
              <input id="reason" type="text" name="reason" required maxlength="255" placeholder="Reason (required)">
              <button class="btn danger" type="submit">Suspend</button>
            </form>
          @endif
        </section>
      @endif
    </div>
  </div>
@endsection
