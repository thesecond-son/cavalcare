<?php

namespace App\Services\Care;

use App\Models\Care\Amendment;
use App\Models\Care\CareNote;
use App\Models\Care\Concern;
use App\Models\Care\MedicationEvent;
use App\Models\Care\Observation;
use App\Models\Care\Professional;
use App\Models\Care\TaskEvent;
use App\Models\Care\Visit;
use App\Models\Care\VisitCheckIn;
use App\Models\Care\VisitCheckOut;
use App\Models\Care\VitalReading;
use App\Models\User;
use App\Services\Notify\Notifier;
use App\Support\AdminOs\OsRoles;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

/**
 * Everything CavalPro records during a visit (backend spec 8.4, 8.5).
 * The same code runs for single calls and for the offline sync outbox.
 *
 * - client_id from the phone: a repeat is "duplicate", which counts as success.
 * - recorded_at is the phone's time; more than 10 minutes off server time is flagged.
 * - after the report is submitted, records can only be amended (with a reason).
 */
class VisitRecorder
{
    public const DRIFT_MINUTES = 10;

    public function __construct(
        private VisitService $visits,
        private CheckInCodeService $codes,
        private Notifier $notifier,
    ) {
    }

    // ── Single actions ──────────────────────────────────────────────────

    public function startTravel(Visit $visit): Visit
    {
        if ($visit->state === 'scheduled') {
            $visit->update(['state' => 'en_route', 'en_route_at' => now()]);
            $name = $visit->professional->name;
            $this->visits->system($this->visits->threadFor($visit), 'en_route', $name . ' is on the way.');
            $this->notifier->sendMany($this->visits->familyUsers($visit->booking), 'cavalcare', 'visits', 'visit_en_route',
                'On the way', $name . ' is on the way.', ['visit_id' => $visit->id]);
        }

        return $visit;
    }

    /**
     * Check-in by QR code (from the patient's phone) or manually with a reason.
     * A manual check-in asks the family to confirm the arrival.
     * @return array{result:string, check_in?:VisitCheckIn}
     */
    public function checkIn(Visit $visit, array $p, ?string $clientId = null): array
    {
        $v = Validator::make($p, [
            'method'      => ['required', Rule::in(['qr', 'manual'])],
            'code'        => ['required_if:method,qr', 'nullable', 'string', 'max:10'],
            'reason'      => ['required_if:method,manual', 'nullable', Rule::in(['phone_off', 'no_smartphone', 'wont_scan', 'no_link', 'other'])],
            'reason_note' => ['nullable', 'string', 'max:255'],
            'lat'         => ['nullable', 'numeric', 'between:-90,90'],
            'lng'         => ['nullable', 'numeric', 'between:-180,180'],
            'recorded_at' => ['required', 'date'],
        ]);
        $data = $v->validate();

        if ($visit->checkIn) {
            return ['result' => 'duplicate', 'check_in' => $visit->checkIn];
        }
        if (! in_array($visit->state, ['scheduled', 'en_route'], true)) {
            throw ValidationException::withMessages(['visit' => 'This visit can\'t be checked into now.']);
        }

        $at = Carbon::parse($data['recorded_at']);
        $codeValid = null;
        if ($data['method'] === 'qr') {
            $codeValid = $this->codes->verify($visit, $data['code'], $at);
            if (! $codeValid) {
                throw ValidationException::withMessages(['code' => 'That code doesn\'t match the one on the patient\'s phone. Scan again, or check in manually.']);
            }
        }

        $checkIn = DB::transaction(function () use ($visit, $data, $at, $clientId, $codeValid) {
            $checkIn = VisitCheckIn::create([
                'visit_id'    => $visit->id,
                'client_id'   => $clientId,
                'at'          => $at,
                'method'      => $data['method'],
                'lat'         => $data['lat'] ?? null,
                'lng'         => $data['lng'] ?? null,
                'distance_m'  => $this->distanceM($visit, $data['lat'] ?? null, $data['lng'] ?? null),
                'reason'      => $data['reason'] ?? null,
                'reason_note' => $data['reason_note'] ?? null,
                'code_valid'  => $codeValid,
            ]);
            $visit->update(['state' => 'checked_in', 'checked_in_at' => $at, 'en_route_at' => $visit->en_route_at ?? $at]);

            return $checkIn;
        });

        $name = $visit->professional->name;
        $how = $data['method'] === 'qr' ? 'QR code on ' . ($visit->patient->address_as ?: 'the patient') . '\'s phone' : 'manual check-in';
        $this->visits->system($this->visits->threadFor($visit), 'checked_in', $name . ' checked in · ' . $at->timezone('Africa/Lagos')->format('H:i') . ' (' . $how . ')');

        if ($data['method'] === 'manual') {
            $this->askFamilyToConfirm($visit, $checkIn);
        } else {
            $this->notifier->sendMany($this->visits->familyUsers($visit->booking), 'cavalcare', 'visits', 'visit_checked_in',
                'Checked in', $name . ' checked in at ' . $at->timezone('Africa/Lagos')->format('H:i') . '.', ['visit_id' => $visit->id]);
        }

        return ['result' => 'accepted', 'check_in' => $checkIn];
    }

