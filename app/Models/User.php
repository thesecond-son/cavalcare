<?php

namespace App\Models;

use App\Support\AdminOs\OsRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public const TYPE_FAMILY       = 'family';
    public const TYPE_PROFESSIONAL = 'professional';
    public const TYPE_ADMIN        = 'admin';

    /**
     * Mass assignable. Security fields (two_factor_*, type) are set
     * explicitly in code, never from request input.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_e164',
        'country_code',
        'time_zone',
        'preferred_currency',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_last_step',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'         => 'datetime',
            'phone_verified_at'         => 'datetime',
            'last_seen_at'              => 'datetime',
            'password'                  => 'hashed',
            'two_factor_secret'         => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at'   => 'datetime',
            'two_factor_last_step'      => 'integer',
        ];
    }

    // ─── Caval OS ─────────────────────────────────────────────────────────

    /** True when the user holds any Caval OS staff role. */
    public function isOsStaff(): bool
    {
        return $this->hasAnyRole(OsRoles::all());
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
    }

    // ─── Relationships ────────────────────────────────────────────────────

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /** Website bookings linked to this app account. */
    public function preBookings()
    {
        return $this->hasMany(PreBooking::class);
    }

    // ─── Filament (old admin, kept until Caval OS replaces it) ────────────

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole([OsRoles::LEGACY_ADMIN, OsRoles::SUPER_ADMIN]);
    }
}
