@php
  $value = $status instanceof \BackedEnum ? $status->value : (string) $status;
  [$class, $label] = match ($value) {
      'paid'            => ['attn', 'Paid · awaiting intake'],
      'converted'       => ['stable', 'Intake received'],
      'pending_payment', 'pending' => ['neutral', 'Checkout not finished'],
      'failed'          => ['urgent', 'Payment failed'],
      default           => ['neutral', ucfirst(str_replace('_', ' ', $value))],
  };
@endphp
<span class="pill {{ $class }}">{{ $label }}</span>