    /** "Has Nurse Adaeze arrived?" to Owners and family Caregivers (spec 5.1). */
    private function askFamilyToConfirm(Visit $visit, VisitCheckIn $checkIn): void
    {
        $family = $this->visits->familyUsers($visit->booking, includeViewers: false);
        $checkIn->update(['confirmation_asked_at' => now(), 'confirmation_asked_to' => $family->first()?->id]);

        $who = $visit->patient->address_as ?: $visit->patient->display_name ?: 'your family member';
        $this->notifier->sendMany($family, 'cavalcare', 'visits', 'arrival_confirmation_requested',
            'Has ' . $visit->professional->name . ' arrived?', 'Please confirm they are with ' . $who . ' now.', ['visit_id' => $visit->id], urgent: true);
    }

    /** The family's answer. "No" disputes the check-in: coordinators alerted, pay held. */
    public function confirmArrival(Visit $visit, User $by, string $answer): VisitCheckIn
    {
        $checkIn = $visit->checkIn;
        if (! $checkIn || $checkIn->method !== 'manual') {
            throw ValidationException::withMessages(['visit' => 'There is no arrival to confirm for this visit.']);
        }
        if ($checkIn->confirmation_answer) {
            return $checkIn;
        }

        $checkIn->update([
            'confirmation_answer'      => $answer,
            'confirmation_answered_by' => $by->id,
            'confirmation_answered_at' => now(),
            'disputed'                 => $answer === 'no',
        ]);

        if ($answer === 'no') {
            $visit->update(['pay_hold_reason' => 'Family said the caregiver had not arrived']);
            $this->notifyCoordinators('arrival_disputed', 'Arrival disputed', 'A family says ' . $visit->professional->name . ' has not arrived. Call them now.', ['visit_id' => $visit->id], true);
        }

        return $checkIn;
    }

    /** POST /pro/visits/{id}/submit: check-out GPS and the report. */
    public function submit(Visit $visit, array $p): Visit
    {
        $data = Validator::make($p, [
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'recorded_at' => ['required', 'date'],
        ])->validate();

        if ($visit->isSubmitted()) {
            return $visit;
        }
        if ($visit->state !== 'checked_in') {
            throw ValidationException::withMessages(['visit' => 'Check in before submitting the report.']);
        }

        // Every planned task needs an outcome; "not done" needs a reason (spec 8.4).
        $planned = collect(\App\Models\Care\PatientCarePlan::where('patient_id', $visit->patient_id)->value('tasks') ?? [])->pluck('id');
        $done = TaskEvent::where('visit_id', $visit->id)->get()->keyBy('task_id');
        $missing = $planned->reject(fn ($id) => $done->has($id))->values();
        $noReason = $done->filter(fn ($t) => $t->state === 'not_done' && blank($t->reason))->keys();
        if ($missing->isNotEmpty() || $noReason->isNotEmpty()) {
            abort(\App\Support\ApiError::make('report_incomplete', 'Mark every task done or not done (with a reason) before submitting.', 422, [
                'missing_tasks' => $missing->all(), 'tasks_needing_reason' => $noReason->values()->all(),
            ]));
        }

        $at = Carbon::parse($data['recorded_at']);
        DB::transaction(function () use ($visit, $data, $at) {
            VisitCheckOut::create([
                'visit_id'   => $visit->id,
                'at'         => $at,
                'lat'        => $data['lat'] ?? null,
                'lng'        => $data['lng'] ?? null,
                'distance_m' => $this->distanceM($visit, $data['lat'] ?? null, $data['lng'] ?? null),
            ]);

            $hold = $visit->checkIn?->disputed ? 'Disputed check-in' : ($at->gt($visit->scheduled_end->copy()->addDay()) ? 'Report more than 24 hours late' : null);
            $visit->update([
                'state'               => 'checked_out',
                'checked_out_at'      => $at,
                'report_submitted_at' => now(),
                'pay_state'           => $hold ? 'on_hold' : 'ready',
                'pay_hold_reason'     => $hold ?? $visit->pay_hold_reason,
            ]);

            $booking = $visit->booking;
            if ($booking->status === 'matched') {
                $booking->update(['status' => 'active']);
            }
            if (! Visit::where('care_booking_id', $booking->id)->whereNull('report_submitted_at')->whereNotIn('state', ['cancelled', 'missed'])->exists()) {
                $booking->update(['status' => 'completed']);
            }
        });

        $who = $visit->patient->address_as ?: $visit->patient->display_name ?: 'Your family member';
        $this->visits->system($this->visits->threadFor($visit), 'report_ready', 'Visit report ready.');
        $this->notifier->sendMany($this->visits->familyUsers($visit->booking), 'cavalcare', 'reports', 'visit_report_submitted',
            'Visit report ready', $who . '\'s visit report is ready.', ['visit_id' => $visit->id]);

        return $visit->fresh();
    }

