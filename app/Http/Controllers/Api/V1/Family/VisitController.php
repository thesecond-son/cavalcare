<?php

namespace App\Http\Controllers\Api\V1\Family;

use App\Http\Controllers\Controller;
use App\Models\Care\Patient;
use App\Models\Care\Visit;
use App\Services\Care\CheckInCodeService;
use App\Services\Care\VisitRecorder;
use App\Support\ApiError;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Visits as the family sees them (backend spec 7.5) and the patient's
 * check-in link (7.2). Family responses never contain out-of-range flags,
 * caregiver pay or safety alerts (spec 13.2).
 */
class VisitController extends Controller
{
    /** GET /family/patients/{id}/visits?from=&to= */
    public function index(Request $request, string $patientId)
    {
        $this->member($request, $patientId);
        $from = Carbon::parse($request->query('from', now()->subDays(14)->toDateString()), 'Africa/Lagos')->startOfDay()->utc();
        $to = Carbon::parse($request->query('to', now()->addDays(30)->toDateString()), 'Africa/Lagos')->endOfDay()->utc();

        $visits = Visit::with('professional.user')->where('patient_id', $patientId)
            ->whereBetween('scheduled_start', [$from, $to])->orderBy('scheduled_start')->get();

        return response()->json(['data' => $visits->map(fn ($v) => $this->card($v))]);
    }

    /** GET /family/visits/{id}/live: state, check-in and confirmation, timeline, latest readings. */
    public function live(Request $request, string $id)
    {
        $visit = $this->visit($request, $id);
        $visit->load(['checkIn', 'tasks', 'vitals', 'medicationEvents']);

        $timeline = collect();
        if ($visit->en_route_at) {
            $timeline->push(['at' => $visit->en_route_at->toIso8601String(), 'event' => 'en_route', 'text' => 'On the way']);
        }
        if ($c = $visit->checkIn) {
            $timeline->push(['at' => $c->at->toIso8601String(), 'event' => 'checked_in', 'text' => 'Checked in · ' . ($c->method === 'qr' ? 'QR code on the patient\'s phone' : 'manual check-in')]);
        }
        foreach ($visit->tasks as $t) {
            $timeline->push(['at' => $t->recorded_at->toIso8601String(), 'event' => 'task', 'text' => ($t->task_title ?: 'Task') . ($t->state === 'done' ? '' : ' · not done: ' . $t->reason)]);
        }
        foreach ($visit->medicationEvents as $m) {
            $timeline->push(['at' => $m->recorded_at->toIso8601String(), 'event' => 'medication', 'text' => $m->medication_name . ' · ' . str_replace('_', ' ', $m->outcome)]);
        }

        return response()->json(['data' => $this->card($visit) + [
            'check_in' => $visit->checkIn ? [
                'method'               => $visit->checkIn->method,
                'at'                   => $visit->checkIn->at->toIso8601String(),
                'needs_your_answer'    => $visit->checkIn->method === 'manual' && ! $visit->checkIn->confirmation_answer,
                'confirmation_answer'  => $visit->checkIn->confirmation_answer,
            ] : null,
            'timeline'      => $timeline->sortBy('at')->values(),
            'latest_vitals' => $visit->vitals->groupBy('type')->map(fn ($g) => $this->vital($g->last()))->values(),
        ]]);
    }

    /** GET /family/visits/{id}/report: the submitted visit report. */
    public function report(Request $request, string $id)
    {
        $visit = $this->visit($request, $id);
        if (! $visit->isSubmitted()) {
            return ApiError::make('report_not_ready', 'The report isn\'t ready yet.', 409);
        }
        $visit->load(['checkIn', 'checkOut', 'tasks', 'vitals', 'medicationEvents', 'observations', 'careNotes', 'concerns', 'amendments']);

        return response()->json(['data' => $this->card($visit) + [
            'checked_in_at'  => $visit->checkIn?->at->toIso8601String(),
            'check_in_method' => $visit->checkIn?->method,
            'checked_out_at' => $visit->checkOut?->at->toIso8601String(),
            'vitals'         => $visit->vitals->map(fn ($v) => $this->vital($v)),
            'tasks'          => $visit->tasks->map(fn ($t) => ['title' => $t->task_title ?: $t->task_id, 'done' => $t->state === 'done', 'reason' => $t->reason, 'at' => $t->recorded_at->toIso8601String()]),
            'medications'    => $visit->medicationEvents->map(fn ($m) => ['name' => $m->medication_name, 'outcome' => $m->outcome, 'reason' => $m->reason, 'at' => $m->recorded_at->toIso8601String()]),
            'observations'   => $visit->observations->last()?->only(['mood', 'mobility', 'appetite', 'water_glasses', 'pain_score', 'skin']),
            'care_note'      => $visit->careNotes->last()?->text,
            // Concerns: what and when the coordinator was told; never internal notes.
            'concerns'       => $visit->concerns->map(fn ($c) => [
                'kind'              => $c->kind,
                'severity'          => $c->severity,
                'coordinator_told_at' => $c->synced_at->toIso8601String(),
                'resolved'          => $c->resolved_at !== null,
            ]),
            'updated'        => $visit->amendments->isNotEmpty(),
            'amendments'     => $visit->amendments->map(fn ($a) => ['what' => $a->record_type . ' ' . $a->field, 'reason' => $a->reason, 'at' => $a->at->toIso8601String()]),
        ]]);
    }

