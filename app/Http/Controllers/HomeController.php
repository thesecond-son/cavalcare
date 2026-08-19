<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarePlan;
use App\Models\CareType;

class HomeController extends Controller
{
    public function index()
{
    // Care plans are no longer listed flat on the homepage — they now
    // live grouped under each care type on the /services page.
    $careTypes = CareType::query()
    ->with([
        'carePlans' => fn ($q) => $q->orderBy('name'),
    ])
    ->orderBy('sort_order')
    ->orderBy('name')
    ->get();

    return view('home', compact('careTypes'));
}

}