    // ── Sync (the outbox) ───────────────────────────────────────────────

    /**
     * Applies up to 200 queued items in order, in one transaction per visit.
     * @return array<int, array{client_id:string, result:string, code?:string, message?:string}>
     */
    public function sync(Professional $pro, array $items): array
    {
        $results = [];
        $touched = [];

        foreach ($items as $item) {
            $clientId = (string) ($item['client_id'] ?? '');
            try {
                $visit = Visit::with(['professional.user', 'patient', 'booking', 'checkIn'])->find($item['visit_id'] ?? null);
                if (! $visit || $visit->professional_id !== $pro->id) {
                    $results[] = ['client_id' => $clientId, 'result' => 'rejected', 'code' => 'not_assigned'];
                    continue;
                }

                $result = DB::transaction(fn () => $this->apply($visit, $pro, (string) ($item['type'] ?? ''), (array) ($item['payload'] ?? []), $clientId, (string) ($item['recorded_at'] ?? '')));
                $results[] = ['client_id' => $clientId, 'result' => $result];
                $touched[$visit->id] = $visit;
            } catch (ValidationException $e) {
                $results[] = ['client_id' => $clientId, 'result' => 'rejected', 'code' => 'validation_failed', 'message' => collect($e->errors())->flatten()->first()];
            } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
                $body = json_decode($e->getResponse()->getContent(), true);
                $results[] = ['client_id' => $clientId, 'result' => 'rejected', 'code' => $body['error']['code'] ?? 'rejected', 'message' => $body['error']['message'] ?? null];
            }
        }

        // Recompute each visit's status once per batch, not once per item (spec 8.5).
        foreach ($touched as $visit) {
            $this->visits->recomputeStatus($visit->fresh());
        }

