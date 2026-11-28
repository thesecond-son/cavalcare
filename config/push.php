<?php

/*
| Push notifications to the Cavalcare and CavalPro apps.
| "log" until Firebase is set up; then FCM_DRIVER=fcm with the project id
| and the path to the service-account JSON file (outside the web root).
*/

return [
    'driver' => env('FCM_DRIVER', 'log'),

    'firebase' => [
        'project_id'  => env('FIREBASE_PROJECT_ID'),
        'credentials' => env('FIREBASE_CREDENTIALS'),
    ],
];
