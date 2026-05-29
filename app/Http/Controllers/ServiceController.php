<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarePlan;
use App\Models\CareType;

class ServiceController extends Controller
{
    public function index()
{
    $carePlans = CarePlan::orderBy('price')->get();

    $plans = CarePlan::query()
    ->get()
    ->map(function ($plan) {
        $plan->care_types = \App\Models\CareType::whereIn(
            'id',
            data_get($plan->meta, 'recommended_for', [])
        )->pluck('name');

        return $plan;
    });

    $careTypes = CareType::query()
        ->whereNotNull('meta->description')
        ->get();


    return view('services.index', compact('carePlans', 'careTypes', 'plans'));
}
}