        return $results;
    }

    /** Applies one record. Returns "accepted" or "duplicate". */
    public function apply(Visit $visit, Professional $pro, string $type, array $p, string $clientId, string $recordedAt): string
    {
        if ($type === 'check_in') {
            return $this->checkIn($visit, $p + ['recorded_at' => $recordedAt], $clientId ?: null)['result'];
        }
        if ($type === 'amendment') {
            return $this->amend($visit, $pro, $p, $clientId, $recordedAt);
        }

        $models = [
            'task' => TaskEvent::class, 'vital' => VitalReading::class, 'medication' => MedicationEvent::class,
            'observation' => Observation::class, 'concern' => Concern::class, 'care_note' => CareNote::class,
        ];
        if (! isset($models[$type])) {
            throw ValidationException::withMessages(['type' => 'Unknown record type.']);
        }
        if (! \Illuminate\Support\Str::isUuid($clientId)) {
            throw ValidationException::withMessages(['client_id' => 'client_id must be a UUID made on the phone.']);
        }
        if ($models[$type]::where('client_id', $clientId)->exists()) {
            return 'duplicate';
        }
        if ($visit->isSubmitted()) {
            abort(\App\Support\ApiError::make('visit_already_submitted', 'The report is submitted. Send an amendment instead.', 409));
        }
        if ($visit->state !== 'checked_in') {
            throw ValidationException::withMessages(['visit' => 'Check in first.']);
        }

        $at = Carbon::parse($recordedAt ?: now());
        $base = [
            'client_id'        => $clientId,
            'visit_id'         => $visit->id,
            'recorded_at'      => $at,
            'synced_at'        => now(),
            'clock_drift_flag' => abs($at->diffInMinutes(now(), false)) > self::DRIFT_MINUTES,
            'recorded_by'      => $pro->id,
        ];

        match ($type) {
            'task'        => $this->task($visit, $p, $base),
            'vital'       => $this->vital($visit, $pro, $p, $base),
            'medication'  => $this->medication($pro, $p, $base),
            'observation' => Observation::create($base + Validator::make($p, [
                'mood' => ['nullable', 'string', 'max:20'], 'mobility' => ['nullable', 'string', 'max:20'],
                'appetite' => ['nullable', 'string', 'max:20'], 'water_glasses' => ['nullable', 'integer', 'between:0,30'],
                'pain_score' => ['nullable', 'integer', 'between:0,10'], 'skin' => ['nullable', 'array'], 'skin.*' => ['string', 'max:40'],
            ])->validate()),
            'concern'     => $this->concern($visit, $p, $base),
            'care_note'   => CareNote::create($base + Validator::make($p, ['text' => ['required', 'string', 'max:5000']])->validate()),
        };

        return 'accepted';
    }

    private function task(Visit $visit, array $p, array $base): void
    {
        $d = Validator::make($p, [
            'task_id' => ['required', 'string', 'max:64'], 'state' => ['required', Rule::in(['done', 'not_done'])],
            'reason' => ['required_if:state,not_done', 'nullable', 'string', 'max:255'],
        ])->validate();
        $title = collect(\App\Models\Care\PatientCarePlan::where('patient_id', $visit->patient_id)->value('tasks') ?? [])->firstWhere('id', $d['task_id'])['title'] ?? null;
        TaskEvent::create($base + $d + ['task_title' => $title]);
    }

    private function vital(Visit $visit, Professional $pro, array $p, array $base): void
    {
        $d = Validator::make($p, [
            'type' => ['required', Rule::in(['bp', 'pulse', 'sugar', 'spo2', 'temp', 'resp', 'pain'])],
            'value' => ['required', 'string', 'max:20'], 'unit' => ['nullable', 'string', 'max:12'],
            'context' => ['nullable', Rule::in(['fasting', 'after_food'])], 'recheck_of' => ['nullable', 'uuid'],
        ])->validate();

        // Carers may record only the vitals the care plan allows (spec 3.2).
        if ($pro->title === 'carer') {
            $allowed = \App\Models\Care\PatientCarePlan::where('patient_id', $visit->patient_id)->value('carer_allowed_vitals') ?? [];
            if (! in_array($d['type'], $allowed, true)) {
                abort(\App\Support\ApiError::make('role_not_allowed', 'This reading needs a registered nurse.', 403));
            }
        }

        VitalReading::create($base + $d + ['patient_id' => $visit->patient_id, 'out_of_range' => $this->outOfRange($d['type'], $d['value'])]);
    }

    private function medication(Professional $pro, array $p, array $base): void
    {
        $d = Validator::make($p, [
            'medication_id' => ['nullable', 'uuid'], 'medication_name' => ['required', 'string', 'max:255'],
            'outcome' => ['required', Rule::in(['given', 'assisted', 'refused', 'not_available'])],
            'ate_with' => ['nullable', 'string', 'max:40'], 'reason' => ['required_if:outcome,refused', 'nullable', 'string', 'max:255'],
        ])->validate();

        // Only registered nurses mark "given"; carers mark "assisted" (spec 3.2).
        if ($d['outcome'] === 'given' && $pro->title !== 'rn') {
            abort(\App\Support\ApiError::make('role_not_allowed', 'Carers mark medicines as "assisted", not "given".', 403));
        }

        MedicationEvent::create($base + $d);

        if ($d['outcome'] === 'not_available') {
            $visit = \App\Models\Care\Visit::find($base['visit_id']);
            $who = $visit->patient->address_as ?: $visit->patient->display_name ?: 'Your family member';
            $this->notifier->sendMany($this->visits->familyUsers($visit->booking, false), 'cavalcare', 'visits', 'medication_not_available',
                'Medicine running out', $who . '\'s ' . $d['medication_name'] . ' is finished.', ['visit_id' => $visit->id]);
        }
    }

    private function concern(Visit $visit, array $p, array $base): void
    {
        $d = Validator::make($p, [
            'kind' => ['required', Rule::in(['swelling', 'fall', 'breathing', 'chest_pain', 'confusion', 'wound', 'other'])],
            'where_on_body' => ['nullable', 'string', 'max:120'], 'description' => ['nullable', 'string', 'max:2000'],
            'action_taken' => ['nullable', 'string', 'max:2000'], 'severity' => ['required', Rule::in(['attention', 'urgent'])],
            'upload_ids' => ['nullable', 'array', 'max:5'], 'upload_ids.*' => ['uuid'],
        ])->validate();

        $paths = \App\Models\Upload::whereIn('id', $d['upload_ids'] ?? [])->whereNull('attached_at')
            ->where('user_id', $visit->professional->user_id)->get()
            ->each(fn ($u) => $u->update(['attached_at' => now()]))->pluck('path')->all();

        Concern::create($base + collect($d)->except('upload_ids')->all() + ['patient_id' => $visit->patient_id, 'photo_paths' => $paths ?: null]);

        // Coordinators now; the family once a coordinator acknowledges it (spec 9.2).
        $this->notifyCoordinators('concern_raised', $d['severity'] === 'urgent' ? 'URGENT concern' : 'New concern',
            ucfirst(str_replace('_', ' ', $d['kind'])) . ' reported by ' . $visit->professional->name . '.', ['visit_id' => $visit->id], $d['severity'] === 'urgent');
    }

    private function amend(Visit $visit, Professional $pro, array $p, string $clientId, string $recordedAt): string
    {
        if (Amendment::where('client_id', $clientId)->exists()) {
            return 'duplicate';
        }
        $d = Validator::make($p, [
            'record_type' => ['required', Rule::in(['task', 'vital', 'medication', 'observation', 'care_note'])],
            'record_id' => ['required', 'uuid'], 'field' => ['required', 'string', 'max:40'],
            'new_value' => ['required', 'string', 'max:2000'], 'reason' => ['required', 'string', 'max:255'],
        ])->validate();
        if (! $visit->isSubmitted()) {
            throw ValidationException::withMessages(['visit' => 'Amendments are for submitted reports. Send the record again instead.']);
        }

        $model = ['task' => TaskEvent::class, 'vital' => VitalReading::class, 'medication' => MedicationEvent::class, 'observation' => Observation::class, 'care_note' => CareNote::class][$d['record_type']];
        $record = $model::where('visit_id', $visit->id)->find($d['record_id']);
        if (! $record) {
            throw ValidationException::withMessages(['record_id' => 'That record is not part of this visit.']);
        }

        Amendment::create([
            'client_id' => $clientId, 'visit_id' => $visit->id, 'record_type' => $d['record_type'], 'record_id' => $record->id,
            'field' => $d['field'], 'old_value' => (string) ($record->{$d['field']} ?? ''), 'new_value' => $d['new_value'],
            'reason' => $d['reason'], 'by' => $pro->id, 'at' => Carbon::parse($recordedAt ?: now()),
        ]);

        return 'accepted';
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    public function outOfRange(string $type, string $value): bool
    {
        $r = config('cavalcare.default_vital_ranges');
        if ($type === 'bp') {
            if (! preg_match('/^(\d{2,3})\s*\/\s*(\d{2,3})$/', $value, $m)) {
                return false;
            }
            return $m[1] < $r['bp_sys'][0] || $m[1] > $r['bp_sys'][1] || $m[2] < $r['bp_dia'][0] || $m[2] > $r['bp_dia'][1];
        }
        if (! isset($r[$type]) || ! is_numeric($value)) {
            return false;
        }

        return (float) $value < $r[$type][0] || (float) $value > $r[$type][1];
    }

    private function distanceM(Visit $visit, $lat, $lng): ?int
    {
        $p = $visit->patient;
        if ($lat === null || $lng === null || $p->lat === null || $p->lng === null) {
            return null;
        }
        $dLat = deg2rad($p->lat - $lat);
        $dLng = deg2rad($p->lng - $lng);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat)) * cos(deg2rad($p->lat)) * sin($dLng / 2) ** 2;

        return (int) round(6371000 * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    public function notifyCoordinators(string $event, string $title, string $body, array $data, bool $urgent = false): void
    {
        $staff = User::whereHas('roles', fn ($q) => $q->whereIn('name', [OsRoles::COORDINATOR, OsRoles::SUPER_ADMIN, OsRoles::LEGACY_ADMIN]))->get();
        foreach ($staff as $u) {
            // Staff see these in Caval OS (in-app inbox); no phone push for staff yet.
            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                'id' => (string) \Illuminate\Support\Str::uuid(), 'type' => $event, 'notifiable_type' => User::class, 'notifiable_id' => $u->id,
                'data' => json_encode(['app' => 'os', 'title' => $title, 'body' => $body, 'urgent' => $urgent] + $data),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
