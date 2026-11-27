<?php

return [
    // URL prefix for Caval OS, e.g. https://usecavalcare.com/adminos
    'path' => env('OS_PATH', 'adminos'),

    // Sign admins out after this many minutes without activity.
    'idle_minutes' => (int) env('OS_IDLE_MINUTES', 30),

    // Failed sign-in attempts allowed per email + IP before a lockout.
    'max_login_attempts' => (int) env('OS_MAX_LOGIN_ATTEMPTS', 5),
];
