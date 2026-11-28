<?php

namespace App\Http\Controllers\Api\V1\Pro;

use App\Http\Controllers\Controller;
use App\Models\Care\PatientCarePlan;
use App\Models\Care\Professional;
use App\Models\Care\SafetyAlert;
use App\Models\Care\Visit;
use App\Services\Care\VisitRecorder;
use App\Support\ApiError;
use Illuminate\Http\Request;

/**
 * CavalPro visit flow (backend spec 8.2, 8.4, 8.5, 8.7).
 * Single calls work online; the app normally sends records through /pro/sync.
 */
class VisitController extends Controller
{
    public function __construct(private VisitRecorder $recorder)
    {
    }

    /** GET /pro/visits?from=YYYY-MM-DD&to=YYYY-MM-DD (default: this week) */
    public function index(Request $request)
    {
        $pro = $this->pro($request);
        $from = $request->query('from', now('Africa/Lagos')->startOfWeek()->toDateString());
        $to = $request->query('to', now('Africa/Lagos')->endOfWeek()->toDateString());

        $visits = Visit::with(['patient', 'booking'])
            ->where('professional_id', $pro->id)
            ->whereBetween('scheduled_start', [\Illuminate\Support\Carbon::parse($from, 'Africa/Lagos')->startOfDay()->utc(), \Illuminate\Support\Carbon::parse($to, 'Africa/Lagos')->endOfDay()->utc()])
            ->orderBy('scheduled_start')
            ->get();

        $overdue = Visit::where('professional_id', $pro->id)->where('state', 'checked_in')
            ->where('scheduled_end', '<', now()->subHours(2))->count();

        return response()->json([
            'data' => $visits->map(fn (Visit $v) => $this->card($v)),
            'meta' => ['overdue_reports' => $overdue],
        ]);
    }

    /** GET /pro/visits/{id}/brief: everything to run the visit offline (spec "bundle"). */
    public function brief(Request $request, string $id)
    {
        $visit = $this->visit($request, $id);
        $p = $visit->patient->load(['allergies', 'conditions', 'medications', 'emergencyContacts']);
        $plan = PatientCarePlan::where('patient_id', $p->id)->first();
        $last = Visit::where('patient_id', $p->id)->whereNotNull('report_submitted_at')->where('id', '!=', $visit->id)
            ->with('careNotes')->latest('scheduled_start')->first();

        return response()->json(['data' => $this->card($visit) + [
            'patient' => [
                'name'        => $p->display_name ?: $p->full_name,
                'full_name'   => $p->full_name,
                'address_as'  => $p->address_as,
                'pronoun'     => $p->pronoun,
                'age_sex'     => $p->age_sex,
                'phone'       => $p->phone_e164,
                'address'     => ['line' => $p->address_line, 'note' => $p->address_note, 'area' => $p->area, 'city' => $p->city, 'lat' => $p->lat, 'lng' => $p->lng],
                'languages'   => $p->languages ?? [],
                'mobility'    => $p->mobility_level,
                'allergies'   => $p->allergies->map(fn ($a) => ['name' => $a->name, 'reaction' => $a->reaction, 'severe' => $a->severe]),
                'conditions'  => $p->conditions->pluck('name'),
                'medications' => $p->medications->where('active', true)->values()->map(fn ($m) => ['id' => $m->id, 'name' => $m->name, 'dose' => $m->dose, 'instructions' => $m->instructions, 'times' => $m->schedule['times'] ?? [], 'with_food' => $m->schedule['with_food'] ?? null]),
                'emergency_contacts' => $p->emergencyContacts->map(fn ($e) => ['name' => $e->name, 'relation' => $e->relation, 'phone' => $e->phone_e164]),
            ],
            'care_plan' => [
                'tasks'           => $plan?->tasks ?? [],
                'vitals_required' => $plan?->vitals_required ?? [],
                'vitals_allowed'  => $this->pro($request)->title === 'rn' ? ['bp', 'pulse', 'sugar', 'spo2', 'temp', 'resp', 'pain'] : ($plan?->carer_allowed_vitals ?? []),
            ],
            'normal_ranges' => config('cavalcare.default_vital_ranges'),
            'last_visit'    => $last ? ['date' => $last->scheduled_start->timezone('Africa/Lagos')->toDateString(), 'note' => $last->careNotes->last()?->text] : null,
            'notes'         => $visit->booking->notes,
        ]]);
    }

    /** POST /pro/visits/{id}/start-travel */
    public function startTravel(Request $request, string $id)
    {
        return response()->json(['data' => $this->card($this->recorder->startTravel($this->visit($request, $id)))]);
    }

