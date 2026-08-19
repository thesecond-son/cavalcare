<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>New Booking — CavalCare</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            font-family: 'Cabin', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .wrapper {
            max-width: 580px;
            margin: 32px auto;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
        }

        .header {
            background: #1A3A2F;
            padding: 36px 40px;
            text-align: center;
        }

        .header img {
            height: 48px;
            width: auto;
        }

        .body {
            padding: 40px;
            color: #1A3A2F;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 400;
            line-height: 1.3;
            margin: 0 0 16px;
            color: #1A3A2F;
        }

        p {
            font-size: .95rem;
            line-height: 1.72;
            color: #3d6b57;
            margin: 0 0 18px;
        }

        .success-box {
            background: #F0FFDB;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            text-align: center;
        }

        .success-icon {
            width: 52px;
            height: 52px;
            line-height: 52px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: #04A9A7;
            color: #fff;
            font-size: 24px;
            font-weight: 700;
        }

        .success-title {
            color: #1A3A2F;
            font-size: 1rem;
            font-weight: 700;
            margin: 0 0 5px;
        }

        .success-text {
            color: #3d6b57;
            font-size: .82rem;
            line-height: 1.55;
            margin: 0;
        }

        .details-box {
            background: #f8fbf6;
            border: 1px solid #e5eee2;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }

        .details-title {
            color: #1A3A2F;
            font-size: .85rem;
            font-weight: 700;
            margin: 0 0 14px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .detail-row {
            padding: 9px 0;
            border-bottom: 1px solid #e8f0e6;
            font-size: .88rem;
            line-height: 1.5;
        }

        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-label {
            color: #6a8978;
        }

        .detail-value {
            color: #1A3A2F;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            background: #e5f8ee;
            color: #167544;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
        }

        .button-wrapper {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background: #1A3A2F;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 24px;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 700;
        }

        .notice {
            background: #fff8f0;
            border-left: 3px solid #FFCDAD;
            border-radius: 0 8px 8px 0;
            padding: 12px 16px;
            margin: 24px 0;
            font-size: .84rem;
            line-height: 1.6;
            color: #7a4016;
        }

        .divider {
            height: 1px;
            background: #e8f4e8;
            margin: 28px 0;
        }

        .footer {
            background: #F0FFDB;
            padding: 24px 40px;
            font-size: .75rem;
            color: #3d6b57;
            text-align: center;
            line-height: 1.65;
        }

        .footer a {
            color: #1A3A2F;
            font-weight: 600;
        }

        @media only screen and (max-width: 620px) {

            .wrapper {
                margin: 0;
                border-radius: 0;
            }

            .header {
                padding: 30px 24px;
            }

            .body {
                padding: 32px 24px;
            }

            .footer {
                padding: 22px 24px;
            }
        }
    </style>
</head>

<body>

<div class="wrapper">

    {{-- HEADER --}}
    <div class="header">

        <img
            src="{{ asset('images/caval-care-logo.png') }}"
            alt="CavalCare"
        >

    </div>


    {{-- BODY --}}
    <div class="body">

        <h1>
            New Booking received
        </h1>

        <p>
            A customer has completed their intake form and their
            PreBooking has now been converted into a Booking.
        </p>


        {{-- SUCCESS --}}
        <div class="success-box">

            <div class="success-icon">
                ✓
            </div>

            <p class="success-title">
                Intake Form Completed
            </p>

            <p class="success-text">
                The customer's care information has been successfully
                submitted and is now available in the CavalCare admin panel.
            </p>

        </div>


        {{-- BOOKING DETAILS --}}
        <div class="details-box">

            <p class="details-title">
                Booking Details
            </p>


            <div class="detail-row">

                <span class="detail-label">
                    Booking Reference:
                </span>

                <br>

                <span class="detail-value">
                    {{ $booking->reference }}
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Booker:
                </span>

                <br>

                <span class="detail-value">
                    {{ $booking->booker_details['name'] ?? '—' }}
                </span>

            </div>


            @if(!empty($booking->booker_details['email']))

                <div class="detail-row">

                    <span class="detail-label">
                        Email:
                    </span>

                    <br>

                    <span class="detail-value">
                        {{ $booking->booker_details['email'] }}
                    </span>

                </div>

            @endif


            @if(!empty($booking->booker_details['phone']))

                <div class="detail-row">

                    <span class="detail-label">
                        Phone:
                    </span>

                    <br>

                    <span class="detail-value">
                        {{ $booking->booker_details['phone'] }}
                    </span>

                </div>

            @endif


            <div class="detail-row">

                <span class="detail-label">
                    Person Receiving Care:
                </span>

                <br>

                <span class="detail-value">
                    {{ $booking->patient_details['full_name'] ?? '—' }}
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Care Type:
                </span>

                <br>

                <span class="detail-value">
                    {{ $booking->careType->name ?? '—' }}
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Care Plan:
                </span>

                <br>

                <span class="detail-value">
                    {{ $booking->carePlan->name ?? '—' }}
                </span>

            </div>


            @if(!empty($booking->booking_details['start_date']))

                <div class="detail-row">

                    <span class="detail-label">
                        Care Start Date:
                    </span>

                    <br>

                    <span class="detail-value">
                        {{ \Carbon\Carbon::parse($booking->booking_details['start_date'])->format('D, d M Y') }}
                    </span>

                </div>

            @endif


            @if(!empty($booking->booking_details['end_date']))

                <div class="detail-row">

                    <span class="detail-label">
                        Care End Date:
                    </span>

                    <br>

                    <span class="detail-value">
                        {{ \Carbon\Carbon::parse($booking->booking_details['end_date'])->format('D, d M Y') }}
                    </span>

                </div>

            @endif


            <div class="detail-row">

                <span class="detail-label">
                    Status:
                </span>

                <br>

                <span class="status-badge">
                    {{ str_replace('_', ' ', strtoupper($booking->status->value ?? $booking->status)) }}
                </span>

            </div>

        </div>


        {{-- ADMIN ACTION --}}
        <div class="button-wrapper">

            <a
                href="{{ $adminUrl }}"
                class="button"
            >
                View Booking in Admin
            </a>

        </div>


        <div class="notice">

            <strong>Action required:</strong>

            Please review the completed intake form in the CavalCare
            admin panel and proceed with the appropriate nurse matching
            and care coordination process.

        </div>


        <div class="divider"></div>


        <p style="font-size:.88rem;margin-bottom:0;">

            This is an automated internal notification from CavalCare.
            Patient and care information should be accessed through the
            secure admin panel rather than forwarded through email.

        </p>

    </div>


    {{-- FOOTER --}}
    <div class="footer">

        © {{ date('Y') }} CavalCare. All rights reserved.<br>

        <a href="{{ url('/') }}">
            cavalcare.com
        </a>

    </div>

</div>

</body>
</html>