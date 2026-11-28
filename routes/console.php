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

// Caregiver checks: mark expiring (30 days) and expired ones, then update each caregiver's status (spec 14.3).
Artisan::command('credentials:expire', function () {
    $soon = \App\Models\Care\Credential::where('status', 'verified')->whereNotNull('valid_to')
        ->whereBetween('valid_to', [now()->toDateString(), now()->addDays(30)->toDateString()])->update(['status' => 'expiring']);
    $gone = \App\Models\Care\Credential::whereIn('status', ['verified', 'expiring'])->whereNotNull('valid_to')
        ->where('valid_to', '<', now()->toDateString())->get();
    foreach ($gone as $c) {
        $c->update(['status' => 'expired']);
    }
    \App\Models\Care\Professional::whereIn('id', $gone->pluck('professional_id')->unique())->get()
        ->each(fn ($p) => $p->syncStatusWithCredentials());
    if ($soon || $gone->count()) {
        $this->info("Expiring: {$soon}, expired: {$gone->count()}");
    }
})->purpose('Mark caregiver checks as expiring or expired');

\Illuminate\Support\Facades\Schedule::command('credentials:expire')->dailyAt('07:00')->timezone('Africa/Lagos');
