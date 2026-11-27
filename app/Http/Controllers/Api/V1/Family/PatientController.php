<?php

namespace App\Http\Controllers\Api\V1\Family;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PatientResource;
use App\Models\Care\Patient;
use App\Models\Care\PatientMember;
use App\Support\ApiError;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * "Add parent" and the parent profile (backend spec 7.2).
 * Step 1 details -> POST /family/patients
 * Step 2 health  -> PUT  /family/patients/{id}/health
 * Step 3 consent -> POST /family/patients/{id}/consent
 *
 * Every patient is reached through the caller's family-circle membership.
 * Owner and Caregiver may edit; Viewer reads only. Not a member = 404.
 */
class PatientController extends Controller
{
    private const DETAILS = [
        'full_name'      => ['string', 'max:255'],
        'display_name'   => ['nullable', 'string', 'max:80'],
        'address_as'     => ['nullable', 'string', 'max:40'],
        'pronoun'        => ['nullable', 'in:she,he,they'],
        'date_of_birth'  => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
        'age'            => ['nullable', 'integer', 'min:0', 'max:120'],
        'sex'            => ['nullable', 'in:female,male'],
        'phone'          => ['nullable', 'string', 'max:20'],
        'address_line'   => ['nullable', 'string', 'max:500'],
        'address_note'   => ['nullable', 'string', 'max:255'],
        'area'           => ['nullable', 'string', 'max:120'],
        'city'           => ['nullable', 'string', 'max:120'],
        'state'          => ['nullable', 'string', 'max:120'],
        'lat'            => ['nullable', 'numeric', 'between:-90,90'],
        'lng'            => ['nullable', 'numeric', 'between:-180,180'],
        'place_id'       => ['nullable', 'string', 'max:255'],
        'languages'      => ['nullable', 'array', 'max:10'],
        'languages.*'    => ['string', 'max:40'],
        'mobility_level' => ['nullable', 'in:independent,assisted,bedridden'],
    ];

    /** GET /family/patients: everyone this user is in the family circle of. */
    public function index(Request $request)
    {
        $patients = Patient::whereHas('members', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['members', 'allergies'])
            ->orderBy('full_name')
            ->get();

        return response()->json(['data' => PatientResource::collection($patients)]);
    }

    /** POST /family/patients: Add parent step 1. The caller becomes the Owner. */
    public function store(Request $request)
    {
        $rules = self::DETAILS;
        $rules['full_name'] = ['required', 'string', 'max:255'];
        $rules['relationship'] = ['nullable', 'string', 'max:30'];   // the caller to the patient: daughter, son...
        $rules['city_of_member'] = ['nullable', 'string', 'max:120']; // where the caller lives
        $data = $request->validate($rules);

        $patient = DB::transaction(function () use ($data, $request) {
            $patient = Patient::create($this->fill($data) + ['source' => 'app', 'created_by' => $request->user()->id]);
            $patient->members()->create([
                'user_id'       => $request->user()->id,
                'role'          => 'owner',
                'relationship'  => $data['relationship'] ?? null,
                'city'          => $data['city_of_member'] ?? null,
                'invite_status' => 'accepted',
                'joined_at'     => now(),
            ]);

            return $patient;
        });

        return response()->json(['data' => new PatientResource($patient->load('members'))], 201);
    }

    /** GET /family/patients/{id}: full profile. */
    public function show(Request $request, string $id)
    {
        $patient = $this->find($request, $id);
        $patient->load(['allergies', 'conditions', 'medications', 'emergencyContacts']);

        return response()->json(['data' => new PatientResource($patient)]);
    }

    /** PATCH /family/patients/{id}: edit details or address. Owner or Caregiver. */
    public function update(Request $request, string $id)
    {
        $patient = $this->find($request, $id, edit: true);
        $rules = collect(self::DETAILS)->map(fn ($r) => array_merge(['sometimes'], $r))->all();
        $data = $request->validate($rules);

        $patient->update($this->fill($data));

        return response()->json(['data' => new PatientResource($patient->fresh()->load(['members', 'allergies', 'conditions', 'medications', 'emergencyContacts']))]);
    }

