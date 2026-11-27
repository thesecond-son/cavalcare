<?php

return [
    // Store links for the family app. Left empty until the app is public;
    // emails hide the download buttons while these are empty.
    'app_links' => [
        'ios'     => env('CAVALCARE_APP_IOS_URL'),
        'android' => env('CAVALCARE_APP_ANDROID_URL'),
    ],

    // Caregiver's share of the family price per visit, in percent (spec 10.4).
    // PLACEHOLDER: the default split is still an open question for the client.
    'default_professional_share_pct' => (float) env('CAVALCARE_PROFESSIONAL_SHARE_PCT', 70),

    // Where "new payment" and "intake completed" alerts go. Empty = don't send (staging).
    'team_email' => env('CAVALCARE_TEAM_EMAIL', 'cavalcareteam@gmail.com'),
];
