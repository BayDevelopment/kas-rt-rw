<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'rt', 'rw', 'no_hp', 'is_active', 'warga_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN        = 'admin';
    public const ROLE_PENGURUS_RW  = 'pengurus_rw';
    public const ROLE_BENDAHARA_RT = 'bendahara_rt';
    public const ROLE_WARGA        = 'warga';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPengurusRw(): bool
    {
        return $this->role === self::ROLE_PENGURUS_RW;
    }

    public function isBendaharaRt(): bool
    {
        return $this->role === self::ROLE_BENDAHARA_RT;
    }

    public function isWarga(): bool
    {
        return $this->role === self::ROLE_WARGA;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        return $this->hasVerifiedEmail();
    }
}
