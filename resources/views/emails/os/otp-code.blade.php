@include('emails.os.partials.shell-top', ['title' => 'Your Cavalcare code'])
  <p style="margin:0 0 12px">Here is your code to sign in to the Cavalcare app:</p>
  <p style="margin:0 0 20px;font-family:Georgia,serif;font-size:36px;letter-spacing:8px;color:#1A3A2F"><b>{{ $code }}</b></p>
  <p style="margin:0 0 8px">It expires in {{ $minutes }} minutes and works once.</p>
  <p style="margin:0;color:#4E6359;font-size:14px">CavalCare will never ask you for this code by phone or message. If you didn't try to sign in, you can ignore this email.</p>
@include('emails.os.partials.shell-bottom')
