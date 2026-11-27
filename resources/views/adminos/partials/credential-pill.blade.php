@php
  $expiredByDate = $credential->valid_to && $credential->valid_to->endOfDay()->isPast() && in_array($credential->status, ['verified', 'expiring'], true);
  $state = $expiredByDate ? 'expired' : $credential->status;
  $class = match ($state) {
      'verified'    => 'done',
      'in_progress' => 'attn',
      'expiring'    => 'attn',
      'expired', 'rejected' => 'urgent',
      default       => 'neutral',
  };
@endphp
<span class="pill {{ $class }}">{{ \App\Models\Care\Credential::STATUSES[$state] ?? ucfirst($state) }}</span>
