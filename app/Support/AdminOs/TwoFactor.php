<?php

namespace App\Support\AdminOs;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * TOTP two-factor for Caval OS (Google Authenticator, 1Password, Authy...).
 * 30-second codes, one step of clock drift allowed, and a used step can
 * never be accepted twice (replay protection via two_factor_last_step).
 */
class TwoFactor
{
    public function __construct(private Google2FA $engine = new Google2FA())
    {
        $this->engine->setWindow(1);
    }

    public function newSecret(): string
    {
        return $this->engine->generateSecretKey(32);
    }

    public function qrSvg(User $user, string $secret): string
    {
        $uri = $this->engine->getQRCodeUrl('Caval OS', $user->email, $secret);

        return (new Writer(new ImageRenderer(new RendererStyle(200, 1), new SvgImageBackEnd())))->writeString($uri);
    }

    /** Returns the accepted time step, or null when the code is wrong or already used. */
    public function verify(string $secret, string $code, ?int $lastStep = null): ?int
    {
        $code = preg_replace('/\D/', '', $code);
        if (strlen($code) !== 6) {
            return null;
        }

        $step = $this->engine->verifyKeyNewer($secret, $code, $lastStep);

        return $step === false ? null : (int) ($step === true ? $this->engine->getTimestamp() : $step);
    }

    /** Plain recovery codes to show once; store only their hashes. */
    public function newRecoveryCodes(int $count = 8): array
    {
        return collect(range(1, $count))
            ->map(fn () => Str::upper(Str::random(5) . '-' . Str::random(5)))
            ->all();
    }

    public function hashCodes(array $codes): array
    {
        return array_map(fn ($c) => Hash::make($c), $codes);
    }

    /** Consumes a matching recovery code. Returns true when one matched. */
    public function useRecoveryCode(User $user, string $input): bool
    {
        $input  = Str::upper(trim($input));
        $hashes = $user->two_factor_recovery_codes ?? [];

        foreach ($hashes as $i => $hash) {
            if (Hash::check($input, $hash)) {
                unset($hashes[$i]);
                $user->forceFill(['two_factor_recovery_codes' => array_values($hashes)])->save();

                return true;
            }
        }

        return false;
    }
}
