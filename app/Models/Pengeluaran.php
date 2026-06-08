<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $fillable = [
        'periode_id',
        'jumlah',
        'tanggal',
        'keterangan',
        'kategori',
    ];

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
