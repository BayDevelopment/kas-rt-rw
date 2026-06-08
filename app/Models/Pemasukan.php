<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    protected $fillable = [
        'warga_id',
        'periode_id',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
