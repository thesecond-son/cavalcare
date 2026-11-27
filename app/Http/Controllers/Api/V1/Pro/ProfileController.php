<?php

namespace App\Http\Controllers\Api\V1\Pro;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProfessionalResource;
use App\Models\Care\Professional;
use App\Models\Upload;
use App\Models\User;
use App\Support\ApiError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * CavalPro onboarding and profile (backend spec 8.1).
 * Step 2 About you -> POST /pro/profile/about
 * Checklist        -> GET  /pro/verification
 * Uploads          -> POST /pro/credentials (after POST /uploads)
 * Step 4           -> PUT  /pro/availability
 */
class ProfileController extends Controller
{
    /** POST /pro/profile/about: creates (or updates) the caregiver profile and starts the ID check. */
    public function about(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'title'       => ['required', Rule::in(['rn', 'carer'])],
            'sex'         => ['nullable', Rule::in(['female', 'male'])],
            'languages'   => ['nullable', 'array', 'max:10'],
            'languages.*' => ['string', 'max:40'],
            'home_area'   => ['required', 'string', 'max:120'],
            'lat'         => ['nullable', 'numeric', 'between:-90,90'],
            'lng'         => ['nullable', 'numeric', 'between:-180,180'],
            // NMCN licence number for nurses, NIN for carers.
            'id_number'   => ['required', 'string', 'max:40'],
        ]);

        $pro = DB::transaction(function () use ($request, $data) {
            $user = $request->user();
            $user->forceFill(['name' => $data['name'], 'type' => User::TYPE_PROFESSIONAL])->save();

            $pro = Professional::firstOrNew(['user_id' => $user->id]);
            if ($pro->exists && $pro->status === 'active' && $pro->title !== $data['title']) {
                abort(ApiError::make('title_locked', 'Your role is verified. Contact CavalCare to change it.', 409));
            }
            $pro->fill(collect($data)->only(['title', 'sex', 'languages', 'home_area', 'lat', 'lng'])->all())->save();

            $type = $data['title'] === 'rn' ? 'nmcn' : 'nin';
            $cred = $pro->credentials()->firstOrNew(['type' => $type]);
            if (! in_array($cred->status, ['verified', 'expiring'], true)) {
                $cred->fill(['number' => trim($data['id_number'])]);
                $cred->status = $cred->file_path ? 'in_progress' : 'to_do';
                $cred->save();
            }

            $pro->syncStatusWithCredentials();

            return $pro;
        });

        return response()->json(['data' => new ProfessionalResource($pro->fresh(['user', 'credentials', 'availabilityRules']))], 201);
    }

    /** GET /pro/profile */
    public function show(Request $request)
    {
        return response()->json(['data' => new ProfessionalResource($this->pro($request)->load('availabilityRules'))]);
    }

    /** PATCH /pro/profile: skills, languages, photo-free details, data saver. */
    public function update(Request $request)
    {
        $pro = $this->pro($request);
        $data = $request->validate([
            'bio'                  => ['sometimes', 'nullable', 'string', 'max:1000'],
            'years_experience'     => ['sometimes', 'nullable', 'integer', 'min:0', 'max:60'],
            'languages'            => ['sometimes', 'array', 'max:10'],
            'languages.*'          => ['string', 'max:40'],
            'skills'               => ['sometimes', 'array', 'max:30'],
            'skills.*'             => ['string', 'max:60'],
            'specialties'          => ['sometimes', 'array', 'max:20'],
            'specialties.*'        => ['string', 'max:60'],
            'home_area'            => ['sometimes', 'string', 'max:120'],
            'lat'                  => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'lng'                  => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'max_travel_km'        => ['sometimes', 'integer', 'min:1', 'max:100'],
            'open_to_extra_shifts' => ['sometimes', 'boolean'],
            'data_saver'           => ['sometimes', 'boolean'],
        ]);

        $pro->update($data);

        return $this->show($request);
    }

    /** GET /pro/verification: the checklist and whether shifts can be taken yet. */
    public function verification(Request $request)
    {
        $pro = $this->pro($request);
        [$done, $total] = $pro->verificationProgress();

        return response()->json(['data' => [
            'status'          => $pro->status,
            'can_take_shifts' => $pro->status === 'active',
            'verified'        => $done,
            'required'        => $total,
            'suspended_reason' => $pro->status === 'suspended' ? 'Your account is paused. Please contact CavalCare.' : null,
            'items'           => ProfessionalResource::checklist($pro),
        ]]);
    }

    /**
     * POST /pro/credentials: add or replace a document for one credential.
     * Sends it for review (status in_progress). A verified credential can be
     * replaced only when it is expiring or expired.
     */
    public function credential(Request $request)
    {
        $pro = $this->pro($request);
        $data = $request->validate([
            'type'      => ['required', Rule::in(['nmcn', 'nin', 'bls', 'reference', 'background', 'training'])],
            'upload_id' => ['required', 'uuid'],
            'number'    => ['nullable', 'string', 'max:40'],
            'valid_to'  => ['nullable', 'date', 'after:today'],
        ]);

        $upload = Upload::where('id', $data['upload_id'])->where('user_id', $request->user()->id)->first();
        if (! $upload) {
            return ApiError::make('validation_failed', 'Upload the file first.', 422, ['upload_id' => ['That upload was not found.']]);
        }
        if ($upload->attached_at) {
            return ApiError::make('validation_failed', 'That file is already in use.', 422, ['upload_id' => ['Upload the file again.']]);
        }

        $cred = $pro->credentials()->firstOrNew(['type' => $data['type']]);
        if ($cred->status === 'verified' && (! $cred->valid_to || $cred->valid_to->gt(now()->addDays(30)))) {
            return ApiError::make('already_verified', 'This is already verified.', 409);
        }

        DB::transaction(function () use ($cred, $data, $upload, $pro) {
            $cred->fill(array_filter([
                'number'   => $data['number'] ?? null,
                'valid_to' => $data['valid_to'] ?? null,
            ], fn ($v) => $v !== null));
            $cred->forceFill([
                'file_path'        => $upload->path,
                'status'           => 'in_progress',
                'rejection_reason' => null,
                'verified_by'      => null,
                'verified_at'      => null,
            ])->save();

            $upload->forceFill(['attached_at' => now()])->save();
            $pro->syncStatusWithCredentials();
        });

        return $this->verification($request);
    }

    /** POST /pro/background-check/consent: agrees to the check; compliance runs it (manual in MVP). */
    public function backgroundConsent(Request $request)
    {
        $pro = $this->pro($request);
        $request->validate(['agreed' => ['required', 'accepted']]);

        $cred = $pro->credentials()->firstOrNew(['type' => 'background']);
        if (! in_array($cred->status, ['verified', 'expiring'], true)) {
            $cred->forceFill(['status' => 'in_progress', 'rejection_reason' => null])->save();
        }
        $pro->syncStatusWithCredentials();

        return $this->verification($request);
    }

    /** POST /pro/training/complete: CavalCare training done (score optional); compliance confirms. */
    public function trainingComplete(Request $request)
    {
        $pro = $this->pro($request);
        $data = $request->validate(['score' => ['nullable', 'integer', 'min:0', 'max:100']]);

        $cred = $pro->credentials()->firstOrNew(['type' => 'training']);
        if (! in_array($cred->status, ['verified', 'expiring'], true)) {
            $cred->forceFill([
                'status' => 'in_progress',
                'number' => isset($data['score']) ? 'Quiz score ' . $data['score'] . '%' : null,
                'rejection_reason' => null,
            ])->save();
        }
        $pro->syncStatusWithCredentials();

        return $this->verification($request);
    }

    /** PUT /pro/availability: the weekly pattern (replaces it) and extra-shift preference. */
    public function availability(Request $request)
    {
        $pro = $this->pro($request);
        $data = $request->validate([
            'rules'                => ['present', 'array', 'max:21'],
            'rules.*.weekday'      => ['required', 'integer', 'between:1,7'],
            'rules.*.start_time'   => ['required', 'date_format:H:i'],
            'rules.*.end_time'     => ['required', 'date_format:H:i'],
            'open_to_extra_shifts' => ['sometimes', 'boolean'],
        ]);

        foreach ($data['rules'] as $i => $r) {
            if ($r['end_time'] <= $r['start_time'] && $r['end_time'] !== '00:00') {
                return ApiError::make('validation_failed', 'Some details need checking.', 422, [
                    "rules.{$i}.end_time" => ['The end time must be after the start time (use 00:00 for midnight).'],
                ]);
            }
        }

        DB::transaction(function () use ($pro, $data) {
            $pro->availabilityRules()->delete();
            foreach ($data['rules'] as $r) {
                $pro->availabilityRules()->create($r);
            }
            if (array_key_exists('open_to_extra_shifts', $data)) {
                $pro->update(['open_to_extra_shifts' => $data['open_to_extra_shifts']]);
            }
        });

        return $this->show($request);
    }

    /** The caller's caregiver profile; 404 profile_missing until "About you" is done. */
    private function pro(Request $request): Professional
    {
        $pro = Professional::with(['user', 'credentials'])->where('user_id', $request->user()->id)->first();
        if (! $pro) {
            abort(ApiError::make('profile_missing', 'Finish "About you" first.', 404));
        }

        return $pro;
    }
}