    /** POST /pro/visits/{id}/check-in: method qr (with code) or manual (with reason); lat, lng, recorded_at. */
    public function checkIn(Request $request, string $id)
    {
        $visit = $this->visit($request, $id);
        $r = $this->recorder->checkIn($visit, $request->all(), $request->input('client_id'));

        return response()->json(['data' => $this->card($visit->fresh(['patient', 'booking', 'checkIn'])) + ['result' => $r['result']]]);
    }

    /** POST /pro/sync: up to 200 queued records, in order. One result per item. */
    public function sync(Request $request)
    {
        $pro = $this->pro($request);
        $request->validate([
            'device_id'             => ['nullable', 'string', 'max:100'],
            'items'                 => ['required', 'array', 'min:1', 'max:200'],
            'items.*.client_id'     => ['required', 'string', 'max:64'],
            'items.*.type'          => ['required', 'string', 'max:20'],
            'items.*.visit_id'      => ['required', 'uuid'],
            'items.*.recorded_at'   => ['required', 'date'],
            'items.*.payload'       => ['present', 'array'],
        ]);

        return response()->json(['data' => $this->recorder->sync($pro, $request->input('items'))]);
    }

    /** POST /pro/visits/{id}/submit: check-out GPS + report. 422 report_incomplete lists what's missing. */
    public function submit(Request $request, string $id)
    {
        $visit = $this->recorder->submit($this->visit($request, $id), $request->all());

        return response()->json(['data' => $this->card($visit->load(['patient', 'booking']))]);
    }

    /** POST /pro/safety-alerts: "Feel unsafe?" Goes to the safety team only, never the family. */
    public function safetyAlert(Request $request)
    {
        $pro = $this->pro($request);
        $data = $request->validate([
            'visit_id' => ['nullable', 'uuid'],
            'call_me'  => ['required', 'boolean'],
            'note'     => ['nullable', 'string', 'max:1000'],
            'lat'      => ['nullable', 'numeric', 'between:-90,90'],
            'lng'      => ['nullable', 'numeric', 'between:-180,180'],
        ]);
        if (! empty($data['visit_id']) && ! Visit::where('id', $data['visit_id'])->where('professional_id', $pro->id)->exists()) {
            $data['visit_id'] = null;
        }

        $alert = SafetyAlert::create($data + ['professional_id' => $pro->id]);
        $this->recorder->notifyCoordinators('safety_alert', 'SAFETY ALERT', $pro->name . ($data['call_me'] ? ' asked to be called now.' : ' raised a safety alert.'), ['safety_alert_id' => $alert->id], true);

        return response()->json(['data' => ['id' => $alert->id, 'received' => true, 'message' => 'CavalCare has been alerted. If you are in danger, leave and call for help.']], 201);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function card(Visit $v): array
    {
        $p = $v->patient;

        return [
            'id'                  => $v->id,
            'booking_reference'   => $v->booking?->reference,
            'patient_name'        => $p?->display_name ?: $p?->full_name,
            'area'                => $p?->area,
            'scheduled_start'     => $v->scheduled_start->toIso8601String(),
            'scheduled_end'       => $v->scheduled_end->toIso8601String(),
            'state'               => $v->state,
            'status'              => $v->status,
            'checked_in_at'       => $v->checked_in_at?->toIso8601String(),
            'check_in_method'     => $v->relationLoaded('checkIn') ? $v->checkIn?->method : null,
            'report_submitted'    => $v->isSubmitted(),
            'can_check_in_now'    => in_array($v->state, ['scheduled', 'en_route'], true) && $v->inCheckInWindow(),
            'pay_kobo'            => $this->proIsAgency ? null : $v->pay_kobo,
            'pay_state'           => $v->pay_state,
        ];
    }

    private bool $proIsAgency = false;

    private function pro(Request $request): Professional
    {
        $pro = Professional::with('user')->where('user_id', $request->user()->id)->first();
        if (! $pro) {
            abort(ApiError::make('profile_missing', 'Finish "About you" first.', 404));
        }
        $this->proIsAgency = $pro->employment === 'agency';

        return $pro;
    }

    /** A visit assigned to this caregiver; anyone else's is "not found". */
    private function visit(Request $request, string $id): Visit
    {
        $pro = $this->pro($request);
        $visit = Visit::with(['patient', 'booking', 'professional.user', 'checkIn'])->find($id);
        abort_unless($visit && $visit->professional_id === $pro->id, 404);

        return $visit;
    }
}
