<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengahasilanKaryawanDetail extends Model
{
    use SoftDeletes;

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
