<?php

namespace App\Http\Controllers\Api\V1\Family;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CarePlanResource;
use App\Models\AddOn;
use App\Models\CareType;
use Illuminate\Http\Request;

/** Booking steps 1 and 2: care types, then the plans and prices for one type. */
class CatalogueController extends Controller
{
    /** GET /family/care-types */
    public function careTypes()
    {
        $types = CareType::withCount('carePlans')->orderBy('sort_order')->orderBy('name')->get()
            ->filter(fn ($t) => $t->care_plans_count > 0)->values();

        return response()->json(['data' => $types->map(fn (CareType $t) => [
            'id'          => $t->id,
            'slug'        => $t->slug,
            'name'        => $t->name,
            'description' => $t->description,
        ])]);
    }

    /** GET /family/care-types/{slug}/plans?level=caretaker|nursing */
    public function plans(Request $request, string $slug)
    {
        $type = CareType::where('slug', $slug)->firstOrFail();
        $level = $request->query('level');

        $plans = $type->carePlans()
            ->when(in_array($level, ['caretaker', 'nursing'], true), fn ($q) => $q->where('service_level', $level))
            ->get();

        return response()->json([
            'data' => CarePlanResource::collection($plans),
            'meta' => [
                'care_type'  => ['id' => $type->id, 'slug' => $type->slug, 'name' => $type->name],
                // reorder(): the relation's own ORDER BY can't be combined with DISTINCT on strict MySQL.
                'levels'     => $type->carePlans()->reorder()->distinct()->orderBy('service_level')->pluck('service_level')->values(),
                'price_note' => 'Care and nursing prices may vary depending on the complexity of the patient\'s needs.',
            ],
        ]);
    }

    /** GET /family/add-ons: optional services (wound dressing etc.) added to any plan. */
    public function addOns()
    {
        return response()->json(['data' => AddOn::where('is_active', true)->orderBy('price')->get()->map(fn (AddOn $a) => [
            'id'          => $a->id,
            'name'        => $a->name,
            'description' => $a->description,
            'price_kobo'  => (int) $a->price * 100,
            'price_from'  => (bool) $a->price_from,
            'display'     => $a->formatted_price ?? ('₦' . number_format($a->price)),
        ])]);
    }
}
