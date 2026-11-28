<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\Care\Concern;
use App\Models\Care\SafetyAlert;
use App\Models\Care\Visit;
use App\Models\Care\VisitCheckIn;
use App\Services\Care\VisitService;
use App\Services\Notify\Notifier;
use App\Support\AdminOs\Audit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Visits & notes (backend spec 12): what needs a coordinator first
 * (safety alerts, urgent concerns, disputed or unconfirmed check-ins,
 * late caregivers), then the day's visits; full visit detail with
 * readings flagged against normal ranges.
 */
class VisitController extends Controller
{
    public function index(Request $request)
    {
        $day = $request->query('day', now('Africa/Lagos')->toDateString());
        $start = \Illuminate\Support\Carbon::parse($day, 'Africa/Lagos')->startOfDay()->utc();

        $visits = Visit::with(['patient', 'professional.user', 'checkIn'])
            ->whereBetween('scheduled_start', [$start, $start->copy()->addDay()])
            ->orderBy('scheduled_start')->get();

        $safety = SafetyAlert::with('professional.user')->whereNull('acknowledged_at')->latest()->get();
        $concerns = Concern::with(['visit.patient', 'visit.professional.user'])->whereNull('resolved_at')
            ->orderByRaw("FIELD(severity, 'urgent', 'attention')")->latest('recorded_at')->get();
        $disputed = VisitCheckIn::with(['visit.patient', 'visit.professional.user'])
            ->where(fn ($q) => $q->where('disputed', true)->orWhere(fn ($w) => $w->where('method', 'manual')->whereNull('confirmation_answer')->where('confirmation_asked_at', '<=', now()->subMinutes(15))))
            ->whereNull('resolved_at')->latest('at')->get();
        $late = Visit::with(['patient', 'professional.user'])->whereIn('state', ['scheduled', 'en_route'])
            ->where('scheduled_start', '<=', now()->subMinutes(30))->where('scheduled_start', '>=', now()->subDay())->get();

        return view('adminos.visits.index', compact('visits', 'safety', 'concerns', 'disputed', 'late', 'day'));
    }

    public function show(Visit $visit)
    {
        $visit->load(['patient.allergies', 'professional.user', 'booking', 'checkIn', 'checkOut', 'tasks', 'vitals',
            'medicationEvents', 'observations', 'concerns', 'careNotes', 'amendments']);
        Audit::log('visit.viewed', $visit, ['patient' => $visit->patient_id]);

        return view('adminos.visits.show', ['v' => $visit]);
    }

    /** Acknowledge: the family is told now (spec 9.2 ConcernRaised). */
    public function acknowledgeConcern(Request $request, Concern $concern, VisitService $visits, Notifier $notifier)
    {
        if (! $concern->acknowledged_at) {
            $concern->update(['acknowledged_by' => $request->user()->id, 'acknowledged_at' => now()]);
            Audit::log('concern.acknowledged', $concern);

            $visit = $concern->visit;
            $who = $visit->patient->address_as ?: $visit->patient->display_name ?: 'your family member';
            $notifier->sendMany($visits->familyUsers($visit->booking), 'cavalcare', 'visits', 'concern_acknowledged',
                'The care team is on it', 'A CavalCare coordinator is following up a concern about ' . $who . '.', ['visit_id' => $visit->id], $concern->severity === 'urgent');
        }

        return back()->with('os_flash', ['stable', 'Concern acknowledged. The family has been told.']);
    }

    /** Resolve with a note: the visit status is recomputed (only this lowers it). */
    public function resolveConcern(Request $request, Concern $concern, VisitService $visits)
    {
        $data = $request->validate(['resolution_note' => ['required', 'string', 'max:2000']]);
        $concern->update([
            'acknowledged_by' => $concern->acknowledged_by ?? $request->user()->id,
            'acknowledged_at' => $concern->acknowledged_at ?? now(),
            'resolved_by'     => $request->user()->id,
            'resolved_at'     => now(),
            'resolution_note' => $data['resolution_note'],
        ]);
        $visits->recomputeStatus($concern->visit->fresh());
        Audit::log('concern.resolved', $concern);

        return back()->with('os_flash', ['stable', 'Concern resolved. Visit status: ' . $concern->visit->fresh()->status . '.']);
    }

    /** Close a disputed or unconfirmed check-in, keeping or releasing the pay hold. */
    public function resolveCheckIn(Request $request, VisitCheckIn $checkIn)
    {
        $data = $request->validate([
            'outcome'         => ['required', Rule::in(['confirmed', 'not_there'])],
            'resolution_note' => ['required', 'string', 'max:255'],
        ]);

        $checkIn->update([
            'coordinator_called_at' => $checkIn->coordinator_called_at ?? now(),
            'resolved_by'           => $request->user()->id,
            'resolved_at'           => now(),
            'resolution_note'       => $data['resolution_note'],
            'disputed'              => $data['outcome'] === 'not_there',
        ]);

        $visit = $checkIn->visit;
        if ($data['outcome'] === 'confirmed') {
            $visit->update(['pay_hold_reason' => null, 'pay_state' => $visit->isSubmitted() ? 'ready' : $visit->pay_state]);
        } else {
            $visit->update(['pay_state' => 'on_hold', 'pay_hold_reason' => 'Coordinator: caregiver was not at the patient\'s home']);
        }
        Audit::log('check_in.resolved', $checkIn, ['outcome' => $data['outcome']]);

        return back()->with('os_flash', ['stable', $data['outcome'] === 'confirmed' ? 'Check-in confirmed. Pay hold released.' : 'Recorded: not at the home. Pay stays on hold.']);
    }

    public function acknowledgeSafety(Request $request, SafetyAlert $alert)
    {
        $data = $request->validate(['resolution_note' => ['nullable', 'string', 'max:2000']]);
        $alert->update(['acknowledged_by' => $request->user()->id, 'acknowledged_at' => now(), 'resolution_note' => $data['resolution_note'] ?? null]);
        Audit::log('safety_alert.acknowledged', $alert);

        return back()->with('os_flash', ['stable', 'Safety alert acknowledged.']);
    }
}
