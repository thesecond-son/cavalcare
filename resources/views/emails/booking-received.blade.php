<h2>Thank you for your booking</h2>

<p>
  We’ve received your request for care.
  Below are your booking details:
</p>

<ul>
  <li><strong>Reference:</strong> {{ $preBooking->reference }}</li>
  <li><strong>Care Plan:</strong> {{ $preBooking->carePlan->name }}</li>
  <li><strong>Care Type:</strong> {{ $preBooking->careType->name }}</li>
</ul>

<p>
  Please proceed with payment using the instructions below.
  Once payment is confirmed, we will activate your booking
  and create your customer account.
</p>

<p>
  <strong>Payment Instructions</strong><br>
  Bank Transfer / Paystack / Manual confirmation
</p>

<p>
  If you have questions, reply to this email.
</p>
