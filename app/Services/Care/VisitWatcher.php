<?php

namespace App\Services\Care;

use App\Models\Care\Visit;
use App\Models\Care\VisitCheckIn;
use App\Services\Notify\Notifier;
use Illuminate\Support\Facades\Cache;

/**
 * Scheduled checks on visits (backend spec 14.3). Each alert is sent once.
 */
class VisitWatcher
{
    public function __construct(private VisitRecorder $recorder, private VisitService $visits, private Notifier $notifier)
    {
    }

    public function run(): array
    {
        return [
            'unanswered_arrivals' => $this->unansweredArrivals(),
            'missed'              => $this->missedVisits(),
            'overdue_reports'     => $this->overdueReports(),
            'reminders'           => $this->reminders(),
        ];
    }

    /** Manual check-in, no family answer in 15 minutes: coordinators call the patient. */
    private function unansweredArrivals(): int
    {
        $n = 0;
        VisitCheckIn::with('visit.professional.user')->where('method', 'manual')->whereNull('confirmation_answer')
            ->whereNotNull('confirmation_asked_at')->where('confirmation_asked_at', '<=', now()->subMinutes(15))
            ->where('confirmation_asked_at', '>=', now()->subDay())->get()
            ->each(function ($c) use (&$n) {
                if (Cache::add('alert:arrival_unanswered:' . $c->id, 1, now()->addDays(2))) {
                    $this->recorder->notifyCoordinators('arrival_unanswered', 'Call the patient',
                        'Nobody confirmed ' . $c->visit->professional->name . '\'s manual check-in after 15 minutes.', ['visit_id' => $c->visit_id], true);
                    $n++;
                }
            });

        return $n;
    }

    /** No check-in 30 minutes after the start. */
    private function missedVisits(): int
    {
        $n = 0;
        Visit::with('professional.user')->whereIn('state', ['scheduled', 'en_route'])
            ->where('scheduled_start', '<=', now()->subMinutes(30))->where('scheduled_start', '>=', now()->subDay())->get()
            ->each(function ($v) use (&$n) {
                if (Cache::add('alert:late:' . $v->id, 1, now()->addDays(2))) {
                    $this->recorder->notifyCoordinators('visit_late', 'Caregiver late',
                        ($v->professional?->name ?? 'The caregiver') . ' has not checked in, 30 minutes after the start.', ['visit_id' => $v->id], true);
                    $n++;
                }
            });

        return $n;
    }

    /** Report not submitted 2 hours after the end: nudge the caregiver. */
    private function overdueReports(): int
    {
        $n = 0;
        Visit::with('professional.user')->where('state', 'checked_in')->whereNull('report_submitted_at')
            ->where('scheduled_end', '<=', now()->subHours(2))->get()
            ->each(function ($v) use (&$n) {
                if ($v->professional?->user && Cache::add('alert:report_overdue:' . $v->id, 1, now()->addDays(3))) {
                    $this->notifier->send($v->professional->user, 'cavalpro', 'shifts', 'report_overdue',
                        'Visit report due', 'Please finish and submit your visit report.', ['visit_id' => $v->id]);
                    $n++;
                }
            });

        return $n;
    }

    /** One hour before: remind the caregiver and the family. */
    private function reminders(): int
    {
        $n = 0;
        Visit::with(['professional.user', 'patient', 'booking.patient'])->where('state', 'scheduled')
            ->whereBetween('scheduled_start', [now()->addMinutes(50), now()->addMinutes(65)])->get()
            ->each(function ($v) use (&$n) {
                if (! Cache::add('alert:reminder:' . $v->id, 1, now()->addDay())) {
                    return;
                }
                $time = $v->scheduled_start->timezone('Africa/Lagos')->format('H:i');
                $who = $v->patient->address_as ?: $v->patient->display_name ?: $v->patient->full_name;
                if ($v->professional?->user) {
                    $this->notifier->send($v->professional->user, 'cavalpro', 'shifts', 'visit_reminder', 'Visit at ' . $time, 'Your visit with ' . $who . ' starts at ' . $time . ' (Lagos).', ['visit_id' => $v->id]);
                }
                $this->notifier->sendMany($this->visits->familyUsers($v->booking), 'cavalcare', 'visits', 'visit_reminder',
                    'Visit at ' . $time . ' Lagos time', ($v->professional?->name ?? 'Your caregiver') . ' visits ' . $who . ' at ' . $time . ' (Lagos).', ['visit_id' => $v->id]);
                $n++;
            });

        return $n;
    }
}
