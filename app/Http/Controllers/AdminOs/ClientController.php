<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\Care\Patient;
use App\Support\AdminOs\Audit;
use Illuminate\Http\Request;

/**
 * Clients: the people being cared for, with their family circle,
 * health basics and bookings. Data comes from the family app and from
 * website intake forms.
 */
class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $filter = $request->query('filter');

        $clients = Patient::query()
            ->with(['members.user', 'preBookings' => fn ($q) => $q->latest()->with('carePlan', 'careType')])
            ->withCount(['allergies', 'allergies as severe_allergies_count' => fn ($q) => $q->where('severe', true)])
            ->when($filter === 'app', fn ($q) => $q->has('members'))
            ->when($filter === 'website', fn ($q) => $q->doesntHave('members'))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';
                $q->where(fn ($w) => $w
                    ->where('full_name', 'like', $like)
                    ->orWhere('display_name', 'like', $like)
                    ->orWhere('area', 'like', $like)
                    ->orWhere('address_line', 'like', $like)
                    ->orWhereHas('members.user', fn ($u) => $u->where('name', 'like', $like)->orWhere('email', 'like', $like)));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'all'     => Patient::count(),
            'app'     => Patient::has('members')->count(),
            'website' => Patient::doesntHave('members')->count(),
        ];

        return view('adminos.clients.index', compact('clients', 'counts', 'search', 'filter'));
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'members.user', 'allergies', 'conditions', 'medications', 'emergencyContacts',
            'preBookings' => fn ($q) => $q->latest()->with('carePlan', 'careType'),
            'sourceBooking',
        ]);

        // Health data was opened: record who looked (spec 13.5).
        Audit::log('client.viewed', $patient, ['name' => $patient->full_name]);

        return view('adminos.clients.show', ['p' => $patient]);
    }
}
