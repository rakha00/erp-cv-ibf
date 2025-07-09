<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use SoftDeletes;

    protected $table = "karyawans";

    protected $fillable = [
        'nik',
        'nama',
        'jabatan',
        'status',
        'no_hp',
        'alamat',
        'gaji_pokok',
        'remarks',
    ];

    public function penghasilanKaryawanDetails()
    {
        return $this->hasMany(PenghasilanKaryawanDetail::class);
    }
}
