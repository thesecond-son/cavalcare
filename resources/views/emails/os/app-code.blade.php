@include('emails.os.partials.shell-top', ['title' => 'Your code for the Cavalcare app'])
  <h1 style="font-family:Georgia,serif;font-weight:500;font-size:26px;margin:0 0 12px;color:#1A3A2F">Hi {{ $firstName }},</h1>
  <p style="margin:0 0 12px">Here is your code to open the Cavalcare app with your booking already set up. Any earlier code we sent no longer works.</p>
  @include('emails.os.partials.app-code-block')
  <p style="margin:0;color:#4E6359;font-size:14px">Didn't book with CavalCare? You can ignore this email.</p>
@include('emails.os.partials.shell-bottom')