    /**
     * PUT /family/patients/{id}/health: Add parent step 2.
     * Replaces conditions, allergies and medicines with what is sent.
     */
    public function health(Request $request, string $id)
    {
        $patient = $this->find($request, $id, edit: true);
        $data = $request->validate([
            'conditions'                => ['present', 'array', 'max:30'],
            'conditions.*'              => ['string', 'max:255'],
            'allergies'                 => ['present', 'array', 'max:30'],
            'allergies.*.name'          => ['required', 'string', 'max:255'],
            'allergies.*.reaction'      => ['nullable', 'string', 'max:255'],
            'allergies.*.severe'        => ['nullable', 'boolean'],
            'medications'               => ['present', 'array', 'max:40'],
            'medications.*.name'        => ['required', 'string', 'max:255'],
            'medications.*.dose'        => ['nullable', 'string', 'max:120'],
            'medications.*.instructions' => ['nullable', 'string', 'max:500'],
            'medications.*.times'       => ['nullable', 'array', 'max:8'],
            'medications.*.times.*'     => ['date_format:H:i'],
            'medications.*.with_food'   => ['nullable', 'boolean'],
            'mobility_level'            => ['nullable', 'in:independent,assisted,bedridden'],
        ]);

        DB::transaction(function () use ($patient, $data) {
            $patient->conditions()->delete();
            $patient->allergies()->delete();
            $patient->medications()->delete();

            foreach (array_unique(array_map('trim', $data['conditions'])) as $name) {
                if ($name !== '') {
                    $patient->conditions()->create(['name' => $name]);
                }
            }
            foreach ($data['allergies'] as $a) {
                $patient->allergies()->create(['name' => trim($a['name']), 'reaction' => $a['reaction'] ?? null, 'severe' => (bool) ($a['severe'] ?? false)]);
            }
            foreach ($data['medications'] as $m) {
                $patient->medications()->create([
                    'name' => trim($m['name']), 'dose' => $m['dose'] ?? null, 'instructions' => $m['instructions'] ?? null,
                    'schedule' => ['times' => array_values($m['times'] ?? []), 'with_food' => $m['with_food'] ?? null],
                ]);
            }
            if (array_key_exists('mobility_level', $data)) {
                $patient->update(['mobility_level' => $data['mobility_level']]);
            }
        });

        return $this->show($request, $id);
    }

    /** PUT /family/patients/{id}/emergency-contacts: replaces the list (up to 5). */
    public function emergencyContacts(Request $request, string $id)
    {
        $patient = $this->find($request, $id, edit: true);
        $data = $request->validate([
            'contacts'            => ['present', 'array', 'max:5'],
            'contacts.*.name'     => ['required', 'string', 'max:255'],
            'contacts.*.relation' => ['nullable', 'string', 'max:60'],
            'contacts.*.phone'    => ['required', 'string', 'max:30'],
            'contacts.*.location' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data['contacts'] as $i => $c) {
            if (! Phone::toE164($c['phone'])) {
                return ApiError::make('validation_failed', 'Some details need checking.', 422, [
                    "contacts.{$i}.phone" => ['Enter the number with its country code, e.g. +234 803 123 4567.'],
                ]);
            }
        }

        DB::transaction(function () use ($patient, $data) {
            $patient->emergencyContacts()->delete();
            foreach ($data['contacts'] as $c) {
                $patient->emergencyContacts()->create([
                    'name' => $c['name'], 'relation' => $c['relation'] ?? null,
                    'phone_e164' => Phone::toE164($c['phone']), 'location' => $c['location'] ?? null,
                ]);
            }
        });

        return $this->show($request, $id);
    }

    /** POST /family/patients/{id}/consent: Add parent step 3. Stores who agreed and when. */
    public function consent(Request $request, string $id)
    {
        $patient = $this->find($request, $id, edit: true);
        $request->validate([
            'agreed'       => ['required', 'accepted'],
            'text_version' => ['required', 'string', 'max:20'],
        ]);

        $patient->forceFill([
            'consent_given_at' => now(),
            'consent_by'       => $request->user()->id,
        ])->save();

        return $this->show($request, $id);
    }

    /** GET /family/patients/{id}/members: the family circle. */
    public function members(Request $request, string $id)
    {
        $patient = $this->find($request, $id);
        $me = $request->user()->id;

        return response()->json(['data' => $patient->members()->with('user')->get()->map(fn (PatientMember $m) => [
            'id'                 => $m->id,
            'name'               => $m->user?->name,
            'role'               => $m->role,
            'relationship'       => $m->relationship,
            'city'               => $m->city,
            'can_view_documents' => $m->can_view_documents,
            'is_you'             => $m->user_id === $me,
        ])]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /** The patient, if the caller is in its family circle (and may edit, when asked). */
    private function find(Request $request, string $id, bool $edit = false): Patient
    {
        $patient = Patient::with('members')->find($id);
        $member = $patient?->members->firstWhere('user_id', $request->user()->id);

        abort_unless($member, 404);
        if ($edit && ! in_array($member->role, ['owner', 'caregiver'], true)) {
            abort(ApiError::make('role_not_allowed', 'Only the owner or a family caregiver can change this.', 403));
        }

        return $patient;
    }

    /** Maps request fields to patient columns. */
    private function fill(array $data): array
    {
        $map = collect($data)->only([
            'full_name', 'display_name', 'address_as', 'pronoun', 'date_of_birth', 'sex', 'address_line',
            'address_note', 'area', 'city', 'state', 'lat', 'lng', 'place_id', 'languages', 'mobility_level',
        ])->all();

        if (array_key_exists('age', $data)) {
            $map['age_years'] = $data['age'];
        }
        if (array_key_exists('phone', $data)) {
            $map['phone_e164'] = Phone::toE164($data['phone']);
        }

        return $map;
    }
}
