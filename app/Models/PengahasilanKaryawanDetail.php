<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengahasilanKaryawanDetail extends Model
{
    protected $table = "pengahasilan_karyawan_details";

    protected $fillable = [
        'karyawan_id',
        'kasbon',
        'lembur',
        'bonus',
        'tanggal',
        'remarks',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
