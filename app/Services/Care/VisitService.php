<?php

namespace App\Services\Care;

use App\Models\Care\CareBooking;
use App\Models\Care\Concern;
use App\Models\Care\PatientCarePlan;
use App\Models\Care\Visit;
use App\Models\Care\VitalReading;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\User;
use App\Services\Notify\Notifier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Visits for a matched booking, the care-team thread, and the visit status
 * rule (backend spec 5.2): stable -> attention (out-of-range vital or an open
 * "attention" concern) -> urgent (an open urgent concern). Only the server
 * sets it; only a coordinator resolving a concern brings it down.
 */
class VisitService
{
    public function __construct(private Notifier $notifier)
    {
    }

    /** Called once a caregiver has accepted: visits, care plan, thread, family told. */
    public function onMatched(CareBooking $booking): void
    {
        $booking->loadMissing(['patient.members.user', 'professional.user', 'careType']);

        DB::transaction(function () use ($booking) {
            $this->createVisits($booking);
            $this->ensureCarePlan($booking);
            $thread = $this->careTeamThread($booking);
            $this->system($thread, 'matched', ($booking->professional->name) . ' will care for ' . ($booking->patient->address_as ?: $booking->patient->display_name ?: $booking->patient->full_name) . '.');
        });

        $who = $booking->patient->address_as ?: $booking->patient->display_name ?: 'your family member';
        $this->notifier->sendMany($this->familyUsers($booking), 'cavalcare', 'bookings', 'professional_matched',
            'Caregiver confirmed', $booking->professional->name . ' will care for ' . $who . '.', ['booking_id' => $booking->id]);
    }

    /** One visit per day of care (live-in = one per day). Flexible 10-visit plans are scheduled by a coordinator. */
    public function createVisits(CareBooking $booking): void
    {
        if ($booking->billing_cycle === 'per_10_visits' || Visit::where('care_booking_id', $booking->id)->exists()) {
            return;
        }

        $plan = $booking->carePlan;
        $hours = (int) ($plan?->meta['daily_hours'] ?? ($booking->billing_cycle === 'per_24h' ? 24 : 9));
        $startHour = $booking->start_hour ?? ($booking->billing_cycle === 'per_night' ? 17 : 8);
        if ($booking->billing_cycle === 'per_night') {
            $hours = 15;
        }

        $dates = in_array($booking->billing_cycle, ['per_week', 'per_month'], true)
            ? collect(range(0, $booking->start_date->diffInDays($booking->end_date)))->map(fn ($i) => $booking->start_date->copy()->addDays($i)->toDateString())->all()
            : $booking->visitDates();

        $offer = $booking->offers()->where('status', 'accepted')->first();
        $pay = $offer?->pay_per_visit_kobo ?? MatchingService::payPerVisitKobo($booking);
        $familyPerVisit = intdiv($booking->total_kobo - $booking->add_ons_total_kobo, max(1, count($dates)));

        foreach ($dates as $date) {
            $start = Carbon::parse($date . ' ' . str_pad($startHour, 2, '0', STR_PAD_LEFT) . ':00', 'Africa/Lagos');
            Visit::create([
                'care_booking_id'   => $booking->id,
                'patient_id'        => $booking->patient_id,
                'professional_id'   => $booking->professional_id,
                'scheduled_start'   => $start->clone()->utc(),
                'scheduled_end'     => $start->clone()->addHours(min(24, max(1, $hours)))->utc(),
                'family_price_kobo' => $familyPerVisit,
                'pay_kobo'          => $pay,
            ]);
        }
    }