    /** POST /family/visits/{id}/confirm-arrival { "answer": "yes" | "no" } (Owner or family Caregiver). */
    public function confirmArrival(Request $request, string $id, VisitRecorder $recorder)
    {
        $visit = $this->visit($request, $id, edit: true);
        $data = $request->validate(['answer' => ['required', Rule::in(['yes', 'no'])]]);
        $recorder->confirmArrival($visit, $request->user(), $data['answer']);

        return response()->json(['data' => [
            'answer'  => $data['answer'],
            'message' => $data['answer'] === 'yes' ? 'Thank you. Confirmed.' : 'Thank you. A CavalCare coordinator is calling now.',
        ]]);
    }

    // ── Check-in link (the patient's phone shows the rotating code) ─────

    /** GET /family/patients/{id}/check-in-link */
    public function link(Request $request, string $patientId, CheckInCodeService $codes)
    {
        $patient = $this->member($request, $patientId, edit: true);
        $link = $codes->linkFor($patient);

        return response()->json(['data' => $this->linkData($link)]);
    }

    /** POST /family/patients/{id}/check-in-link/send { "channel": "whatsapp" | "sms" | "copied" }: records how it was shared. */
    public function sendLink(Request $request, string $patientId, CheckInCodeService $codes)
    {
        $patient = $this->member($request, $patientId, edit: true);
        $data = $request->validate(['channel' => ['required', Rule::in(['whatsapp', 'sms', 'copied'])]]);
        $link = $codes->linkFor($patient);
        $link->update(['sent_by' => $request->user()->id, 'sent_at' => now(), 'channel' => $data['channel']]);

        return response()->json(['data' => $this->linkData($link) + [
            'share_text' => 'This is your CavalCare check-in page. Open it when your nurse or caregiver arrives and let them scan the code: ' . $link->url(),
        ]]);
    }

    /** POST /family/patients/{id}/check-in-link/revoke: the old link stops working; a new one is made. */
    public function revokeLink(Request $request, string $patientId, CheckInCodeService $codes)
    {
        $patient = $this->member($request, $patientId, edit: true);

        return response()->json(['data' => $this->linkData($codes->rotate($patient))]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function linkData($link): array
    {
        return ['url' => $link->url(), 'sent_at' => $link->sent_at?->toIso8601String(), 'channel' => $link->channel];
    }

    /** Readings as recorded: value and unit only, never a range flag. */
    private function vital($v): array
    {
        return ['type' => $v->type, 'value' => $v->value, 'unit' => $v->unit, 'context' => $v->context, 'recorded_at' => $v->recorded_at->toIso8601String()];
    }

    private function card(Visit $v): array
    {
        return [
            'id'              => $v->id,
            'patient_id'      => $v->patient_id,
            'scheduled_start' => $v->scheduled_start->toIso8601String(),
            'scheduled_end'   => $v->scheduled_end->toIso8601String(),
            'state'           => $v->state,
            'state_label'     => Visit::STATES[$v->state] ?? $v->state,
            'status'          => $v->status,   // stable | attention | urgent (the word, never the reason)
            'caregiver'       => $v->professional ? ['name' => $v->professional->name, 'title_label' => $v->professional->title === 'rn' ? 'Registered nurse' : 'Caregiver'] : null,
            'report_ready'    => $v->isSubmitted(),
        ];
    }

    private function member(Request $request, string $patientId, bool $edit = false): Patient
    {
        $patient = Patient::with('members')->find($patientId);
        $m = $patient?->members->firstWhere('user_id', $request->user()->id);
        abort_unless($m, 404);
        if ($edit && ! in_array($m->role, ['owner', 'caregiver'], true)) {
            abort(ApiError::make('role_not_allowed', 'Only the owner or a family caregiver can do this.', 403));
        }

        return $patient;
    }

    private function visit(Request $request, string $id, bool $edit = false): Visit
    {
        $visit = Visit::with(['professional.user', 'patient'])->find($id);
        abort_unless($visit, 404);
        $this->member($request, $visit->patient_id, $edit);

        return $visit;
    }
}
