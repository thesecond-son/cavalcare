<?php

return [
    // Store links for the family app. Left empty until the app is public;
    // emails hide the download buttons while these are empty.
    'app_links' => [
        'ios'     => env('CAVALCARE_APP_IOS_URL'),
        'android' => env('CAVALCARE_APP_ANDROID_URL'),
    ],

    // Where "new payment" and "intake completed" alerts go. Empty = don't send (staging).
    'team_email' => env('CAVALCARE_TEAM_EMAIL', 'cavalcareteam@gmail.com'),
];