    /** A starting care plan from the level of care; coordinators adjust it in Caval OS. */
    public function ensureCarePlan(CareBooking $booking): PatientCarePlan
    {
        $nursing = $booking->service_level === 'nursing';
        $tasks = $nursing
            ? [['id' => 'vitals', 'title' => 'Check vital signs', 'kind' => 'clinical'],
               ['id' => 'meds', 'title' => 'Give medicines as scheduled', 'kind' => 'clinical'],
               ['id' => 'hygiene', 'title' => 'Personal hygiene and skin check', 'kind' => 'care'],
               ['id' => 'meal', 'title' => 'Meal and fluids', 'kind' => 'care'],
               ['id' => 'mobility', 'title' => 'Mobility or exercises', 'kind' => 'care']]
            : [['id' => 'hygiene', 'title' => 'Help with washing and dressing', 'kind' => 'care'],
               ['id' => 'meds', 'title' => 'Medicine reminders', 'kind' => 'care'],
               ['id' => 'meal', 'title' => 'Prepare a meal and fluids', 'kind' => 'care'],
               ['id' => 'company', 'title' => 'Companionship and a short walk', 'kind' => 'care'],
               ['id' => 'tidy', 'title' => 'Tidy the room and bed', 'kind' => 'care']];

        return PatientCarePlan::firstOrCreate(['patient_id' => $booking->patient_id], [
            'tasks'                => $tasks,
            'vitals_required'      => $nursing ? ['bp', 'pulse', 'temp', 'spo2'] : [],
            'carer_allowed_vitals' => ['temp', 'pulse'],
        ]);
    }

    public function careTeamThread(CareBooking $booking): Thread
    {
        $thread = Thread::firstOrCreate(
            ['kind' => 'care_team', 'care_booking_id' => $booking->id],
            ['patient_id' => $booking->patient_id, 'title' => 'Care team · ' . ($booking->patient->display_name ?: $booking->patient->full_name)]
        );

        foreach ($booking->patient->members as $m) {
            $thread->participants()->firstOrCreate(['user_id' => $m->user_id], ['role_in_thread' => 'family', 'read_only' => $m->role === 'viewer']);
        }
        if ($booking->professional) {
            $thread->participants()->firstOrCreate(['user_id' => $booking->professional->user_id], ['role_in_thread' => 'professional']);
        }

        return $thread;
    }

    /** A system line in a thread: "Adaeze checked in · 10:02". */
    public function system(?Thread $thread, string $event, string $text): void
    {
        if (! $thread) {
            return;
        }
        Message::create(['thread_id' => $thread->id, 'sender_kind' => 'system', 'system_event' => $event, 'text' => $text, 'sent_at' => now()]);
        $thread->update(['last_message_at' => now()]);
    }

    public function threadFor(Visit $visit): ?Thread
    {
        return Thread::where('kind', 'care_team')->where('care_booking_id', $visit->care_booking_id)->first();
    }

    /** Status rule (spec 5.2). Returns true when it changed. */
    public function recomputeStatus(Visit $visit): bool
    {
        $open = Concern::where('visit_id', $visit->id)->whereNull('resolved_at');
        $status = 'stable';

        if ((clone $open)->where('severity', 'urgent')->exists()) {
            $status = 'urgent';
        } elseif ((clone $open)->where('severity', 'attention')->exists() || $this->hasOutOfRangeVital($visit)) {
            $status = 'attention';
        }

        if ($status === $visit->status) {
            return false;
        }
        $wentUp = array_search($status, ['stable', 'attention', 'urgent']) > array_search($visit->status, ['stable', 'attention', 'urgent']);
        $visit->update(['status' => $status]);

        // The family is told when it goes up (never the reading itself).
        if ($wentUp) {
            $who = $visit->patient->address_as ?: $visit->patient->display_name ?: 'your family member';
            $this->notifier->sendMany($this->familyUsers($visit->booking), 'cavalcare', 'visits', 'visit_status_changed',
                $status === 'urgent' ? 'Urgent: care team alerted' : 'Something to know',
                'The care team is following up on ' . $who . '. Open the app for details.', ['visit_id' => $visit->id], $status === 'urgent');
        }

        return true;
    }

    /** Latest reading of each type, after rechecks, is out of range. */
    private function hasOutOfRangeVital(Visit $visit): bool
    {
        $readings = VitalReading::where('visit_id', $visit->id)->orderBy('recorded_at')->get();
        $rechecked = $readings->pluck('recheck_of')->filter()->all();

        return $readings->reject(fn ($r) => in_array($r->id, $rechecked, true))->contains('out_of_range', true);
    }

    /** Owners and family Caregivers of the booking's patient (who act on alerts). */
    public function familyUsers(CareBooking $booking, bool $includeViewers = true): \Illuminate\Support\Collection
    {
        return User::whereIn('id', $booking->patient->members()
            ->when(! $includeViewers, fn ($q) => $q->whereIn('role', ['owner', 'caregiver']))
            ->pluck('user_id'))->get();
    }
}
