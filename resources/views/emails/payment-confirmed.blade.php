<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmed</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f6f6f6; padding:24px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; padding:32px; border-radius:6px;">
                    <tr>
                        <td>

                            <h1>Payment Confirmed ✅</h1>

                            <p>
                                Hi <strong>{{ data_get($booking->booker_details, 'name', 'there') }}</strong>,
                            </p>

                            <p>
                                We’ve successfully confirmed your payment for your CavalCare booking.
                            </p>

                            <p><strong>📌 Booking Reference</strong><br>
                                {{ $booking->reference }}
                            </p>

                            <p><strong>🩺 Care Plan</strong><br>
                                {{ $booking->carePlan?->name ?? '—' }}
                            </p>

                            <p><strong>🧑‍⚕️ Care Type</strong><br>
                                {{ $booking->careType?->name ?? '—' }}
                            </p>

                            <p>
                                Our team will reach out shortly to begin the next steps.
                            </p>

                            <p style="margin-top:32px;">
                                Thank you for choosing CavalCare 💜<br>
                                <strong>The CavalCare Team</strong>
                            </p>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
