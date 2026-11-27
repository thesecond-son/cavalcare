<?php

return [
    // Store links for the family app. Left empty until the app is public;
    // emails hide the download buttons while these are empty.
    'app_links' => [
        'ios'     => env('CAVALCARE_APP_IOS_URL'),
        'android' => env('CAVALCARE_APP_ANDROID_URL'),
    ],
];
