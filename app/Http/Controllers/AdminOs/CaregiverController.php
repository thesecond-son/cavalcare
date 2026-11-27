<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\Care\Credential;
use App\Models\Care\Professional;
use App\Support\AdminOs\Audit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Caregivers: nurses and carers from CavalPro. Compliance reviews their
 * credentials here; a caregiver becomes Active once every required one
 * is verified, and can be suspended (and reinstated) with a reason.
 */
class CaregiverController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $title  = $request->query('title');
        $search = trim((string) $request->query('q', ''));

        $caregivers = Professional::query()
            ->with(['user', 'credentials'])
            ->when($status && isset(Professional::STATUSES[$status]), fn ($q) => $q->where('status', $status))
            ->when($title && isset(Professional::TITLES[$title]), fn ($q) => $q->where('title', $title))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';
                $q->where(fn ($w) => $w
                    ->where('home_area', 'like', $like)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('phone_e164', 'like', $like)));
            })
            ->orderByRaw("FIELD(status, 'verifying', 'applicant', 'active', 'suspended')")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = Professional::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');
        $awaitingReview = Credential::where('status', 'in_progress')->count();

        return view('adminos.caregivers.index', compact('caregivers', 'counts', 'status', 'title', 'search', 'awaitingReview'));
    }

    public function show(Professional $professional)
    {
        $professional->load(['user', 'credentials.verifier', 'availabilityRules']);
        Audit::log('caregiver.viewed', $professional, ['name' => $professional->name]);

        return view('adminos.caregivers.show', ['pro' => $professional]);
    }

    /** Shows an uploaded credential document (inline, never cached). Every view is logged. */
    public function document(Professional $professional, Credential $credential)
    {
        abort_unless($credential->professional_id === $professional->id && $credential->file_path, 404);
        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        abort_unless($disk->exists($credential->file_path), 404);

        Audit::log('caregiver.document_viewed', $credential, ['professional' => $professional->id, 'type' => $credential->type]);

        return $disk->response($credential->file_path, $credential->type . '-' . substr($professional->id, 0, 8), [
            'Cache-Control' => 'no-store, private',
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** Approve or reject one credential. */
    public function reviewCredential(Request $request, Professional $professional, Credential $credential)
    {
        abort_unless($credential->professional_id === $professional->id, 404);

        $data = $request->validate([
            'decision'         => ['required', Rule::in(['approve', 'reject'])],
            'valid_to'         => ['nullable', 'date', 'after:today'],
            'rejection_reason' => ['required_if:decision,reject', 'nullable', 'string', 'max:255'],
        ]);

        if ($data['decision'] === 'approve') {
            $credential->forceFill([
                'status'           => 'verified',
                'valid_to'         => $data['valid_to'] ?? $credential->valid_to,
                'verified_by'      => $request->user()->id,
                'verified_at'      => now(),
                'rejection_reason' => null,
            ])->save();
            $message = Credential::TYPES[$credential->type] . ' verified.';
        } else {
            $credential->forceFill([
                'status'           => 'rejected',
                'verified_by'      => $request->user()->id,
                'verified_at'      => now(),
                'rejection_reason' => $data['rejection_reason'],
            ])->save();
            $message = Credential::TYPES[$credential->type] . ' rejected. The caregiver will see the reason in CavalPro.';
        }

        Audit::log('caregiver.credential_' . ($data['decision'] === 'approve' ? 'verified' : 'rejected'), $credential, [
            'professional' => $professional->id,
            'type'         => $credential->type,
            'reason'       => $data['rejection_reason'] ?? null,
        ]);

        $before = $professional->status;
        $professional->syncStatusWithCredentials();
        if ($before !== 'active' && $professional->status === 'active') {
            $message .= ' All checks are done: ' . $professional->name . ' can now take shifts.';
        }

        return back()->with('os_flash', ['stable', $message]);
    }

    /** Suspend or reinstate, always with a reason. */
    public function updateStatus(Request $request, Professional $professional)
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['suspend', 'reinstate'])],
            'reason' => ['required_if:action,suspend', 'nullable', 'string', 'max:255'],
        ]);

        if ($data['action'] === 'suspend') {
            $professional->forceFill([
                'status' => 'suspended', 'suspended_reason' => $data['reason'], 'status_changed_at' => now(),
            ])->save();
            $message = $professional->name . ' is suspended and can\'t take new shifts.';
        } else {
            $professional->forceFill(['status' => 'verifying', 'suspended_reason' => null, 'status_changed_at' => now()])->save();
            $professional->syncStatusWithCredentials();
            $message = $professional->name . ' is reinstated (' . strtolower(Professional::STATUSES[$professional->status]) . ').';
        }

        Audit::log('caregiver.' . ($data['action'] === 'suspend' ? 'suspended' : 'reinstated'), $professional, ['reason' => $data['reason'] ?? null]);

        return back()->with('os_flash', [$data['action'] === 'suspend' ? 'attn' : 'stable', $message]);
    }
}
