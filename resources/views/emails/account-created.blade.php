@php
    dump($user);
@endphp


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your CavalCare Account Is Ready</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f6f6; font-family:Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; padding:32px; border-radius:6px;">
                    
                    <tr>
                        <td>
                            <h1 style="margin-top:0;">Your CavalCare Account Is Ready 🎉</h1>

                            <p>Hi <strong>{{ $user?->name ?? 'there' }}</strong>,</p>

                            <p>
                                Your booking has been confirmed and we’ve created your
                                <strong>CavalCare customer account</strong>.
                            </p>

                            <h3>🔐 Set your password</h3>

                            <p>
                                To access your customer dashboard, please set your password
                                using the secure link below:
                            </p>

                            <p style="text-align:center; margin:32px 0;">
                                <a href="{{ $resetUrl }}"
                                   style="
                                       background:#2563eb;
                                       color:#ffffff;
                                       padding:12px 24px;
                                       text-decoration:none;
                                       border-radius:4px;
                                       display:inline-block;
                                       font-weight:bold;
                                   ">
                                    Set My Password
                                </a>
                            </p>

                            <p><strong>Email:</strong> {{ $user->email }}</p>

                            <p>Once your password is set, you’ll be able to:</p>
                            <ul>
                                <li>View your bookings</li>
                                <li>Track payment &amp; care status</li>
                                <li>Manage your account securely</li>
                            </ul>

                            <p>
                                If you did not expect this email, you can safely ignore it.
                            </p>

                            <p style="margin-top:32px;">
                                Thanks for trusting CavalCare,<br>
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
