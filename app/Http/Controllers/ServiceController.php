<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarePlan;
use App\Models\CareType;

class ServiceController extends Controller
{
    public function index()
{
    // Each care type carries its own ordered list of plans, so the view
    // can render "plans under each care type" instead of one flat list.
    $careTypes = CareType::query()
        ->with(['carePlans' => fn ($q) => $q->orderBy('price')])
        ->orderBy('name')
        ->get();

    return view('services.index', compact('careTypes'));
}
}