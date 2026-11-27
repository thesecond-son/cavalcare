<?php

namespace App\Http\Controllers\Api\V1\Family;

use App\Http\Controllers\Controller;
use App\Models\Care\Professional;
use Illuminate\Http\Request;

/**
 * Booking step 4, "I'll choose myself" (spec 7.4): verified caregivers only.
 * Never shows pay rates, phone numbers or exact locations.
 */
class ProfessionalController extends Controller
{
    /** GET /family/professionals?level=nursing|caretaker&sex=&language=&care_type_id= */
    public function index(Request $request)
    {
        $level = $request->query('level');
        $sex = $request->query('sex');
        $language = mb_strtolower((string) $request->query('language', ''));
        $careType = $request->query('care_type_id');

        $pros = Professional::with(['user', 'credentials'])
            ->where('status', 'active')
            ->when($level === 'nursing', fn ($q) => $q->where('title', 'rn'))
            ->when(in_array($sex, ['female', 'male'], true), fn ($q) => $q->where('sex', $sex))
            ->get()
            ->filter(fn (Professional $p) => $p->isFullyVerified())
            ->filter(fn (Professional $p) => $language === '' || in_array($language, array_map('mb_strtolower', $p->languages ?? []), true))
            ->filter(fn (Professional $p) => ! $careType || empty($p->care_types) || in_array((int) $careType, array_map('intval', $p->care_types), true))
            ->sortByDesc(fn (Professional $p) => [$p->rating_avg ?? 0, $p->visits_count])
            ->values();

        return response()->json(['data' => $pros->map(fn ($p) => $this->card($p))]);
    }

    /** GET /family/professionals/{id}: profile with licence and background-check status. */
    public function show(string $id)
    {
        $p = Professional::with(['user', 'credentials'])->where('status', 'active')->find($id);
        abort_unless($p && $p->isFullyVerified(), 404);

        $checks = $p->credentials->keyBy('type');

        return response()->json(['data' => $this->card($p) + [
            'bio'          => $p->bio,
            'specialties'  => $p->specialties ?? [],
            'checks'       => [
                'licence'    => $p->title === 'rn' ? ($checks->get('nmcn')?->countsAsVerified() ?? false) : null,
                'id_checked' => $p->title === 'carer' ? ($checks->get('nin')?->countsAsVerified() ?? false) : null,
                'background' => $checks->get('background')?->countsAsVerified() ?? false,
                'training'   => $checks->get('training')?->countsAsVerified() ?? false,
                'bls'        => $checks->get('bls')?->countsAsVerified() ?? false,
            ],
        ]]);
    }

    private function card(Professional $p): array
    {
        return [
            'id'               => $p->id,
            'name'             => $p->user?->name,
            'title'            => $p->title,
            'title_label'      => $p->title === 'rn' ? 'Registered nurse' : 'Caregiver',
            'sex'              => $p->sex,
            'years_experience' => $p->years_experience,
            'languages'        => $p->languages ?? [],
            'skills'           => $p->skills ?? [],
            'area'             => $p->home_area,
            'rating'           => $p->rating_avg,
            'reviews_count'    => $p->reviews_count,
            'visits_count'     => $p->visits_count,
        ];
    }
}
