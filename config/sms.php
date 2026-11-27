<?php

/*
| Text messages (sign-in codes now, visit alerts later).
|
| No provider is chosen yet. "log" writes messages to the Laravel log so
| development and testing work. In production the API refuses SMS sign-in
| while the driver is "log", and the apps fall back to email codes.
|
| Suggested: Termii for Nigerian numbers (cheap, local sender ID), with
| Twilio as the fallback for UK/US numbers. Adding one means writing a
| class that implements App\Services\Sms\SmsSender and registering it in
| AppServiceProvider under its driver name.
*/

return [
    'driver' => env('SMS_DRIVER', 'log'),

    // Staging only (APP_ENV=staging): every phone sign-in code is this value.
    'staging_test_code' => env('SMS_STAGING_TEST_CODE'),

    'sender_id' =>env('SMS_SENDER_ID', 'Cavalcare'),

    'termii' => [
        'api_key' => env('TERMII_API_KEY'),
        'base_url' => env('TERMII_BASE_URL', 'https://v3.api.termii.com'),
    ],

    'twilio' => [
        'sid'   => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from'  => env('TWILIO_FROM'),
    ],
];
