<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarePlan;
use App\Models\CareType;

class ServiceController extends Controller
{
    public function index()
    {
        $careTypes = CareType::query()
            ->with([
                'carePlans' => fn ($q) => $q->orderBy('name'),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    
        return view('services.index', compact('careTypes'));
    }
}