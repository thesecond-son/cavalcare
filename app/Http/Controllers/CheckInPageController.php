<?php

namespace App\Http\Controllers;

use App\Models\Care\CheckInLink;
use App\Services\Care\CheckInCodeService;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * cavalcare.org/c/{code}: the page on the patient's phone (backend spec 13.3).
 * No login. Shows the rotating code only during a visit window, with the
 * caregiver's name, so the patient knows who to expect. Nothing else.
 */
class CheckInPageController extends Controller
{
    public function __construct(private CheckInCodeService $codes)
    {
    }

    public function show(string $code)
    {
        return response()->view('checkin.show', ['linkCode' => $code] + $this->state($code))
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Referrer-Policy', 'no-referrer');
    }

    /** Polled by the page every few seconds. */
    public function now(string $code)
    {
        return response()->json($this->state($code))->header('Cache-Control', 'no-store, private');
    }

    private function state(string $code): array
    {
        $link = CheckInLink::where('code', $code)->whereNull('revoked_at')->first();
        abort_unless($link, 404);

        $visit = $this->codes->activeVisit($link);
        if (! $visit) {
            return ['active' => false, 'support_phone' => config('cavalcare.support_phone')];
        }

        $value = $this->codes->codeAt($link, $visit, now());
        $qr = (new Writer(new ImageRenderer(new RendererStyle(240, 1), new SvgImageBackEnd())))->writeString('CAVAL:' . $value);

        return [
            'active'        => true,
            'checked_in'    => $visit->state === 'checked_in',
            'code'          => $value,
            'qr_svg'        => $qr,
            'seconds_left'  => $this->codes->secondsLeft(),
            'caregiver'     => $visit->professional?->name,
            'caregiver_role' => $visit->professional?->title === 'rn' ? 'Registered nurse' : 'Caregiver',
            'starts'        => $visit->scheduled_start->timezone('Africa/Lagos')->format('H:i'),
            'support_phone' => config('cavalcare.support_phone'),
        ];
    }
}
