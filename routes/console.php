<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Caval OS / apps: expire unanswered shift offers and move on to the next caregiver (spec 11.3).
// Needs the scheduler: `* * * * * cd /path && php artisan schedule:run` in the server's crontab.
Artisan::command('offers:expire', function (\App\Services\Care\MatchingService $matching) {
    $n = $matching->expireDue();
    if ($n) {
        $this->info("Expired {$n} offer(s).");
    }
})->purpose('Expire unanswered shift offers and offer the next caregiver');

\Illuminate\Support\Facades\Schedule::command('offers:expire')->everyMinute()->withoutOverlapping();

// Visit alerts: unanswered arrival checks, late caregivers, overdue reports, reminders (spec 14.3).
Artisan::command('visits:watch', function (\App\Services\Care\VisitWatcher $watcher) {
    $r = $watcher->run();
    if (array_sum($r)) {
        $this->info(json_encode($r));
    }
})->purpose('Send visit alerts and reminders');

\Illuminate\Support\Facades\Schedule::command('visits:watch')->everyFiveMinutes()->withoutOverlapping();
