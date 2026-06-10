<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $fillable = [
        'tenant_id',
        'bulan',
        'tahun',
        'target_kas',
        'status',
    ];

    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class);
    }
    public function tenant()
    {
        return $this->belongsTo(Tenants::class);
    }
}
