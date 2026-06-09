<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenants extends Model
{
     use HasFactory;

    protected $fillable = [
        'nama',
        'kode',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function wargas(): HasMany
    {
        return $this->hasMany(Warga::class, 'tenant_id', 'id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id', 'id');
    }

    public function periodes(): HasMany
    {
        return $this->hasMany(Periode::class, 'tenant_id', 'id');
    }

    public function pemasukans(): HasMany
    {
        return $this->hasMany(Pemasukan::class, 'tenant_id', 'id');
    }

    public function pengeluarans(): HasMany
    {
        return $this->hasMany(Pengeluaran::class, 'tenant_id', 'id');
    }
}
