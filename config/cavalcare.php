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

    // Default normal ranges for vital signs, used to flag readings for the care
    // team (never shown to families). PLACEHOLDER: the clinical lead must
    // confirm these (spec 18); per-patient ranges come later.
    'default_vital_ranges' => [
        'bp_sys' => [90, 140], 'bp_dia' => [60, 90], 'pulse' => [60, 100], 'sugar' => [70, 180],
        'spo2'   => [94, 100], 'temp' => [36.1, 37.8], 'resp' => [12, 20], 'pain' => [0, 4],
    ],

    // Support line shown on the check-in page and in the apps.
    'support_phone' => env('CAVALCARE_SUPPORT_PHONE'),

    // Where "new payment" and "intake completed" alerts go. Empty = don't send (staging).
    'team_email' => env('CAVALCARE_TEAM_EMAIL', 'cavalcareteam@gmail.com'),
];
