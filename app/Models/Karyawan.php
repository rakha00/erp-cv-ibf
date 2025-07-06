<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = "karyawans";

    protected $fillable = [
        'nama',
        'jabatan',
        'no_hp',
        'alamat',
        'gaji_pokok',
        'remarks',
    ];

    public function penghasilanKaryawanDetails()
    {
        return $this->hasMany(PengahasilanKaryawanDetail::class);
    }
}
