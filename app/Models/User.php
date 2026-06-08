<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'rt', 'rw', 'no_hp', 'is_active', 'warga_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_ADMIN        = 'admin';
    const ROLE_PENGURUS_RW  = 'pengurus_rw';
    const ROLE_BENDAHARA_RT = 'bendahara_rt';
    const ROLE_WARGA        = 'warga';

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

    public function canAccessPanel(): bool
    {
        return $this->is_active && $this->role !== self::ROLE_WARGA;
    }
}