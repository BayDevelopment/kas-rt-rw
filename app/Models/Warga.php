<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'nik', 'no_rumah', 'rt', 'rw', 'no_hp', 'jabatan', 'status'])]
class Warga extends Model
{
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class);
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }
}